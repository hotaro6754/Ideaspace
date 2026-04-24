import { supabase } from '@/lib/supabase';
import { logger } from '@/lib/logger';

export interface Comment {
  id: string;
  user_id: string;
  idea_id?: string;
  bounty_id?: string;
  news_id?: string;
  parent_id?: string;
  content: string;
  created_at: string;
  profiles?: {
    full_name: string;
  };
  replies?: Comment[];
}

export class CommentService {
  static async getComments(targetId: string, targetType: 'idea' | 'bounty' | 'news') {
    try {
      const column = `${targetType}_id`;
      const { data, error } = await supabase
        .from('comments')
        .select(`
          *,
          profiles:user_id (full_name)
        `)
        .eq(column, targetId)
        .order('created_at', { ascending: true });

      if (error) throw error;
      return this.buildTree(data as Comment[]);
    } catch (error) {
      logger.error('CommentService', 'Failed to fetch comments', error);
      return [];
    }
  }

  private static buildTree(comments: Comment[]): Comment[] {
    const map = new Map<string, Comment>();
    const tree: Comment[] = [];
    comments.forEach(c => {
      map.set(c.id, { ...c, replies: [] });
    });
    map.forEach(c => {
      if (c.parent_id && map.has(c.parent_id)) {
        map.get(c.parent_id)!.replies!.push(c);
      } else {
        tree.push(c);
      }
    });
    return tree;
  }

  static async postComment(data: {
    userId: string;
    targetId: string;
    targetType: 'idea' | 'bounty' | 'news';
    content: string;
    parentId?: string;
  }) {
    const column = `${data.targetType}_id`;
    const { data: result, error } = await supabase
      .from('comments')
      .insert({
        user_id: data.userId,
        [column]: data.targetId,
        content: data.content,
        parent_id: data.parentId
      })
      .select(`
        *,
        profiles:user_id (full_name)
      `)
      .single();

    if (error) throw error;
    return result;
  }
}
