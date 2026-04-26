import { supabase } from '@/lib/supabase';
import { logger } from '@/lib/logger';

export interface XPTransaction {
  id: string;
  user_id: string;
  amount: number;
  reason: string;
  category: 'project' | 'bounty' | 'community' | 'referral';
  created_at: string;
}

export const XPService = {
  async getUserXP(userId: string) {
    const { data, error } = await supabase
      .from('profiles')
      .select('xp, rank')
      .eq('id', userId)
      .single();

    if (error) {
      logger.error('XPService', 'Failed to fetch user XP', error);
      throw error;
    }
    return data;
  },

  async getXPTransactions(userId: string) {
    const { data, error } = await supabase
      .from('xp_transactions')
      .select('*')
      .eq('user_id', userId)
      .order('created_at', { ascending: false });

    if (error) {
      logger.error('XPService', 'Failed to fetch XP transactions', error);
      throw error;
    }
    return data as XPTransaction[];
  },

  async getLeaderboard(limit = 10) {
    const { data, error } = await supabase
      .from('profiles')
      .select('id, full_name, xp, rank, department')
      .order('xp', { ascending: false })
      .limit(limit);

    if (error) {
      logger.error('XPService', 'Failed to fetch leaderboard', error);
      throw error;
    }
    return data;
  },

  async awardXP(userId: string, amount: number, reason: string, category: XPTransaction['category']) {
    const { data, error } = await supabase.rpc('award_xp', {
      p_user_id: userId,
      p_amount: amount,
      p_reason: reason,
      p_category: category
    });

    if (error) {
      logger.error('XPService', 'Failed to award XP', error);
      throw error;
    }
    return data;
  }
};
