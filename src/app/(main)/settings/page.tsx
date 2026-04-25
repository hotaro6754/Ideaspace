"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion } from "framer-motion";
import { User, Shield, Bell, Moon, Sun, Globe, Lock, ShieldCheck, Loader2, Save } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

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
    setSaving(true);
    const { error } = await supabase.from('profiles').update({
      full_name: profile.full_name,
      department: profile.department
    }).eq('id', profile.id);

    if (error) {
      toast.error(error.message);
    } else {
      toast.success("Security profile updated.");
    }
    setSaving(false);
  };

  if (loading) return <div className="h-screen flex items-center justify-center bg-black"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>;

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="System Configuration" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-4xl mx-auto">
          <div className="mb-12">
            <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2">Personnel Settings</h1>
            <p className="text-white/40 font-medium">Manage your digital footprint and authorization levels.</p>
          </div>

          <div className="space-y-8 pb-20">
            <section className="glass rounded-[2.5rem] p-10 border border-white/5 relative overflow-hidden">
               <div className="flex items-center gap-4 mb-10">
                 <div className="p-3 rounded-2xl bg-lendi-blue/10 text-lendi-blue"><User className="w-6 h-6" /></div>
                 <h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Identity Matrix</h2>
               </div>

               <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                 <div>
                   <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Full Name</label>
                   <input type="text" value={profile?.full_name} onChange={e => setProfile({...profile, full_name: e.target.value})} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm focus:outline-none focus:border-lendi-blue/50" />
                 </div>
                 <div>
                   <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Roll Number (Read-only)</label>
                   <input type="text" value={profile?.roll_number} disabled className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm opacity-40 cursor-not-allowed" />
                 </div>
                 <div className="md:col-span-2">
                   <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Department</label>
                   <input type="text" value={profile?.department} onChange={e => setProfile({...profile, department: e.target.value})} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm focus:outline-none focus:border-lendi-blue/50" />
                 </div>
               </div>
            </section>

            <section className="glass rounded-[2.5rem] p-10 border border-white/5">
              <div className="flex items-center gap-4 mb-10">
                <div className="p-3 rounded-2xl bg-purple-500/10 text-purple-500"><Shield className="w-6 h-6" /></div>
                <h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Privacy & Visibility</h2>
              </div>
              <div className="space-y-6">
                {[
                  { label: "Public Profile Dossier", desc: "Allow other students and alumni to view your mission history.", active: true },
                  { label: "Verified Skill Endorsements", desc: "Show badges and verified skills on the Talent Board.", active: true },
                  { label: "Campus Search Index", desc: "Include your profile in the Cmd+K omni-search.", active: true }
                ].map((item, i) => (
                  <div key={i} className="flex items-center justify-between p-6 rounded-3xl bg-white/[0.02] border border-white/5">
                    <div>
                      <p className="text-sm font-bold">{item.label}</p>
                      <p className="text-xs text-white/20">{item.desc}</p>
                    </div>
                    <div className="w-12 h-6 rounded-full bg-lendi-blue/20 border border-lendi-blue/50 relative">
                      <div className="absolute top-1 right-1 w-4 h-4 rounded-full bg-lendi-blue shadow-[0_0_10px_rgba(0,74,153,1)]" />
                    </div>
                  </div>
                ))}
              </div>
            </section>

            <div className="flex justify-end gap-4">
              <Button variant="glass" className="h-14 px-10 rounded-2xl text-white/40 hover:text-white">Discard Changes</Button>
              <Button onClick={handleSave} disabled={saving} className="h-14 px-10 rounded-2xl font-black bg-white text-black hover:bg-white/90 shadow-2xl flex gap-2">
                {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : <Save className="w-4 h-4" />}
                Save Protocol
              </Button>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
