"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { BidModal } from "@/components/bounties/BidModal";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import { Shield, Search, Filter, ArrowRight, Loader2, AlertCircle } from "lucide-react";
import { Button } from "@/components/ui/Button";

export default function BountyBoard() {
  const [bounties, setBounties] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState("all");
  const [selectedBounty, setSelectedBounty] = useState<any>(null);

  const fetchBounties = async () => {
    setLoading(true);
    const { data } = await supabase.from('bounties').select('*').order('created_at', { ascending: false });
    if (data) setBounties(data);
    setLoading(false);
  };

  useEffect(() => { fetchBounties(); }, []);

  const filteredBounties = filter === "all" ? bounties : bounties.filter(b => b.status === filter);

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black">
      <Header title="Bounty Board" />
      <main className="flex-1 overflow-y-auto p-6 md:p-10 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2">Global Tasks</h1>
              <p className="text-white/40 font-medium tracking-tight">Resolve campus-wide challenges and earn verified XP.</p>
            </div>
            <div className="flex bg-white/5 p-1 rounded-2xl border border-white/5">
              {["all", "open", "claimed"].map((f) => (
                <button key={f} onClick={() => setFilter(f)} className={`px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all ${filter === f ? "bg-lendi-blue text-white" : "text-white/30"}`} >{f}</button>
              ))}
            </div>
          </div>
          {loading ? (<div className="h-64 flex items-center justify-center"><Loader2 className="w-8 h-8 animate-spin text-lendi-blue" /></div>) : (
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-20">
              {filteredBounties.map((bounty, i) => (
                <motion.div key={bounty.id} initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.05 }} className="glass rounded-[2.5rem] p-10 border border-white/5 hover:border-lendi-blue/20 transition-all group relative overflow-hidden" >
                  <div className="flex justify-between items-start mb-8 relative z-10">
                    <div className="flex items-center gap-4">
                      <div className="p-3 rounded-2xl bg-red-500/10 text-red-400 border border-red-500/10"><AlertCircle className="w-6 h-6" /></div>
                      <div><h3 className="text-2xl font-black font-plus-jakarta tracking-tight leading-tight">{bounty.title}</h3><p className="text-[10px] font-black uppercase tracking-widest text-white/20 mt-1">Ref ID: {bounty.id.slice(0,8)}</p></div>
                    </div>
                    <div className="text-right"><p className="text-2xl font-black font-plus-jakarta text-lendi-blue">+{bounty.points_reward}</p><p className="text-[8px] font-black uppercase tracking-tighter text-white/20">Verified XP</p></div>
                  </div>
                  <p className="text-white/50 text-sm leading-relaxed mb-10 font-medium relative z-10 line-clamp-3">{bounty.description}</p>
                  <div className="flex items-center justify-between pt-8 border-t border-white/5 relative z-10">
                    <div className="flex items-center gap-2"><div className={`w-1.5 h-1.5 rounded-full ${bounty.status === 'open' ? 'bg-green-500 animate-pulse' : 'bg-yellow-500'}`} /><span className="text-[10px] font-black uppercase tracking-widest text-white/40">{bounty.status}</span></div>
                    <Button onClick={() => setSelectedBounty(bounty)} className="rounded-2xl px-10 h-12 font-black uppercase tracking-widest text-xs flex gap-2 group/btn">Propose Solution <ArrowRight className="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" /></Button>
                  </div>
                </motion.div>
              ))}
            </div>
          )}
        </div>
      </main>
      {selectedBounty && <BidModal isOpen={!!selectedBounty} onClose={() => setSelectedBounty(null)} bountyId={selectedBounty.id} bountyTitle={selectedBounty.title} />}
    </div>
  );
}
