"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion } from "framer-motion";
import { Trophy, Medal, Award, Star, Loader2, TrendingUp, Zap } from "lucide-react";

export default function LeaderboardPage() {
  const [leaders, setLeaders] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchLeaders = async () => {
      const { data } = await supabase
        .from('profiles')
        .select('*')
        .order('points', { ascending: false })
        .limit(20);
      if (data) setLeaders(data);
      setLoading(false);
    };
    fetchLeaders();
  }, []);

  const getRankIcon = (index: number) => {
    if (index === 0) return <Trophy className="w-6 h-6 text-yellow-500" />;
    if (index === 1) return <Medal className="w-6 h-6 text-slate-400" />;
    if (index === 2) return <Award className="w-6 h-6 text-orange-600" />;
    return <span className="text-xl font-black opacity-10">{index + 1}</span>;
  };

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black">
      <Header title="Hall of Fame" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-4xl mx-auto">
          <div className="flex items-end justify-between mb-16">
            <div>
              <h1 className="text-5xl font-black font-plus-jakarta tracking-tightest mb-4">Elite Tier</h1>
              <p className="text-white/40 font-medium">Top contributors across the Lendi Innovation network.</p>
            </div>
            <div className="px-6 py-3 rounded-2xl bg-lendi-blue/10 border border-lendi-blue/20 flex items-center gap-3">
              <Zap className="w-5 h-5 text-lendi-blue" />
              <span className="text-xs font-black uppercase tracking-widest text-lendi-blue">Season 1: Active</span>
            </div>
          </div>

          {loading ? (
            <div className="flex justify-center py-20"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>
          ) : (
            <div className="space-y-4">
              {leaders.map((leader, i) => (
                <motion.div
                  key={leader.id}
                  initial={{ opacity: 0, x: -20 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ delay: i * 0.05 }}
                  className={`glass rounded-2xl p-6 border border-white/5 flex items-center justify-between group hover:border-lendi-blue/30 transition-all ${
                    i < 3 ? "bg-white/[0.03] py-8" : ""
                  }`}
                >
                  <div className="flex items-center gap-8">
                    <div className="w-12 flex justify-center">{getRankIcon(i)}</div>
                    <div className="flex items-center gap-4">
                      <div className="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center text-lg font-black text-white/40">
                        {leader.full_name?.[0] || 'A'}
                      </div>
                      <div>
                        <h4 className="font-black text-lg group-hover:text-lendi-blue transition-colors">{leader.full_name}</h4>
                        <div className="flex items-center gap-2">
                          <span className="text-[10px] font-black uppercase tracking-widest text-white/20">{leader.department || "General"}</span>
                          <div className="w-1 h-1 rounded-full bg-white/10" />
                          <span className="text-[10px] font-black uppercase tracking-widest text-lendi-blue">{leader.rank}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div className="text-right">
                    <div className="flex items-center gap-2 justify-end">
                      <p className="text-2xl font-black font-plus-jakarta">{leader.points}</p>
                      <TrendingUp className="w-4 h-4 text-green-500" />
                    </div>
                    <p className="text-[10px] font-black uppercase tracking-tighter text-white/20">Synthesized XP</p>
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
