"use client";

import { motion } from "framer-motion";
import { TrendingUp, Users, ArrowRight, Flame, Sparkles } from "lucide-react";

const TRENDING_TOPICS = [
  { name: "EcoTrack AI", count: 124, growth: "+12%", hot: true },
  { name: "Lendi Mesh", count: 85, growth: "+24%", hot: true },
  { name: "Hackathon V3", count: 242, growth: "+8%", hot: false },
  { name: "Rust Workshop", count: 64, growth: "+15%", hot: false },
];

export const TrendingWidget = () => {
  return (
    <div className="glass rounded-[2.5rem] p-8 border border-white/5 h-full flex flex-col relative overflow-hidden group">
      <div className="absolute top-0 right-0 w-32 h-32 bg-lendi-blue/5 blur-[60px] rounded-full group-hover:bg-lendi-blue/10 transition-colors duration-700" />
      <div className="flex items-center justify-between mb-8 relative z-10">
        <div className="flex items-center gap-3">
          <div className="p-2 rounded-xl bg-lendi-blue/10 text-lendi-blue">
            <TrendingUp className="w-5 h-5" />
          </div>
          <h3 className="font-black font-plus-jakarta text-xs uppercase tracking-[0.2em] text-white/40">What's Hot</h3>
        </div>
        <div className="flex items-center gap-1.5 px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 animate-pulse">
          <div className="w-1 h-1 rounded-full bg-red-500" />
          <span className="text-[8px] font-black uppercase text-red-400">Live Pulse</span>
        </div>
      </div>
      <div className="space-y-4 flex-1 relative z-10">
        {TRENDING_TOPICS.map((topic, i) => (
          <motion.div
            key={topic.name}
            initial={{ opacity: 0, x: -20 }}
            animate={{ opacity: 1, x: 0 }}
            transition={{ delay: i * 0.1, ease: "circOut" }}
            whileHover={{ x: 5, backgroundColor: "rgba(255,255,255,0.03)" }}
            className="group/item flex items-center justify-between p-4 rounded-2xl border border-transparent hover:border-white/5 transition-all cursor-pointer"
          >
            <div className="flex items-center gap-4">
              <div className="text-white/10 font-black font-plus-jakarta text-xl group-hover/item:text-lendi-blue/30 transition-colors">
                {String(i + 1).padStart(2, '0')}
              </div>
              <div>
                <div className="flex items-center gap-2">
                  <p className="text-sm font-black group-hover/item:text-lendi-blue transition-colors">{topic.name}</p>
                  {topic.hot && <Flame className="w-3 h-3 text-orange-500" />}
                </div>
                <div className="flex items-center gap-2 mt-1">
                  <div className="flex items-center gap-1 opacity-20 text-[10px] font-bold">
                    <Users className="w-3 h-3" />
                    <span>{topic.count} interacting</span>
                  </div>
                </div>
              </div>
            </div>
            <div className="text-right">
              <span className="text-[10px] font-black text-green-400 bg-green-500/10 px-2 py-1 rounded-md">
                {topic.growth}
              </span>
            </div>
          </motion.div>
        ))}
      </div>
      <button className="mt-8 group/btn flex items-center justify-center gap-2 py-4 rounded-2xl bg-white/5 hover:bg-lendi-blue transition-all duration-500 relative z-10 overflow-hidden">
        <span className="text-[10px] font-black uppercase tracking-widest group-hover/btn:text-white transition-colors">View All Tracks</span>
        <ArrowRight className="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" />
        <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000" />
      </button>
      <div className="mt-6 flex items-center justify-center gap-2 opacity-10">
        <Sparkles className="w-3 h-3" />
        <p className="text-[8px] uppercase tracking-[0.3em] font-black">Lendi Tech Pulse Engine</p>
      </div>
    </div>
  );
};
