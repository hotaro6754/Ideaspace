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

  const removeSkill = (skill: string) => {
    setSkills(skills.filter(s => s !== skill));
  };

  const handleSubmit = async () => {
    if (!message || loading) return;
    setLoading(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) throw new Error("Unauthorized access denied");

      const { error } = await supabase.from("project_applications").insert({
        project_id: projectId,
        applicant_id: user.id,
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
        setMessage("");
        setSkills([]);
      }, 3000);
    } catch (error: any) {
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
            className="fixed inset-0 bg-black/60 backdrop-blur-md z-[150]"
          />
          <motion.div
            initial={{ opacity: 0, scale: 0.95, y: 20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.95, y: 20 }}
            className="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xl inst-card p-10 z-[151] shadow- premium overflow-hidden"
          >
            {success ? (
              <div className="py-12 flex flex-col items-center text-center space-y-6">
                <div className="w-20 h-20 rounded-3xl bg-green-500/10 flex items-center justify-center text-green-500 shadow-sm animate-bounce">
                  <Sparkles className="w-10 h-10" />
                </div>
                <div>
                  <h2 className="text-3xl font-black tracking-tight mb-2 uppercase">Request Transmitted</h2>
                  <p className="text-muted-foreground font-medium text-balance">Your application protocol has been initialized. The mission lead will review your credentials soon.</p>
                </div>
              </div>
            ) : (
              <>
                <div className="flex items-center justify-between mb-10">
                  <div>
                    <h2 className="text-2xl font-black tracking-tight uppercase">Join Mission</h2>
                    <p className="text-[10px] font-black uppercase tracking-[0.2em] text-lendi-blue mt-1.5">{projectTitle}</p>
                  </div>
                  <button onClick={onClose} className="p-2 hover:bg-secondary rounded-xl transition-colors">
                    <X className="w-5 h-5 text-muted-foreground" />
                  </button>
                </div>

                <div className="space-y-8">
                  <div className="space-y-3">
                    <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Motivation Statement</label>
                    <textarea
                      placeholder="Why do you want to join this mission? What specific value can you contribute?"
                      value={message}
                      onChange={(e) => setMessage(e.target.value)}
                      className="w-full bg-secondary border border-border rounded-2xl p-6 min-h-[140px] text-sm font-medium leading-relaxed focus:outline-none focus:border-lendi-blue/50 transition-all placeholder:text-muted-foreground/30 resize-none"
                    />
                  </div>

                  <div className="space-y-4">
                    <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Core Competencies</label>
                    <div className="flex flex-wrap gap-2 mb-2">
                      {skills.map(s => (
                        <span key={s} className="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase">
                          {s}
                          <button onClick={() => removeSkill(s)} className="hover:text-lendi-red transition-colors"><X size={10} /></button>
                        </span>
                      ))}
                    </div>
                    <div className="relative flex gap-3">
                      <div className="relative flex-1">
                        <Code2 className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/40" />
                        <input
                          type="text"
                          placeholder="e.g., React, AI, Systems Design"
                          value={currentSkill}
                          onChange={(e) => setCurrentSkill(e.target.value)}
                          onKeyDown={(e) => e.key === 'Enter' && addSkill()}
                          className="w-full bg-secondary border border-border rounded-xl pl-12 pr-6 h-12 text-xs font-bold focus:outline-none focus:border-lendi-blue transition-all"
                        />
                      </div>
                      <Button onClick={addSkill} variant="secondary" className="h-12 w-12 rounded-xl border border-border flex items-center justify-center">
                        <Plus className="w-5 h-5" />
                      </Button>
                    </div>
                  </div>

                  <Button
                    onClick={handleSubmit}
                    disabled={loading || !message}
                    className="w-full rounded-2xl h-14 font-black uppercase tracking-widest text-xs shadow-lendi mt-4"
                  >
                    {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : (
                      <div className="flex items-center gap-3">
                        Transmit Application
                        <Send className="w-4 h-4" />
                      </div>
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
