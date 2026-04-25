"use client";

import { useEffect, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Search, Rocket, Users, FileText, X, Command } from "lucide-react";
import { supabase } from "@/lib/supabase";
import Link from "next/link";

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
      const [{ data: projects }, { data: talent }] = await Promise.all([
        supabase.from('projects').select('id, title').ilike('title', `%${query}%`).limit(5),
        supabase.from('profiles').select('id, full_name').ilike('full_name', `%${query}%`).limit(5)
      ]);
      setResults({ projects: projects || [], talent: talent || [] });
      setLoading(false);
    };

    const timer = setTimeout(search, 300);
    return () => clearTimeout(timer);
  }, [query]);

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && e.key === "k") {
        e.preventDefault();
        isOpen ? onClose() : null; // Handled by parent but good to have
      }
      if (e.key === "Escape") onClose();
    };
    window.addEventListener("keydown", handleKeyDown);
    return () => window.removeEventListener("keydown", handleKeyDown);
  }, [isOpen, onClose]);

  return (
    <AnimatePresence>
      {isOpen && (
        <div className="fixed inset-0 z-[200] flex items-start justify-center pt-[15vh] px-6">
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            onClick={onClose}
            className="absolute inset-0 bg-black/60 backdrop-blur-md"
          />
          <motion.div
            initial={{ opacity: 0, scale: 0.95, y: -20 }}
            animate={{ opacity: 1, scale: 1, y: 0 }}
            exit={{ opacity: 0, scale: 0.95, y: -20 }}
            className="w-full max-w-2xl bg-[#0A0A0A] border border-white/10 rounded-[2.5rem] shadow-2xl overflow-hidden relative z-10"
          >
            <div className="p-6 border-b border-white/5 flex items-center gap-4">
              <Search className="w-5 h-5 text-white/20" />
              <input
                autoFocus
                type="text"
                placeholder="Search projects, talent, or intelligence..."
                value={query}
                onChange={(e) => setQuery(e.target.value)}
                className="flex-1 bg-transparent border-none focus:outline-none text-lg font-medium placeholder:text-white/10"
              />
              <div className="flex items-center gap-2 px-2 py-1 rounded-md bg-white/5 border border-white/10 text-[10px] font-black text-white/20 uppercase tracking-widest">
                <Command className="w-3 h-3" /> K
              </div>
            </div>

            <div className="max-h-[60vh] overflow-y-auto p-4 custom-scrollbar">
              {!query && (
                <div className="p-8 text-center">
                  <p className="text-[10px] font-black uppercase tracking-[0.2em] text-white/20 mb-2">Omni Search Protocol</p>
                  <p className="text-xs text-white/10 italic">Type to scan the Lendi innovation network...</p>
                </div>
              )}

              {query && results.projects.length === 0 && results.talent.length === 0 && !loading && (
                <div className="p-8 text-center text-white/20 italic text-xs">No matches found in the current sector.</div>
              )}

              {results.projects.length > 0 && (
                <section className="mb-6">
                  <h4 className="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white/20">Missions</h4>
                  {results.projects.map(p => (
                    <Link key={p.id} href={`/projects/${p.id}`} onClick={onClose}>
                      <div className="flex items-center gap-4 p-4 rounded-2xl hover:bg-white/5 transition-all group">
                        <div className="w-10 h-10 rounded-xl bg-lendi-blue/10 flex items-center justify-center text-lendi-blue group-hover:bg-lendi-blue group-hover:text-white transition-colors">
                          <Rocket className="w-5 h-5" />
                        </div>
                        <span className="text-sm font-bold">{p.title}</span>
                      </div>
                    </Link>
                  ))}
                </section>
              )}

              {results.talent.length > 0 && (
                <section>
                  <h4 className="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white/20">Personnel</h4>
                  {results.talent.map(t => (
                    <Link key={t.id} href={`/profile/${t.id}`} onClick={onClose}>
                      <div className="flex items-center gap-4 p-4 rounded-2xl hover:bg-white/5 transition-all group">
                        <div className="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/20 group-hover:text-lendi-blue transition-colors">
                          <Users className="w-5 h-5" />
                        </div>
                        <span className="text-sm font-bold">{t.full_name}</span>
                      </div>
                    </Link>
                  ))}
                </section>
              )}
            </div>

            <div className="p-4 border-t border-white/5 bg-white/[0.01] flex justify-between items-center px-8">
              <div className="flex items-center gap-6">
                <div className="flex items-center gap-2 text-[8px] font-black uppercase text-white/20"><span className="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-white/40">Enter</span> to select</div>
                <div className="flex items-center gap-2 text-[8px] font-black uppercase text-white/20"><span className="px-1.5 py-0.5 rounded bg-white/5 border border-white/10 text-white/40">↑↓</span> to navigate</div>
              </div>
              <p className="text-[8px] font-black uppercase tracking-widest text-lendi-blue opacity-50">Sentinel Omni-Search v1</p>
            </div>
          </motion.div>
        </div>
      )}
    </AnimatePresence>
  );
};
