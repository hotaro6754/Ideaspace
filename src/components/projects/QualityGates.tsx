"use client";

import { motion } from "framer-motion";
import { CheckCircle2, Circle, ArrowRight, Shield, Rocket, MessageSquare, Ship } from "lucide-react";

export const QualityGates = ({ currentStage = "Discuss" }: { currentStage?: string }) => {
  const gates = [
    { name: "Discuss", icon: MessageSquare, desc: "Mission Ideation" },
    { name: "Charter", icon: Shield, desc: "Technical Scope" },
    { name: "Build", icon: Rocket, desc: "Development" },
    { name: "Ship", icon: Ship, desc: "Launch Protocol" },
  ];

  const currentIdx = gates.findIndex(g => g.name === currentStage);

  return (
    <div className="glass rounded-[2.5rem] p-10 border border-white/5 relative overflow-hidden">
      <h3 className="text-[10px] font-black uppercase tracking-[0.3em] text-white/20 mb-10">Quality Gates Roadmap</h3>

      <div className="flex items-center justify-between relative">
        <div className="absolute top-5 left-0 right-0 h-px bg-white/5 z-0" />
        <motion.div
          initial={{ width: 0 }}
          animate={{ width: `${(currentIdx / (gates.length - 1)) * 100}%` }}
          className="absolute top-5 left-0 h-px bg-lendi-blue z-0 shadow-[0_0_10px_rgba(0,74,153,0.5)]"
        />

        {gates.map((gate, i) => {
          const isCompleted = i < currentIdx;
          const isActive = i === currentIdx;
          const Icon = gate.icon;

          return (
            <div key={gate.name} className="relative z-10 flex flex-col items-center gap-4">
              <div className={`w-10 h-10 rounded-xl flex items-center justify-center transition-all duration-500 ${
                isCompleted ? "bg-lendi-blue text-white shadow-lg" :
                isActive ? "bg-white text-black scale-110 shadow-2xl" :
                "bg-white/5 text-white/20 border border-white/5"
              }`}>
                {isCompleted ? <CheckCircle2 className="w-5 h-5" /> : <Icon className="w-5 h-5" />}
              </div>
              <div className="text-center">
                <p className={`text-[10px] font-black uppercase tracking-widest mb-1 ${isActive ? "text-white" : "text-white/20"}`}>{gate.name}</p>
                <p className="text-[8px] font-bold text-white/10 uppercase tracking-tighter">{gate.desc}</p>
              </div>
            </div>
          );
        })}
      </div>

      <div className="mt-10 p-5 rounded-2xl bg-white/[0.02] border border-white/5 flex items-center justify-between">
        <div className="flex items-center gap-3">
          <div className="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse" />
          <p className="text-[10px] font-black uppercase text-white/40 tracking-widest">Target: Next Quality Gate</p>
        </div>
        <button className="text-[10px] font-black uppercase text-lendi-blue hover:text-white transition-colors flex items-center gap-2">
          Promote Mission <ArrowRight className="w-3 h-3" />
        </button>
      </div>
    </div>
  );
};
