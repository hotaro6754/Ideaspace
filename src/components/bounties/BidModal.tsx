"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { X, Send, Loader2, Sparkles, Clock, Target, FileText } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { supabase } from "@/lib/supabase";
import { logger } from "@/lib/logger";

interface BidModalProps {
  isOpen: boolean;
  onClose: () => void;
  bountyId: string;
  bountyTitle: string;
}

export const BidModal = ({ isOpen, onClose, bountyId, bountyTitle }: BidModalProps) => {
  const [proposal, setProposal] = useState("");
  const [timeline, setTimeline] = useState("");
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);

  const handleSubmit = async () => {
    if (!proposal || loading) return;
    setLoading(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) throw new Error("Authentication required");

      const { error } = await supabase.from("bounty_bids").insert({
        bounty_id: bountyId,
        applicant_id: user.id,
        proposal,
        timeline_est: timeline,
        status: "pending"
      });

      if (error) throw error;

      setSuccess(true);
      setTimeout(() => {
        onClose();
        setSuccess(false);
        setProposal("");
        setTimeline("");
      }, 3000);
    } catch (e) {
      logger.error("Bidding", "Failed to submit bid", e);
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
            className="fixed inset-0 bg-black/60 backdrop-blur-md z-[200]"
          />
          <motion.div
            initial={{ opacity: 0, scale: 0.95, y: 20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.95, y: 20 }}
            className="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xl inst-card p-10 z-[201] shadow-premium overflow-hidden"
          >
            {success ? (
              <div className="py-12 flex flex-col items-center text-center space-y-6">
                <div className="w-20 h-20 rounded-3xl bg-lendi-blue/10 flex items-center justify-center text-lendi-blue shadow-sm animate-bounce">
                  <Sparkles className="w-10 h-10" />
                </div>
                <div>
                  <h2 className="text-3xl font-black tracking-tight mb-2 uppercase">Proposal Transmitted</h2>
                  <p className="text-muted-foreground font-medium text-balance">Your solution architecture is being reviewed by the respective Lendi department.</p>
                </div>
              </div>
            ) : (
              <>
                <div className="flex items-center justify-between mb-10 pb-6 border-b border-border">
                  <div className="flex items-center gap-4">
                    <div className="w-12 h-12 rounded-2xl bg-secondary flex items-center justify-center text-lendi-blue shadow-sm border border-border">
                      <Target size={24} />
                    </div>
                    <div>
                      <h2 className="text-2xl font-black tracking-tight uppercase leading-tight">Propose Solution</h2>
                      <p className="text-[10px] font-black uppercase tracking-[0.2em] text-lendi-blue mt-1.5">{bountyTitle}</p>
                    </div>
                  </div>
                  <button onClick={onClose} className="p-2 hover:bg-secondary rounded-xl transition-colors">
                    <X className="w-6 h-6 text-muted-foreground" />
                  </button>
                </div>

                <div className="space-y-8">
                  <div className="space-y-3">
                    <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Technical Architecture</label>
                    <textarea
                      placeholder="Detail your proposed technical solution and methodology..."
                      value={proposal}
                      onChange={(e) => setProposal(e.target.value)}
                      className="w-full bg-secondary border border-border rounded-2xl p-6 min-h-[160px] text-sm font-medium focus:outline-none focus:border-lendi-blue transition-all resize-none shadow-sm placeholder:text-muted-foreground/30"
                    />
                  </div>

                  <div className="space-y-3">
                    <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Implementation Timeline</label>
                    <div className="relative">
                      <Clock className="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/40" />
                      <input
                        type="text"
                        placeholder="e.g. 14 Working Days"
                        value={timeline}
                        onChange={(e) => setTimeline(e.target.value)}
                        className="w-full bg-secondary border border-border rounded-2xl pl-14 pr-6 h-14 text-sm font-bold focus:outline-none focus:border-lendi-blue transition-all shadow-sm"
                      />
                    </div>
                  </div>

                  <Button
                    onClick={handleSubmit}
                    disabled={loading || !proposal}
                    className="w-full rounded-[2rem] h-16 font-black uppercase tracking-widest text-xs shadow-lendi mt-4 gap-3"
                  >
                    {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : (
                      <>
                        Submit Formal Proposal
                        <Send size={16} />
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
