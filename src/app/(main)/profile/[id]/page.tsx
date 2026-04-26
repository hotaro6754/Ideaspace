"use client";
export const dynamic = "force-dynamic";

import { useEffect, useState } from "react";
import { useParams } from "next/navigation";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import {
  ShieldCheck,
  Star,
  Rocket,
  FileText,
  Loader2,
  Zap,
  CheckCircle2,
  Mail,
  Github,
  Linkedin,
  Globe,
  Award,
  BadgeCheck, ArrowUpRight
} from "lucide-react";
import { Button } from "@/components/ui/Button";
import { ActivityHeatmap } from "@/components/profile/ActivityHeatmap";
import { motion } from "framer-motion";
import { toast } from "sonner";

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
        const { data: myProfile } = await supabase.from('profiles').select('role, xp').eq('id', user.id).single();
        if (myProfile) {
          setIsMentor(myProfile.role === 'alumni' || (myProfile.xp && myProfile.xp > 1000));
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
    try {
      const { error } = await supabase.from('verified_skills').insert({
        user_id: id,
        skill_name: skillName,
        verifier_id: currentUser.id,
        verification_level: 'departmental'
      });
      if (error) throw error;
      toast.success(`Institutional verification recorded for ${skillName}`);
      const { data: skills } = await supabase.from('verified_skills').select('*').eq('user_id', id);
      if (skills) setVSkills(skills);
    } catch (e: any) {
      toast.error(e.message);
    }
  };

  if (loading) return (
    <div className="h-screen flex items-center justify-center bg-background">
      <Loader2 className="w-10 h-10 animate-spin text-lendi-blue" />
    </div>
  );

  if (!profile) return (
    <div className="h-screen flex items-center justify-center bg-background">
      <p className="text-muted-foreground font-black uppercase tracking-widest text-xs">Personnel Record Not Found</p>
    </div>
  );

  const unverifiedInterests = profile.interests?.filter((i: string) =>
    !vSkills.some(s => s.skill_name === i)
  ) || [];

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Personnel Dossier" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar print:p-0 print:bg-white print:text-black">
        <div className="max-w-7xl mx-auto space-y-12">
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">
            {/* Sidebar Profile Card */}
            <div className="lg:col-span-4 space-y-8">
              <motion.div
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                className="inst-card p-10 bg-card shadow-premium relative overflow-hidden"
              >
                <div className="relative z-10 text-center">
                  <div className="w-32 h-32 rounded-[2.5rem] bg-secondary border-4 border-white shadow-premium mx-auto mb-8 flex items-center justify-center relative overflow-hidden">
                    {profile.avatar_url ? (
                      <img src={profile.avatar_url} alt="" className="w-full h-full object-cover" />
                    ) : (
                      <span className="text-4xl font-black text-muted-foreground/30">{profile.full_name?.[0]}</span>
                    )}
                    <div className="absolute inset-0 bg-lendi-blue/5" />
                  </div>

                  <h1 className="text-3xl font-black tracking-tight-inst mb-2">{profile.full_name}</h1>
                  <div className="flex items-center justify-center gap-2 mb-8">
                    <BadgeCheck size={16} className="text-lendi-blue" />
                    <span className="text-[10px] font-black uppercase tracking-[0.2em] text-lendi-blue">{profile.rank}</span>
                  </div>

                  <div className="grid grid-cols-3 gap-4 py-8 border-y border-border mb-8">
                    <div>
                      <p className="text-2xl font-black text-foreground leading-none mb-1">{profile.xp || 0}</p>
                      <p className="text-[8px] font-black uppercase tracking-widest text-muted-foreground/50">Inst. XP</p>
                    </div>
                    <div>
                      <p className="text-2xl font-black text-foreground leading-none mb-1">{missions.length}</p>
                      <p className="text-[8px] font-black uppercase tracking-widest text-muted-foreground/50">Missions</p>
                    </div>
                    <div>
                      <p className="text-2xl font-black text-foreground leading-none mb-1">{vSkills.length}</p>
                      <p className="text-[8px] font-black uppercase tracking-widest text-muted-foreground/50">Verified</p>
                    </div>
                  </div>

                  <div className="space-y-3 no-print">
                    <Button className="w-full h-14 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lendi flex gap-3" onClick={() => window.print()}>
                      <FileText size={18} />
                      Export Resume
                    </Button>
                    <div className="flex gap-2">
                      <Button variant="outline" className="flex-1 h-12 rounded-xl border-border">
                        <Mail size={16} />
                      </Button>
                      <Button variant="outline" className="flex-1 h-12 rounded-xl border-border">
                        <Github size={16} />
                      </Button>
                      <Button variant="outline" className="flex-1 h-12 rounded-xl border-border">
                        <Linkedin size={16} />
                      </Button>
                    </div>
                  </div>
                </div>

                {/* Visual Decoration */}
                <div className="absolute -bottom-10 -right-10 opacity-[0.03] text-lendi-blue pointer-events-none">
                  <Award size={200} />
                </div>
              </motion.div>

              <div className="inst-card p-8 bg-muted/30">
                <h3 className="text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground mb-8 flex items-center gap-2">
                  <ShieldCheck size={14} className="text-lendi-blue" />
                  Verified Expertise
                </h3>
                <div className="space-y-3">
                  {vSkills.length === 0 ? (
                    <p className="text-[10px] font-bold text-muted-foreground/40 italic">No departmental verifications found.</p>
                  ) : vSkills.map(s => (
                    <div key={s.id} className="flex items-center justify-between p-4 rounded-2xl bg-white border border-border group hover:border-lendi-blue/30 transition-all shadow-sm">
                      <div className="flex items-center gap-3">
                        <div className="p-2 rounded-lg bg-lendi-blue/5 text-lendi-blue">
                          <Zap size={16} fill="currentColor" />
                        </div>
                        <span className="text-xs font-black uppercase tracking-widest text-foreground">{s.skill_name}</span>
                      </div>
                      <div className="px-2 py-0.5 rounded-md bg-secondary text-[8px] font-black uppercase text-muted-foreground">DEP</div>
                    </div>
                  ))}
                </div>
              </div>
            </div>

            {/* Main Profile Content */}
            <div className="lg:col-span-8 space-y-12">
              <section>
                <h3 className="text-xs font-black uppercase tracking-[0.3em] text-muted-foreground mb-8 ml-1">Identity & Interests</h3>
                <div className="flex flex-wrap gap-3">
                  {unverifiedInterests.map((i: string) => (
                    <div key={i} className="px-6 py-3 rounded-2xl bg-secondary border border-border text-sm font-bold flex items-center gap-4 group/skill hover:border-lendi-blue/50 transition-all cursor-default">
                      {i}
                      {isMentor && id !== currentUser?.id && (
                        <button
                          onClick={() => verifySkill(i)}
                          className="p-1 rounded-md hover:bg-green-500/10 text-muted-foreground/20 hover:text-green-600 transition-all no-print"
                        >
                          <CheckCircle2 size={16} />
                        </button>
                      )}
                    </div>
                  ))}
                  {profile.interests?.filter((i: string) => vSkills.some(s => s.skill_name === i)).map((i: string) => (
                    <div key={i} className="px-6 py-3 rounded-2xl bg-lendi-blue/5 border border-lendi-blue/20 text-sm font-bold flex items-center gap-3 text-lendi-blue">
                      {i}
                      <ShieldCheck size={18} />
                    </div>
                  ))}
                </div>
              </section>

              <section className="no-print">
                <h3 className="text-xs font-black uppercase tracking-[0.3em] text-muted-foreground mb-8 ml-1">Academic Pulse</h3>
                <div className="inst-card p-8 bg-card shadow-sm">
                  <ActivityHeatmap />
                </div>
              </section>

              <section>
                <h3 className="text-xs font-black uppercase tracking-[0.3em] text-muted-foreground mb-8 ml-1">Evidence of Contribution</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6 pb-12">
                  {missions.length === 0 ? (
                    <div className="col-span-full h-48 rounded-[2rem] border-2 border-dashed border-border flex flex-col items-center justify-center text-center p-10 bg-muted/10">
                      <Rocket size={32} className="text-muted-foreground/20 mb-4" />
                      <p className="text-sm font-bold text-muted-foreground/30 uppercase tracking-widest">No mission logs detected.</p>
                    </div>
                  ) : missions.map(m => (
                    <div key={m.id} className="inst-card p-6 flex items-center justify-between group hover:border-lendi-blue transition-all bg-card" >
                      <div className="flex items-center gap-4">
                        <div className="w-12 h-12 rounded-xl bg-secondary border border-border flex items-center justify-center text-lendi-blue shadow-sm group-hover:scale-110 transition-transform">
                          <Rocket size={20} />
                        </div>
                        <div>
                          <h4 className="font-black text-sm text-foreground tracking-tight">{m.projects?.title}</h4>
                          <p className="text-[10px] font-bold text-muted-foreground/50 uppercase tracking-widest mt-0.5">{m.role || 'Contributor'}</p>
                        </div>
                      </div>
                      <ArrowUpRight size={16} className="text-muted-foreground/20 group-hover:text-lendi-blue group-hover:translate-x-0.5 transition-all" />
                    </div>
                  ))}
                </div>
              </section>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
