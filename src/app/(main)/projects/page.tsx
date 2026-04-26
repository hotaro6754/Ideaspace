"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { ProjectCard } from "@/components/ui/ProjectCard";
import { CreateProjectModal } from "@/components/projects/CreateProjectModal";
import { Plus, Search, Loader2, Sparkles, LayoutGrid, ListFilter } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";

export default function ProjectsPage() {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [projects, setProjects] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showToast, setShowToast] = useState(false);

  const fetchProjects = async () => {
    setLoading(true);
    const { data } = await supabase
      .from('projects')
      .select('*, profiles:user_id(full_name)')
      .order('created_at', { ascending: false });

    if (data) setProjects(data);
    setLoading(false);
  };

  useEffect(() => {
    fetchProjects();
  }, []);

  const handleSuccess = () => {
    fetchProjects();
    setShowToast(true);
    setTimeout(() => setShowToast(false), 3000);
  };

  return (
    <div className="flex flex-col h-full overflow-hidden relative bg-background">
      <Header title="Innovation Missions" />

      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">
                <LayoutGrid size={12} />
                Mission Registry
              </div>
              <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst">Institutional Projects</h1>
              <p className="text-muted-foreground font-medium max-w-xl text-balance">
                Explore and contribute to active research tracks and development missions within the Lendi innovation ecosystem.
              </p>
            </div>

            <div className="flex items-center gap-4">
              <div className="relative group">
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/40 group-focus-within:text-lendi-blue transition-colors" />
                <input
                  type="text"
                  placeholder="Filter missions..."
                  className="bg-card border border-border rounded-2xl pl-12 pr-6 h-14 w-full md:w-72 text-sm font-bold focus:outline-none focus:border-lendi-blue transition-all shadow-sm"
                />
              </div>
              <Button
                onClick={() => setIsModalOpen(true)}
                className="h-14 rounded-2xl px-8 font-black uppercase tracking-widest text-xs gap-3 shadow-lendi"
              >
                <Plus size={18} />
                Initiate Mission
              </Button>
            </div>
          </div>

          {loading ? (
            <div className="h-[400px] flex flex-col items-center justify-center gap-6">
              <div className="w-12 h-12 border-4 border-lendi-blue border-t-transparent rounded-full animate-spin" />
              <p className="text-muted-foreground font-black uppercase tracking-[0.3em] text-[10px]">Retrieving Mission Data...</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-24">
              {projects.map((project) => (
                <ProjectCard
                  key={project.id}
                  id={project.id}
                  title={project.title}
                  description={project.description}
                  tags={project.tech_stack || []}
                  stars={project.stars_count || 0}
                  members={project.members_count || 1}
                  progress={project.status === 'ship' ? 100 : 45}
                />
              ))}

              <motion.div
                whileHover={{ y: -4 }}
                onClick={() => setIsModalOpen(true)}
                className="h-[440px] rounded-3xl border-2 border-dashed border-border flex flex-col items-center justify-center p-12 text-center hover:border-lendi-blue/30 hover:bg-secondary/50 transition-all group cursor-pointer bg-muted/20"
              >
                <div className="w-16 h-16 rounded-2xl bg-white border border-border flex items-center justify-center mb-6 shadow-sm group-hover:scale-110 group-hover:text-lendi-blue transition-all">
                  <Plus size={32} />
                </div>
                <h4 className="text-lg font-black mb-2 uppercase tracking-tight">Spawn New Track</h4>
                <p className="text-muted-foreground text-xs font-medium max-w-[200px]">Propose a new institutional mission to the community.</p>
              </motion.div>
            </div>
          )}
        </div>
      </main>

      <CreateProjectModal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} onSuccess={handleSuccess} />

      <AnimatePresence>
        {showToast && (
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 20 }}
            className="fixed bottom-12 left-1/2 -translate-x-1/2 bg-white dark:bg-card px-8 py-5 rounded-2xl border border-border shadow-premium z-[200] flex items-center gap-4"
          >
            <div className="w-10 h-10 rounded-xl bg-green-500/10 flex items-center justify-center text-green-600 shadow-sm">
              <Sparkles size={20} />
            </div>
            <p className="text-sm font-black uppercase tracking-widest text-foreground">Mission Successfully Spawned</p>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}
