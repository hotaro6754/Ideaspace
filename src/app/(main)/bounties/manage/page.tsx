"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import { Check, X, Shield, Plus, Award, Loader2, AlertCircle, Trash2, ChevronRight, User } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

export default function BountyManagementPage() {
  const [bounties, setBounties] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isAdding, setIsAdding] = useState(false);
  const [selectedBounty, setSelectedBounty] = useState<any>(null);
  const [bids, setBids] = useState<any[]>([]);
  const [loadingBids, setLoadingBids] = useState(false);

  const [newBounty, setNewBounty] = useState({
    title: "",
    description: "",
    problem_statement: "",
    deliverables: "",
    deadline: "",
    points_reward: 500,
    is_team_based: false
  });

  const fetchData = async () => {
    setLoading(true);
    const { data } = await supabase.from('bounties').select('*').order('created_at', { ascending: false });
    if (data) setBounties(data);
    setLoading(false);
  };

  useEffect(() => {
    fetchData();
  }, []);

  const fetchBids = async (bountyId: string) => {
    setLoadingBids(true);
    const { data } = await supabase
      .from('bounty_bids')
      .select('*, profiles:user_id (full_name, roll_number, rank)')
      .eq('bounty_id', bountyId);
    if (data) setBids(data);
    setLoadingBids(false);
  };

  const handleCreate = async () => {
    if (!newBounty.title || !newBounty.deadline) return;
    const { error } = await supabase.from('bounties').insert(newBounty);
    if (error) {
      toast.error(error.message);
    } else {
      toast.success("Bounty Broadcast successfully!");
      setIsAdding(false);
      fetchData();
    }
  };

  const deleteBounty = async (id: string) => {
    const { error } = await supabase.from('bounties').delete().eq('id', id);
    if (!error) {
      toast.success("Bounty retracted.");
      fetchData();
    }
  };

  const awardBounty = async (bid: any) => {
    // 1. Mark bounty as claimed
    await supabase.from('bounties').update({ status: 'claimed' }).eq('id', bid.bounty_id);
    // 2. Mark bid as accepted
    await supabase.from('bounty_bids').update({ status: 'accepted' }).eq('id', bid.id);
    // 3. Award XP
    const bounty = bounties.find(b => b.id === bid.bounty_id);
    await supabase.from('xp_transactions').insert({
      user_id: bid.user_id,
      amount: bounty.points_reward,
      reason: `Bounty Won: ${bounty.title}`
    });
    // 4. Send notification
    await supabase.from('notifs').insert({
      user_id: bid.user_id,
      title: "Bounty Claimed!",
      content: `Congratulations! You have been selected as the winner for the ${bounty.title} bounty. +${bounty.points_reward} XP awarded.`,
      type: 'success'
    });

    toast.success(`Bounty awarded to ${bid.profiles?.full_name}`);
    setSelectedBounty(null);
    fetchData();
  };

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Bounty Control" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-6xl mx-auto">
          <div className="flex justify-between items-end mb-12">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2">Faculty Terminal</h1>
              <p className="text-white/40 font-medium">Spawn global challenges and evaluate student solutions.</p>
            </div>
            <Button onClick={() => setIsAdding(true)} className="rounded-2xl h-14 px-8 bg-lendi-blue hover:bg-lendi-blue/80 flex gap-2 font-black text-[10px] uppercase tracking-widest shadow-2xl shadow-lendi-blue/20">
              <Plus className="w-4 h-4" />
              New Challenge
            </Button>
          </div>

          <AnimatePresence>
            {isAdding && (
              <motion.div initial={{ opacity: 0, y: -20 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -20 }} className="glass rounded-[3rem] p-10 border border-lendi-blue/20 mb-12 relative overflow-hidden" >
                <div className="absolute top-0 right-0 w-64 h-64 bg-lendi-blue/5 blur-[100px] rounded-full" />
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                  <div className="space-y-6">
                    <div>
                      <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Bounty Title</label>
                      <input type="text" placeholder="e.g. Optimize Campus WiFi Routing" value={newBounty.title} onChange={e => setNewBounty({...newBounty, title: e.target.value})} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm focus:outline-none focus:border-lendi-blue/50" />
                    </div>
                    <div>
                      <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Problem Statement</label>
                      <textarea placeholder="Detailed description of the problem..." value={newBounty.problem_statement} onChange={e => setNewBounty({...newBounty, problem_statement: e.target.value})} rows={4} className="w-full bg-white/5 border border-white/5 rounded-[2rem] p-6 text-sm focus:outline-none focus:border-lendi-blue/50 resize-none" />
                    </div>
                  </div>
                  <div className="space-y-6">
                    <div>
                      <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Deadline</label>
                      <input type="date" value={newBounty.deadline} onChange={e => setNewBounty({...newBounty, deadline: e.target.value})} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm focus:outline-none focus:border-lendi-blue/50" />
                    </div>
                    <div>
                      <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Points Pool</label>
                      <input type="number" value={newBounty.points_reward} onChange={e => setNewBounty({...newBounty, points_reward: parseInt(e.target.value)})} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm focus:outline-none focus:border-lendi-blue/50" />
                    </div>
                    <div className="flex items-center gap-6 pt-4">
                      <Button onClick={handleCreate} className="flex-1 h-16 rounded-2xl font-black bg-white text-black">Publish Challenge</Button>
                      <Button onClick={() => setIsAdding(false)} variant="glass" className="h-16 rounded-2xl px-10">Cancel</Button>
                    </div>
                  </div>
                </div>
              </motion.div>
            )}
          </AnimatePresence>

          {loading ? (
            <div className="flex justify-center py-20"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>
          ) : (
            <div className="grid grid-cols-1 gap-6">
              {bounties.map((b) => (
                <div key={b.id} className="glass rounded-[2.5rem] p-8 border border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-8 group">
                  <div className="flex gap-6 items-start">
                    <div className="p-4 rounded-2xl bg-white/5 border border-white/5"><AlertCircle className="w-6 h-6 text-white/20" /></div>
                    <div>
                      <h3 className="text-xl font-black font-plus-jakarta mb-1">{b.title}</h3>
                      <p className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-4">{b.status} • {new Date(b.deadline).toLocaleDateString()}</p>
                    </div>
                  </div>
                  <div className="flex items-center gap-6">
                    <div className="text-right">
                      <p className="text-xl font-black font-plus-jakarta text-lendi-blue">+{b.points_reward}</p>
                      <p className="text-[8px] font-black uppercase text-white/20 tracking-widest">Points Pool</p>
                    </div>
                    <Button onClick={() => { setSelectedBounty(b); fetchBids(b.id); }} variant="glass" className="h-12 px-6 rounded-xl text-[10px] font-black uppercase tracking-widest flex gap-2">
                      Review Bids
                      <ChevronRight className="w-4 h-4" />
                    </Button>
                    <button onClick={() => deleteBounty(b.id)} className="p-3 text-white/10 hover:text-red-500 transition-colors"><Trash2 className="w-5 h-5" /></button>
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
             <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={() => setSelectedBounty(null)} className="absolute inset-0 bg-black/80 backdrop-blur-xl" />
             <motion.div initial={{ opacity: 0, scale: 0.9, y: 20 }} animate={{ opacity: 1, scale: 1, y: 0 }} exit={{ opacity: 0, scale: 0.9, y: 20 }} className="w-full max-w-4xl glass rounded-[3rem] p-10 border border-white/10 relative z-10 overflow-hidden" >
                <div className="flex items-center justify-between mb-10">
                  <div>
                    <h2 className="text-3xl font-black font-plus-jakarta tracking-tight">{selectedBounty.title}</h2>
                    <p className="text-[10px] font-black uppercase tracking-widest text-white/20">Reviewing Solutions</p>
                  </div>
                  <button onClick={() => setSelectedBounty(null)} className="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center hover:bg-white/10 transition-colors"><X className="w-6 h-6" /></button>
                </div>

                <div className="space-y-6 max-h-[50vh] overflow-y-auto custom-scrollbar pr-4">
                  {loadingBids ? <Loader2 className="w-8 h-8 animate-spin text-lendi-blue mx-auto" /> : bids.length === 0 ? <p className="text-center text-white/20 italic py-10">No solutions submitted yet.</p> : bids.map((bid) => (
                    <div key={bid.id} className="p-8 rounded-[2rem] bg-white/[0.02] border border-white/5 flex items-start justify-between gap-8 group">
                      <div className="flex gap-6">
                        <div className="w-14 h-14 rounded-2xl bg-lendi-blue/10 flex items-center justify-center text-xl font-black text-lendi-blue">{bid.profiles?.full_name?.[0]}</div>
                        <div>
                          <div className="flex items-center gap-3 mb-2">
                            <h4 className="font-black">{bid.profiles?.full_name}</h4>
                            <span className="text-[8px] font-black uppercase tracking-widest text-white/20 px-2 py-0.5 rounded bg-white/5">{bid.profiles?.rank}</span>
                          </div>
                          <p className="text-sm text-white/60 leading-relaxed mb-4">"{bid.proposal}"</p>
                          <p className="text-[10px] font-black uppercase tracking-widest text-white/20">Estimate: {bid.timeline_est}</p>
                        </div>
                      </div>
                      <Button onClick={() => awardBounty(bid)} className="rounded-xl h-12 px-8 bg-green-500 hover:bg-green-600 text-white font-black text-[10px] uppercase tracking-widest flex gap-2">
                        <Award className="w-4 h-4" />
                        Award
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
