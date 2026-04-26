import { supabase } from './supabase';
import { logger } from './logger';

const ALLOWED_DOMAINS = ['lendi.org', 'liethub.org', 'lendi.edu.in'];

export const validateLendiEmail = (email: string): boolean => {
  if (!email) return false;
  const domain = email.split('@')[1];
  return ALLOWED_DOMAINS.includes(domain?.toLowerCase());
};

export const signUp = async (email: string, password: string, fullName: string) => {
  if (!validateLendiEmail(email)) {
    logger.slop('Auth', 'Registration attempt with non-Lendi email', { email });
    throw new Error('Access restricted to Lendi College email addresses only (@lendi.org, @liethub.org, or @lendi.edu.in).');
  }

  const { data, error } = await supabase.auth.signUp({
    email,
    password,
    options: {
      data: {
        full_name: fullName,
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
    throw new Error('Access restricted to Lendi College email addresses only.');
  }

  logger.info('Auth', 'User logged in', { userId: data.user?.id });
  return data;
};

export const signInWithGoogle = async () => {
  const { data, error } = await supabase.auth.signInWithOAuth({
    provider: 'google',
    options: {
      redirectTo: `${window.location.origin}/auth/callback`,
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
