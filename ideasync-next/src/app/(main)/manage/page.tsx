"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion } from "framer-motion";
import { Check, X, Loader2 } from "lucide-react";
import { Button } from "@/components/ui/Button";

interface AppProfile {
  full_name: string;
  rank: string;
  roll_number: string;
}

interface AppProject {
  title: string;
}

interface Application {
  id: string;
  user_id: string;
  project_id: string;
  message: string;
  created_at: string;
  status: string;
  profiles: AppProfile;
  projects: AppProject;
}

interface MemberProfile {
  full_name: string;
  rank: string;
  department: string;
}

interface Member {
  id: string;
  user_id: string;
  project_id: string;
  role: string;
  joined_at: string;
  profiles: MemberProfile;
  projects: AppProject;
}

export default function LeadDashboard() {
  const [applications, setApplications] = useState<Application[]>([]);
  const [members, setMembers] = useState<Member[]>([]);
  const [loading, setLoading] = useState(true);
  const [tab, setTab] = useState<"apps" | "personnel">("apps");

  const fetchData = async () => {
    setLoading(true);
    const { data: { user } } = await supabase.auth.getUser();
    if (!user) return;

    const { data: apps } = await supabase
      .from('project_applications')
      .select('*, profiles:user_id (full_name, rank, roll_number), projects:project_id (title)')
      .order('created_at', { ascending: false });

    const { data: mems } = await supabase
      .from('project_members')
      .select('*, profiles:user_id (full_name, rank, department), projects:project_id (title)')
      .order('joined_at', { ascending: false });

    if (apps) setApplications(apps as unknown as Application[]);
    if (mems) setMembers(mems as unknown as Member[]);
    setLoading(false);
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleAction = async (id: string, status: 'approved' | 'rejected', userId?: string, projectId?: string) => {
    const { error } = await supabase.from('project_applications').update({ status }).eq('id', id);
    if (!error && status === 'approved' && userId && projectId) {
      await supabase.from('project_members').insert({ project_id: projectId, user_id: userId, role: 'contributor' });
    }
    fetchData();
  };

  const removeMember = async (id: string) => {
    if (confirm("Remove this member from the mission?")) {
      await supabase.from('project_members').delete().eq('id', id);
      fetchData();
    }
  };

  return (
    <div className="flex flex-col h-full overflow-hidden">
      <Header title="Mission Control" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-5xl mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-8">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2">Management Terminal</h1>
              <p className="text-white/40 font-medium">Verify credentials and manage personnel for your active sectors.</p>
            </div>
            <div className="flex bg-white/5 p-1 rounded-2xl border border-white/5">
              <button onClick={() => setTab("apps")} className={`px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all ${tab === "apps" ? "bg-lendi-blue text-white shadow-lg" : "text-white/30 hover:text-white"}`}>Requests</button>
              <button onClick={() => setTab("personnel")} className={`px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all ${tab === "personnel" ? "bg-lendi-blue text-white shadow-lg" : "text-white/30 hover:text-white"}`}>Personnel</button>
            </div>
          </div>

          {loading ? (
            <div className="flex justify-center py-20"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>
          ) : tab === "apps" ? (
            <div className="space-y-6">
              {applications.length === 0 ? <p className="text-center text-white/10 italic py-20">No active transmissions.</p> : applications.map((app) => (
                <motion.div key={app.id} layout className="glass rounded-3xl p-8 border border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-8">
                  <div className="flex gap-6 items-start">
                    <div className="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-xl font-black text-white/40">{app.profiles?.full_name?.[0]}</div>
                    <div>
                      <div className="flex items-center gap-3 mb-1"><h4 className="text-lg font-black">{app.profiles?.full_name}</h4><div className="px-2 py-0.5 rounded-md bg-lendi-blue/10 border border-lendi-blue/20 text-[8px] font-black text-lendi-blue uppercase tracking-widest">{app.profiles?.rank}</div></div>
                      <p className="text-xs text-white/40 font-medium mb-3">Targeting <span className="text-white font-bold">{app.projects?.title}</span></p>
                      <div className="p-4 rounded-xl bg-white/5 border border-white/5 text-sm text-white/60 leading-relaxed max-w-lg italic">&quot;{app.message}&quot;</div>
                    </div>
                  </div>
                  <div className="flex md:flex-col gap-3">
                    <Button onClick={() => handleAction(app.id, 'approved', app.user_id, app.project_id)} className="bg-green-500 hover:bg-green-600 text-white rounded-xl h-12 px-8 flex gap-2 font-black"><Check className="w-4 h-4" />Approve</Button>
                    <Button onClick={() => handleAction(app.id, 'rejected')} variant="glass" className="rounded-xl h-12 px-8 flex gap-2 font-black text-white/40 hover:text-red-400"><X className="w-4 h-4" />Reject</Button>
                  </div>
                </motion.div>
              ))}
            </div>
          ) : (
            <div className="space-y-4">
              {members.length === 0 ? <p className="text-center text-white/10 italic py-20">Personnel records empty.</p> : members.map((m) => (
                <div key={m.id} className="glass rounded-2xl p-6 border border-white/5 flex items-center justify-between group" >
                  <div className="flex items-center gap-6">
                    <div className="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center text-sm font-black text-white/20">{m.profiles?.full_name?.[0]}</div>
                    <div>
                      <h4 className="font-black">{m.profiles?.full_name}</h4>
                      <p className="text-[10px] font-black uppercase text-white/20">{m.profiles?.department} • {m.projects?.title}</p>
                    </div>
                  </div>
                  <div className="flex items-center gap-6">
                    <div className="px-3 py-1 rounded-lg bg-white/5 border border-white/5 text-[8px] font-black uppercase text-white/40 tracking-widest">{m.role}</div>
                    <button onClick={() => removeMember(m.id)} className="text-white/0 group-hover:text-red-500 transition-all"><X className="w-4 h-4" /></button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
