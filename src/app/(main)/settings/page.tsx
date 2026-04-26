"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion } from "framer-motion";
import { User, Shield, Bell, Loader2, Save, BadgeCheck, Fingerprint, Mail, Building2, Rocket } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

const DEPARTMENTS = ["CSE", "CSM", "CSIT", "ECE", "EEE", "MECH"];

export default function SettingsPage() {
  const [profile, setProfile] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);

  useEffect(() => {
    const fetchProfile = async () => {
      const { data: { user } } = await supabase.auth.getUser();
      if (user) {
        const { data } = await supabase.from('profiles').select('*').eq('id', user.id).single();
        setProfile(data);
      }
      setLoading(false);
    };
    fetchProfile();
  }, []);

  const handleSave = async () => {
    if (!profile) return;
    setSaving(true);
    try {
      const { error } = await supabase.from('profiles').update({
        full_name: profile.full_name,
        department: profile.department,
        bio: profile.bio,
        linkedin_url: profile.linkedin_url,
        github_username: profile.github_username
      }).eq('id', profile.id);

      if (error) throw error;
      toast.success("Institutional profile synchronized");
    } catch (e: any) {
      toast.error(e.message);
    } finally {
      setSaving(false);
    }
  };

  if (loading) return (
    <div className="h-screen flex items-center justify-center bg-background">
      <Loader2 className="w-10 h-10 animate-spin text-lendi-blue" />
    </div>
  );

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Personnel Configuration" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-4xl mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">
                <Shield size={12} />
                Identity Terminal
              </div>
              <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst">Account Settings</h1>
              <p className="text-muted-foreground font-medium max-w-xl">
                Manage your institutional identity, research credentials, and networking authorization levels.
              </p>
            </div>

            <Button onClick={handleSave} disabled={saving} className="h-14 rounded-2xl px-10 font-black uppercase tracking-widest text-xs gap-3 shadow-lendi">
              {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save size={18} />}
              Sync Profile
            </Button>
          </div>

          <div className="space-y-10 pb-24">
            <section className="inst-card p-10 bg-card shadow-sm">
               <div className="flex items-center gap-4 mb-10 border-b border-border pb-6">
                 <div className="w-12 h-12 rounded-xl bg-secondary flex items-center justify-center text-lendi-blue shadow-sm">
                   <User size={24} />
                 </div>
                 <h2 className="text-2xl font-black tracking-tight uppercase">Core Identity Matrix</h2>
               </div>

               <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                 <div className="space-y-2">
                   <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Full Academic Name</label>
                   <input
                    type="text"
                    value={profile?.full_name}
                    onChange={e => setProfile({...profile, full_name: e.target.value})}
                    className="w-full bg-secondary border border-border rounded-2xl px-6 h-14 text-sm font-bold focus:outline-none focus:border-lendi-blue transition-all"
                   />
                 </div>
                 <div className="space-y-2">
                   <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Roll Number (Verified)</label>
                   <div className="relative">
                     <Fingerprint className="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/30" />
                     <input
                      type="text"
                      value={profile?.roll_number}
                      disabled
                      className="w-full bg-muted border border-border rounded-2xl pl-14 pr-6 h-14 text-sm font-bold opacity-60 cursor-not-allowed"
                     />
                   </div>
                 </div>
                 <div className="space-y-2">
                   <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Department Sector</label>
                   <select
                    value={profile?.department}
                    onChange={e => setProfile({...profile, department: e.target.value})}
                    className="w-full bg-secondary border border-border rounded-2xl px-6 h-14 text-sm font-bold focus:outline-none focus:border-lendi-blue appearance-none"
                   >
                     {DEPARTMENTS.map(d => <option key={d} value={d}>{d}</option>)}
                   </select>
                 </div>
                 <div className="space-y-2">
                   <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Institutional Rank</label>
                   <div className="w-full bg-muted border border-border rounded-2xl px-6 h-14 flex items-center gap-3">
                     <BadgeCheck size={18} className="text-lendi-blue" />
                     <span className="text-sm font-black text-foreground uppercase tracking-widest">{profile?.rank}</span>
                   </div>
                 </div>
                 <div className="md:col-span-2 space-y-2">
                   <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Research Bio</label>
                   <textarea
                    value={profile?.bio || ""}
                    onChange={e => setProfile({...profile, bio: e.target.value})}
                    placeholder="Describe your technical focus and academic goals..."
                    className="w-full bg-secondary border border-border rounded-2xl p-6 min-h-[140px] text-sm font-medium focus:outline-none focus:border-lendi-blue transition-all resize-none shadow-sm placeholder:text-muted-foreground/30"
                   />
                 </div>
               </div>
            </section>

            <section className="inst-card p-10 bg-card shadow-sm">
              <div className="flex items-center gap-4 mb-10 border-b border-border pb-6">
                <div className="w-12 h-12 rounded-xl bg-secondary flex items-center justify-center text-purple-600 shadow-sm">
                  <Rocket size={24} />
                </div>
                <h2 className="text-2xl font-black tracking-tight uppercase">Presence & Uplinks</h2>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div className="space-y-2">
                   <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">LinkedIn URL</label>
                   <input
                    type="url"
                    value={profile?.linkedin_url || ""}
                    onChange={e => setProfile({...profile, linkedin_url: e.target.value})}
                    className="w-full bg-secondary border border-border rounded-2xl px-6 h-14 text-sm font-bold focus:outline-none focus:border-lendi-blue transition-all"
                   />
                </div>
                <div className="space-y-2">
                   <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">GitHub Username</label>
                   <input
                    type="text"
                    value={profile?.github_username || ""}
                    onChange={e => setProfile({...profile, github_username: e.target.value})}
                    className="w-full bg-secondary border border-border rounded-2xl px-6 h-14 text-sm font-bold focus:outline-none focus:border-lendi-blue transition-all"
                   />
                </div>
              </div>
            </section>

            <div className="flex justify-end gap-4 pt-6">
              <Button variant="outline" className="h-14 px-10 rounded-2xl text-[10px] font-black uppercase tracking-widest">Discard Changes</Button>
              <Button onClick={handleSave} disabled={saving} className="h-14 px-12 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lendi gap-3">
                {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save size={16} />}
                Confirm Protocol Update
              </Button>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
