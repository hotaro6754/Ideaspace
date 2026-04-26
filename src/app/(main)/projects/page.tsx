
"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { ProjectCard } from "@/components/ui/ProjectCard";
import { CreateProjectModal } from "@/components/projects/CreateProjectModal";
import { Plus, Search, Filter, Loader2, Sparkles } from "lucide-react";
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
    const { data, error } = await supabase
      .from('projects')
      .select('*')
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
    <div className="flex flex-col h-full overflow-hidden relative">
      <Header title="Collaborative Projects" />

      <main className="flex-1 overflow-y-auto p-6 md:p-10 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">

          <div className="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2">Build Together</h1>
              <p className="text-white/40 font-medium">Join active projects or spawn a new mission in the campus network.</p>
            </div>
            <div className="flex gap-3">
              <div className="relative group">
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20 group-focus-within:text-lendi-blue transition-colors" />
                <input
                  type="text"
                  placeholder="Search projects..."
                  className="bg-white/5 border border-white/5 rounded-2xl pl-12 pr-6 h-12 w-64 text-sm focus:outline-none focus:border-lendi-blue/50 transition-all"
                />
              </div>
              <Button
                onClick={() => setIsModalOpen(true)}
                className="rounded-full px-8 h-12 font-black shadow-lg shadow-lendi-blue/20 flex gap-2"
              >
                <Plus className="w-4 h-4" />
                New Project
              </Button>
            </div>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-20">
            {loading ? (
              <div className="col-span-full h-64 flex flex-col items-center justify-center gap-4">
                <Loader2 className="w-8 h-8 animate-spin text-lendi-blue" />
                <p className="text-white/20 italic font-bold uppercase tracking-widest text-[10px]">Syncing with GitHub layer...</p>
              </div>
            ) : (
              <>
                {projects.map((project) => (
                  <ProjectCard
                    key={project.id}
                    id={project.id}
                    title={project.title}
                    description={project.description}
                    tags={project.tech_stack || []}
                    stars={project.stars_count || 0}
                    members={1}
                    progress={project.status === 'completed' ? 100 : 45}
                  />
                ))}

                <motion.div
                  whileHover={{ scale: 1.02 }}
                  whileTap={{ scale: 0.98 }}
                  onClick={() => setIsModalOpen(true)}
                  className="h-[420px] rounded-[2.5rem] border-2 border-dashed border-white/5 flex flex-col items-center justify-center p-10 text-center hover:border-lendi-blue/20 transition-all group cursor-pointer bg-white/[0.01]"
                >
                  <div className="p-4 rounded-full bg-white/5 mb-4 group-hover:bg-lendi-blue/10 transition-colors">
                    <Plus className="w-8 h-8 text-white/20 group-hover:text-lendi-blue" />
                  </div>
                  <p className="text-white/20 font-bold uppercase tracking-widest text-xs">Spawn New Project</p>
                </motion.div>
              </>
            )}
          </div>
        </div>
      </main>
      <CreateProjectModal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} onSuccess={handleSuccess} />
      <AnimatePresence>
        {showToast && (
          <motion.div initial={{ opacity: 0, y: 50 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: 20 }} className="fixed bottom-10 left-1/2 -translate-x-1/2 glass px-8 py-4 rounded-2xl border border-white/10 z-[200] flex items-center gap-3 shadow-2xl" >
            <div className="p-2 bg-lendi-blue/20 rounded-lg"><Sparkles className="w-4 h-4 text-lendi-blue" /></div>
            <p className="text-sm font-bold tracking-tight">Mission Spawned Successfully</p>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}
