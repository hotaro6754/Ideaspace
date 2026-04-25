"use client";

import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { Header } from "@/components/layout/Header";
import { Button } from "@/components/ui/Button";
import { BackgroundGradient } from "@/components/ui/BackgroundGradient";
import { SkillTagging } from "@/components/profile/SkillTagging";
import { Globe, Zap, Rocket, ShieldCheck, Star, Loader2 } from "lucide-react";
import { useParams } from "next/navigation";
import { supabase } from "@/lib/supabase";

export default function ProfilePage() {
  const { id } = useParams();
  const [profile, setProfile] = useState<any>(null);
  const [vSkills, setVSkills] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchProfile = async () => {
      const { data } = await supabase.from('profiles').select('*').eq('id', id).single();
      const { data: skills } = await supabase.from('verified_skills').select('*').eq('user_id', id);
      if (data) setProfile(data);
      if (skills) setVSkills(skills);
      setLoading(false);
    };
    fetchProfile();
  }, [id]);

  if (loading) return <div className="h-screen flex items-center justify-center bg-black"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>;

  return (
    <div className="relative min-h-screen text-white font-inter bg-black overflow-x-hidden">
      <Header title="Personnel Dossier" />
      <main className="p-6 md:p-12 max-w-7xl mx-auto relative z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">
          <div className="lg:col-span-4 space-y-8">
            <div className="glass rounded-[3rem] p-10 border border-white/10 relative overflow-hidden group">
              <div className="absolute top-0 right-0 w-32 h-32 bg-lendi-blue/10 blur-[80px] rounded-full" />
              <div className="relative z-10 text-center">
                <div className="w-32 h-32 rounded-[2.5rem] bg-gradient-to-br from-lendi-blue to-purple-600 p-1 mb-8 shadow-2xl mx-auto">
                  <div className="w-full h-full rounded-[2.2rem] bg-black flex items-center justify-center text-4xl font-black">{profile.full_name?.[0]}</div>
                </div>
                <h1 className="text-3xl font-black font-plus-jakarta tracking-tight mb-2">{profile.full_name}</h1>
                <div className="flex items-center justify-center gap-2 mb-6">
                  <div className="px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">{profile.rank}</div>
                </div>
                <div className="grid grid-cols-3 gap-4 py-6 border-y border-white/5 mb-8">
                  <div><p className="text-xl font-black font-plus-jakarta">{profile.points}</p><p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter">Points</p></div>
                  <div><p className="text-xl font-black font-plus-jakarta">12</p><p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter">Missions</p></div>
                  <div><p className="text-xl font-black font-plus-jakarta">84</p><p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter">Rep</p></div>
                </div>
                <Button className="w-full rounded-2xl h-12 font-black shadow-xl shadow-lendi-blue/20 mb-3">Follow</Button>
                <Button variant="glass" className="w-full rounded-2xl h-12 font-black">Message</Button>
              </div>
            </div>

            {/* Verified Skills Badge Collection */}
            <div className="glass rounded-[2.5rem] p-8 border border-white/5">
              <h3 className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-8 ml-1 flex items-center gap-2">
                <ShieldCheck className="w-3 h-3 text-lendi-blue" />
                Verified Expertise
              </h3>
              <div className="space-y-3">
                {vSkills.length === 0 ? <p className="text-[10px] font-bold text-white/10 italic">No departmental verifications found.</p> : vSkills.map(s => (
                  <div key={s.id} className="flex items-center justify-between p-4 rounded-2xl bg-lendi-blue/[0.03] border border-lendi-blue/10 group hover:border-lendi-blue/30 transition-all">
                    <div className="flex items-center gap-3">
                      <div className="p-2 rounded-lg bg-lendi-blue/10 text-lendi-blue"><Zap className="w-4 h-4" /></div>
                      <span className="text-xs font-black uppercase tracking-widest">{s.skill_name}</span>
                    </div>
                    <div className="px-2 py-0.5 rounded-md bg-white/5 text-[8px] font-black uppercase text-white/40">{s.verification_level}</div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          <div className="lg:col-span-8 space-y-12">
            <section>
              <h3 className="text-xs font-black uppercase tracking-[0.3em] text-white/20 mb-8 ml-1">Identity & Interests</h3>
              <div className="flex flex-wrap gap-3">
                {profile.interests?.map((i: string) => (
                  <div key={i} className="px-6 py-3 rounded-2xl bg-white/5 border border-white/5 text-sm font-bold hover:border-lendi-blue/50 transition-colors">{i}</div>
                ))}
              </div>
            </section>

            <section>
              <h3 className="text-xs font-black uppercase tracking-[0.3em] text-white/20 mb-8 ml-1">Evidence of Contribution</h3>
              <div className="h-64 rounded-[3rem] border-2 border-dashed border-white/5 flex flex-col items-center justify-center text-center p-10">
                <div className="p-4 rounded-full bg-white/5 mb-4"><Star className="w-8 h-8 text-white/10" /></div>
                <p className="text-sm font-bold text-white/20">Contribution Heatmap Initializing...</p>
              </div>
            </section>
          </div>
        </div>
      </main>
    </div>
  );
}
