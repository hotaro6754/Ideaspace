"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { X, Send, Loader2, Sparkles, Code2, Plus, Target, Layout } from "lucide-react";
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
  const [techStack, setTechStack] = useState<string[]>([]);
  const [currentTag, setCurrentTag] = useState("");
  const [githubUrl, setGithubUrl] = useState("");
  const [liveUrl, setLiveUrl] = useState("");
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
    if (!title || !description || loading) return;
    setLoading(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) throw new Error("Unauthorized access");

      const { error } = await supabase.from("projects").insert({
        user_id: user.id,
        title,
        description,
        tech_stack: techStack,
        github_url: githubUrl,
        live_url: liveUrl,
        status: "discuss"
      });

      if (error) throw error;

      logger.info("Projects", "Mission spawned", { title });
      onSuccess();
      onClose();
      // Reset form
      setTitle("");
      setDescription("");
      setTechStack([]);
      setGithubUrl("");
      setLiveUrl("");
    } catch (error) {
      logger.error("Projects", "Failed to spawn mission", error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <>
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="fixed inset-0 bg-black/60 backdrop-blur-md z-[150]"
          />
          <motion.div
            initial={{ opacity: 0, scale: 0.95, y: 20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.95, y: 20 }}
            className="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl inst-card p-10 z-[151] shadow-premium overflow-hidden"
          >
            <div className="flex items-center justify-between mb-10 pb-6 border-b border-border">
              <div className="flex items-center gap-4">
                <div className="w-12 h-12 rounded-2xl bg-lendi-blue/10 flex items-center justify-center text-lendi-blue shadow-sm">
                  <Target size={24} />
                </div>
                <div>
                  <h2 className="text-2xl font-black tracking-tight uppercase">Spawn New Mission</h2>
                  <p className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mt-1">Initiate Innovation Protocol</p>
                </div>
              </div>
              <button onClick={onClose} className="p-2 hover:bg-secondary rounded-xl transition-colors">
                <X className="w-6 h-6 text-muted-foreground" />
              </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
              <div className="space-y-8">
                <div className="space-y-3">
                  <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1 text-balance">Mission Title</label>
                  <input
                    type="text"
                    placeholder="Brief name for the mission..."
                    value={title}
                    onChange={(e) => setTitle(e.target.value)}
                    className="w-full bg-secondary border border-border rounded-2xl px-6 h-14 text-sm font-bold focus:outline-none focus:border-lendi-blue transition-all shadow-sm"
                  />
                </div>
                <div className="space-y-3">
                  <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Objective & Scope</label>
                  <textarea
                    placeholder="Describe the institutional impact and technical scope..."
                    value={description}
                    onChange={(e) => setDescription(e.target.value)}
                    className="w-full bg-secondary border border-border rounded-2xl p-6 min-h-[180px] text-sm font-medium focus:outline-none focus:border-lendi-blue transition-all resize-none shadow-sm"
                  />
                </div>
              </div>

              <div className="space-y-8">
                <div className="space-y-6">
                  <div className="space-y-3">
                    <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">GitHub Repository (Optional)</label>
                    <input
                      type="url"
                      placeholder="https://github.com/liethub/..."
                      value={githubUrl}
                      onChange={(e) => setGithubUrl(e.target.value)}
                      className="w-full bg-secondary border border-border rounded-2xl px-6 h-14 text-xs font-bold focus:outline-none focus:border-lendi-blue transition-all"
                    />
                  </div>
                  <div className="space-y-3">
                    <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Live Deployment (Optional)</label>
                    <input
                      type="url"
                      placeholder="https://..."
                      value={liveUrl}
                      onChange={(e) => setLiveUrl(e.target.value)}
                      className="w-full bg-secondary border border-border rounded-2xl px-6 h-14 text-xs font-bold focus:outline-none focus:border-lendi-blue transition-all"
                    />
                  </div>
                </div>

                <div className="space-y-4">
                  <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Technical Stack</label>
                  <div className="flex flex-wrap gap-2 mb-2">
                    {techStack.map(tag => (
                      <span key={tag} className="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase">
                        {tag}
                        <button onClick={() => removeTag(tag)} className="hover:text-lendi-red transition-colors"><X className="w-3 h-3" /></button>
                      </span>
                    ))}
                  </div>
                  <div className="relative flex gap-3">
                    <div className="relative flex-1">
                      <Code2 className="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/40" />
                      <input
                        type="text"
                        placeholder="Add module (e.g., React, Python)..."
                        value={currentTag}
                        onChange={(e) => setCurrentTag(e.target.value)}
                        onKeyDown={(e) => e.key === 'Enter' && addTag()}
                        className="w-full bg-secondary border border-border rounded-xl pl-14 pr-6 h-12 text-xs font-bold focus:outline-none focus:border-lendi-blue transition-all shadow-sm"
                      />
                    </div>
                    <Button onClick={addTag} variant="secondary" className="h-12 w-12 rounded-xl border border-border">
                      <Plus className="w-6 h-6" />
                    </Button>
                  </div>
                </div>
              </div>
            </div>

            <div className="mt-12 pt-8 border-t border-border flex items-center justify-between">
              <div className="flex items-center gap-3">
                <div className="w-2 h-2 rounded-full bg-green-500 animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]" />
                <span className="text-[10px] font-black text-muted-foreground uppercase tracking-widest">Protocol System Ready</span>
              </div>
              <Button
                onClick={handleSubmit}
                disabled={loading || !title || !description}
                className="h-16 px-12 rounded-[2rem] font-black uppercase tracking-[0.2em] text-xs shadow-lendi flex gap-4"
              >
                {loading ? <Loader2 className="w-6 h-6 animate-spin" /> : (
                  <>
                    Initialize Track
                    <Send className="w-5 h-5" />
                  </>
                )}
              </Button>
            </div>
          </motion.div>
        </>
      )}
    </AnimatePresence>
  );
};
