"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Reply, CornerDownRight, Send, Loader2 } from "lucide-react";
import { Comment, CommentService } from "@/services/CommentService";
import { Button } from "@/components/ui/Button";
import { supabase } from "@/lib/supabase";

interface CommentItemProps {
  comment: Comment;
  targetId: string;
  targetType: 'idea' | 'bounty' | 'news';
  onReplyAdded: () => void;
  depth?: number;
}

export const CommentItem = ({ comment, targetId, targetType, onReplyAdded, depth = 0 }: CommentItemProps) => {
  const [isReplying, setIsReplying] = useState(false);
  const [replyText, setReplyText] = useState("");
  const [loading, setLoading] = useState(false);

  const handleReply = async () => {
    if (!replyText || loading) return;
    setLoading(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;
      await CommentService.postComment({
        userId: user.id,
        targetId,
        targetType,
        content: replyText,
        parentId: comment.id
      });
      setReplyText("");
      setIsReplying(false);
      onReplyAdded();
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className={`space-y-4 ${depth > 0 ? "ml-6 mt-4 border-l border-white/5 pl-6" : ""}`}>
      <div className="group relative">
        <div className="flex items-center gap-2 mb-1">
          <div className="w-5 h-5 rounded-full bg-white/5 flex items-center justify-center text-[8px] font-bold text-white/40">
            {comment.profiles?.full_name?.[0] || 'A'}
          </div>
          <span className="text-[10px] font-black uppercase tracking-widest text-white/40">
            {comment.profiles?.full_name}
          </span>
          <span className="text-[8px] opacity-20 font-medium">
            {new Date(comment.created_at).toLocaleTimeString()}
          </span>
        </div>
        <p className="text-xs text-white/70 leading-relaxed font-medium">{comment.content}</p>
        <button onClick={() => setIsReplying(!isReplying)} className="mt-2 flex items-center gap-1 text-[8px] font-black uppercase tracking-widest text-white/20 hover:text-lendi-blue transition-colors">
          <Reply className="w-2.5 h-2.5" />Reply
        </button>
      </div>
      <AnimatePresence>
        {isReplying && (
          <motion.div initial={{ opacity: 0, x: -10 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: -10 }} className="flex gap-2">
            <div className="pt-2"><CornerDownRight className="w-3 h-3 text-white/20" /></div>
            <input type="text" placeholder="Write a reply..." value={replyText} onChange={(e) => setReplyText(e.target.value)} className="flex-1 bg-white/5 border border-white/5 rounded-lg px-3 py-1.5 text-[10px] focus:outline-none focus:border-lendi-blue/50" />
            <Button className="h-8 w-8 p-0 rounded-lg" onClick={handleReply} disabled={!replyText}>
              {loading ? <Loader2 className="w-3 h-3 animate-spin" /> : <Send className="w-3 h-3 mx-auto" />}
            </Button>
          </motion.div>
        )}
      </AnimatePresence>
      {comment.replies && comment.replies.length > 0 && (
        <div className="space-y-4">
          {comment.replies.map(reply => (
            <CommentItem key={reply.id} comment={reply} targetId={targetId} targetType={targetType} onReplyAdded={onReplyAdded} depth={depth + 1} />
          ))}
        </div>
      )}
    </div>
  );
};
