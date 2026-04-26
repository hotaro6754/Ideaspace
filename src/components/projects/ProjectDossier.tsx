"use client";

import { Project } from "@/services/ProjectService";
import { QualityGates } from "./QualityGates";
import { motion } from "framer-motion";
import { Github, ExternalLink, Users, Calendar, Tag, ShieldCheck, Printer, FileText } from "lucide-react";
import { Button } from "@/components/ui/Button";

interface ProjectDossierProps {
  project: Project;
}

export const ProjectDossier = ({ project }: ProjectDossierProps) => {
  const handlePrint = () => {
    window.print();
  };

  return (
    <div className="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {/* Left Column: Mission Content */}
        <div className="lg:col-span-8 space-y-8">
          <div className="inst-card p-10 bg-card/50 backdrop-blur-xl">
            <div className="flex items-center justify-between mb-8">
              <div className="flex items-center gap-3">
                <div className="px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">
                  ID: {project.id.slice(0, 8)}
                </div>
                <div className="px-3 py-1 rounded-full bg-secondary border border-border text-[10px] font-black text-muted-foreground uppercase tracking-widest">
                  Mission Layer: {project.status}
                </div>
              </div>
              <Button variant="ghost" size="sm" onClick={handlePrint} className="no-print gap-2 text-[10px] uppercase font-black">
                <Printer size={14} />
                Export Resume
              </Button>
            </div>

            <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst mb-6">
              {project.title}
            </h1>

            <p className="text-lg text-muted-foreground font-medium leading-relaxed mb-10 text-balance">
              {project.description}
            </p>

            <div className="flex flex-wrap gap-3 mb-10">
              {project.tech_stack.map((tech) => (
                <div key={tech} className="flex items-center gap-2 px-4 py-2 rounded-xl bg-secondary border border-border text-xs font-bold">
                  <Tag size={14} className="text-lendi-blue" />
                  {tech}
                </div>
              ))}
            </div>

            <div className="flex gap-4 no-print">
              {project.repo_url && (
                <Button variant="secondary" className="gap-2" onClick={() => window.open(project.repo_url, '_blank')}>
                  <Github size={18} />
                  Repository
                </Button>
              )}
              {project.demo_url && (
                <Button variant="primary" className="gap-2" onClick={() => window.open(project.demo_url, '_blank')}>
                  <ExternalLink size={18} />
                  Live Demo
                </Button>
              )}
            </div>
          </div>

          <div className="no-print">
            <QualityGates currentStage={project.status.charAt(0).toUpperCase() + project.status.slice(1)} />
          </div>
        </div>

        {/* Right Column: Intel & Team */}
        <div className="lg:col-span-4 space-y-8 no-print">
          <div className="inst-card p-8 bg-muted/30">
            <h3 className="text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground mb-6">Mission Intelligence</h3>

            <div className="space-y-6">
              <div className="flex items-center gap-4">
                <div className="w-10 h-10 rounded-xl bg-white border border-border flex items-center justify-center text-lendi-blue shadow-sm">
                  <ShieldCheck size={20} />
                </div>
                <div>
                  <p className="text-[10px] font-black uppercase text-muted-foreground tracking-widest">Project Lead</p>
                  <p className="text-sm font-bold">{project.profiles?.full_name || 'Anonymous'}</p>
                </div>
              </div>

              <div className="flex items-center gap-4">
                <div className="w-10 h-10 rounded-xl bg-white border border-border flex items-center justify-center text-lendi-blue shadow-sm">
                  <Calendar size={20} />
                </div>
                <div>
                  <p className="text-[10px] font-black uppercase text-muted-foreground tracking-widest">Inception Date</p>
                  <p className="text-sm font-bold">{new Date(project.created_at).toLocaleDateString()}</p>
                </div>
              </div>

              <div className="flex items-center gap-4">
                <div className="w-10 h-10 rounded-xl bg-white border border-border flex items-center justify-center text-lendi-blue shadow-sm">
                  <Users size={20} />
                </div>
                <div>
                  <p className="text-[10px] font-black uppercase text-muted-foreground tracking-widest">Collaborators</p>
                  <p className="text-sm font-bold">4 Active Members</p>
                </div>
              </div>
            </div>

            <div className="mt-10 pt-8 border-t border-border">
              <Button className="w-full h-14 rounded-2xl font-black uppercase tracking-widest text-xs shadow-lendi">
                Apply to Mission
              </Button>
              <p className="text-center text-[9px] font-bold text-muted-foreground mt-4 uppercase tracking-tighter">
                Institutional Credentials Required
              </p>
            </div>
          </div>

          <div className="inst-card p-8 bg-gradient-to-br from-lendi-blue to-lendi-dark text-white shadow-lendi">
            <div className="flex items-center gap-3 mb-4">
              <FileText size={20} />
              <h4 className="text-lg font-black uppercase tracking-tight">Quality Gate Health</h4>
            </div>
            <p className="text-xs font-medium text-white/70 mb-8">Current performance metrics based on ZeroSlop institutional guidelines.</p>

            <div className="space-y-6">
              <div>
                <div className="flex justify-between text-[10px] font-black uppercase tracking-widest mb-2.5">
                  <span>Architecture</span>
                  <span>92%</span>
                </div>
                <div className="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                  <motion.div initial={{ width: 0 }} animate={{ width: '92%' }} className="h-full bg-white shadow-[0_0_10px_white]" />
                </div>
              </div>
              <div>
                <div className="flex justify-between text-[10px] font-black uppercase tracking-widest mb-2.5">
                  <span>Security</span>
                  <span>88%</span>
                </div>
                <div className="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                  <motion.div initial={{ width: 0 }} animate={{ width: '88%' }} className="h-full bg-white shadow-[0_0_10px_white]" />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};
