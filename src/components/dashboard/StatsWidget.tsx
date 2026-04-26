"use client";

import { motion } from "framer-motion";
import { Award, Zap, Target } from "lucide-react";

export const StatsWidget = () => {
  return (
    <div className="glass rounded-3xl p-6 border border-white/5 h-full relative overflow-hidden bg-lendi-blue/5">
      <div className="absolute -top-12 -right-12 w-32 h-32 bg-lendi-blue/10 blur-[80px] rounded-full" />
      <h3 className="font-bold font-plus-jakarta text-sm uppercase tracking-wider mb-6 opacity-40">Your Impact</h3>
      <div className="grid grid-cols-2 gap-4">
        <div className="p-4 rounded-2xl bg-white/5 border border-white/5">
          <Zap className="w-4 h-4 text-yellow-400 mb-2" />
          <p className="text-2xl font-black font-plus-jakarta">42</p>
          <p className="text-[10px] opacity-30 uppercase tracking-tighter">Day Streak</p>
        </div>
        <div className="p-4 rounded-2xl bg-white/5 border border-white/5">
          <Award className="w-4 h-4 text-lendi-blue mb-2" />
          <p className="text-2xl font-black font-plus-jakarta">#12</p>
          <p className="text-[10px] opacity-30 uppercase tracking-tighter">Global Rank</p>
        </div>
      </div>
      <div className="mt-4 p-4 rounded-2xl bg-white/5 border border-white/5">
        <div className="flex items-center justify-between mb-2">
          <p className="text-[10px] font-bold opacity-40 uppercase">Rank Progress</p>
          <p className="text-[10px] font-bold text-lendi-blue">84%</p>
        </div>
        <div className="h-1.5 w-full bg-white/5 rounded-full overflow-hidden">
          <motion.div
            initial={{ width: 0 }}
            animate={{ width: "84%" }}
            transition={{ duration: 1, ease: "easeOut" }}
            className="h-full bg-lendi-blue shadow-[0_0_10px_rgba(0,74,153,0.5)]"
          />
        </div>
        <p className="text-[10px] opacity-30 mt-2 text-center italic">120 pts to reach "Innovator"</p>
      </div>
      <div className="mt-6 flex items-center justify-center gap-2 opacity-20">
        <Target className="w-3 h-3" />
        <span className="text-[10px] font-bold uppercase tracking-widest">Protocol V1.0</span>
      </div>
    </div>
  );
};
