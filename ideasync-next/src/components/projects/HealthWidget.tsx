"use client";

import { motion } from "framer-motion";
import { Activity, AlertTriangle, CheckCircle2, ShieldAlert } from "lucide-react";

export const HealthWidget = ({ score = 85 }: { score?: number }) => {
  const getStatusColor = () => {
    if (score > 80) return "text-green-400";
    if (score > 50) return "text-yellow-400";
    return "text-red-400";
  };

  const risks = [
    { name: "Silent Partner", status: "low", desc: "All personnel active" },
    { name: "Scope Creep", status: "med", desc: "Minor roadmap drift" },
    { name: "Deadline Drift", status: "low", desc: "On track for Ship" },
  ];

  return (
    <div className="glass rounded-[2.5rem] p-8 border border-white/5 relative overflow-hidden bg-white/[0.01]">
      <div className="flex items-center justify-between mb-8">
        <h3 className="text-[10px] font-black uppercase tracking-[0.3em] text-white/30 flex items-center gap-2">
          <Activity className="w-3.5 h-3.5 text-lendi-blue" />
          Sector Health
        </h3>
        <span className={`text-2xl font-black font-plus-jakarta ${getStatusColor()}`}>{score}%</span>
      </div>

      <div className="space-y-4">
        {risks.map((risk) => (
          <div key={risk.name} className="flex items-center justify-between p-4 rounded-2xl bg-white/[0.02] border border-white/5 group hover:border-white/10 transition-all">
            <div className="flex flex-col">
              <span className="text-[10px] font-black uppercase tracking-widest text-white/60 mb-0.5">{risk.name}</span>
              <span className="text-[8px] font-bold text-white/20 uppercase tracking-tighter">{risk.desc}</span>
            </div>
            {risk.status === "low" ? (
              <CheckCircle2 className="w-4 h-4 text-green-500/40 group-hover:text-green-500 transition-colors" />
            ) : (
              <AlertTriangle className="w-4 h-4 text-yellow-500/40 group-hover:text-yellow-500 transition-colors" />
            )}
          </div>
        ))}
      </div>

      <div className="mt-8 pt-6 border-t border-white/5">
        <p className="text-[7px] font-black uppercase tracking-[0.4em] text-center opacity-10">Anti-Pattern Detection Model Active</p>
      </div>
    </div>
  );
};
