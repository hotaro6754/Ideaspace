import { supabase } from './supabase';
import { logger } from './logger';

const ALLOWED_DOMAINS = ['lendi.org', 'liethub.org'];

export const validateLendiEmail = (email: string): boolean => {
  const domain = email.split('@')[1];
  return ALLOWED_DOMAINS.includes(domain?.toLowerCase());
};

export const signUp = async (email: string, password: string, fullName: string) => {
  if (!validateLendiEmail(email)) {
    logger.slop('Auth', 'Registration attempt with non-Lendi email', { email });
    throw new Error('Access restricted to Lendi College email addresses only (@lendi.org or @liethub.org).');
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

  logger.info('Auth', 'User logged in', { userId: data.user?.id });
  return data;
};

export const signOut = async () => {
  const { error } = await supabase.auth.signOut();
  if (error) logger.error('Auth', 'Logout failed', error);
};
