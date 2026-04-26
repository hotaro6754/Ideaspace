"use client";

import { motion } from "framer-motion";

export const ActivityHeatmap = () => {
  const days = Array.from({ length: 91 }, (_, i) => i);
  const levels = [0, 1, 2, 3, 2, 1, 0, 0, 4, 3, 2, 1];

  return (
    <div className="p-8 rounded-3xl bg-card border border-border shadow-sm">
      <div className="flex items-center justify-between mb-8">
        <div>
          <h3 className="text-lg font-black tracking-tighter text-foreground">Mission Velocity</h3>
          <p className="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Active Contribution Registry (Last 90 Days)</p>
        </div>
        <div className="flex items-center gap-2 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">
          <span>Less</span>
          <div className="flex gap-1">
            <div className="w-3 h-3 rounded-sm bg-secondary" />
            <div className="w-3 h-3 rounded-sm bg-lendi-blue/20" />
            <div className="w-3 h-3 rounded-sm bg-lendi-blue/50" />
            <div className="w-3 h-3 rounded-sm bg-lendi-blue" />
          </div>
          <span>More</span>
        </div>
      </div>

      <div className="grid grid-cols-[repeat(13,1fr)] gap-2">
        {days.map((day) => {
          const level = levels[day % levels.length];
          return (
            <motion.div
              key={day}
              initial={{ scale: 0.8, opacity: 0 }}
              whileInView={{ scale: 1, opacity: 1 }}
              transition={{ delay: day * 0.005 }}
              className="aspect-square rounded-sm border border-border/5"
              style={{
                backgroundColor: level === 0 ? "var(--secondary)" :
                                level === 1 ? "rgba(0, 74, 153, 0.15)" :
                                level === 2 ? "rgba(0, 74, 153, 0.4)" :
                                level === 3 ? "rgba(0, 74, 153, 0.7)" :
                                "rgba(0, 74, 153, 1)"
              }}
              title={`Activity Level: ${level}`}
            />
          );
        })}
      </div>

      <div className="mt-8 flex items-center justify-between py-4 border-t border-border/50">
        <div className="text-center">
          <p className="text-2xl font-black text-foreground tracking-tighter">142</p>
          <p className="text-[9px] font-black text-muted-foreground uppercase tracking-widest">Total Missions</p>
        </div>
        <div className="text-center">
          <p className="text-2xl font-black text-foreground tracking-tighter">12</p>
          <p className="text-[9px] font-black text-muted-foreground uppercase tracking-widest">Current Streak</p>
        </div>
        <div className="text-center">
          <p className="text-2xl font-black text-foreground tracking-tighter">84%</p>
          <p className="text-[9px] font-black text-muted-foreground uppercase tracking-widest">Commits/Week</p>
        </div>
      </div>
    </div>
  );
};
