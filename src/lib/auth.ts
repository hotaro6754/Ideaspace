import { supabase } from './supabase';
import { logger } from './logger';

const ALLOWED_DOMAINS = ['lendi.org', 'liethub.org', 'lendi.edu.in'];

export const validateLendiEmail = (email: string): boolean => {
  if (!email) return false;
  const domain = email.split('@')[1];
  const isValid = ALLOWED_DOMAINS.includes(domain?.toLowerCase());

  if (!isValid) {
    logger.slop('Auth', 'Domain validation failed', { email, domain });
  }

  return isValid;
};

export const signUp = async (email: string, password: string, fullName: string) => {
  if (!validateLendiEmail(email)) {
    throw new Error('Access restricted to Lendi College email addresses only (@lendi.org, @liethub.org, or @lendi.edu.in).');
  }

  const { data, error } = await supabase.auth.signUp({
    email,
    password,
    options: {
      data: {
        full_name: fullName,
        onboarded: false
      },
    },
  });

  if (error) {
    logger.error('Auth', 'Registration failed', error);
    throw error;
  }

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
    throw new Error('Access restricted to Lendi College email addresses only.');
  }

  return data;
};

export const signInWithGoogle = async () => {
  return await supabase.auth.signInWithOAuth({
    provider: 'google',
    options: {
      redirectTo: `${window.location.origin}/auth/callback`,
    },
  });
};

export const signInWithGitHub = async () => {
  return await supabase.auth.signInWithOAuth({
    provider: 'github',
    options: {
      redirectTo: `${window.location.origin}/auth/callback`,
    },
  });
};

export const signOut = async () => {
  await supabase.auth.signOut();
};
