import { supabase } from '@/lib/supabase';
import { logger } from '@/lib/logger';

export interface FeedItem {
  id: string;
  type: 'idea' | 'bounty' | 'news';
  title: string;
  description: string;
  author_id: string;
  author_name: string;
  created_at: string;
  domain?: string;
  category?: string;
  points_reward?: number;
  upvotes?: number;
  is_followed?: boolean;
  image_url?: string;
  external_url?: string;
}

export class FeedService {
  static async getPersonalizedFeed(userId: string, limit = 10, offset = 0) {
    try {
      logger.info('Feed', 'Fetching personalized feed batch', { userId, offset });

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

      const { data: rawIdeas, error: ideasError } = await supabase
        .from('ideas')
        .select(`
          id,
          title,
          description,
          domain,
          user_id,
          upvotes_count,
          created_at,
          profiles:user_id (full_name)
        `)
        .order('created_at', { ascending: false })
        .range(offset, offset + limit - 1);

      if (ideasError) throw ideasError;

      const { data: rawBounties, error: bountiesError } = await supabase
        .from('bounties')
        .select('*')
        .eq('status', 'open')
        .order('created_at', { ascending: false })
        .range(offset, offset + limit - 1);

      if (bountiesError) throw bountiesError;

      const { data: rawNews, error: newsError } = await supabase
        .from('news')
        .select('*')
        .order('created_at', { ascending: false })
        .range(offset, offset + limit - 1);

      if (newsError) throw newsError;

      const items: FeedItem[] = [
        ...(rawIdeas?.map(i => ({
          id: i.id,
          type: 'idea' as const,
          title: i.title,
          description: i.description,
          domain: i.domain,
          author_id: i.user_id,
          author_name: (i.profiles as any)?.full_name || 'Anonymous',
          upvotes: i.upvotes_count,
          created_at: i.created_at,
          is_followed: followedIds.includes(i.user_id)
        })) || []),
        ...(rawBounties?.map(b => ({
          id: b.id,
          type: 'bounty' as const,
          title: b.title,
          description: b.description,
          points_reward: b.points_reward,
          author_id: 'system',
          author_name: 'Lendi Faculty',
          created_at: b.created_at,
          is_followed: false
        })) || []),
        ...(rawNews?.map(n => ({
          id: n.id,
          type: 'news' as const,
          title: n.title,
          description: n.content,
          category: n.category,
          author_id: n.author_id || 'system',
          author_name: 'Tech Desk',
          created_at: n.created_at,
          image_url: n.image_url,
          external_url: n.external_url,
          is_followed: false
        })) || [])
      ];

      const rankedFeed = items.sort((a, b) => {
        let scoreA = 0;
        let scoreB = 0;
        if (a.is_followed) scoreA += 50;
        if (b.is_followed) scoreB += 50;
        if (a.domain && interests.includes(a.domain)) scoreA += 30;
        if (b.domain && interests.includes(b.domain)) scoreB += 30;
        if (a.type === 'news') scoreA += 10;
        if (b.type === 'news') scoreB += 10;
        scoreA += new Date(a.created_at).getTime() / 10000000000;
        scoreB += new Date(b.created_at).getTime() / 10000000000;
        return scoreB - scoreA;
      });

      return rankedFeed.slice(0, limit);
    } catch (error) {
      logger.error('Feed', 'Failed to generate feed batch', error);
      return [];
    }
  }
}
