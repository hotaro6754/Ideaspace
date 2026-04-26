"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Brain, Search, ShieldCheck, Zap, ChevronRight, Loader2, Sparkles } from "lucide-react";
import { Button } from "@/components/ui/Button";

export const AIAgents = () => {
  const [activePersona, setActivePersona] = useState<string | null>(null);
  const [query, setQuery] = useState("");
  const [isProcessing, setIsProcessing] = useState(false);
  const [response, setResponse] = useState<string | null>(null);

  const personas = [
    { id: "researcher", name: "Researcher", icon: Search, color: "text-blue-400", bg: "bg-blue-400/10" },
    { id: "advisor", name: "Advisor", icon: ShieldCheck, color: "text-green-400", bg: "bg-green-400/10" },
    { id: "lead", name: "Lead", icon: Zap, color: "text-yellow-400", bg: "bg-yellow-400/10" },
  ];

  const handleConsult = async () => {
    if (!query) return;
    setIsProcessing(true);
    setResponse(null);

    // Simulate AI response
    setTimeout(() => {
      setResponse("Based on the technical dossier provided, I recommend prioritizing the integration of distributed ledger protocols for higher data integrity. Previous LIET missions in this sector encountered bottlenecks in real-time sync which can be mitigated using WebSockets.");
      setIsProcessing(false);
    }, 2000);
  };

  return (
    <div className="glass rounded-[2.5rem] p-8 border border-white/5 relative overflow-hidden">
      <div className="flex items-center gap-3 mb-8">
        <div className="p-2 rounded-lg bg-lendi-blue/10 border border-lendi-blue/20">
          <Brain className="w-4 h-4 text-lendi-blue" />
        </div>
        <h4 className="text-[10px] font-black uppercase tracking-[0.3em] text-white/30">AI Project Agents</h4>
      </div>

      {!activePersona ? (
        <div className="space-y-3">
          {personas.map((p) => (
            <button
              key={p.id}
              onClick={() => setActivePersona(p.id)}
              className="w-full flex items-center justify-between p-4 rounded-2xl bg-white/[0.02] border border-white/5 hover:border-lendi-blue/30 transition-all group"
            >
              <div className="flex items-center gap-4">
                <div className={`w-10 h-10 rounded-xl ${p.bg} flex items-center justify-center`}>
                  <p.icon className={`w-5 h-5 ${p.color}`} />
                </div>
                <span className="text-xs font-black uppercase tracking-widest">{p.name}</span>
              </div>
              <ChevronRight className="w-4 h-4 text-white/10 group-hover:text-lendi-blue transition-colors" />
            </button>
          ))}
        </div>
      ) : (
        <motion.div initial={{ opacity: 0, x: 20 }} animate={{ opacity: 1, x: 0 }} className="space-y-6">
          <div className="flex items-center justify-between">
            <div className="flex items-center gap-3">
               <button onClick={() => setActivePersona(null)} className="text-[10px] font-black uppercase text-white/20 hover:text-white transition-colors tracking-widest">&larr; Back</button>
               <div className="h-3 w-px bg-white/10" />
               <span className="text-xs font-black uppercase tracking-widest text-lendi-blue">{activePersona}</span>
            </div>
          </div>

          <div className="space-y-4">
            <textarea
              placeholder={`Consult with the ${activePersona} persona...`}
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              className="w-full bg-white/5 border border-white/5 rounded-2xl p-5 text-xs font-medium focus:outline-none focus:border-lendi-blue/50 resize-none h-24"
            />
            <Button
              onClick={handleConsult}
              disabled={isProcessing || !query}
              className="w-full rounded-xl h-12 bg-white text-black font-black uppercase text-[10px] tracking-widest shadow-2xl"
            >
              {isProcessing ? <Loader2 className="w-4 h-4 animate-spin" /> : "Initiate Consultation"}
            </Button>
          </div>

          <AnimatePresence>
            {response && (
              <motion.div initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} className="p-5 rounded-2xl bg-lendi-blue/5 border border-lendi-blue/10 relative overflow-hidden">
                <div className="absolute top-0 right-0 p-3 opacity-10"><Sparkles className="w-4 h-4 text-lendi-blue" /></div>
                <p className="text-[10px] font-medium leading-relaxed text-white/70 italic">&quot;{response}&quot;</p>
              </motion.div>
            )}
          </AnimatePresence>
        </motion.div>
      )}
    </div>
  );
};
