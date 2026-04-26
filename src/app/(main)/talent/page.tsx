
"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import { Users, Search, Filter, Code2, ShieldCheck, Mail, ArrowUpRight, Loader2, Sparkles, Star } from "lucide-react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";

export default function TalentBoard() {
  const [talents, setTalents] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");

  useEffect(() => {
    const fetchTalent = async () => {
      setLoading(true);
      const { data } = await supabase
        .from('profiles')
        .select('*')
        .order('points', { ascending: false });
      if (data) setTalents(data);
      setLoading(false);
    };
    fetchTalent();
  }, []);

  const filteredTalents = talents.filter(t =>
    t.full_name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
    t.interests?.some((i: string) => i.toLowerCase().includes(searchQuery.toLowerCase())) ||
    t.department?.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black">
      <Header title="Talent Network" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2 flex items-center gap-4">
                Personnel Directory
                <div className="px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">
                  Verified Talent
                </div>
              </h1>
              <p className="text-white/40 font-medium tracking-tight">Source elite contributors for your missions based on verified skills and reputation.</p>
            </div>
            <div className="flex gap-4">
              <div className="relative group">
                <Search className="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20 group-focus-within:text-lendi-blue transition-colors" />
                <input
                  type="text"
                  placeholder="Search by name, skill, or dept..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="bg-white/5 border border-white/5 rounded-2xl pl-14 pr-6 h-14 w-80 text-sm focus:outline-none focus:border-lendi-blue/50 transition-all placeholder:text-white/10"
                />
              </div>
              <Button variant="glass" className="rounded-2xl px-6 h-14"><Filter className="w-5 h-5 text-white/20" /></Button>
            </div>
          </div>

          {loading ? (
            <div className="flex justify-center py-20"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 pb-20">
              <AnimatePresence mode="popLayout">
                {filteredTalents.map((talent, i) => (
                  <motion.div
                    key={talent.id}
                    layout
                    initial={{ opacity: 0, scale: 0.95 }}
                    animate={{ opacity: 1, scale: 1 }}
                    exit={{ opacity: 0, scale: 0.95 }}
                    transition={{ delay: i * 0.05 }}
                    className="glass rounded-[2.5rem] p-8 border border-white/5 hover:border-lendi-blue/30 transition-all group relative overflow-hidden flex flex-col"
                  >
                    <div className="absolute top-0 right-0 w-24 h-24 bg-lendi-blue/5 blur-2xl rounded-full group-hover:bg-lendi-blue/10 transition-colors" />

                    <div className="flex items-start justify-between mb-8 relative z-10">
                      <div className="w-16 h-16 rounded-2xl bg-white/5 p-px flex items-center justify-center relative">
                        <div className="w-full h-full rounded-[15px] bg-black flex items-center justify-center text-2xl font-black text-white/40">
                          {talent.full_name?.[0]}
                        </div>
                        <div className="absolute -bottom-1 -right-1 w-6 h-6 rounded-full bg-lendi-blue flex items-center justify-center border-2 border-black">
                          <Star className="w-3 h-3 text-white fill-white" />
                        </div>
                      </div>
                      <div className="text-right">
                        <p className="text-xl font-black font-plus-jakarta tracking-tight">{talent.points}</p>
                        <p className="text-[8px] font-black uppercase tracking-widest text-white/20">Rep Score</p>
                      </div>
                    </div>

                    <div className="flex-1 relative z-10">
                      <h3 className="text-xl font-black font-plus-jakarta mb-1 group-hover:text-lendi-blue transition-colors">
                        {talent.full_name}
                      </h3>
                      <p className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-6">
                        {talent.department} • {talent.role}
                      </p>

                      <div className="flex flex-wrap gap-2 mb-8">
                        {talent.interests?.slice(0, 3).map((skill: string) => (
                          <span key={skill} className="px-3 py-1 rounded-lg bg-white/5 border border-white/5 text-[9px] font-black text-white/40 uppercase tracking-tighter">
                            {skill}
                          </span>
                        ))}
                        {(talent.interests?.length || 0) > 3 && (
                          <span className="text-[9px] font-bold text-white/10">+{talent.interests.length - 3} more</span>
                        )}
                      </div>
                    </div>

                    <div className="flex gap-3 pt-6 border-t border-white/5 relative z-10">
                      <Link href={`/profile/${talent.id}`} className="flex-1">
                        <Button variant="glass" className="w-full rounded-xl h-12 text-[10px] font-black uppercase tracking-widest flex gap-2">
                          Dossier
                          <ArrowUpRight className="w-3.5 h-3.5" />
                        </Button>
                      </Link>
                      <Button className="flex-1 rounded-xl h-12 text-[10px] font-black uppercase tracking-widest bg-white/5 hover:bg-white/10 text-white shadow-none border border-white/5">
                        Inquire
                      </Button>
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
