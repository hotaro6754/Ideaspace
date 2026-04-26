import { createClient } from '@supabase/supabase-js';

const supabaseUrl = process.env.NEXT_PUBLIC_SUPABASE_URL || 'https://yaggidzzdkcvxebnffim.supabase.co';
const supabaseAnonKey = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY || 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InlhZ2dpZHp6ZGtjdnhlYm5mZmltIiwicm9sZSI6ImFub24iLCJpYXQiOjE3MTQxMjk0NTYsImV4cCI6MjAzOTcwNTQ1Nn0.fallback';

export const supabase = createClient(supabaseUrl, supabaseAnonKey);
