"use client";

import { motion } from "framer-motion";
import { useEffect, useState } from "react";
import { Activity, Github } from "lucide-react";

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
      case 1: return "bg-lendi-blue/30";
      case 2: return "bg-lendi-blue/60";
      case 3: return "bg-lendi-blue";
      default: return "bg-secondary";
    }
  };

  return (
    <div className="space-y-8">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3 text-muted-foreground">
          <Activity size={16} className="text-lendi-blue" />
          <h3 className="text-[10px] font-black uppercase tracking-[0.3em]">Sector Activity Stream</h3>
        </div>
        <div className="flex items-center gap-2">
          <span className="text-[9px] font-black uppercase text-muted-foreground/40 mr-1">Intensity</span>
          <div className="flex gap-1.5">
            {[0, 1, 2, 3].map(l => (
              <div key={l} className={`w-3 h-3 rounded-sm ${getLevelColor(l)} border border-border/50`} />
            ))}
          </div>
        </div>
      </div>

      <div className="flex gap-1.5 flex-wrap">
        {days.map((day, i) => (
          <motion.div
            key={i}
            initial={{ opacity: 0, scale: 0.8 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ delay: i * 0.002 }}
            className={`w-[12px] h-[12px] rounded-[3px] ${getLevelColor(day.level)} border border-border/20 transition-all hover:border-lendi-blue cursor-crosshair`}
            title={`${day.date.toDateString()}: ${day.level} activity units`}
          />
        ))}
      </div>

      <div className="pt-6 border-t border-border flex justify-between items-end">
        <div className="flex gap-10">
          <div>
            <p className="text-2xl font-black tracking-tight text-foreground leading-none mb-1.5">582</p>
            <p className="text-[9px] font-black uppercase tracking-widest text-muted-foreground/50">Contributions</p>
          </div>
          <div>
            <p className="text-2xl font-black tracking-tight text-foreground leading-none mb-1.5">24</p>
            <p className="text-[9px] font-black uppercase tracking-widest text-muted-foreground/50">Day Streak</p>
          </div>
        </div>

        <div className="flex items-center gap-2 px-4 py-2 rounded-xl bg-secondary border border-border">
          <Github size={14} className="text-muted-foreground" />
          <span className="text-[10px] font-black uppercase tracking-widest text-muted-foreground">GitHub Sentinel Active</span>
        </div>
      </div>
    </div>
  );
};
