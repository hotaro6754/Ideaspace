"use client";

import { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { MessageSquare, ArrowUp, Share2, Tag, Rocket, Shield, Send, Loader2 } from "lucide-react";
import { FeedItem } from "@/services/FeedService";
import { Comment, CommentService } from "@/services/CommentService";
import { CommentItem } from "@/components/dashboard/CommentItem";
import { Button } from "@/components/ui/Button";
import { supabase } from "@/lib/supabase";
import { logger } from "@/lib/logger";

interface FeedCardProps {
  item: FeedItem;
  index: number;
}

export const FeedCard = ({ item, index }: FeedCardProps) => {
  const [upvoted, setUpvoted] = useState(false);
  const [upvotesCount, setUpvotesCount] = useState(item.upvotes || 0);
  const [showComments, setShowComments] = useState(false);
  const [commentText, setCommentText] = useState("");
  const [comments, setComments] = useState<Comment[]>([]);
  const [isBouncing, setIsBouncing] = useState(false);
  const [commenting, setCommenting] = useState(false);
  const [loadingComments, setLoadingComments] = useState(false);

  const isBounty = item.type === "bounty";
  const isNews = item.type === "news";

  const fetchComments = async () => {
    setLoadingComments(true);
    const data = await CommentService.getComments(item.id, item.type);
    setComments(data);
    setLoadingComments(false);
  };

  useEffect(() => {
    if (showComments) {
      fetchComments();
      const channel = supabase
        .channel(`comments-${item.id}`)
        .on('postgres_changes', {
          event: 'INSERT',
          schema: 'public',
          table: 'comments',
          filter: `${item.type}_id=eq.${item.id}`
        }, () => {
          fetchComments();
        })
        .subscribe();
      return () => {
        supabase.removeChannel(channel);
      };
    }
  }, [showComments, item.id, item.type]);

  const handleUpvote = async () => {
    if (upvoted) return;
    setIsBouncing(true);
    setUpvoted(true);
    setUpvotesCount(prev => prev + 1);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;
      await supabase.from("reactions").insert({
        user_id: user.id,
        target_id: item.id,
        target_type: item.type,
        reaction_type: "upvote"
      });
      if (item.type === "idea") {
        await supabase.rpc('increment_upvotes', { row_id: item.id });
      }
    } catch (error) {
      logger.error("FeedCard", "Upvote failed", error);
    } finally {
      setTimeout(() => setIsBouncing(false), 500);
    }
  };

  const handleComment = async () => {
    if (!commentText || commenting) return;
    setCommenting(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;
      await CommentService.postComment({
        userId: user.id,
        targetId: item.id,
        targetType: item.type,
        content: commentText
      });
      setCommentText("");
      fetchComments();
    } catch (error) {
      logger.error("FeedCard", "Failed to post comment", error);
    } finally {
      setCommenting(false);
    }
  };

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ delay: index * 0.1 }}
      className="glass rounded-3xl p-6 border border-white/5 hover:border-white/10 transition-all group relative overflow-hidden"
    >
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center gap-2">
          <div className={`p-1.5 rounded-lg ${
            isBounty ? "bg-red-500/20 text-red-400" :
            isNews ? "bg-lendi-blue/20 text-lendi-blue" :
            "bg-green-500/20 text-green-400"
          }`}>
            {isBounty ? <Shield className="w-4 h-4" /> : isNews ? <Rocket className="w-4 h-4" /> : <Tag className="w-4 h-4" />}
          </div>
          <span className="text-xs font-bold uppercase tracking-widest opacity-40">{item.type}</span>
        </div>
        <span className="text-[10px] opacity-30 font-medium">{new Date(item.created_at).toLocaleDateString()}</span>
      </div>

      <h3 className="text-xl font-bold font-plus-jakarta mb-2 group-hover:text-lendi-blue transition-colors">{item.title}</h3>
      <p className="text-white/50 text-sm leading-relaxed mb-6 line-clamp-3">{item.description}</p>

      <div className="flex items-center justify-between pt-4 border-t border-white/5">
        <div className="flex items-center gap-3">
          <div className="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-xs font-bold text-white/40">{item.author_name?.[0] || 'A'}</div>
          <div>
            <p className="text-xs font-bold">{item.author_name}</p>
            <p className="text-[10px] opacity-30">{item.domain || item.category || "General"}</p>
          </div>
        </div>

        <div className="flex items-center gap-4">
          <motion.button
            whileHover={{ scale: 1.1 }}
            whileTap={{ scale: 0.9, rotate: -5 }}
            animate={isBouncing ? { scale: [1, 1.4, 1] } : {}}
            onClick={handleUpvote}
            className={`flex items-center gap-1.5 transition-colors ${upvoted ? "text-lendi-blue" : "text-white/40 hover:text-white"}`}
          >
            <ArrowUp className={`w-4 h-4 ${upvoted ? "fill-current" : ""}`} />
            <span className="text-xs font-bold">{upvotesCount}</span>
          </motion.button>

          <motion.button
            whileHover={{ scale: 1.1 }}
            whileTap={{ scale: 0.9 }}
            onClick={() => setShowComments(!showComments)}
            className={`flex items-center gap-1.5 transition-colors ${showComments ? "text-lendi-blue" : "text-white/40 hover:text-white"}`}
          >
            <MessageSquare className="w-4 h-4" />
          </motion.button>

          <motion.button
            whileHover={{ scale: 1.1 }}
            whileTap={{ scale: 0.9 }}
            onClick={() => navigator.clipboard.writeText(`${window.location.origin}/post/${item.id}`)}
            className="text-white/40 hover:text-white transition-colors"
          >
            <Share2 className="w-4 h-4" />
          </motion.button>
        </div>
      </div>

      <AnimatePresence>
        {showComments && (
          <motion.div
            initial={{ height: 0, opacity: 0 }}
            animate={{ height: "auto", opacity: 1 }}
            exit={{ height: 0, opacity: 0 }}
            className="overflow-hidden mt-6 pt-6 border-t border-white/5"
          >
            <div className="flex gap-4 mb-8">
              <input
                type="text"
                placeholder="Write a comment..."
                value={commentText}
                onChange={(e) => setCommentText(e.target.value)}
                onKeyDown={(e) => e.key === 'Enter' && handleComment()}
                className="flex-1 bg-white/5 border border-white/5 rounded-xl px-4 py-2 text-xs focus:outline-none focus:border-lendi-blue/50"
              />
              <Button className="rounded-xl px-3 h-8" disabled={!commentText || commenting} onClick={handleComment}>
                {commenting ? <Loader2 className="w-3 h-3 animate-spin" /> : <Send className="w-3 h-3" />}
              </Button>
            </div>

            <div className="space-y-6">
              {loadingComments ? (
                <div className="flex justify-center py-4">
                  <Loader2 className="w-4 h-4 animate-spin text-white/10" />
                </div>
              ) : comments.map(c => (
                <CommentItem
                  key={c.id}
                  comment={c}
                  targetId={item.id}
                  targetType={item.type}
                  onReplyAdded={fetchComments}
                />
              ))}
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </motion.div>
  );
};
