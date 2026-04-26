"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion } from "framer-motion";
import { BarChart3, TrendingUp, Users, Rocket, Zap, Shield, Loader2, Globe, Activity } from "lucide-react";
import { Button } from "@/components/ui/Button";

export default function AnalyticsDashboard() {
  const [stats, setStats] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      const [{ count: userCount }, { count: projectCount }, { count: bountyCount }, { data: xpData }] = await Promise.all([
        supabase.from('profiles').select('*', { count: 'exact', head: true }),
        supabase.from('projects').select('*', { count: 'exact', head: true }),
        supabase.from('bounties').select('*', { count: 'exact', head: true }),
        supabase.from('xp_transactions').select('amount')
      ]);

      const totalXP = xpData?.reduce((acc, curr) => acc + curr.amount, 0) || 0;

      setStats({
        users: userCount || 0,
        projects: projectCount || 0,
        bounties: bountyCount || 0,
        xp: totalXP,
        activeSectors: 12, // Mocked for UI
        uplinkStrength: "98.4%"
      });
      setLoading(false);
    };
    fetchStats();
  }, []);

  if (loading) return <div className="h-screen flex items-center justify-center bg-black"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>;

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Intelligence Command" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-6xl mx-auto">
          <div className="mb-12">
            <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2 flex items-center gap-4">
              Network Analytics
              <div className="px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">
                Sentinel Real-time
              </div>
            </h1>
            <p className="text-white/40 font-medium">Monitoring growth, engagement, and innovation throughput across the Lendi network.</p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            {[
              { label: "Total Innovators", value: stats.users, icon: Users, color: "text-lendi-blue", bg: "bg-lendi-blue/10" },
              { label: "Active Missions", value: stats.projects, icon: Rocket, color: "text-purple-500", bg: "bg-purple-500/10" },
              { label: "Network XP", value: stats.xp, icon: Zap, color: "text-yellow-500", bg: "bg-yellow-500/10" },
              { label: "Global Bounties", value: stats.bounties, icon: Shield, color: "text-red-500", bg: "bg-red-500/10" },
              { label: "Active Sectors", value: stats.activeSectors, icon: Globe, color: "text-cyan-500", bg: "bg-cyan-500/10" },
              { label: "Uplink Stability", value: stats.uplinkStrength, icon: Activity, color: "text-green-500", bg: "bg-green-500/10" },
            ].map((stat, i) => (
              <motion.div
                key={i}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: i * 0.1 }}
                className="glass rounded-[2.5rem] p-8 border border-white/5 relative overflow-hidden group"
              >
                <div className={`w-12 h-12 rounded-2xl ${stat.bg} flex items-center justify-center mb-6`}>
                  <stat.icon className={`w-6 h-6 ${stat.color}`} />
                </div>
                <p className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-1">{stat.label}</p>
                <h3 className="text-3xl font-black font-plus-jakarta">{stat.value}</h3>
                <div className="absolute -bottom-4 -right-4 w-24 h-24 bg-white/[0.02] rounded-full group-hover:scale-150 transition-transform duration-700" />
              </motion.div>
            ))}
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-20">
             <div className="glass rounded-[3rem] p-10 border border-white/5">
                <div className="flex items-center justify-between mb-10">
                  <h3 className="text-lg font-black font-plus-jakarta flex items-center gap-3">
                    <TrendingUp className="w-5 h-5 text-lendi-blue" />
                    Growth Velocity
                  </h3>
                  <span className="text-[10px] font-black text-white/20 uppercase tracking-widest">Last 30 Cycles</span>
                </div>
                <div className="h-64 flex items-end gap-3">
                   {Array.from({ length: 12 }).map((_, i) => (
                     <div key={i} className="flex-1 flex flex-col items-center gap-4">
                        <motion.div
                          initial={{ height: 0 }}
                          animate={{ height: `${Math.random() * 60 + 40}%` }}
                          transition={{ delay: i * 0.05, duration: 1 }}
                          className="w-full bg-lendi-blue/20 border-t-2 border-lendi-blue rounded-t-lg group-hover:bg-lendi-blue/40 transition-colors"
                        />
                        <span className="text-[8px] font-black text-white/10 uppercase">C{i+1}</span>
                     </div>
                   ))}
                </div>
             </div>

             <div className="glass rounded-[3rem] p-10 border border-white/5 bg-gradient-to-br from-lendi-blue/5 to-transparent">
                <h3 className="text-lg font-black font-plus-jakarta mb-10 flex items-center gap-3">
                  <BarChart3 className="w-5 h-5 text-purple-500" />
                  Innovation Distribution
                </h3>
                <div className="space-y-6">
                  {[
                    { label: "AI & ML Research", val: 84, color: "bg-lendi-blue" },
                    { label: "Web3 Infrastructure", val: 62, color: "bg-purple-500" },
                    { label: "IoT & Robotics", val: 45, color: "bg-cyan-500" },
                    { label: "Cybersecurity Ops", val: 38, color: "bg-red-500" },
                  ].map((item, i) => (
                    <div key={i} className="space-y-2">
                      <div className="flex justify-between items-center text-[10px] font-black uppercase tracking-widest">
                        <span className="text-white/40">{item.label}</span>
                        <span>{item.val}%</span>
                      </div>
                      <div className="h-2 w-full bg-white/5 rounded-full overflow-hidden">
                        <motion.div
                          initial={{ width: 0 }}
                          animate={{ width: `${item.val}%` }}
                          transition={{ delay: 0.5 + (i * 0.1), duration: 1.5 }}
                          className={`h-full ${item.color} shadow-[0_0_10px_rgba(0,0,0,0.5)]`}
                        />
                      </div>
                    </div>
                  ))}
                </div>
             </div>
          </div>
        </div>
      </main>
    </div>
  );
}
