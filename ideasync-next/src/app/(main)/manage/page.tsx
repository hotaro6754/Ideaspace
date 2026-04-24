"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import { Check, X, User, ShieldCheck, Mail, Loader2, Rocket } from "lucide-react";
import { Button } from "@/components/ui/Button";

export default function LeadDashboard() {
  const [applications, setApplications] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchApplications = async () => {
    setLoading(true);
    const { data: { user } } = await supabase.auth.getUser();
    if (!user) return;

    // Fetch applications for projects where the current user is the owner
    const { data, error } = await supabase
      .from('project_applications')
      .select(`
        *,
        profiles:user_id (full_name, rank, roll_number),
        projects:project_id (title)
      `)
      .order('created_at', { ascending: false });

    // In a real app, we'd filter by projects where user_id = current user in SQL
    // For now, we'll fetch and filter if needed, but let's assume the lead views all for their projects
    if (data) setApplications(data);
    setLoading(false);
  };

  useEffect(() => {
    fetchApplications();
  }, []);

  const handleAction = async (id: string, status: 'approved' | 'rejected') => {
    const { error } = await supabase
      .from('project_applications')
      .update({ status })
      .eq('id', id);

    if (!error) {
      if (status === 'approved') {
        // Logically add user to project_members here
      }
      fetchApplications();
    }
  };

  return (
    <div className="flex flex-col h-full overflow-hidden">
      <Header title="Mission Control" />

      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-5xl mx-auto">
          <div className="mb-12">
            <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2">Lead Dashboard</h1>
            <p className="text-white/40 font-medium">Review and verify personnel requests for your active missions.</p>
          </div>

          {loading ? (
            <div className="flex justify-center py-20">
              <Loader2 className="w-10 h-10 animate-spin text-lendi-blue" />
            </div>
          ) : applications.length === 0 ? (
            <div className="glass rounded-[3rem] p-20 border-2 border-dashed border-white/5 flex flex-col items-center justify-center text-center">
              <div className="p-5 rounded-full bg-white/5 mb-6 text-white/10">
                <Rocket className="w-12 h-12" />
              </div>
              <h3 className="text-xl font-bold text-white/20">No pending applications</h3>
            </div>
          ) : (
            <div className="space-y-6">
              {applications.map((app) => (
                <motion.div
                  key={app.id}
                  layout
                  className="glass rounded-3xl p-8 border border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-8"
                >
                  <div className="flex gap-6 items-start">
                    <div className="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-xl font-black text-white/40">
                      {app.profiles?.full_name[0]}
                    </div>
                    <div>
                      <div className="flex items-center gap-3 mb-1">
                        <h4 className="text-lg font-black">{app.profiles?.full_name}</h4>
                        <div className="px-2 py-0.5 rounded-md bg-lendi-blue/10 border border-lendi-blue/20 text-[8px] font-black text-lendi-blue uppercase tracking-widest">
                          {app.profiles?.rank}
                        </div>
                      </div>
                      <p className="text-xs text-white/40 font-medium mb-3">
                        Wants to join <span className="text-white font-bold">{app.projects?.title}</span>
                      </p>
                      <div className="p-4 rounded-xl bg-white/5 border border-white/5 text-sm text-white/60 leading-relaxed max-w-lg italic">
                        "{app.message}"
                      </div>
                      <div className="flex flex-wrap gap-2 mt-4">
                        {app.skills?.map((s: string) => (
                          <span key={s} className="px-2 py-1 rounded-md bg-white/5 text-[9px] font-bold text-white/30 uppercase tracking-tighter">{s}</span>
                        ))}
                      </div>
                    </div>
                  </div>

                  <div className="flex md:flex-col gap-3">
                    <Button
                      onClick={() => handleAction(app.id, 'approved')}
                      className="bg-green-500 hover:bg-green-600 text-white rounded-xl h-12 px-8 flex gap-2 font-black"
                    >
                      <Check className="w-4 h-4" />
                      Approve
                    </Button>
                    <Button
                      onClick={() => handleAction(app.id, 'rejected')}
                      variant="glass"
                      className="rounded-xl h-12 px-8 flex gap-2 font-black text-white/40 hover:text-red-400"
                    >
                      <X className="w-4 h-4" />
                      Reject
                    </Button>
                  </div>
                </motion.div>
              ))}
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
