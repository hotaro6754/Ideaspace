"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion } from "framer-motion";
import {
  BarChart3,
  TrendingUp,
  Users,
  Rocket,
  Zap,
  Shield,
  Loader2,
  Globe,
  Activity,
  ArrowUpRight,
  Target,
  BadgeCheck
} from "lucide-react";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

export default function AnalyticsDashboard() {
  const [stats, setStats] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchStats = async () => {
      try {
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
          activeSectors: 14,
          uplinkStrength: "99.2%"
        });
      } catch (error) {
        toast.error("Intelligence uplink failed");
      } finally {
        setLoading(false);
      }
    };
    fetchStats();
  }, []);

  if (loading) return (
    <div className="h-screen flex items-center justify-center bg-background">
      <Loader2 className="w-10 h-10 animate-spin text-lendi-blue" />
    </div>
  );

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Network Intelligence" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-7xl mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">
                <Activity size={12} />
                Real-time Sentinel
              </div>
              <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst text-balance">Institutional Analytics</h1>
              <p className="text-muted-foreground font-medium max-w-xl">
                Monitoring growth metrics, research throughput, and institutional engagement across the Lendi Innovation Network.
              </p>
            </div>

            <Button variant="outline" className="h-14 rounded-2xl px-8 font-black uppercase tracking-widest text-xs gap-3">
              Generate Report
              <ArrowUpRight size={18} />
            </Button>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            {[
              { label: "Total Innovators", value: stats.users, icon: Users, color: "text-blue-600", bg: "bg-blue-600/10" },
              { label: "Active Missions", value: stats.projects, icon: Rocket, color: "text-purple-600", bg: "bg-purple-600/10" },
              { label: "Network XP", value: stats.xp.toLocaleString(), icon: Zap, color: "text-amber-600", bg: "bg-amber-600/10" },
              { label: "Faculty Bounties", value: stats.bounties, icon: Shield, color: "text-lendi-red", bg: "bg-lendi-red/10" },
              { label: "Academic Sectors", value: stats.activeSectors, icon: Globe, color: "text-cyan-600", bg: "bg-cyan-600/10" },
              { label: "Protocol Stability", value: stats.uplinkStrength, icon: BadgeCheck, color: "text-green-600", bg: "bg-green-600/10" },
            ].map((stat, i) => (
              <motion.div
                key={i}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.05 }}
                className="inst-card p-10 bg-card shadow-sm group relative overflow-hidden"
              >
                <div className={`w-14 h-14 rounded-[1.25rem] ${stat.bg} flex items-center justify-center mb-8 shadow-sm group-hover:scale-110 transition-transform duration-500 border border-border/50`}>
                  <stat.icon className={`w-7 h-7 ${stat.color}`} />
                </div>
                <div>
                  <p className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground/50 mb-1.5">{stat.label}</p>
                  <h3 className="text-4xl font-black tracking-tight-inst text-foreground">{stat.value}</h3>
                </div>

                {/* Subtle background decoration */}
                <div className="absolute -bottom-6 -right-6 text-lendi-blue/5 opacity-0 group-hover:opacity-100 transition-opacity duration-700">
                  <stat.icon size={120} />
                </div>
              </motion.div>
            ))}
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-10 pb-24">
             <div className="inst-card p-10 bg-card shadow-sm relative overflow-hidden">
                <div className="flex items-center justify-between mb-12">
                  <div>
                    <h3 className="text-lg font-black tracking-tight flex items-center gap-3">
                      <TrendingUp className="w-5 h-5 text-lendi-blue" />
                      Innovation Velocity
                    </h3>
                    <p className="text-[10px] font-black uppercase tracking-widest text-muted-foreground/40 mt-1">Growth Metric Cycles</p>
                  </div>
                  <div className="px-3 py-1 rounded-lg bg-secondary border border-border text-[9px] font-black text-muted-foreground uppercase tracking-widest">
                    Last 14 Days
                  </div>
                </div>

                <div className="h-64 flex items-end gap-4 px-4">
                   {Array.from({ length: 14 }).map((_, i) => (
                     <div key={i} className="flex-1 flex flex-col items-center gap-4 group">
                        <motion.div
                          initial={{ height: 0 }}
                          whileInView={{ height: `${Math.random() * 60 + 30}%` }}
                          viewport={{ once: true }}
                          transition={{ delay: i * 0.03, duration: 1.2, ease: "circOut" }}
                          className="w-full bg-secondary border-t-4 border-lendi-blue/30 rounded-t-xl group-hover:bg-lendi-blue/10 group-hover:border-lendi-blue transition-all duration-300"
                        />
                        <span className="text-[8px] font-black text-muted-foreground/30 uppercase">D{i+1}</span>
                     </div>
                   ))}
                </div>
             </div>

             <div className="inst-card p-10 bg-card shadow-sm border-l-4 border-l-lendi-blue">
                <div className="flex items-center justify-between mb-12">
                  <div>
                    <h3 className="text-lg font-black tracking-tight flex items-center gap-3">
                      <BarChart3 className="w-5 h-5 text-lendi-blue" />
                      Research Distribution
                    </h3>
                    <p className="text-[10px] font-black uppercase tracking-widest text-muted-foreground/40 mt-1">Sectors by Activity</p>
                  </div>
                </div>

                <div className="space-y-8">
                  {[
                    { label: "AI & Neural Systems", val: 88, color: "bg-blue-600" },
                    { label: "Full-Stack Infrastructure", val: 74, color: "bg-indigo-600" },
                    { label: "Robotics & Hardware", val: 56, color: "bg-cyan-600" },
                    { label: "Blockchain Protocol", val: 42, color: "bg-amber-600" },
                  ].map((item, i) => (
                    <div key={i} className="space-y-3">
                      <div className="flex justify-between items-end text-[10px] font-black uppercase tracking-widest">
                        <span className="text-muted-foreground/60">{item.label}</span>
                        <span className="text-foreground">{item.val}%</span>
                      </div>
                      <div className="h-2 w-full bg-secondary rounded-full overflow-hidden">
                        <motion.div
                          initial={{ width: 0 }}
                          whileInView={{ width: `${item.val}%` }}
                          viewport={{ once: true }}
                          transition={{ delay: 0.2 + (i * 0.1), duration: 1.5, ease: "expoOut" }}
                          className={`h-full ${item.color} shadow-sm`}
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
