import { supabase } from '@/lib/supabase';
import { logger } from '@/lib/logger';

export interface FeedItem {
  id: string;
  type: 'project' | 'bounty' | 'news';
  title: string;
  description: string;
  author_id: string;
  author_name: string;
  created_at: string;
  domain?: string;
  category?: string;
  reward_amount?: number;
  upvotes?: number;
  is_followed?: boolean;
  image_url?: string;
  external_url?: string;
}

export class FeedService {
  static async getPersonalizedFeed(userId: string, limit = 10, offset = 0) {
    try {
      const { data: profile } = await supabase
        .from('profiles')
        .select('interests')
        .eq('id', userId)
        .single();

      const { data: following } = await supabase
        .from('follows')
        .select('following_id')
        .eq('follower_id', userId);

      const followedIds = following?.map(f => f.following_id) || [];
      const interests = profile?.interests || [];

      // Fetch Projects (formerly ideas)
      const { data: projects } = await supabase
        .from('projects')
        .select(`id, title, description, lead_id, created_at, stars_count, profiles:lead_id (full_name)`)
        .order('created_at', { ascending: false })
        .range(offset, offset + limit - 1);

      // Fetch Bounties
      const { data: bounties } = await supabase
        .from('bounties')
        .select('*')
        .eq('status', 'open')
        .order('created_at', { ascending: false })
        .range(offset, offset + limit - 1);

      // Fetch News
      const { data: news } = await supabase
        .from('news')
        .select('*')
        .order('created_at', { ascending: false })
        .range(offset, offset + limit - 1);

      const items: FeedItem[] = [
        ...(projects?.map(p => ({
          id: p.id,
          type: 'project' as const,
          title: p.title,
          description: p.description,
          author_id: p.lead_id,
          author_name: (p.profiles as any)?.full_name || 'Anonymous',
          upvotes: p.stars_count,
          created_at: p.created_at,
          is_followed: followedIds.includes(p.lead_id)
        })) || []),
        ...(bounties?.map(b => ({
          id: b.id,
          type: 'bounty' as const,
          title: b.title,
          description: b.description,
          reward_amount: b.reward_amount,
          author_id: 'system',
          author_name: 'Lendi Faculty',
          created_at: b.created_at,
          is_followed: false
        })) || []),
        ...(news?.map(n => ({
          id: n.id,
          type: 'news' as const,
          title: n.title,
          description: n.content,
          category: n.category,
          author_id: 'system',
          author_name: 'Institutional News',
          created_at: n.created_at,
          external_url: n.external_url,
          is_followed: false
        })) || [])
      ];

      // Sophisticated scoring for discovery
      const rankedFeed = items.sort((a, b) => {
        let scoreA = 0;
        let scoreB = 0;

        if (a.is_followed) scoreA += 50;
        if (b.is_followed) scoreB += 50;

        // Boost projects matching interests
        if (a.type === 'project' && interests.some(i => a.description.toLowerCase().includes(i.toLowerCase()))) scoreA += 30;
        if (b.type === 'project' && interests.some(i => b.description.toLowerCase().includes(i.toLowerCase()))) scoreB += 30;

        // Recency factor
        scoreA += new Date(a.created_at).getTime() / 1e12;
        scoreB += new Date(b.created_at).getTime() / 1e12;

        return scoreB - scoreA;
      });

      return rankedFeed.slice(0, limit);
    } catch (error) {
      logger.error('Feed', 'Batch synthesis failed', error);
      return [];
    }
  }
}
