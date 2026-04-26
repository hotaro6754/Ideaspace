"use client";

import { motion } from "framer-motion";
import { Award, Zap, Target, ShieldCheck } from "lucide-react";

export const StatsWidget = () => {
  return (
    <div className="inst-card p-8 bg-card shadow-sm relative overflow-hidden group">
      <div className="absolute -top-12 -right-12 w-48 h-48 bg-lendi-blue/5 blur-3xl rounded-full" />

      <div className="flex items-center gap-3 mb-8">
        <div className="p-2 rounded-xl bg-lendi-blue/10 text-lendi-blue">
          <ShieldCheck size={18} />
        </div>
        <h3 className="font-black text-[10px] uppercase tracking-[0.2em] text-muted-foreground">Institutional Impact</h3>
      </div>

      <div className="grid grid-cols-2 gap-4">
        <div className="p-5 rounded-2xl bg-secondary border border-border group-hover:border-lendi-blue/20 transition-colors">
          <Zap size={18} className="text-amber-500 mb-3" fill="currentColor" />
          <p className="text-3xl font-black tracking-tight text-foreground leading-none mb-1.5">24</p>
          <p className="text-[9px] font-black text-muted-foreground/50 uppercase tracking-widest">Active Streak</p>
        </div>
        <div className="p-5 rounded-2xl bg-secondary border border-border group-hover:border-lendi-blue/20 transition-colors">
          <Award size={18} className="text-lendi-blue mb-3" />
          <p className="text-3xl font-black tracking-tight text-foreground leading-none mb-1.5">#08</p>
          <p className="text-[9px] font-black text-muted-foreground/50 uppercase tracking-widest">Dept Rank</p>
        </div>
      </div>

      <div className="mt-6 p-6 rounded-2xl bg-secondary border border-border">
        <div className="flex items-center justify-between mb-3">
          <p className="text-[10px] font-black text-muted-foreground/60 uppercase tracking-widest">Rank Protocol</p>
          <p className="text-[10px] font-black text-lendi-blue uppercase">Level 12</p>
        </div>
        <div className="h-2 w-full bg-white border border-border rounded-full overflow-hidden">
          <motion.div
            initial={{ width: 0 }}
            whileInView={{ width: "72%" }}
            viewport={{ once: true }}
            transition={{ duration: 1.5, ease: "circOut" }}
            className="h-full bg-lendi-blue shadow-sm"
          />
        </div>
        <p className="text-[9px] text-muted-foreground/40 mt-3 text-center font-bold uppercase tracking-tighter">840 XP until "Lead Innovator" rank</p>
      </div>

      <div className="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-lendi-blue/10 to-transparent" />
    </div>
  );
};
