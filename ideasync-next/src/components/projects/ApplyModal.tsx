"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { X, Send, Loader2, Sparkles, Code2, Plus } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { supabase } from "@/lib/supabase";
import { logger } from "@/lib/logger";

interface ApplyModalProps {
  isOpen: boolean;
  onClose: () => void;
  projectId: string;
  projectTitle: string;
}

export const ApplyModal = ({ isOpen, onClose, projectId, projectTitle }: ApplyModalProps) => {
  const [message, setMessage] = useState("");
  const [skills, setSkills] = useState<string[]>([]);
  const [currentSkill, setCurrentSkill] = useState("");
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);

  const addSkill = () => {
    if (currentSkill && !skills.includes(currentSkill)) {
      setSkills([...skills, currentSkill]);
      setCurrentSkill("");
    }
  };

  const handleSubmit = async () => {
    if (!message || loading) return;
    setLoading(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;

      const { error } = await supabase.from("project_applications").insert({
        project_id: projectId,
        user_id: user.id,
        message,
        skills,
        status: "pending"
      });

      if (error) throw error;

      logger.info("Collaboration", "Application submitted", { projectId });
      setSuccess(true);
      setTimeout(() => {
        onClose();
        setSuccess(false);
      }, 3000);
    } catch (error) {
      logger.error("Collaboration", "Failed to submit application", error);
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
            className="fixed inset-0 bg-black/90 backdrop-blur-xl z-[150]"
          />
          <motion.div
            initial={{ opacity: 0, scale: 0.9, y: 20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.9, y: 20 }}
            className="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xl glass p-10 rounded-[3rem] z-[151] border border-white/10 shadow-2xl"
          >
            {success ? (
              <div className="py-12 flex flex-col items-center text-center space-y-6">
                <div className="w-20 h-20 rounded-full bg-green-500/20 flex items-center justify-center">
                  <Sparkles className="w-10 h-10 text-green-400" />
                </div>
                <div>
                  <h2 className="text-3xl font-black font-plus-jakarta mb-2">Request Transmitted</h2>
                  <p className="text-white/40 font-medium tracking-tight">The mission lead will review your credentials soon.</p>
                </div>
              </div>
            ) : (
              <>
                <div className="flex items-center justify-between mb-8">
                  <div>
                    <h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Join Mission</h2>
                    <p className="text-[10px] font-black uppercase tracking-[0.2em] text-lendi-blue mt-1">{projectTitle}</p>
                  </div>
                  <button onClick={onClose} className="p-2 hover:bg-white/5 rounded-full transition-colors">
                    <X className="w-5 h-5 text-white/20" />
                  </button>
                </div>

                <div className="space-y-8">
                  <div className="space-y-3">
                    <label className="text-[10px] font-black uppercase tracking-widest text-white/30 ml-1">Motivation / Contribution</label>
                    <textarea
                      placeholder="Why do you want to join? What can you contribute to this project?"
                      value={message}
                      onChange={(e) => setMessage(e.target.value)}
                      className="w-full bg-white/5 border border-white/5 rounded-2xl p-6 min-h-[120px] text-sm font-medium leading-relaxed focus:outline-none focus:border-lendi-blue/50 transition-all placeholder:text-white/10 resize-none"
                    />
                  </div>

                  <div className="space-y-3">
                    <label className="text-[10px] font-black uppercase tracking-widest text-white/30 ml-1">Relevant Skills</label>
                    <div className="flex flex-wrap gap-2 mb-3">
                      {skills.map(s => (
                        <span key={s} className="px-3 py-1 rounded-lg bg-white/5 border border-white/5 text-[10px] font-black text-white/40">{s}</span>
                      ))}
                    </div>
                    <div className="relative flex gap-2">
                      <div className="relative flex-1">
                        <Code2 className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20" />
                        <input
                          type="text"
                          placeholder="Add skill..."
                          value={currentSkill}
                          onChange={(e) => setCurrentSkill(e.target.value)}
                          onKeyDown={(e) => e.key === 'Enter' && addSkill()}
                          className="w-full bg-white/5 border border-white/5 rounded-xl pl-12 pr-6 h-12 text-xs font-medium focus:outline-none focus:border-lendi-blue/50 transition-all placeholder:text-white/10"
                        />
                      </div>
                      <button onClick={addSkill} className="h-12 w-12 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center hover:bg-white/10 transition-colors">
                        <Plus className="w-5 h-5 text-white/20" />
                      </button>
                    </div>
                  </div>

                  <Button
                    onClick={handleSubmit}
                    disabled={loading || !message}
                    className="w-full rounded-2xl h-16 font-black shadow-2xl shadow-lendi-blue/20 flex gap-3 text-lg mt-4"
                  >
                    {loading ? <Loader2 className="w-6 h-6 animate-spin" /> : (
                      <>
                        Transmit Application
                        <Send className="w-5 h-5" />
                      </>
                    )}
                  </Button>
                </div>
              </>
            )}
          </motion.div>
        </>
      )}
    </AnimatePresence>
  );
};
