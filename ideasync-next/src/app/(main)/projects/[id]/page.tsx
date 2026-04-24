"use client";

import { useEffect, useState } from "react";
import { useParams } from "next/navigation";
import { Header } from "@/components/layout/Header";
import { ApplyModal } from "@/components/projects/ApplyModal";
import { supabase } from "@/lib/supabase";
import { Star, GitFork, Users, Calendar, Terminal, ArrowLeft, Loader2, Sparkles, ShieldCheck } from "lucide-react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";

export default function ProjectDetailPage() {
  const { id } = useParams();
  const [project, setProject] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [isApplyModalOpen, setIsApplyModalOpen] = useState(false);
  const [hasApplied, setHasApplied] = useState(false);

  useEffect(() => {
    const fetchProject = async () => {
      const { data: { user } } = await supabase.auth.getUser();
      const { data } = await supabase.from('projects').select(`*, profiles:user_id (full_name, rank, roll_number)`).eq('id', id).single();
      if (data) {
        setProject(data);
        if (user) {
          const { data: app } = await supabase.from('project_applications').select('id').eq('project_id', id).eq('user_id', user.id).maybeSingle();
          if (app) setHasApplied(true);
        }
      }
      setLoading(false);
    };
    fetchProject();
  }, [id]);

  if (loading) return <div className="h-screen flex items-center justify-center bg-black"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>;
  if (!project) return <div className="h-screen flex flex-col items-center justify-center gap-6 bg-black"><p className="text-white/20 font-black uppercase tracking-[0.3em]">Transmission Lost</p><Link href="/projects"><Button variant="glass">Return to Base</Button></Link></div>;

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Project Dossier" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-[1200px] mx-auto">
          <Link href="/projects" className="flex items-center gap-2 text-white/20 hover:text-white transition-colors mb-10 group"><ArrowLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform" /><span className="text-[10px] font-black uppercase tracking-widest">Back to Projects</span></Link>
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-20">
            <div className="lg:col-span-8">
              <div className="flex items-center gap-3 mb-6">
                <div className={`px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest ${project.status === 'completed' ? "bg-green-500/20 text-green-400" : "bg-lendi-blue/20 text-lendi-blue"}`}>{project.status.replace('_', ' ')}</div>
                <div className="flex items-center gap-2 px-3 py-1 rounded-full bg-white/5 border border-white/5 text-[10px] font-black uppercase tracking-widest text-white/40"><Calendar className="w-3 h-3" />Launched {new Date(project.created_at).toLocaleDateString()}</div>
              </div>
              <h1 className="text-6xl font-black font-plus-jakarta tracking-tightest mb-8 leading-[0.9]">{project.title}</h1>
              <p className="text-xl text-white/50 leading-relaxed font-medium mb-10">{project.description}</p>
              <div className="flex flex-wrap gap-4">
                {project.github_url && <a href={project.github_url} target="_blank" rel="noopener noreferrer"><Button variant="glass" className="rounded-2xl px-6 h-12 flex gap-2 font-black uppercase tracking-widest text-xs">Source</Button></a>}
                {project.live_url && <a href={project.live_url} target="_blank" rel="noopener noreferrer"><Button className="rounded-2xl px-6 h-12 flex gap-2 font-black uppercase tracking-widest text-xs shadow-xl shadow-lendi-blue/20">Live Demo</Button></a>}
              </div>
            </div>
            <div className="lg:col-span-4 space-y-8">
              <div className="glass rounded-[2.5rem] p-8 border border-white/5 bg-white/[0.02]">
                <h3 className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-8">Repository Stats</h3>
                <div className="space-y-6">
                  <div className="flex items-center justify-between"><div className="flex items-center gap-3"><Star className="w-4 h-4 text-yellow-500 fill-yellow-500" /><span className="text-sm font-bold">Stars</span></div><span className="text-sm font-black font-plus-jakarta">{project.stars_count}</span></div>
                  <div className="flex items-center justify-between"><div className="flex items-center gap-3"><GitFork className="w-4 h-4 text-lendi-blue" /><span className="text-sm font-bold">Forks</span></div><span className="text-sm font-black font-plus-jakarta">{project.forks_count}</span></div>
                  <div className="flex items-center justify-between"><div className="flex items-center gap-3"><Users className="w-4 h-4 text-purple-500" /><span className="text-sm font-bold">Contributors</span></div><span className="text-sm font-black font-plus-jakarta">1</span></div>
                </div>
              </div>
              <div className="glass rounded-[2.5rem] p-8 border border-white/5 bg-gradient-to-br from-lendi-blue/5 to-transparent">
                <h3 className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-8">Mission Lead</h3>
                <div className="flex items-center gap-4 mb-6">
                  <div className="w-12 h-12 rounded-2xl bg-lendi-blue flex items-center justify-center text-xl font-black">{project.profiles?.full_name?.[0]}</div>
                  <div><p className="text-sm font-black">{project.profiles?.full_name}</p><div className="flex items-center gap-1.5 mt-0.5"><ShieldCheck className="w-3 h-3 text-lendi-blue" /><span className="text-[10px] font-black uppercase tracking-widest text-lendi-blue">{project.profiles?.rank}</span></div></div>
                </div>
                <Button variant="glass" className="w-full rounded-xl h-10 text-[10px] font-black uppercase tracking-widest">Connect</Button>
              </div>
            </div>
          </div>
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div className="lg:col-span-8 space-y-12">
              <section><div className="flex items-center gap-3 mb-6"><Terminal className="w-5 h-5 text-lendi-blue" /><h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Tech Stack</h2></div><div className="flex flex-wrap gap-3">{project.tech_stack?.map((tech: string) => (<div key={tech} className="px-6 py-3 rounded-2xl bg-white/5 border border-white/5 text-sm font-bold hover:border-lendi-blue/50 transition-colors">{tech}</div>))}</div></section>
              <section><div className="flex items-center gap-3 mb-6"><Sparkles className="w-5 h-5 text-yellow-500" /><h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Roadmap</h2></div><div className="space-y-4">{[{ label: "V1.0 MVP Launch", status: "completed", date: "Jan 12" }, { label: "Beta Testing", status: "completed", date: "Feb 05" }, { label: "Public Release", status: "pending", date: "Mar 20" }].map((milestone) => (<div key={milestone.label} className="flex items-center justify-between p-6 rounded-3xl bg-white/5 border border-white/5"><div className="flex items-center gap-4"><div className={`w-2 h-2 rounded-full ${milestone.status === 'completed' ? 'bg-green-500' : 'bg-white/10'}`} /><span className={`font-bold ${milestone.status === 'completed' ? 'text-white' : 'text-white/20'}`}>{milestone.label}</span></div><span className="text-[10px] font-black uppercase tracking-widest opacity-20">{milestone.date}</span></div>))}</div></section>
            </div>
            <div className="lg:col-span-4"><div className="sticky top-10"><div className="glass rounded-[2.5rem] p-10 border-2 border-dashed border-white/5 flex flex-col items-center justify-center text-center"><div className="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center mb-6"><Users className="w-8 h-8 text-white/20" /></div><h4 className="text-xl font-black mb-2">Join Mission</h4><p className="text-xs text-white/30 font-medium mb-8 leading-relaxed">Active missions looking for expertise.</p><Button onClick={() => setIsApplyModalOpen(true)} disabled={hasApplied} className="w-full rounded-2xl h-14 font-black shadow-2xl shadow-lendi-blue/20">{hasApplied ? "Pending" : "Apply"}</Button></div></div></div>
          </div>
        </div>
      </main>
      <ApplyModal isOpen={isApplyModalOpen} onClose={() => setIsApplyModalOpen(false)} projectId={project.id} projectTitle={project.title} />
    </div>
  );
}
