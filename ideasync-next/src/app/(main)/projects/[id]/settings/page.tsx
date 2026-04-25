"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { Settings, Save, ArrowLeft, Loader2, Trash2, Shield, Code2, Plus, X, Map } from "lucide-react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";
import { RoadmapEditor } from "@/app/(main)/manage/RoadmapEditor";

export default function ProjectSettings() {
  const { id } = useParams();
  const router = useRouter();
  const [project, setProject] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [title, setTitle] = useState("");
  const [desc, setDesc] = useState("");
  const [tech, setTech] = useState<string[]>([]);
  const [milestones, setMilestones] = useState<any[]>([]);

  useEffect(() => {
    const fetchProject = async () => {
      const { data } = await supabase.from('projects').select('*').eq('id', id).single();
      if (data) {
        setProject(data);
        setTitle(data.title);
        setDesc(data.description);
        setTech(data.tech_stack || []);
        setMilestones(data.roadmap || []);
      }
      setLoading(false);
    };
    fetchProject();
  }, [id]);

  const handleSave = async () => {
    setSaving(true);
    const { error } = await supabase.from('projects').update({
      title,
      description: desc,
      tech_stack: tech,
      roadmap: milestones
    }).eq('id', id);
    setSaving(false);
    if (!error) router.push(`/projects/${id}`);
  };

  const handleTerminate = async () => {
    if (confirm("Are you absolutely sure you want to terminate this mission?")) {
      const { error } = await supabase.from('projects').delete().eq('id', id);
      if (!error) router.push('/projects');
    }
  };

  if (loading) return <div className="h-screen flex items-center justify-center bg-black"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>;

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black">
      <Header title="Mission Configuration" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-4xl mx-auto">
          <Link href={`/projects/${id}`} className="flex items-center gap-2 text-white/20 hover:text-white transition-colors mb-12 group">
            <ArrowLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
            <span className="text-[10px] font-black uppercase tracking-widest">Abort to Dossier</span>
          </Link>

          <div className="grid grid-cols-1 md:grid-cols-12 gap-12">
            <div className="md:col-span-8 space-y-16">
              <section className="space-y-6">
                <div className="flex items-center gap-3 mb-8">
                  <div className="p-2 rounded-xl bg-lendi-blue/10 text-lendi-blue"><Settings className="w-5 h-5" /></div>
                  <h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Core Protocol</h2>
                </div>
                <div className="space-y-4">
                  <div className="space-y-2">
                    <label className="text-[10px] font-black uppercase tracking-widest text-white/20 ml-1">Mission Name</label>
                    <input type="text" value={title} onChange={(e) => setTitle(e.target.value)} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm font-bold focus:border-lendi-blue/50 transition-all" />
                  </div>
                  <div className="space-y-2">
                    <label className="text-[10px] font-black uppercase tracking-widest text-white/20 ml-1">Mission Objective</label>
                    <textarea value={desc} onChange={(e) => setDesc(e.target.value)} className="w-full bg-white/5 border border-white/5 rounded-2xl p-6 min-h-[160px] text-sm font-medium focus:border-lendi-blue/50 transition-all resize-none" />
                  </div>
                </div>
              </section>

              <section className="space-y-6">
                <div className="flex items-center gap-3 mb-8">
                  <div className="p-2 rounded-xl bg-green-500/10 text-green-400"><Map className="w-5 h-5" /></div>
                  <h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Mission Roadmap</h2>
                </div>
                <RoadmapEditor milestones={milestones} onUpdate={setMilestones} />
              </section>

              <section className="space-y-6">
                <div className="flex items-center gap-3 mb-8">
                  <div className="p-2 rounded-xl bg-purple-500/10 text-purple-400"><Code2 className="w-5 h-5" /></div>
                  <h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Tech Stack</h2>
                </div>
                <div className="flex flex-wrap gap-3">
                  {tech.map((t, i) => (
                    <div key={i} className="flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 border border-white/5 text-[10px] font-black">
                      {t} <button onClick={() => setTech(tech.filter(x => x !== t))}><X className="w-3 h-3 text-white/20" /></button>
                    </div>
                  ))}
                  <button onClick={() => {
                    const next = prompt("Enter module name:");
                    if (next) setTech([...tech, next]);
                  }} className="px-4 py-2 rounded-xl border border-dashed border-white/10 text-[10px] font-black text-white/20 hover:border-white/30 transition-colors">
                    Add Module +
                  </button>
                </div>
              </section>
            </div>

            <div className="md:col-span-4">
              <div className="sticky top-10 space-y-6">
                <Button onClick={handleSave} disabled={saving} className="w-full rounded-2xl h-14 font-black flex gap-2 shadow-2xl shadow-lendi-blue/20">
                  {saving ? <Loader2 className="w-4 h-4 animate-spin" /> : <><Save className="w-4 h-4" />Sync Changes</>}
                </Button>
                <div className="p-8 rounded-[2rem] border border-red-900/20 bg-red-900/5 group hover:bg-red-900/10 transition-colors cursor-pointer">
                  <div className="flex items-center gap-3 mb-4 text-red-500">
                    <Trash2 className="w-5 h-5" />
                    <h4 className="text-sm font-black uppercase tracking-widest">Danger Zone</h4>
                  </div>
                  <p className="text-[10px] text-red-500/40 font-medium leading-relaxed mb-6">Termination protocol will permanently remove all mission data from the network.</p>
                  <Button onClick={handleTerminate} variant="glass" className="w-full rounded-xl h-10 text-[10px] font-black uppercase text-red-500 border-red-500/20">Terminate Mission</Button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
    </div>
  );
}
