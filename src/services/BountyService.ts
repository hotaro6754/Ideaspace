import { supabase } from '@/lib/supabase';
import { logger } from '@/lib/logger';

export interface Bounty {
  id: string;
  title: string;
  description: string;
  reward_amount: number;
  reward_type: 'XP' | 'INR';
  status: 'open' | 'closed' | 'archived';
  created_by: string;
  created_at: string;
  deadline: string;
  tags: string[];
}

export const BountyService = {
  async getAllBounties() {
    const { data, error } = await supabase
      .from('bounties')
      .select('*')
      .order('created_at', { ascending: false });

    if (error) {
      logger.error('BountyService', 'Failed to fetch bounties', error);
      throw error;
    }
    return data as Bounty[];
  },

  async getBountyById(id: string) {
    const { data, error } = await supabase
      .from('bounties')
      .select('*')
      .eq('id', id)
      .single();

    if (error) {
      logger.error('BountyService', 'Failed to fetch bounty', error);
      throw error;
    }
    return data as Bounty;
  },

  async submitBid(bountyId: string, studentId: string, proposal: string) {
    const { data, error } = await supabase
      .from('bounty_bids')
      .insert([{ bounty_id: bountyId, student_id: studentId, proposal }])
      .select()
      .single();

    if (error) {
      logger.error('BountyService', 'Failed to submit bid', error);
      throw error;
    }
    return data;
  }
};
