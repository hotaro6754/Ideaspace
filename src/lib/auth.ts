import { supabase } from './supabase';
import { logger } from './logger';

const ALLOWED_DOMAINS = ['lendi.org', 'liethub.org', 'lendi.edu.in'];

/**
 * Validates if the email belongs to Lendi domains and extracts metadata if possible.
 */
export const validateLendiEmail = (email: string): boolean => {
  if (!email) return false;
  const lowercaseEmail = email.toLowerCase();

  // Basic domain check
  const hasValidDomain = ALLOWED_DOMAINS.some(domain => lowercaseEmail.endsWith(`@${domain}`));

  // Specific check for lendi.edu.in roll number format (e.g., 25kd1a0580@lendi.edu.in)
  const rollNoPattern = /^[0-9]{2}[a-z0-9]{2}[0-9][a-z0-9]{1}[0-9]{4}@lendi\.edu\.in$/;
  const isRollNoFormat = rollNoPattern.test(lowercaseEmail);

  return hasValidDomain || isRollNoFormat;
};

/**
 * Extracts student/faculty metadata from the email for profile enrichment.
 */
export const extractEmailMetadata = (email: string) => {
  if (!email) return null;
  const [localPart, domain] = email.toLowerCase().split('@');

  // Example for roll number: 25kd1a0580
  // 25 = Joining Year, kd = College Code, 1 = Category, a = Dept code, 05 = Course code, 80 = Serial
  if (domain === 'lendi.edu.in' && localPart.length >= 10) {
    return {
      role: 'student',
      joiningYear: `20${localPart.substring(0, 2)}`,
      rollNumber: localPart.toUpperCase(),
      branchCode: localPart.substring(6, 8).toUpperCase(),
    };
  }

  if (domain === 'lendi.org' || domain === 'liethub.org') {
    return { role: 'faculty' };
  }

  return { role: 'guest' };
};

export const signUp = async (email: string, password: string, fullName: string) => {
  if (!validateLendiEmail(email)) {
    logger.slop('Auth', 'Registration attempt with non-Lendi email', { email });
    throw new Error('Access restricted to Lendi College institutional email addresses (@lendi.org, @liethub.org, or @lendi.edu.in).');
  }

  const metadata = extractEmailMetadata(email);

  const { data, error } = await supabase.auth.signUp({
    email,
    password,
    options: {
      data: {
        full_name: fullName,
        ...metadata,
      },
    },
  });

  if (error) {
    logger.error('Auth', 'Registration failed', error);
    throw error;
  }

  logger.info('Auth', 'User registered successfully', { userId: data.user?.id });
  return data;
};

export const signIn = async (email: string, password: string) => {
  const { data, error } = await supabase.auth.signInWithPassword({
    email,
    password,
  });

  if (error) {
    logger.error('Auth', 'Login failed', error);
    throw error;
  }

  if (data.user?.email && !validateLendiEmail(data.user.email)) {
    await supabase.auth.signOut();
    throw new Error('Access restricted to verified institutional accounts only.');
  }

  logger.info('Auth', 'User logged in', { userId: data.user?.id });
  return data;
};

export const signInWithGoogle = async () => {
  const { data, error } = await supabase.auth.signInWithOAuth({
    provider: 'google',
    options: {
      redirectTo: `${window.location.origin}/auth/callback`,
      queryParams: {
        prompt: 'select_account',
      },
    },
  });

  if (error) {
    logger.error('Auth', 'Google login failed', error);
    throw error;
  }

  return data;
};

export const signInWithGitHub = async () => {
  const { data, error } = await supabase.auth.signInWithOAuth({
    provider: 'github',
    options: {
      redirectTo: `${window.location.origin}/auth/callback`,
    },
  });

  if (error) {
    logger.error('Auth', 'GitHub login failed', error);
    throw error;
  }

  return data;
};

export const signOut = async () => {
  const { error } = await supabase.auth.signOut();
  if (error) logger.error('Auth', 'Logout failed', error);
};
