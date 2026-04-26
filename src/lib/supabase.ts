import { createClient } from '@supabase/supabase-js';

const supabaseUrl = 'https://yaggidzzdkcvxebnffim.supabase.co';
const supabaseAnonKey = 'sb_publishable_4VlNwD5d9ZIe-mTsfKC91Q_g3zmiFs1';

export const supabase = createClient(supabaseUrl, supabaseAnonKey);
