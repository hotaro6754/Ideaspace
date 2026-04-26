"use client";

import { motion } from "framer-motion";
import { TrendingUp, Users, ArrowUpRight } from "lucide-react";
import Link from "next/link";

const TRENDS = [
  { id: 1, title: "Smart Grid Optimization", category: "EEE", growth: "+12%" },
  { id: 2, title: "Autonomous Campus Bot", category: "MECH", growth: "+45%" },
  { id: 3, title: "ML for Health Screening", category: "CSE", growth: "+28%" },
];

export const TrendingWidget = () => {
  return (
    <div className="inst-card p-8 bg-card shadow-sm group">
      <div className="flex items-center justify-between mb-8">
        <div className="flex items-center gap-3">
          <div className="p-2 rounded-xl bg-lendi-blue/10 text-lendi-blue">
            <TrendingUp size={18} />
          </div>
          <h3 className="font-black text-[10px] uppercase tracking-[0.2em] text-muted-foreground">Department Trends</h3>
        </div>
        <Users size={16} className="text-muted-foreground/30" />
      </div>

      <div className="space-y-4">
        {TRENDS.map((trend, idx) => (
          <motion.div
            key={trend.id}
            initial={{ opacity: 0, x: -10 }}
            whileInView={{ opacity: 1, x: 0 }}
            transition={{ delay: idx * 0.1 }}
            className="p-4 rounded-2xl border border-border bg-secondary/50 hover:bg-secondary hover:border-lendi-blue/20 transition-all group/item"
          >
            <div className="flex items-start justify-between">
              <div>
                <span className="text-[9px] font-black text-lendi-blue uppercase tracking-widest bg-lendi-blue/5 px-2 py-0.5 rounded-md mb-2 inline-block">
                  {trend.category}
                </span>
                <h4 className="font-bold text-sm text-foreground tracking-tight line-clamp-1">{trend.title}</h4>
              </div>
              <div className="flex flex-col items-end">
                <span className="text-[10px] font-black text-emerald-600 mb-1">{trend.growth}</span>
                <ArrowUpRight size={14} className="text-muted-foreground/20 group-hover/item:text-lendi-blue transition-colors" />
              </div>
            </div>
          </motion.div>
        ))}
      </div>

      <Link
        href="/projects"
        className="mt-6 flex items-center justify-center gap-2 p-3 rounded-xl border border-border text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:bg-secondary hover:text-foreground transition-all"
      >
        Explore Research Registry
      </Link>
    </div>
  );
};
