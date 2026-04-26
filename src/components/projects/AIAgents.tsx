"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Brain, Search, ShieldCheck, Zap, ChevronRight, Loader2, Sparkles, FileText, Fingerprint } from "lucide-react";
import { Button } from "@/components/ui/Button";

export const AIAgents = () => {
  const [activePersona, setActivePersona] = useState<string | null>(null);
  const [query, setQuery] = useState("");
  const [isProcessing, setIsProcessing] = useState(false);
  const [response, setResponse] = useState<string | null>(null);

  const personas = [
    {
      id: "auditor",
      name: "ZeroSlop Auditor",
      icon: Fingerprint,
      color: "text-lendi-blue",
      bg: "bg-lendi-blue/10",
      description: "Verifies technical integrity and adherence to institutional standards."
    },
    {
      id: "researcher",
      name: "Contextual Researcher",
      icon: Search,
      color: "text-blue-600",
      bg: "bg-blue-600/10",
      description: "Scans global repositories for similar tracks and optimization patterns."
    },
    {
      id: "advisor",
      name: "Mission Advisor",
      icon: ShieldCheck,
      color: "text-green-600",
      bg: "bg-green-600/10",
      description: "Provides strategic guidance on project milestones and team management."
    },
  ];

  const handleConsult = async () => {
    if (!query) return;
    setIsProcessing(true);
    setResponse(null);

    // Simulate agentic reasoning based on ZeroSlop and GSD frameworks
    setTimeout(() => {
      let msg = "";
      if (activePersona === 'auditor') {
        msg = "Audit Complete: The mission architecture demonstrates high structural integrity. Identified 2 potential 'slop' vectors in the proposed middleware. Recommendation: Consolidate state management to avoid sync-drift.";
      } else if (activePersona === 'researcher') {
        msg = "Discovery Scan: Found 14 similar tracks in the Lendi archive. Integrating these patterns could reduce the 'Build' phase by 15%. Recommend adopting the LIET-Standard-V2 authentication protocol.";
      } else {
        msg = "Strategic Insight: To hit the 'Ship' gate by May, I recommend reallocating resources from UI polish to core service hardening. The GSD framework suggests a 70/30 split between function and form at this stage.";
      }
      setResponse(msg);
      setIsProcessing(false);
    }, 1800);
  };

  return (
    <div className="inst-card p-8 bg-card shadow-sm relative overflow-hidden">
      <div className="flex items-center gap-3 mb-8">
        <div className="p-2 rounded-xl bg-lendi-blue/10 border border-lendi-blue/20">
          <Brain className="w-5 h-5 text-lendi-blue" />
        </div>
        <h4 className="text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground">Intelligence Sector</h4>
      </div>

      {!activePersona ? (
        <div className="space-y-4">
          {personas.map((p) => (
            <button
              key={p.id}
              onClick={() => setActivePersona(p.id)}
              className="w-full text-left p-5 rounded-2xl bg-secondary border border-border hover:border-lendi-blue transition-all group relative overflow-hidden"
            >
              <div className="flex items-center gap-5 relative z-10">
                <div className={`w-12 h-12 rounded-xl ${p.bg} flex items-center justify-center shrink-0`}>
                  <p.icon className={`w-6 h-6 ${p.color}`} />
                </div>
                <div>
                  <span className="text-sm font-black uppercase tracking-tight text-foreground block mb-0.5">{p.name}</span>
                  <p className="text-[10px] text-muted-foreground font-medium leading-tight line-clamp-1">{p.description}</p>
                </div>
                <ChevronRight className="ml-auto w-4 h-4 text-muted-foreground group-hover:text-lendi-blue transition-colors" />
              </div>
              <div className="absolute inset-y-0 right-0 w-1 bg-lendi-blue opacity-0 group-hover:opacity-100 transition-opacity" />
            </button>
          ))}
        </div>
      ) : (
        <motion.div initial={{ opacity: 0, x: 10 }} animate={{ opacity: 1, x: 0 }} className="space-y-6">
          <div className="flex items-center justify-between border-b border-border pb-4">
            <div className="flex items-center gap-3">
               <button onClick={() => { setActivePersona(null); setResponse(null); }} className="text-[10px] font-black uppercase text-muted-foreground hover:text-foreground transition-colors tracking-widest">&larr; Systems</button>
               <div className="h-3 w-px bg-border" />
               <span className="text-[10px] font-black uppercase tracking-widest text-lendi-blue">{activePersona} Active</span>
            </div>
            <Sparkles size={14} className="text-lendi-blue animate-pulse" />
          </div>

          <div className="space-y-4">
            <textarea
              placeholder={`Initialize inquiry with the ${activePersona} persona...`}
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              className="w-full bg-secondary border border-border rounded-2xl p-6 text-sm font-medium focus:outline-none focus:border-lendi-blue transition-all resize-none h-32 placeholder:text-muted-foreground/30 shadow-sm"
            />
            <Button
              onClick={handleConsult}
              disabled={isProcessing || !query}
              className="w-full h-14 rounded-2xl font-black uppercase text-xs tracking-[0.1em] shadow-lendi gap-3"
            >
              {isProcessing ? (
                <>
                  <Loader2 className="w-4 h-4 animate-spin" />
                  Synthesizing Node Data
                </>
              ) : (
                <>
                  Launch Consultation
                  <Zap size={16} fill="currentColor" />
                </>
              )}
            </Button>
          </div>

          <AnimatePresence mode="wait">
            {response && (
              <motion.div
                initial={{ opacity: 0, y: 10 }}
                animate={{ opacity: 1, y: 0 }}
                className="p-6 rounded-2xl bg-lendi-blue/5 border border-lendi-blue/10 relative overflow-hidden shadow-sm"
              >
                <div className="flex items-center gap-2 mb-3">
                  <div className="w-1 h-1 rounded-full bg-lendi-blue animate-pulse" />
                  <span className="text-[9px] font-black uppercase tracking-widest text-lendi-blue">Transmission Received</span>
                </div>
                <p className="text-xs font-medium leading-relaxed text-foreground italic">&quot;{response}&quot;</p>
                <div className="absolute top-0 right-0 p-3 opacity-[0.05] text-lendi-blue">
                  <Fingerprint size={64} />
                </div>
              </motion.div>
            )}
          </AnimatePresence>
        </motion.div>
      )}

      <div className="mt-8 pt-6 border-t border-border">
        <div className="flex items-center justify-between text-[8px] font-black uppercase tracking-[0.2em] text-muted-foreground/40">
           <span>Model: Lendi-Sentinel-AI</span>
           <span>Latency: 142ms</span>
        </div>
      </div>
    </div>
  );
};
