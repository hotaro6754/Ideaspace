"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { X, Image as ImageIcon, Link as LinkIcon, Send, Terminal, Shield, Rocket, Loader2 } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { supabase } from "@/lib/supabase";
import { logger } from "@/lib/logger";

interface CreatePostModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSuccess: () => void;
}

export const CreatePostModal = ({ isOpen, onClose, onSuccess }: CreatePostModalProps) => {
  const [type, setType] = useState<"idea" | "bounty" | "news">("idea");
  const [title, setTitle] = useState("");
  const [content, setContent] = useState("");
  const [domain, setDomain] = useState("General");
  const [loading, setLoading] = useState(false);

  const handleSubmit = async () => {
    if (!title || !content) return;
    setLoading(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) throw new Error("Not authenticated");
      let error;
      if (type === "idea") {
        ({ error } = await supabase.from("ideas").insert({
          user_id: user.id,
          title,
          description: content,
          domain,
          status: "open"
        }));
      } else if (type === "news") {
        ({ error } = await supabase.from("news").insert({
          author_id: user.id,
          title,
          content,
          category: domain.toLowerCase()
        }));
      } else {
        ({ error } = await supabase.from("bounties").insert({
          title,
          description: content,
          problem_statement: content,
          deliverables: "TBD",
          deadline: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString(),
          points_reward: 500
        }));
      }
      if (error) throw error;
      logger.info("PostCreation", "New post created", { type, title });
      onSuccess();
      onClose();
      setTitle("");
      setContent("");
    } catch (error) {
      logger.error("PostCreation", "Failed to create post", error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <>
          <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={onClose} className="fixed inset-0 bg-black/80 backdrop-blur-md z-[100]" />
          <motion.div initial={{ opacity: 0, scale: 0.95, y: 20 }} animate={{ opacity: 1, scale: 1, y: 0 }} exit={{ opacity: 0, scale: 0.95, y: 20 }} className="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-2xl glass p-8 rounded-[2.5rem] z-[101] border border-white/10 shadow-2xl" >
            <div className="flex items-center justify-between mb-8">
              <h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Spawn New Transmission</h2>
              <button onClick={onClose} className="p-2 hover:bg-white/5 rounded-full transition-colors"><X className="w-5 h-5 text-white/40" /></button>
            </div>
            <div className="flex gap-4 mb-8 p-1.5 bg-white/5 rounded-2xl border border-white/5">
              {[ { id: "idea", icon: Terminal, label: "Idea" }, { id: "bounty", icon: Shield, label: "Bounty" }, { id: "news", icon: Rocket, label: "News" } ].map((t) => (
                <button key={t.id} onClick={() => setType(t.id as any)} className={`flex-1 flex items-center justify-center gap-2 py-3 rounded-xl transition-all duration-300 font-bold text-xs uppercase tracking-widest ${type === t.id ? "bg-lendi-blue text-white shadow-lg" : "text-white/40 hover:text-white/60"}`} >
                  <t.icon className="w-4 h-4" />
                  {t.label}
                </button>
              ))}
            </div>
            <div className="space-y-6">
              <input type="text" placeholder="Transmission Title..." value={title} onChange={(e) => setTitle(e.target.value)} className="w-full bg-transparent border-none text-xl font-bold focus:outline-none placeholder:text-white/10" />
              <textarea placeholder="Describe your vision..." value={content} onChange={(e) => setContent(e.target.value)} className="w-full bg-transparent border-none resize-none min-h-[150px] focus:outline-none text-white/60 leading-relaxed font-medium placeholder:text-white/10" />
              <div className="flex items-center justify-between pt-6 border-t border-white/5">
                <div className="flex gap-4">
                  <button className="flex items-center gap-2 text-white/20 hover:text-white transition-colors"><ImageIcon className="w-5 h-5" /><span className="text-[10px] font-black uppercase tracking-widest">Media</span></button>
                  <button className="flex items-center gap-2 text-white/20 hover:text-white transition-colors"><LinkIcon className="w-5 h-5" /><span className="text-[10px] font-black uppercase tracking-widest">Link</span></button>
                </div>
                <div className="flex items-center gap-4">
                  <select value={domain} onChange={(e) => setDomain(e.target.value)} className="bg-white/5 border border-white/5 rounded-xl px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white/40 focus:outline-none focus:border-lendi-blue/50" >
                    <option>General</option><option>AI/ML</option><option>Web Dev</option><option>Hardware</option><option>Campus Life</option>
                  </select>
                  <Button onClick={handleSubmit} disabled={loading || !title || !content} className="rounded-2xl px-8 h-12 font-black shadow-lg shadow-lendi-blue/20 flex gap-2">
                    {loading ? <Loader2 className="w-4 h-4 animate-spin" /> : <><Send className="w-4 h-4" />Transmit</>}
                  </Button>
                </div>
              </div>
            </div>
          </motion.div>
        </>
      )}
    </AnimatePresence>
  );
};
