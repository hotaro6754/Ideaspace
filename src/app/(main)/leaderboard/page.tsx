"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { XPService } from "@/services/XPService";
import { Trophy, Medal, Crown, Star, Loader2, Target, ArrowUpRight } from "lucide-react";
import { motion } from "framer-motion";
import { toast } from "sonner";

export default function LeaderboardPage() {
  const [leaders, setLeaders] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchLeaders = async () => {
      try {
        const data = await XPService.getLeaderboard(20);
        setLeaders(data);
      } catch (error) {
        toast.error("Failed to sync leaderboard data");
      } finally {
        setLoading(false);
      }
    };
    fetchLeaders();
  }, []);

  const getRankIcon = (index: number) => {
    switch (index) {
      case 0: return <Crown size={24} className="text-amber-500 fill-amber-500 shadow-xl" />;
      case 1: return <Medal size={24} className="text-slate-400 fill-slate-400" />;
      case 2: return <Medal size={24} className="text-amber-700 fill-amber-700" />;
      default: return <span className="text-sm font-black text-muted-foreground">#{index + 1}</span>;
    }
  };

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Hall of Innovation" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-5xl mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">
                <Trophy size={12} />
                Global Rankings
              </div>
              <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst text-balance">Top Contributors</h1>
              <p className="text-muted-foreground font-medium max-w-xl">
                The most active innovators across the Lendi ecosystem, ranked by verified institutional XP.
              </p>
            </div>
            <div className="p-6 rounded-2xl bg-secondary border border-border shadow-sm flex items-center gap-6">
              <div className="flex flex-col">
                <span className="text-[10px] font-black uppercase text-muted-foreground tracking-widest">Your Ranking</span>
                <span className="text-2xl font-black text-lendi-blue">---</span>
              </div>
              <div className="w-px h-10 bg-border" />
              <div className="flex flex-col">
                <span className="text-[10px] font-black uppercase text-muted-foreground tracking-widest">Total XP</span>
                <span className="text-2xl font-black text-foreground">---</span>
              </div>
            </div>
          </div>

          {loading ? (
            <div className="h-[400px] flex flex-col items-center justify-center gap-6">
              <div className="w-12 h-12 border-4 border-lendi-blue border-t-transparent rounded-full animate-spin" />
              <p className="text-muted-foreground font-black uppercase tracking-[0.3em] text-[10px]">Processing Data Nodes...</p>
            </div>
          ) : (
            <div className="space-y-4 pb-24">
              {leaders.map((leader, i) => (
                <motion.div
                  key={leader.id}
                  initial={{ opacity: 0, x: -20 }}
                  whileInView={{ opacity: 1, x: 0 }}
                  viewport={{ once: true }}
                  transition={{ delay: i * 0.05 }}
                  className="inst-card p-6 flex items-center justify-between group hover:border-lendi-blue transition-all"
                >
                  <div className="flex items-center gap-8">
                    <div className="w-12 flex justify-center">
                      {getRankIcon(i)}
                    </div>
                    <div className="flex items-center gap-5">
                      <div className="w-12 h-12 rounded-xl bg-secondary flex items-center justify-center text-xl font-black text-muted-foreground/30 border border-border">
                        {leader.full_name?.[0]}
                      </div>
                      <div>
                        <h4 className="font-black text-foreground text-lg tracking-tight group-hover:text-lendi-blue transition-colors">{leader.full_name}</h4>
                        <div className="flex gap-4 mt-1">
                          <p className="text-[10px] font-black uppercase tracking-widest text-muted-foreground/50">{leader.department}</p>
                          <p className="text-[10px] font-black uppercase tracking-widest text-lendi-blue">{leader.rank}</p>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div className="flex items-center gap-12">
                    <div className="text-right hidden md:block">
                      <div className="flex items-center gap-2 justify-end mb-1">
                        <Target size={12} className="text-muted-foreground" />
                        <span className="text-[10px] font-black uppercase tracking-widest text-muted-foreground/60">Success Rate</span>
                      </div>
                      <p className="text-sm font-bold">92%</p>
                    </div>
                    <div className="text-right">
                      <div className="flex items-center gap-2 justify-end mb-1">
                        <Star size={12} className="text-amber-500 fill-amber-500" />
                        <span className="text-[10px] font-black uppercase tracking-widest text-muted-foreground/60">Institutional XP</span>
                      </div>
                      <p className="text-2xl font-black text-foreground">{leader.xp.toLocaleString()}</p>
                    </div>
                  </div>
                </motion.div>
              ))}
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
