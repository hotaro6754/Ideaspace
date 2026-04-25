"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { Header } from "@/components/layout/Header";
import { ApplyModal } from "@/components/projects/ApplyModal";
import { supabase } from "@/lib/supabase";
import { Star, GitFork, Users, Calendar, Terminal, ArrowLeft, Loader2, Sparkles, ShieldCheck, Settings, Map, Brain } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { AIAgents } from "@/components/projects/AIAgents";
import { QualityGates } from "@/components/projects/QualityGates";
import { HealthWidget } from "@/components/projects/HealthWidget";
import Link from "next/link";
import { toast } from "sonner";

export default function ProjectDetailPage() {
  const { id } = useParams();
  const router = useRouter();
  const [project, setProject] = useState<any>(null);
  const [loading, setLoading] = useState(true);
  const [isApplyModalOpen, setIsApplyModalOpen] = useState(false);
  const [hasApplied, setHasApplied] = useState(false);
  const [isOwner, setIsOwner] = useState(false);
  const [isForking, setIsForking] = useState(false);

  const fetchProject = async () => {
    const { data: { user } } = await supabase.auth.getUser();
    const { data } = await supabase.from('projects').select(`*, profiles:user_id (full_name, rank, roll_number)`).eq('id', id).single();
    if (data) {
      setProject(data);
      if (user) {
        setIsOwner(data.user_id === user.id);
        const { data: app } = await supabase.from('project_applications').select('id').eq('project_id', id).eq('user_id', user.id).maybeSingle();
        if (app) setHasApplied(true);
      }
    }
    setLoading(false);
  };

  useEffect(() => {
    fetchProject();
  }, [id]);

  const handleFork = async () => {
    setIsForking(true);
    const { data: { user } } = await supabase.auth.getUser();
    if (!user) return;

    const { data: forkedProject, error } = await supabase.from('projects').insert({
      user_id: user.id,
      title: `${project.title} (Forked)`,
      description: project.description,
      tech_stack: project.tech_stack,
      roadmap: project.roadmap,
      image_url: project.image_url,
      github_url: project.github_url,
      status: 'in_progress'
    }).select().single();

    if (error) {
      toast.error(error.message);
    } else {
      await supabase.from('projects').update({ forks_count: (project.forks_count || 0) + 1 }).eq('id', project.id);
      toast.success("Mission Forked! Entering personnel sector...");
      router.push(`/projects/${forkedProject.id}`);
    }
    setIsForking(false);
  };

  if (loading) return <div className="h-screen flex items-center justify-center bg-black"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>;
  if (!project) return <div className="h-screen flex flex-col items-center justify-center gap-6 bg-black"><p className="text-white/20 font-black uppercase tracking-[0.3em]">Transmission Lost</p><Link href="/projects"><Button variant="glass">Return to Base</Button></Link></div>;

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Project Dossier" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-[1200px] mx-auto">
          <div className="flex justify-between items-center mb-10">
            <Link href="/projects" className="flex items-center gap-2 text-white/20 hover:text-white transition-colors group"><ArrowLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform" /><span className="text-[10px] font-black uppercase tracking-widest">Back to Projects</span></Link>
            <div className="flex gap-3">
              {!isOwner && (
                <Button
                  onClick={handleFork}
                  disabled={isForking}
                  variant="glass"
                  className="rounded-xl h-10 px-4 flex gap-2 text-[10px] font-black uppercase tracking-widest border-lendi-blue/20 text-lendi-blue"
                >
                  {isForking ? <Loader2 className="w-3 h-3 animate-spin" /> : <GitFork className="w-3 h-3" />}
                  Fork Mission
                </Button>
              )}
              {isOwner && (
                <Link href={`/projects/${id}/settings`}>
                  <Button variant="glass" className="rounded-xl h-10 px-4 flex gap-2 text-[10px] font-black uppercase tracking-widest">
                    <Settings className="w-4 h-4" /> Config
                  </Button>
                </Link>
              )}
            </div>
          </div>
          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-12">
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
              <AIAgents />
              <HealthWidget score={project.status === 'completed' ? 98 : 84} />
            </div>
          </div>

          <div className="mb-12">
            <QualityGates currentStage={project.status === 'completed' ? 'Ship' : 'Build'} />
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <div className="lg:col-span-8 space-y-12">
              <section><div className="flex items-center gap-3 mb-6"><Terminal className="w-5 h-5 text-lendi-blue" /><h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Tech Stack</h2></div><div className="flex flex-wrap gap-3">{project.tech_stack?.map((tech: string) => (<div key={tech} className="px-6 py-3 rounded-2xl bg-white/5 border border-white/5 text-sm font-bold hover:border-lendi-blue/50 transition-colors">{tech}</div>))}</div></section>
              <section><div className="flex items-center gap-3 mb-6"><Map className="w-5 h-5 text-green-500" /><h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Roadmap</h2></div><div className="space-y-4">{project.roadmap && project.roadmap.length > 0 ? project.roadmap.map((milestone: any, i: number) => (<div key={i} className="flex items-center justify-between p-6 rounded-3xl bg-white/5 border border-white/5"><div className="flex items-center gap-4"><div className={`w-2 h-2 rounded-full ${milestone.status === 'completed' ? 'bg-green-500' : 'bg-white/10'}`} /><span className={`font-bold ${milestone.status === 'completed' ? 'text-white' : 'text-white/20'}`}>{milestone.label}</span></div><span className="text-[10px] font-black uppercase tracking-widest opacity-20">{milestone.date}</span></div>)) : <div className="p-10 rounded-3xl border border-dashed border-white/10 text-center text-white/20 italic">No roadmap modules initialized.</div>}</div></section>
            </div>
            <div className="lg:col-span-4 space-y-8">
              <div className="glass rounded-[2.5rem] p-8 border border-white/5 bg-white/[0.02]">
                <h3 className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-8">Repository Stats</h3>
                <div className="space-y-6">
                  <div className="flex items-center justify-between"><div className="flex items-center gap-3"><Star className="w-4 h-4 text-yellow-500 fill-yellow-500" /><span className="text-sm font-bold">Stars</span></div><span className="text-sm font-black font-plus-jakarta">{project.stars_count}</span></div>
                  <div className="flex items-center justify-between"><div className="flex items-center gap-3"><GitFork className="w-4 h-4 text-lendi-blue" /><span className="text-sm font-bold">Forks</span></div><span className="text-sm font-black font-plus-jakarta">{project.forks_count}</span></div>
                </div>
              </div>
              <div className="glass rounded-[2.5rem] p-10 border-2 border-dashed border-white/5 flex flex-col items-center justify-center text-center">
                <div className="w-16 h-16 rounded-full bg-white/5 flex items-center justify-center mb-6"><Users className="w-8 h-8 text-white/20" /></div>
                <h4 className="text-xl font-black mb-2">Join Mission</h4>
                <p className="text-xs text-white/30 font-medium mb-8 leading-relaxed">Active missions looking for expertise.</p>
                <Button onClick={() => setIsApplyModalOpen(true)} disabled={hasApplied} className="w-full rounded-2xl h-14 font-black shadow-2xl shadow-lendi-blue/20">{hasApplied ? "Pending" : "Apply"}</Button>
              </div>
            </div>
          </div>
        </div>
      </main>
      <ApplyModal isOpen={isApplyModalOpen} onClose={() => setIsApplyModalOpen(false)} projectId={project.id} projectTitle={project.title} />
    </div>
  );
}
