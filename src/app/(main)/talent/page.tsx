"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import {
  Users,
  Search,
  ArrowUpRight,
  Loader2,
  Star,
  GraduationCap,
  Target,
  Filter,
  BadgeCheck
} from "lucide-react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";
import { toast } from "sonner";

export default function TalentBoard() {
  const [talents, setTalents] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");

  useEffect(() => {
    const fetchTalent = async () => {
      setLoading(true);
      try {
        const { data, error } = await supabase
          .from('profiles')
          .select('*')
          .order('xp', { ascending: false });

        if (error) throw error;
        if (data) setTalents(data);
      } catch (error) {
        toast.error("Failed to sync personnel directory");
      } finally {
        setLoading(false);
      }
    };
    fetchTalent();
  }, []);

  const filteredTalents = talents.filter(t =>
    t.full_name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
    t.interests?.some((i: string) => i.toLowerCase().includes(searchQuery.toLowerCase())) ||
    t.department?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Personnel Directory" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">
                <Users size={12} />
                Talent Pool
              </div>
              <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst">Institutional Talent</h1>
              <p className="text-muted-foreground font-medium max-w-xl text-balance">
                Identify elite contributors and research partners across the LIET ecosystem based on verified institutional XP and track record.
              </p>
            </div>

            <div className="flex items-center gap-4">
              <div className="relative group">
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/40 group-focus-within:text-lendi-blue transition-colors" />
                <input
                  type="text"
                  placeholder="Search by name, track, or dept..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="bg-card border border-border rounded-2xl pl-12 pr-6 h-14 w-full md:w-80 text-sm font-bold focus:outline-none focus:border-lendi-blue transition-all shadow-sm"
                />
              </div>
              <Button variant="secondary" className="h-14 w-14 p-0 rounded-2xl border border-border">
                <Filter size={18} className="text-muted-foreground" />
              </Button>
            </div>
          </div>

          {loading ? (
            <div className="h-[400px] flex flex-col items-center justify-center gap-6">
              <div className="w-12 h-12 border-4 border-lendi-blue border-t-transparent rounded-full animate-spin" />
              <p className="text-muted-foreground font-black uppercase tracking-[0.3em] text-[10px]">Retrieving Personnel Records...</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-24">
              <AnimatePresence mode="popLayout">
                {filteredTalents.map((talent, i) => (
                  <motion.div
                    key={talent.id}
                    layout
                    initial={{ opacity: 0, scale: 0.95 }}
                    animate={{ opacity: 1, scale: 1 }}
                    exit={{ opacity: 0, scale: 0.95 }}
                    transition={{ delay: i * 0.05 }}
                    className="inst-card p-10 bg-card shadow-sm hover:border-lendi-blue transition-all group relative overflow-hidden flex flex-col"
                  >
                    <div className="flex items-start justify-between mb-8 relative z-10">
                      <div className="w-20 h-20 rounded-[2rem] bg-secondary border border-border flex items-center justify-center relative shadow-sm overflow-hidden group-hover:rotate-3 transition-transform">
                        {talent.avatar_url ? (
                          <img src={talent.avatar_url} alt="" className="w-full h-full object-cover" />
                        ) : (
                          <span className="text-2xl font-black text-muted-foreground/30">{talent.full_name?.[0]}</span>
                        )}
                        <div className="absolute -bottom-1 -right-1 w-7 h-7 rounded-full bg-white flex items-center justify-center border-2 border-secondary shadow-sm">
                           <BadgeCheck size={16} className="text-lendi-blue" />
                        </div>
                      </div>
                      <div className="text-right p-4 rounded-2xl bg-secondary border border-border">
                        <p className="text-xl font-black text-foreground">{talent.xp || 0}</p>
                        <p className="text-[8px] font-black uppercase tracking-widest text-muted-foreground/50">Inst. XP</p>
                      </div>
                    </div>

                    <div className="flex-1 relative z-10">
                      <h3 className="text-xl font-black tracking-tight mb-1 group-hover:text-lendi-blue transition-colors">
                        {talent.full_name}
                      </h3>
                      <div className="flex items-center gap-2 mb-6">
                        <span className="text-[10px] font-black uppercase tracking-widest text-muted-foreground/60">{talent.department}</span>
                        <div className="h-1 w-1 rounded-full bg-border" />
                        <span className="text-[10px] font-black uppercase tracking-widest text-lendi-blue">{talent.rank}</span>
                      </div>

                      <div className="flex flex-wrap gap-2 mb-8">
                        {talent.interests?.slice(0, 3).map((skill: string) => (
                          <span key={skill} className="px-3 py-1.5 rounded-xl bg-secondary border border-border text-[9px] font-bold text-muted-foreground uppercase tracking-widest">
                            {skill}
                          </span>
                        ))}
                        {(talent.interests?.length || 0) > 3 && (
                          <span className="text-[9px] font-black text-muted-foreground/30 uppercase tracking-widest">+{talent.interests.length - 3} More</span>
                        )}
                      </div>
                    </div>

                    <div className="flex gap-4 pt-8 border-t border-border relative z-10 mt-auto">
                      <Link href={`/profile/${talent.id}`} className="flex-1">
                        <Button variant="outline" className="w-full h-11 rounded-xl text-[10px] font-black uppercase tracking-widest gap-2">
                          Dossier
                          <ArrowUpRight size={14} />
                        </Button>
                      </Link>
                      <Button className="flex-1 rounded-xl h-11 text-[10px] font-black uppercase tracking-widest shadow-sm">
                        Collaborate
                      </Button>
                    </div>

                    {/* Subtle aesthetic backdrop */}
                    <div className="absolute -bottom-6 -right-6 text-lendi-blue/5 pointer-events-none group-hover:scale-110 transition-transform duration-700">
                      <Target size={120} />
                    </div>
                  </motion.div>
                ))}
              </AnimatePresence>
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
