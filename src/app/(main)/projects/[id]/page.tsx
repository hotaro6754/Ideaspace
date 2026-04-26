"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { Header } from "@/components/layout/Header";
import { ProjectDossier } from "@/components/projects/ProjectDossier";
import { ProjectService, Project } from "@/services/ProjectService";
import { ArrowLeft, Loader2, Settings, Share2, GitFork } from "lucide-react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";
import { supabase } from "@/lib/supabase";
import { toast } from "sonner";

export default function ProjectDetailPage() {
  const { id } = useParams();
  const router = useRouter();
  const [project, setProject] = useState<Project | null>(null);
  const [loading, setLoading] = useState(true);
  const [isOwner, setIsOwner] = useState(false);

  useEffect(() => {
    const fetchProject = async () => {
      try {
        const data = await ProjectService.getProjectById(id as string);
        setProject(data);

        const { data: { user } } = await supabase.auth.getUser();
        if (user && data.lead_id === user.id) {
          setIsOwner(true);
        }
      } catch (error) {
        toast.error("Failed to retrieve mission dossier");
      } finally {
        setLoading(false);
      }
    };
    fetchProject();
  }, [id]);

  if (loading) {
    return (
      <div className="h-screen flex items-center justify-center bg-background">
        <div className="flex flex-col items-center gap-6">
          <div className="w-12 h-12 border-4 border-lendi-blue border-t-transparent rounded-full animate-spin" />
          <p className="text-muted-foreground font-black uppercase tracking-[0.3em] text-[10px]">Accessing Secure Dossier...</p>
        </div>
      </div>
    );
  }

  if (!project) {
    return (
      <div className="h-screen flex flex-col items-center justify-center bg-background p-6 text-center">
        <h1 className="text-2xl font-black mb-4 uppercase tracking-widest">Mission Not Found</h1>
        <p className="text-muted-foreground mb-8">The requested mission protocol does not exist or has been archived.</p>
        <Link href="/projects">
          <Button variant="secondary">Return to Registry</Button>
        </Link>
      </div>
    );
  }

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Project Intelligence" />

      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
            <Link href="/projects" className="flex items-center gap-2 text-muted-foreground hover:text-foreground transition-colors group">
              <ArrowLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
              <span className="text-[10px] font-black uppercase tracking-[0.2em]">Back to Mission Registry</span>
            </Link>

            <div className="flex items-center gap-3">
              <Button variant="secondary" size="sm" className="gap-2 rounded-xl text-[10px] uppercase tracking-widest">
                <Share2 size={14} />
                Share
              </Button>
              <Button variant="secondary" size="sm" className="gap-2 rounded-xl text-[10px] uppercase tracking-widest">
                <GitFork size={14} />
                Fork
              </Button>
              {isOwner && (
                <Link href={`/projects/${id}/settings`}>
                  <Button size="sm" className="gap-2 rounded-xl text-[10px] uppercase tracking-widest shadow-sm">
                    <Settings size={14} />
                    Configure
                  </Button>
                </Link>
              )}
            </div>
          </div>

          <ProjectDossier project={project} />
        </div>
      </main>
    </div>
  );
}
