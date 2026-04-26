"use client";

import { useEffect, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Search, Rocket, Users, Command, X, Target, Zap, ShieldCheck, ArrowUpRight } from "lucide-react";
import { supabase } from "@/lib/supabase";
import Link from "next/link";
import { Loader2 } from "lucide-react";

export const SearchOverlay = ({ isOpen, onClose }: { isOpen: boolean; onClose: () => void }) => {
  const [query, setQuery] = useState("");
  const [results, setResults] = useState<{ projects: any[]; talent: any[] }>({ projects: [], talent: [] });
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    if (!query) {
      setResults({ projects: [], talent: [] });
      return;
    }

    const search = async () => {
      setLoading(true);
      try {
        const [{ data: projects }, { data: talent }] = await Promise.all([
          supabase.from('projects').select('id, title, status').ilike('title', `%${query}%`).limit(5),
          supabase.from('profiles').select('id, full_name, rank, department').ilike('full_name', `%${query}%`).limit(5)
        ]);
        setResults({ projects: projects || [], talent: talent || [] });
      } catch (error) {
        console.error("Search failed:", error);
      } finally {
        setLoading(false);
      }
    };

    const timer = setTimeout(search, 300);
    return () => clearTimeout(timer);
  }, [query]);

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === "Escape") onClose();
    };
    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, [onClose]);

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-[200] flex items-start justify-center pt-[12vh] px-6">
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="absolute inset-0 bg-black/40 backdrop-blur-xl"
          />
          <motion.div
            initial={{ opacity: 0, scale: 0.98, y: -20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.98, y: -20 }}
            className="w-full max-w-2xl bg-card border border-border rounded-[2.5rem] shadow-premium overflow-hidden relative z-10"
          >
            <div className="p-8 border-b border-border flex items-center gap-6">
              <Search className="w-6 h-6 text-muted-foreground/30" />
              <input
                autoFocus
                type="text"
                placeholder="Search missions, personnel, or intelligence..."
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                className="flex-1 bg-transparent border-none focus:outline-none text-xl font-bold placeholder:text-muted-foreground/20 text-foreground"
              />
              <div className="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-secondary border border-border text-[10px] font-black text-muted-foreground uppercase tracking-widest">
                <Command className="w-3 h-3" /> K
              </div>
            </div>

            <div className="max-h-[55vh] overflow-y-auto p-4 custom-scrollbar bg-background/50">
              {!query && (
                <div className="py-20 text-center space-y-4">
                  <div className="w-16 h-16 rounded-3xl bg-secondary mx-auto flex items-center justify-center text-muted-foreground/20 border border-border">
                    <Target size={32} />
                  </div>
                  <div>
                    <p className="text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground/40 mb-1">Omni Search Protocol</p>
                    <p className="text-sm text-muted-foreground/60 font-medium">Scanning the institutional innovation network...</p>
                  </div>
                </div>
              )}

              {loading && (
                <div className="py-12 flex flex-col items-center gap-4 text-center">
                  <Loader2 className="w-8 h-8 animate-spin text-lendi-blue opacity-50" />
                  <p className="text-[9px] font-black uppercase tracking-widest text-muted-foreground/30">Processing Data Nodes</p>
                </div>
              )}

              {query && results.projects.length === 0 && results.talent.length === 0 && !loading && (
                <div className="py-20 text-center">
                  <p className="text-sm font-bold text-muted-foreground/30 uppercase tracking-widest italic">No matching records found in this sector.</p>
                </div>
              )}

              <div className="space-y-8 p-2">
                {results.projects.length > 0 && (
                  <section>
                    <div className="flex items-center justify-between px-4 mb-4">
                       <h4 className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground/40">Active Missions</h4>
                       <div className="h-px flex-1 mx-6 bg-border opacity-50" />
                    </div>
                    <div className="space-y-2">
                      {results.projects.map(p => (
                        <Link key={p.id} href={`/projects/${p.id}`} onClick={onClose}>
                          <div className="flex items-center justify-between p-4 rounded-2xl hover:bg-secondary transition-all group">
                            <div className="flex items-center gap-4">
                              <div className="w-11 h-11 rounded-xl bg-lendi-blue/10 flex items-center justify-center text-lendi-blue shadow-sm border border-lendi-blue/10">
                                <Rocket className="w-5 h-5" />
                              </div>
                              <div>
                                <span className="text-sm font-bold text-foreground group-hover:text-lendi-blue transition-colors">{p.title}</span>
                                <p className="text-[9px] font-black uppercase tracking-widest text-muted-foreground/40 mt-0.5">{p.status}</p>
                              </div>
                            </div>
                            <ArrowUpRight size={16} className="text-muted-foreground/20 group-hover:text-lendi-blue group-hover:translate-x-0.5 transition-all" />
                          </div>
                        </Link>
                      ))}
                    </div>
                  </section>
                )}

                {results.talent.length > 0 && (
                  <section>
                    <div className="flex items-center justify-between px-4 mb-4">
                       <h4 className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground/40">Verified Personnel</h4>
                       <div className="h-px flex-1 mx-6 bg-border opacity-50" />
                    </div>
                    <div className="space-y-2">
                      {results.talent.map(t => (
                        <Link key={t.id} href={`/profile/${t.id}`} onClick={onClose}>
                          <div className="flex items-center justify-between p-4 rounded-2xl hover:bg-secondary transition-all group">
                            <div className="flex items-center gap-4">
                              <div className="w-11 h-11 rounded-xl bg-secondary border border-border flex items-center justify-center text-muted-foreground/30 overflow-hidden">
                                {t.full_name?.[0]}
                              </div>
                              <div>
                                <span className="text-sm font-bold text-foreground group-hover:text-lendi-blue transition-colors">{t.full_name}</span>
                                <div className="flex items-center gap-2 mt-0.5">
                                  <p className="text-[9px] font-black uppercase tracking-widest text-muted-foreground/40">{t.department}</p>
                                  <div className="h-1 w-1 rounded-full bg-border" />
                                  <p className="text-[9px] font-black uppercase tracking-widest text-lendi-blue">{t.rank}</p>
                                </div>
                              </div>
                            </div>
                            <ArrowUpRight size={16} className="text-muted-foreground/20 group-hover:text-lendi-blue transition-all" />
                          </div>
                        </Link>
                      ))}
                    </div>
                  </section>
                )}
              </div>
            </div>

            <div className="p-6 border-t border-border bg-muted/30 flex justify-between items-center px-10">
              <div className="flex items-center gap-8">
                <div className="flex items-center gap-2.5 text-[9px] font-black uppercase text-muted-foreground/40">
                  <span className="px-2 py-1 rounded-lg bg-white border border-border text-foreground shadow-sm">Enter</span>
                  Select
                </div>
                <div className="flex items-center gap-2.5 text-[9px] font-black uppercase text-muted-foreground/40">
                  <span className="px-2 py-1 rounded-lg bg-white border border-border text-foreground shadow-sm">Esc</span>
                  Abort
                </div>
              </div>
              <div className="flex items-center gap-2">
                <Zap size={10} className="text-lendi-blue" />
                <p className="text-[9px] font-black uppercase tracking-[0.3em] text-lendi-blue opacity-40">Intelligence v1.0</p>
              </div>
            </div>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  );
};
