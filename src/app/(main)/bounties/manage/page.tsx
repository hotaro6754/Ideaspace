"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import {
  Shield,
  Plus,
  Trash2,
  ChevronRight,
  X,
  Award,
  Loader2,
  Target,
  Users,
  Calendar,
  AlertCircle
} from "lucide-react";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

export default function BountyManagement() {
  const [bounties, setBounties] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isAdding, setIsAdding] = useState(false);
  const [selectedBounty, setSelectedBounty] = useState<any>(null);
  const [bids, setBids] = useState<any[]>([]);
  const [loadingBids, setLoadingBids] = useState(false);

  const [newBounty, setNewBounty] = useState({
    title: "",
    description: "",
    reward_amount: 500,
    reward_type: "XP",
    deadline: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
  });

  const fetchData = async () => {
    setLoading(true);
    const { data } = await supabase.from('bounties').select('*').order('created_at', { ascending: false });
    if (data) setBounties(data);
    setLoading(false);
  };

  useEffect(() => { fetchData(); }, []);

  const handleCreate = async () => {
    if (!newBounty.title || !newBounty.description) return;
    const { data: { user } } = await supabase.auth.getUser();
    if (!user) return;

    const { error } = await supabase.from('bounties').insert({
      ...newBounty,
      created_by: user.id,
      status: 'open'
    });

    if (error) {
      toast.error(error.message);
    } else {
      toast.success("Institutional challenge published");
      setIsAdding(false);
      setNewBounty({
        title: "",
        description: "",
        reward_amount: 500,
        reward_type: "XP",
        deadline: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0]
      });
      fetchData();
    }
  };

  const fetchBids = async (bountyId: string) => {
    setLoadingBids(true);
    const { data } = await supabase
      .from('bounty_bids')
      .select('*, profiles:applicant_id (full_name, rank, department)')
      .eq('bounty_id', bountyId);
    if (data) setBids(data);
    setLoadingBids(false);
  };

  const awardBounty = async (bid: any) => {
    try {
      // 1. Mark bounty as claimed
      await supabase.from('bounties').update({ status: 'claimed' }).eq('id', bid.bounty_id);
      // 2. Mark bid as accepted
      await supabase.from('bounty_bids').update({ status: 'accepted' }).eq('id', bid.id);

      const bounty = bounties.find(b => b.id === bid.bounty_id);

      // 3. Award XP via RPC
      await supabase.rpc('award_xp', {
        p_user_id: bid.applicant_id,
        p_amount: bounty.reward_amount,
        p_reason: `Bounty Won: ${bounty.title}`,
        p_category: 'bounty'
      });

      toast.success(`Bounty successfully awarded to ${bid.profiles?.full_name}`);
      setSelectedBounty(null);
      fetchData();
    } catch (e) {
      toast.error("Failed to process award protocol");
    }
  };

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Bounty Control Terminal" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-[1200px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">
                <Shield size={12} />
                Faculty Control
              </div>
              <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst">Bounty Terminal</h1>
              <p className="text-muted-foreground font-medium max-w-xl text-balance">
                Manage institutional challenges, evaluate student proposals, and award academic recognition.
              </p>
            </div>

            <Button
              onClick={() => setIsAdding(true)}
              className="h-14 rounded-2xl px-8 font-black uppercase tracking-widest text-xs gap-3 shadow-lendi"
            >
              <Plus size={18} />
              Publish Challenge
            </Button>
          </div>

          <AnimatePresence>
            {isAdding && (
              <motion.div
                initial={{ opacity: 0, y: -20 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -20 }}
                className="inst-card p-10 bg-card border-lendi-blue/30 mb-16 relative overflow-hidden"
              >
                <div className="grid grid-cols-1 md:grid-cols-2 gap-10 relative z-10">
                  <div className="space-y-8">
                    <div className="space-y-3">
                      <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Challenge Title</label>
                      <input
                        type="text"
                        placeholder="e.g. LLM Optimization for Library Search"
                        value={newBounty.title}
                        onChange={e => setNewBounty({...newBounty, title: e.target.value})}
                        className="w-full bg-secondary border border-border rounded-2xl px-6 h-14 text-sm font-bold focus:outline-none focus:border-lendi-blue transition-all shadow-sm"
                      />
                    </div>
                    <div className="space-y-3">
                      <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Problem Statement</label>
                      <textarea
                        placeholder="Detailed technical requirements and success criteria..."
                        value={newBounty.description}
                        onChange={e => setNewBounty({...newBounty, description: e.target.value})}
                        className="w-full bg-secondary border border-border rounded-[2rem] p-8 text-sm font-medium focus:outline-none focus:border-lendi-blue transition-all resize-none shadow-sm min-h-[160px]"
                      />
                    </div>
                  </div>
                  <div className="space-y-8">
                    <div className="grid grid-cols-2 gap-6">
                      <div className="space-y-3">
                        <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Submission Deadline</label>
                        <input
                          type="date"
                          value={newBounty.deadline}
                          onChange={e => setNewBounty({...newBounty, deadline: e.target.value})}
                          className="w-full bg-secondary border border-border rounded-2xl px-6 h-14 text-sm font-bold focus:outline-none focus:border-lendi-blue shadow-sm"
                        />
                      </div>
                      <div className="space-y-3">
                        <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Reward (XP)</label>
                        <input
                          type="number"
                          value={newBounty.reward_amount}
                          onChange={e => setNewBounty({...newBounty, reward_amount: parseInt(e.target.value)})}
                          className="w-full bg-secondary border border-border rounded-2xl px-6 h-14 text-sm font-bold focus:outline-none focus:border-lendi-blue shadow-sm"
                        />
                      </div>
                    </div>
                    <div className="flex items-center gap-6 pt-10 border-t border-border">
                      <Button onClick={handleCreate} className="flex-1 h-16 rounded-[2rem] font-black uppercase tracking-widest text-xs shadow-lendi">Publish Challenge Protocol</Button>
                      <Button onClick={() => setIsAdding(false)} variant="secondary" className="h-16 rounded-[2rem] px-10 font-black uppercase tracking-widest text-xs">Cancel</Button>
                    </div>
                  </div>
                </div>
              </motion.div>
            )}
          </AnimatePresence>

          {loading ? (
            <div className="h-[400px] flex justify-center items-center">
              <Loader2 className="w-10 h-10 animate-spin text-lendi-blue opacity-50" />
            </div>
          ) : (
            <div className="space-y-6 pb-24">
              {bounties.length === 0 ? (
                <div className="inst-card p-20 text-center flex flex-col items-center">
                  <div className="w-16 h-16 rounded-3xl bg-secondary flex items-center justify-center text-muted-foreground/30 mb-6">
                    <Target size={32} />
                  </div>
                  <h3 className="text-xl font-black uppercase tracking-widest text-muted-foreground">No Active Bounties</h3>
                  <p className="text-muted-foreground mt-2 font-medium">Publish your first institutional challenge to get started.</p>
                </div>
              ) : bounties.map((b) => (
                <div key={b.id} className="inst-card p-8 flex flex-col md:flex-row md:items-center justify-between gap-10 group" >
                  <div className="flex gap-6 items-start">
                    <div className="w-14 h-14 rounded-2xl bg-secondary border border-border flex items-center justify-center text-muted-foreground group-hover:text-lendi-blue transition-colors">
                      <AlertCircle size={24} />
                    </div>
                    <div>
                      <h3 className="text-xl font-black tracking-tight mb-1">{b.title}</h3>
                      <div className="flex gap-4 items-center">
                        <span className={`text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md ${
                          b.status === 'open' ? 'bg-green-500/10 text-green-600' : 'bg-amber-500/10 text-amber-600'
                        }`}>
                          {b.status}
                        </span>
                        <div className="h-1 w-1 rounded-full bg-border" />
                        <p className="text-[10px] font-bold text-muted-foreground/50 uppercase tracking-widest">
                          Deadline: {new Date(b.deadline).toLocaleDateString()}
                        </p>
                      </div>
                    </div>
                  </div>
                  <div className="flex items-center gap-8">
                    <div className="text-right p-4 rounded-2xl bg-secondary border border-border">
                      <p className="text-xl font-black text-lendi-blue">+{b.reward_amount}</p>
                      <p className="text-[8px] font-black uppercase text-muted-foreground/50 tracking-tighter">Points Pool</p>
                    </div>
                    <Button
                      onClick={() => { setSelectedBounty(b); fetchBids(b.id); }}
                      variant="outline"
                      className="h-12 px-8 rounded-xl text-[10px] font-black uppercase tracking-widest flex gap-2"
                    >
                      Review Bids
                      <ChevronRight size={14} />
                    </Button>
                    <button className="p-3 text-muted-foreground/30 hover:text-lendi-red transition-colors">
                      <Trash2 size={20} />
                    </button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </main>

      <AnimatePresence>
        {selectedBounty && (
          <div className="fixed inset-0 z-[200] flex items-center justify-center p-6">
             <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={() => setSelectedBounty(null)} className="absolute inset-0 bg-black/60 backdrop-blur-md" />
             <motion.div
               initial={{ opacity: 0, scale: 0.95, y: 20 }}
               animate={{ opacity: 1, scale: 1, y: 0 }}
               exit={{ opacity: 0, scale: 0.95, y: 20 }}
               className="w-full max-w-4xl inst-card p-10 bg-card z-[210] relative overflow-hidden"
             >
                <div className="flex items-center justify-between mb-10 pb-6 border-b border-border">
                  <div className="flex items-center gap-4">
                    <div className="w-12 h-12 rounded-2xl bg-secondary flex items-center justify-center text-lendi-blue shadow-sm border border-border">
                      <Users size={24} />
                    </div>
                    <div>
                      <h2 className="text-2xl font-black tracking-tight uppercase leading-tight">{selectedBounty.title}</h2>
                      <p className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mt-1.5">Analyzing Submitted Proposals</p>
                    </div>
                  </div>
                  <button onClick={() => setSelectedBounty(null)} className="p-2 hover:bg-secondary rounded-xl transition-colors">
                    <X size={24} />
                  </button>
                </div>

                <div className="space-y-6 max-h-[60vh] overflow-y-auto custom-scrollbar pr-4 pb-4">
                  {loadingBids ? (
                    <div className="py-20 flex justify-center">
                      <Loader2 className="w-8 h-8 animate-spin text-lendi-blue opacity-50" />
                    </div>
                  ) : bids.length === 0 ? (
                    <div className="py-20 text-center">
                      <p className="text-muted-foreground font-medium italic uppercase tracking-widest text-xs">No active submissions received for this track.</p>
                    </div>
                  ) : bids.map((bid) => (
                    <div key={bid.id} className="p-8 rounded-[2rem] bg-secondary border border-border flex flex-col md:flex-row items-start justify-between gap-8 group hover:border-lendi-blue/30 transition-all" >
                      <div className="flex gap-6 items-start">
                        <div className="w-16 h-16 rounded-[2rem] bg-white border border-border flex items-center justify-center text-2xl font-black text-lendi-blue shadow-sm">
                          {bid.profiles?.full_name?.[0]}
                        </div>
                        <div className="flex-1">
                          <div className="flex items-center gap-3 mb-2">
                            <h4 className="font-black text-lg tracking-tight text-foreground">{bid.profiles?.full_name}</h4>
                            <span className="text-[10px] font-black uppercase tracking-widest text-lendi-blue bg-lendi-blue/10 px-2 py-0.5 rounded-md">{bid.profiles?.rank}</span>
                          </div>
                          <div className="p-5 rounded-2xl bg-white border border-border text-sm text-muted-foreground leading-relaxed italic mb-4">
                            &quot;{bid.proposal}&quot;
                          </div>
                          <div className="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-muted-foreground/50">
                            <div className="flex items-center gap-1.5">
                              <Calendar size={12} />
                              Est: {bid.timeline_est}
                            </div>
                            <div className="h-3 w-px bg-border" />
                            <div className="flex items-center gap-1.5">
                              <Target size={12} />
                              Department: {bid.profiles?.department}
                            </div>
                          </div>
                        </div>
                      </div>
                      <Button
                        onClick={() => awardBounty(bid)}
                        className="h-14 px-10 rounded-2xl font-black text-[10px] uppercase tracking-widest flex gap-3 shadow-sm bg-green-600 hover:bg-green-700"
                      >
                        <Award size={18} />
                        Award Bounty
                      </Button>
                    </div>
                  ))}
                </div>
             </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
}
