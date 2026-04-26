"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { X, Send, Loader2, Sparkles, Clock, Target } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { supabase } from "@/lib/supabase";
import { logger } from "@/lib/logger";

export const BidModal = ({ isOpen, onClose, bountyId, bountyTitle }: any) => {
  const [proposal, setProposal] = useState("");
  const [timeline, setTimeline] = useState("");
  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState(false);

  const handleSubmit = async () => {
    if (!proposal || loading) return;
    setLoading(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;
      const { error } = await supabase.from("bounty_bids").insert({
        bounty_id: bountyId,
        user_id: user.id,
        proposal,
        timeline_est: timeline
      });
      if (error) throw error;
      setSuccess(true);
      setTimeout(() => { onClose(); setSuccess(false); }, 3000);
    } catch (e) { logger.error("Bidding", "Failed to submit bid", e); }
    finally { setLoading(false); }
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <>
          <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={onClose} className="fixed inset-0 bg-black/90 backdrop-blur-xl z-[200]" />
          <motion.div initial={{ opacity: 0, scale: 0.9, y: 20 }} animate={{ opacity: 1, scale: 1, y: 0 }} exit={{ opacity: 0, scale: 0.9, y: 20 }} className="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-xl glass p-10 rounded-[3rem] z-[201] border border-white/10 shadow-2xl" >
            {success ? (
              <div className="py-12 flex flex-col items-center text-center space-y-6">
                <div className="w-20 h-20 rounded-full bg-lendi-blue/20 flex items-center justify-center"><Sparkles className="w-10 h-10 text-lendi-blue" /></div>
                <div><h2 className="text-3xl font-black font-plus-jakarta mb-2">Bid Transmitted</h2><p className="text-white/40 font-medium">Your solution proposal is being reviewed by the department.</p></div>
              </div>
            ) : (
              <>
                <div className="flex items-center justify-between mb-8">
                  <div><h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Propose Solution</h2><p className="text-[10px] font-black uppercase tracking-[0.2em] text-red-500 mt-1">{bountyTitle}</p></div>
                  <button onClick={onClose} className="p-2 hover:bg-white/5 rounded-full"><X className="w-5 h-5 text-white/20" /></button>
                </div>
                <div className="space-y-8">
                  <div className="space-y-3">
                    <label className="text-[10px] font-black uppercase tracking-widest text-white/30 ml-1">Technical Proposal</label>
                    <textarea placeholder="Describe your intended solution architecture..." value={proposal} onChange={(e) => setProposal(e.target.value)} className="w-full bg-white/5 border border-white/5 rounded-2xl p-6 min-h-[140px] text-sm font-medium focus:border-lendi-blue/50 resize-none" />
                  </div>
                  <div className="space-y-3">
                    <label className="text-[10px] font-black uppercase tracking-widest text-white/30 ml-1">Estimated Timeline</label>
                    <div className="relative">
                      <Clock className="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20" />
                      <input type="text" placeholder="e.g. 10 days" value={timeline} onChange={(e) => setTimeline(e.target.value)} className="w-full bg-white/5 border border-white/5 rounded-2xl pl-14 pr-6 h-14 text-sm font-bold focus:border-lendi-blue/50" />
                    </div>
                  </div>
                  <Button onClick={handleSubmit} disabled={loading || !proposal} className="w-full rounded-2xl h-16 font-black shadow-2xl shadow-lendi-blue/20 flex gap-3 text-lg mt-4">
                    {loading ? <Loader2 className="w-6 h-6 animate-spin" /> : <>Submit Proposal <Target className="w-5 h-5" /></>}
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
