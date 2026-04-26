"use client";

import { useEffect, useState } from "react";
import { supabase } from "@/lib/supabase";
import { motion } from "framer-motion";
import { Award, Star, ArrowUpRight, Loader2, Sparkles } from "lucide-react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";

export const AlumniSpotlight = () => {
  const [spotlight, setSpotlight] = useState<any>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchSpotlight = async () => {
      const { data } = await supabase
        .from('profiles')
        .select('*')
        .eq('role', 'alumni')
        .order('points', { ascending: false })
        .limit(1)
        .single();

      if (data) setSpotlight(data);
      setLoading(false);
    };
    fetchSpotlight();
  }, []);

  if (loading) return (
    <div className="glass rounded-[2.5rem] p-8 border border-white/5 flex items-center justify-center h-48">
      <Loader2 className="w-6 h-6 animate-spin text-orange-500" />
    </div>
  );

  if (!spotlight) return null;

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      animate={{ opacity: 1, y: 0 }}
      className="glass rounded-[2.5rem] p-8 border border-white/5 relative overflow-hidden group h-full flex flex-col"
    >
      <div className="absolute top-0 right-0 w-48 h-48 bg-orange-500/10 blur-[60px] rounded-full" />

      <div className="flex items-center gap-3 mb-8 relative z-10">
        <div className="p-2 rounded-lg bg-orange-500/10 border border-orange-500/20">
          <Sparkles className="w-4 h-4 text-orange-500" />
        </div>
        <h4 className="text-[10px] font-black uppercase tracking-[0.3em] text-white/30">Alumni Spotlight</h4>
      </div>

      <div className="flex-1 relative z-10">
        <div className="flex items-center gap-6 mb-6">
          <div className="w-20 h-20 rounded-[2rem] bg-gradient-to-br from-orange-500 to-red-600 p-px">
            <div className="w-full h-full rounded-[31px] bg-black flex items-center justify-center text-2xl font-black">
              {spotlight.full_name?.[0]}
            </div>
          </div>
          <div>
            <h3 className="text-xl font-black font-plus-jakarta mb-1 group-hover:text-orange-500 transition-colors">
              {spotlight.full_name}
            </h3>
            <p className="text-[10px] font-black uppercase tracking-widest text-white/40">
              {spotlight.department} • Alumnus
            </p>
          </div>
        </div>

        <div className="p-5 rounded-2xl bg-white/[0.02] border border-white/5 mb-8 italic text-xs text-white/60 leading-relaxed">
          "Currently spearheading distributed systems at enterprise scale. Proud LIET veteran focusing on mentoring the next generation of engineers."
        </div>

        <div className="flex items-center gap-6 mb-8">
          <div>
            <p className="text-lg font-black font-plus-jakarta">{spotlight.points}</p>
            <p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter">Legacy XP</p>
          </div>
          <div className="h-8 w-px bg-white/5" />
          <div>
            <p className="text-lg font-black font-plus-jakarta">Expert</p>
            <p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter">Ranking</p>
          </div>
        </div>
      </div>

      <Link href={`/profile/${spotlight.id}`} className="relative z-10">
        <Button variant="glass" className="w-full rounded-xl h-12 text-[10px] font-black uppercase tracking-widest flex gap-2 border-orange-500/20 text-orange-500 hover:bg-orange-500/10">
          Request Guidance
          <ArrowUpRight className="w-3.5 h-3.5" />
        </Button>
      </Link>
    </motion.div>
  );
};
