"use client";

import { motion } from "framer-motion";
import { Activity, AlertTriangle, CheckCircle2, ShieldAlert } from "lucide-react";

export const HealthWidget = ({ score = 85 }: { score?: number }) => {
  const getStatusColor = () => {
    if (score > 80) return "text-green-600";
    if (score > 50) return "text-amber-600";
    return "text-lendi-red";
  };

  const risks = [
    { name: "Silent Partner", status: "low", desc: "All personnel active" },
    { name: "Scope Creep", status: "med", desc: "Minor roadmap drift" },
    { name: "Deadline Drift", status: "low", desc: "On track for Ship" },
  ];

  return (
    <div className="inst-card p-8 bg-card shadow-sm relative overflow-hidden">
      <div className="flex items-center justify-between mb-8">
        <h3 className="text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground flex items-center gap-2">
          <Activity className="w-3.5 h-3.5 text-lendi-blue" />
          Mission Health
        </h3>
        <span className={`text-3xl font-black ${getStatusColor()}`}>{score}%</span>
      </div>

      <div className="space-y-4">
        {risks.map((risk) => (
          <div key={risk.name} className="flex items-center justify-between p-4 rounded-2xl bg-secondary border border-border group hover:border-lendi-blue transition-all">
            <div className="flex flex-col">
              <span className="text-[10px] font-black uppercase tracking-widest text-foreground/80 mb-0.5">{risk.name}</span>
              <span className="text-[8px] font-bold text-muted-foreground uppercase tracking-tighter">{risk.desc}</span>
            </div>
            {risk.status === "low" ? (
              <CheckCircle2 className="w-4 h-4 text-green-500/60 group-hover:text-green-500 transition-colors" />
            ) : (
              <AlertTriangle className="w-4 h-4 text-amber-500/60 group-hover:text-amber-500 transition-colors" />
            )}
          </div>
        ))}
      </div>

      <div className="mt-8 pt-6 border-t border-border">
        <p className="text-[8px] font-black uppercase tracking-[0.4em] text-center text-muted-foreground/30">ZeroSlop Validation Active</p>
      </div>
    </div>
  );
};
