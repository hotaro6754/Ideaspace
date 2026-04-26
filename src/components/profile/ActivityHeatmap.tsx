"use client";

import { motion } from "framer-motion";
import { useEffect, useState } from "react";

interface DayData {
  level: number;
  date: Date;
}

export const ActivityHeatmap = () => {
  const [days, setDays] = useState<DayData[]>([]);

  useEffect(() => {
    // Generate random activity data for the demo
    const data = Array.from({ length: 154 }, (_, i) => ({
      level: Math.floor(Math.random() * 4), // 0 to 3 intensity
      date: new Date(Date.now() - (153 - i) * 24 * 60 * 60 * 1000)
    }));
    setDays(data);
  }, []);

  const getLevelColor = (level: number) => {
    switch (level) {
      case 1: return "bg-lendi-blue/20";
      case 2: return "bg-lendi-blue/50";
      case 3: return "bg-lendi-blue";
      default: return "bg-white/5";
    }
  };

  return (
    <div className="glass rounded-[2.5rem] p-10 border border-white/5 relative overflow-hidden">
      <div className="flex items-center justify-between mb-8">
        <h3 className="text-xs font-black uppercase tracking-[0.3em] text-white/20">Contribution Pulse</h3>
        <div className="flex items-center gap-2">
          <span className="text-[8px] font-black uppercase text-white/20">Less</span>
          <div className="flex gap-1">
            {[0, 1, 2, 3].map(l => (
              <div key={l} className={`w-2.5 h-2.5 rounded-sm ${getLevelColor(l)}`} />
            ))}
          </div>
          <span className="text-[8px] font-black uppercase text-white/20">More</span>
        </div>
      </div>

      <div className="flex gap-1.5 flex-wrap">
        {days.map((day, i) => (
          <motion.div
            key={i}
            initial={{ opacity: 0, scale: 0.5 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ delay: i * 0.005 }}
            className={`w-3 h-3 rounded-sm ${getLevelColor(day.level)} transition-colors hover:border hover:border-white/20 cursor-crosshair`}
            title={`${day.date.toDateString()}: ${day.level} sectors active`}
          />
        ))}
      </div>

      <div className="mt-8 pt-8 border-t border-white/5 flex justify-between items-center">
        <div className="flex gap-8">
          <div>
            <p className="text-xl font-black font-plus-jakarta tracking-tight">412</p>
            <p className="text-[8px] font-black uppercase tracking-widest text-white/20">Total Commits</p>
          </div>
          <div>
            <p className="text-xl font-black font-plus-jakarta tracking-tight">18</p>
            <p className="text-[8px] font-black uppercase tracking-widest text-white/20">Current Streak</p>
          </div>
        </div>
        <p className="text-[8px] font-black uppercase tracking-[0.4em] text-lendi-blue/40">Syncing with GitHub Sentinel...</p>
      </div>
    </div>
  );
};
