"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { X, Globe, Code2, Rocket, Plus, Send, Loader2 } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { supabase } from "@/lib/supabase";
import { logger } from "@/lib/logger";

interface CreateProjectModalProps {
  isOpen: boolean;
  onClose: () => void;
  onSuccess: () => void;
}

export const CreateProjectModal = ({ isOpen, onClose, onSuccess }: CreateProjectModalProps) => {
  const [title, setTitle] = useState("");
  const [description, setDescription] = useState("");
  const [githubUrl, setGithubUrl] = useState("");
  const [liveUrl, setLiveUrl] = useState("");
  const [techStack, setTechStack] = useState<string[]>([]);
  const [currentTag, setCurrentTag] = useState("");
  const [loading, setLoading] = useState(false);

  const addTag = () => {
    if (currentTag && !techStack.includes(currentTag)) {
      setTechStack([...techStack, currentTag]);
      setCurrentTag("");
    }
  };

  const removeTag = (tag: string) => {
    setTechStack(techStack.filter(t => t !== tag));
  };

  const handleSubmit = async () => {
    if (!title || !description) return;
    setLoading(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) throw new Error("Not authenticated");
      const { error } = await supabase.from("projects").insert({
        user_id: user.id,
        title,
        description,
        github_url: githubUrl,
        live_url: liveUrl,
        tech_stack: techStack,
        status: "in_progress"
      });
      if (error) throw error;
      logger.info("ProjectCreation", "New project spawned", { title });
      onSuccess();
      onClose();
      setTitle(""); setDescription(""); setGithubUrl(""); setLiveUrl(""); setTechStack([]);
    } catch (error) {
      logger.error("ProjectCreation", "Failed to spawn project", error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <>
          <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={onClose} className="fixed inset-0 bg-black/90 backdrop-blur-xl z-[100]" />
          <motion.div initial={{ opacity: 0, scale: 0.95, y: 30 }} animate={{ opacity: 1, scale: 1, y: 0 }} exit={{ opacity: 0, scale: 0.95, y: 30 }} className="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-3xl glass p-10 rounded-[3rem] z-[101] border border-white/10 shadow-2xl overflow-y-auto max-h-[90vh] custom-scrollbar" >
            <div className="flex items-center justify-between mb-10">
              <div className="flex items-center gap-4"><div className="p-3 rounded-2xl bg-lendi-blue/20 text-lendi-blue"><Rocket className="w-6 h-6" /></div><div><h2 className="text-3xl font-black font-plus-jakarta tracking-tighter">Spawn Project</h2><p className="text-xs font-bold uppercase tracking-widest text-white/20">GitHub Layer Initialized</p></div></div>
              <button onClick={onClose} className="p-3 hover:bg-white/5 rounded-full group"><X className="w-6 h-6 text-white/20 group-hover:text-white" /></button>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
              <div className="space-y-8">
                <div className="space-y-3"><label className="text-[10px] font-black uppercase tracking-[0.2em] text-white/30 ml-1">Mission Name</label><input type="text" placeholder="Project Title" value={title} onChange={(e) => setTitle(e.target.value)} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm font-bold focus:outline-none focus:border-lendi-blue/50" /></div>
                <div className="space-y-3"><label className="text-[10px] font-black uppercase tracking-[0.2em] text-white/30 ml-1">Objective</label><textarea placeholder="Describe the problem..." value={description} onChange={(e) => setDescription(e.target.value)} className="w-full bg-white/5 border border-white/5 rounded-2xl p-6 min-h-[160px] text-sm font-medium focus:outline-none focus:border-lendi-blue/50 resize-none" /></div>
              </div>
              <div className="space-y-8">
                <div className="space-y-6">
                  <div className="space-y-3"><label className="text-[10px] font-black uppercase tracking-[0.2em] text-white/30 ml-1">GitHub Repository</label><input type="url" placeholder="https://github.com/..." value={githubUrl} onChange={(e) => setGithubUrl(e.target.value)} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-xs font-medium focus:outline-none focus:border-lendi-blue/50" /></div>
                  <div className="space-y-3"><label className="text-[10px] font-black uppercase tracking-[0.2em] text-white/30 ml-1">Live Deployment</label><input type="url" placeholder="https://..." value={liveUrl} onChange={(e) => setLiveUrl(e.target.value)} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-xs font-medium focus:outline-none focus:border-lendi-blue/50" /></div>
                </div>
                <div className="space-y-3"><label className="text-[10px] font-black uppercase tracking-[0.2em] text-white/30 ml-1">Tech Stack</label><div className="flex flex-wrap gap-2 mb-4">{techStack.map(tag => (<span key={tag} className="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue">{tag}<button onClick={() => removeTag(tag)}><X className="w-3 h-3" /></button></span>))}</div><div className="relative flex gap-2"><div className="relative flex-1"><Code2 className="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20" /><input type="text" placeholder="Add module..." value={currentTag} onChange={(e) => setCurrentTag(e.target.value)} onKeyDown={(e) => e.key === 'Enter' && addTag()} className="w-full bg-white/5 border border-white/5 rounded-2xl pl-14 pr-6 h-12 text-xs font-medium focus:outline-none focus:border-lendi-blue/50" /></div><button onClick={addTag} className="h-12 w-12 rounded-2xl bg-white/5 border border-white/5 flex items-center justify-center hover:bg-white/10"><Plus className="w-5 h-5 text-white/40" /></button></div></div>
              </div>
            </div>
            <div className="mt-12 pt-8 border-t border-white/5 flex items-center justify-between"><div className="flex items-center gap-2 text-[10px] font-black text-white/20 uppercase tracking-widest"><div className="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse" />System Ready</div><Button onClick={handleSubmit} disabled={loading || !title || !description} className="rounded-full px-12 h-16 font-black shadow-2xl shadow-lendi-blue/30 flex gap-3 text-lg">{loading ? <Loader2 className="w-6 h-6 animate-spin" /> : <>Spawn Mission<Send className="w-5 h-5" /></>}</Button></div>
          </motion.div>
        </>
      )}
    </AnimatePresence>
  );
};
