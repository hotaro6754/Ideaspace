"use client";

import { Header } from "@/components/layout/Header";
import { motion } from "framer-motion";
import { Rocket, Shield, Zap, Ship, CheckCircle2, Circle, ArrowRight, Sparkles } from "lucide-react";

export default function RoadmapPage() {
  const versions = [
    {
      v: "1.0",
      name: "Sentinel Core",
      status: "Operational",
      features: ["Elite Feed Architecture", "Mission Hub Spawning", "Talent Directory", "Real-time Encryption"],
      date: "Q1 2026"
    },
    {
      v: "1.1",
      name: "Personnel Uplink",
      status: "Initializing",
      features: ["Alumni Mentorship Hub", "Bounty Execution Board", "Verified Skill Badges", "Dossier Export"],
      date: "Q2 2026"
    },
    {
      v: "2.0",
      name: "Neural Network",
      status: "Planned",
      features: ["Deep Learning Agents", "Automated Mission Matching", "Cross-Departmental DAO", "Innovation Marketplace"],
      date: "Q4 2026"
    }
  ];

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Platform Intelligence" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-4xl mx-auto">
          <div className="mb-20 text-center">
            <h1 className="text-6xl font-black font-plus-jakarta tracking-tightest mb-4">Evolutionary Roadmap</h1>
            <p className="text-white/40 text-lg font-medium">Tracking the continuous enhancement of the IdeaSync ecosystem.</p>
          </div>

          <div className="space-y-12 relative">
            <div className="absolute left-[39px] top-0 bottom-0 w-px bg-white/5 z-0" />

            {versions.map((ver, i) => (
              <motion.div
                key={ver.v}
                initial={{ opacity: 0, x: -20 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ delay: i * 0.2 }}
                className="flex gap-12 relative z-10"
              >
                <div className={`w-20 h-20 rounded-[2rem] flex items-center justify-center shrink-0 border-2 ${
                  ver.status === "Operational" ? "bg-lendi-blue/10 border-lendi-blue/40" :
                  ver.status === "Initializing" ? "bg-orange-500/10 border-orange-500/40" :
                  "bg-white/5 border-white/10"
                }`}>
                   <span className={`text-xl font-black font-plus-jakarta ${
                     ver.status === "Operational" ? "text-lendi-blue" :
                     ver.status === "Initializing" ? "text-orange-500" :
                     "text-white/20"
                   }`}>{ver.v}</span>
                </div>

                <div className="flex-1 glass rounded-[3rem] p-10 border border-white/5 group hover:border-white/10 transition-all">
                  <div className="flex justify-between items-start mb-6">
                    <div>
                      <h3 className="text-2xl font-black font-plus-jakarta mb-1 group-hover:text-lendi-blue transition-colors">{ver.name}</h3>
                      <p className="text-[10px] font-black uppercase tracking-widest text-white/20">{ver.date}</p>
                    </div>
                    <div className={`px-4 py-1.5 rounded-full text-[8px] font-black uppercase tracking-widest border ${
                      ver.status === "Operational" ? "bg-green-500/10 border-green-500/20 text-green-400" :
                      ver.status === "Initializing" ? "bg-orange-500/10 border-orange-500/20 text-orange-400" :
                      "bg-white/5 border-white/5 text-white/20"
                    }`}>
                      {ver.status}
                    </div>
                  </div>

                  <div className="grid grid-cols-2 gap-4">
                    {ver.features.map(f => (
                      <div key={f} className="flex items-center gap-3">
                         <div className={`w-1.5 h-1.5 rounded-full ${ver.status === 'Operational' ? 'bg-lendi-blue' : 'bg-white/10'}`} />
                         <span className={`text-xs font-bold ${ver.status === 'Operational' ? 'text-white/80' : 'text-white/30'}`}>{f}</span>
                      </div>
                    ))}
                  </div>
                </div>
              </motion.div>
            ))}
          </div>

          <div className="mt-32 p-12 rounded-[4rem] bg-gradient-to-br from-lendi-blue/20 via-black to-purple-600/20 border border-white/10 text-center relative overflow-hidden">
             <div className="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20 pointer-events-none" />
             <div className="relative z-10">
               <Sparkles className="w-12 h-12 text-lendi-blue mx-auto mb-6" />
               <h2 className="text-4xl font-black font-plus-jakarta mb-4">Suggest a Protocol</h2>
               <p className="text-white/40 max-w-xl mx-auto mb-10 leading-relaxed font-medium">The IdeaSync network is shaped by its innovators. Propose new modules or feature sectors for the next Sentinel version.</p>
               <button className="h-16 px-12 rounded-2xl bg-white text-black font-black uppercase text-xs tracking-[0.2em] hover:bg-white/90 shadow-2xl transition-all">Submit Protocol Request</button>
             </div>
          </div>
        </div>
      </main>
    </div>
  );
}
