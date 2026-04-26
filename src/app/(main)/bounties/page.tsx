"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { BidModal } from "@/components/bounties/BidModal";
import { BountyService, Bounty } from "@/services/BountyService";
import { motion, AnimatePresence } from "framer-motion";
import { Shield, Search, ArrowRight, Loader2, Target, Award, Clock, Tag } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

export default function BountyBoard() {
  const [bounties, setBounties] = useState<Bounty[]>([]);
  const [loading, setLoading] = useState(true);
  const [filter, setFilter] = useState("all");
  const [selectedBounty, setSelectedBounty] = useState<Bounty | null>(null);

  const fetchBounties = async () => {
    setLoading(true);
    try {
      const data = await BountyService.getAllBounties();
      setBounties(data);
    } catch (error) {
      toast.error("Failed to load institutional challenges");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchBounties(); }, []);

  const filteredBounties = filter === "all" ? bounties : bounties.filter(b => b.status === filter);

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Bounty Terminal" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">
                <Target size={12} />
                Open Challenges
              </div>
              <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst">Faculty Bounties</h1>
              <p className="text-muted-foreground font-medium max-w-xl text-balance">
                Solve verified institutional challenges posted by Lendi departments and earn academic recognition and XP.
              </p>
            </div>

            <div className="flex bg-secondary p-1.5 rounded-2xl border border-border">
              {["all", "open", "closed"].map((f) => (
                <button
                  key={f}
                  onClick={() => setFilter(f)}
                  className={`px-8 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all ${
                    filter === f ? "bg-white text-lendi-blue shadow-sm" : "text-muted-foreground hover:text-foreground"
                  }`}
                >
                  {f}
                </button>
              ))}
            </div>
          </div>

          {loading ? (
            <div className="h-[400px] flex flex-col items-center justify-center gap-6">
              <div className="w-12 h-12 border-4 border-lendi-blue border-t-transparent rounded-full animate-spin" />
              <p className="text-muted-foreground font-black uppercase tracking-[0.3em] text-[10px]">Syncing Challenge Network...</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-24">
              {filteredBounties.map((bounty, i) => (
                <motion.div
                  key={bounty.id}
                  initial={{ opacity: 0, y: 20 }}
                  whileInView={{ opacity: 1, y: 0 }}
                  viewport={{ once: true }}
                  transition={{ delay: i * 0.05 }}
                  className="inst-card p-10 bg-card shadow-sm hover:border-lendi-blue transition-all group relative overflow-hidden"
                >
                  <div className="flex justify-between items-start mb-8 relative z-10">
                    <div className="flex items-center gap-5">
                      <div className="w-14 h-14 rounded-2xl bg-secondary flex items-center justify-center text-lendi-blue shadow-sm group-hover:scale-110 transition-transform duration-500 border border-border">
                        <Shield size={28} />
                      </div>
                      <div>
                        <h3 className="text-2xl font-black tracking-tight leading-tight mb-1.5">{bounty.title}</h3>
                        <div className="flex gap-4">
                          <p className="text-[10px] font-black uppercase tracking-widest text-muted-foreground/50">ID: {bounty.id.slice(0,8)}</p>
                          <div className="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-muted-foreground/50">
                            <Clock size={12} />
                            Ends {new Date(bounty.deadline).toLocaleDateString()}
                          </div>
                        </div>
                      </div>
                    </div>
                    <div className="text-right p-4 rounded-2xl bg-lendi-blue/5 border border-lendi-blue/10">
                      <p className="text-2xl font-black text-lendi-blue leading-none">+{bounty.reward_amount}</p>
                      <p className="text-[8px] font-black uppercase tracking-tighter text-lendi-blue/60 mt-1">{bounty.reward_type}</p>
                    </div>
                  </div>

                  <p className="text-muted-foreground text-sm leading-relaxed mb-10 font-medium text-balance line-clamp-3">
                    {bounty.description}
                  </p>

                  <div className="flex items-center justify-between pt-8 border-t border-border relative z-10">
                    <div className="flex items-center gap-3">
                      <div className={`w-2 h-2 rounded-full ${bounty.status === 'open' ? 'bg-green-500 animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]' : 'bg-amber-500'}`} />
                      <span className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground">{bounty.status}</span>
                    </div>

                    <Button
                      onClick={() => setSelectedBounty(bounty)}
                      variant="outline"
                      className="rounded-xl px-10 h-12 font-black uppercase tracking-widest text-[10px] flex gap-2 group/btn"
                    >
                      Propose Solution
                      <ArrowRight className="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
                    </Button>
                  </div>

                  {/* Aesthetic Background Decoration */}
                  <div className="absolute -bottom-12 -right-12 w-48 h-48 bg-lendi-blue/5 rounded-full blur-3xl pointer-events-none" />
                </motion.div>
              ))}
            </div>
          )}
        </div>
      </main>
      {selectedBounty && (
        <BidModal
          isOpen={!!selectedBounty}
          onClose={() => setSelectedBounty(null)}
          bountyId={selectedBounty.id}
          bountyTitle={selectedBounty.title}
        />
      )}
    </div>
  );
}
