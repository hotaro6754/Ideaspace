"use client";

import { useEffect, useState } from "react";
import { motion } from "framer-motion";
import { Header } from "@/components/layout/Header";
import { Button } from "@/components/ui/Button";
import { Globe, Zap, Rocket, ShieldCheck, Star, Loader2, Download, FileText, CheckCircle2 } from "lucide-react";
import { useParams } from "next/navigation";
import { supabase } from "@/lib/supabase";
import { toast } from "sonner";
import { ActivityHeatmap } from "@/components/profile/ActivityHeatmap";

export default function ProfilePage() {
  const { id } = useParams();
  const [profile, setProfile] = useState<any>(null);
  const [vSkills, setVSkills] = useState<any[]>([]);
  const [missions, setMissions] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [currentUser, setCurrentUser] = useState<any>(null);
  const [isMentor, setIsMentor] = useState(false);

  useEffect(() => {
    const fetchData = async () => {
      const { data: { user } } = await supabase.auth.getUser();
      if (user) {
        setCurrentUser(user);
        const { data: myProfile } = await supabase.from('profiles').select('role, points').eq('id', user.id).single();
        if (myProfile) {
          setIsMentor(myProfile.role === 'alumni' || myProfile.points > 500);
        }
      }

      const { data } = await supabase.from('profiles').select('*').eq('id', id).single();
      const { data: skills } = await supabase.from('verified_skills').select('*').eq('user_id', id);
      const { data: mems } = await supabase.from('project_members').select('*, projects:project_id(*)').eq('user_id', id);

      if (data) setProfile(data);
      if (skills) setVSkills(skills || []);
      if (mems) setMissions(mems || []);
      setLoading(false);
    };
    fetchData();
  }, [id]);

  const verifySkill = async (skillName: string) => {
    if (!currentUser) return;

    const { error } = await supabase.from('verified_skills').insert({
      user_id: id,
      skill_name: skillName,
      verifier_id: currentUser.id,
      verification_level: 'expert'
    });

    if (error) {
      toast.error(error.message);
    } else {
      toast.success(`${skillName} verified!`);
      const { data: skills } = await supabase.from('verified_skills').select('*').eq('user_id', id);
      if (skills) setVSkills(skills);
    }
  };

  const exportDossier = () => {
    toast.info("Generating encrypted technical dossier...");
    setTimeout(() => {
      window.print();
    }, 1000);
  };

  if (loading) return <div className="h-screen flex items-center justify-center bg-black"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>;
  if (!profile) return <div className="h-screen flex items-center justify-center bg-black">Profile not found.</div>;

  const unverifiedInterests = profile.interests?.filter((i: string) =>
    !vSkills.some(s => s.skill_name === i)
  ) || [];

  return (
    <div className="relative min-h-screen text-white font-inter bg-black overflow-x-hidden">
      <Header title="Personnel Dossier" />
      <main className="p-6 md:p-12 max-w-7xl mx-auto relative z-10 print:p-0 print:bg-white print:text-black">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">
          <div className="lg:col-span-4 space-y-8 print:col-span-12">
            <div className="glass rounded-[3rem] p-10 border border-white/10 relative overflow-hidden group print:border-none print:p-0 print:shadow-none">
              <div className="absolute top-0 right-0 w-32 h-32 bg-lendi-blue/10 blur-[80px] rounded-full print:hidden" />
              <div className="relative z-10 text-center print:text-left">
                <div className="w-32 h-32 rounded-[2.5rem] bg-gradient-to-br from-lendi-blue to-purple-600 p-1 mb-8 shadow-2xl mx-auto print:mx-0 print:w-20 print:h-20">
                  <div className="w-full h-full rounded-[2.2rem] bg-black flex items-center justify-center text-4xl font-black print:text-white print:text-2xl">{profile.full_name?.[0]}</div>
                </div>
                <h1 className="text-3xl font-black font-plus-jakarta tracking-tight mb-2">{profile.full_name}</h1>
                <div className="flex items-center justify-center gap-2 mb-6 print:justify-start">
                  <div className="px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest print:border-black print:text-black">{profile.rank}</div>
                </div>
                <div className="grid grid-cols-3 gap-4 py-6 border-y border-white/5 mb-8 print:border-black">
                  <div><p className="text-xl font-black font-plus-jakarta">{profile.points}</p><p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter print:text-black/40">Points</p></div>
                  <div><p className="text-xl font-black font-plus-jakarta">{missions.length}</p><p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter print:text-black/40">Missions</p></div>
                  <div><p className="text-xl font-black font-plus-jakarta">{vSkills.length}</p><p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter print:text-black/40">Skills</p></div>
                </div>

                <div className="space-y-3 print:hidden">
                  <Button onClick={exportDossier} className="w-full rounded-2xl h-14 font-black shadow-xl shadow-lendi-blue/20 flex gap-3">
                    <FileText className="w-4 h-4" />
                    Export Dossier
                  </Button>
                  <Button variant="glass" className="w-full rounded-2xl h-12 font-black">Message</Button>
                </div>
              </div>
            </div>

            <div className="glass rounded-[2.5rem] p-8 border border-white/5 print:border-none print:p-0 print:pt-8">
              <h3 className="text-[10px] font-black uppercase tracking-[0.3em] text-white/20 mb-8 ml-1 flex items-center gap-2 print:text-black/40">
                <ShieldCheck className="w-3 h-3 text-lendi-blue print:text-black" />
                Verified Expertise
              </h3>
              <div className="space-y-3">
                {vSkills.length === 0 ? <p className="text-[10px] font-bold text-white/10 italic">No departmental verifications found.</p> : vSkills.map(s => (
                  <div key={s.id} className="flex items-center justify-between p-4 rounded-2xl bg-lendi-blue/[0.03] border border-lendi-blue/10 group hover:border-lendi-blue/30 transition-all print:border-black/10">
                    <div className="flex items-center gap-3">
                      <div className="p-2 rounded-lg bg-lendi-blue/10 text-lendi-blue print:bg-black/5 print:text-black"><Zap className="w-4 h-4" /></div>
                      <span className="text-xs font-black uppercase tracking-widest">{s.skill_name}</span>
                    </div>
                    <div className="px-2 py-0.5 rounded-md bg-white/5 text-[8px] font-black uppercase text-white/40 print:text-black/40">{s.verification_level}</div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          <div className="lg:col-span-8 space-y-12 print:col-span-12">
            <section>
              <h3 className="text-xs font-black uppercase tracking-[0.3em] text-white/20 mb-8 ml-1 print:text-black/40">Identity & Interests</h3>
              <div className="flex flex-wrap gap-3">
                {unverifiedInterests.map((i: string) => (
                  <div key={i} className="px-6 py-3 rounded-2xl bg-white/5 border border-white/5 text-sm font-bold flex items-center gap-3 group/skill hover:border-lendi-blue/50 transition-colors print:border-black/20 print:text-black/60">
                    {i}
                    {isMentor && id !== currentUser?.id && (
                      <button
                        onClick={() => verifySkill(i)}
                        className="p-1 rounded-md hover:bg-lendi-blue/20 text-white/20 hover:text-lendi-blue transition-all print:hidden"
                      >
                        <CheckCircle2 className="w-4 h-4" />
                      </button>
                    )}
                  </div>
                ))}
                {profile.interests?.filter((i: string) => vSkills.some(s => s.skill_name === i)).map((i: string) => (
                  <div key={i} className="px-6 py-3 rounded-2xl bg-lendi-blue/5 border border-lendi-blue/20 text-sm font-bold flex items-center gap-3 text-lendi-blue print:border-black print:text-black">
                    {i}
                    <ShieldCheck className="w-4 h-4" />
                  </div>
                ))}
              </div>
            </section>

            <section className="print:hidden">
              <h3 className="text-xs font-black uppercase tracking-[0.3em] text-white/20 mb-8 ml-1">Contribution Heatmap</h3>
              <ActivityHeatmap />
            </section>

            <section>
              <h3 className="text-xs font-black uppercase tracking-[0.3em] text-white/20 mb-8 ml-1 print:text-black/40">Evidence of Contribution</h3>
              <div className="space-y-4">
                {missions.length === 0 ? (
                  <div className="h-64 rounded-[3rem] border-2 border-dashed border-white/5 flex flex-col items-center justify-center text-center p-10">
                    <div className="p-4 rounded-full bg-white/5 mb-4"><Star className="w-8 h-8 text-white/10" /></div>
                    <p className="text-sm font-bold text-white/20">No mission logs detected.</p>
                  </div>
                ) : (
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-4 print:grid-cols-1">
                    {missions.map(m => (
                      <div key={m.id} className="glass p-6 rounded-3xl border border-white/5 flex items-center justify-between group print:border-black/10">
                        <div className="flex items-center gap-4">
                          <div className="p-3 rounded-xl bg-lendi-blue/10 text-lendi-blue print:bg-black/5 print:text-black"><Rocket className="w-5 h-5" /></div>
                          <div>
                            <h4 className="font-black text-sm">{m.projects?.title}</h4>
                            <p className="text-[10px] font-bold text-white/20 uppercase tracking-widest print:text-black/40">{m.role}</p>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                )}
              </div>
            </section>
          </div>
        </div>
      </main>
    </div>
  );
}
