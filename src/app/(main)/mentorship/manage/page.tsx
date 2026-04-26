"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import { Check, X, User, ShieldCheck, Mail, Loader2, Sparkles, MessageSquare, Clock, GraduationCap } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

export default function MentorManagementPage() {
  const [requests, setRequests] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchRequests = async () => {
    setLoading(true);
    const { data: { user } } = await supabase.auth.getUser();
    if (!user) return;

    const { data, error } = await supabase
      .from('mentorship_requests')
      .select(`*, student:student_id (full_name, rank, roll_number, department, avatar_url)`)
      .eq('mentor_id', user.id)
      .order('created_at', { ascending: false });

    if (error) {
      toast.error(error.message);
    } else {
      setRequests(data || []);
    }
    setLoading(false);
  };

  useEffect(() => {
    fetchRequests();
  }, []);

  const handleAction = async (id: string, status: 'accepted' | 'declined') => {
    const { error } = await supabase
      .from('mentorship_requests')
      .update({ status })
      .eq('id', id);

    if (error) {
      toast.error(error.message);
    } else {
      toast.success(`Transmission protocol ${status}`);
      fetchRequests();

      const req = requests.find(r => r.id === id);
      if (status === 'accepted') {
        await supabase.from('notifs').insert({
          user_id: req.student_id,
          title: "Guidance Accepted",
          content: "Your mentorship request has been approved by the alumni mentor.",
          type: 'success'
        });
      }
    }
  };

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Mentor Terminal" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-[1200px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-[10px] font-black text-amber-600 uppercase tracking-widest">
                <GraduationCap size={12} />
                Alumni Channel
              </div>
              <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst">Guidance Requests</h1>
              <p className="text-muted-foreground font-medium max-w-xl text-balance">
                Evaluate student innovation dossiers and establish academic connections for institutional growth.
              </p>
            </div>
          </div>

          {loading ? (
            <div className="h-[400px] flex justify-center items-center">
              <Loader2 className="w-10 h-10 animate-spin text-amber-600 opacity-50" />
            </div>
          ) : (
            <div className="space-y-6 pb-24">
              {requests.length === 0 ? (
                <div className="inst-card p-20 text-center flex flex-col items-center bg-muted/20">
                  <div className="w-16 h-16 rounded-3xl bg-white border border-border flex items-center justify-center text-muted-foreground/30 mb-6">
                    <Sparkles size={32} />
                  </div>
                  <h3 className="text-xl font-black uppercase tracking-widest text-muted-foreground">Channel Inactive</h3>
                  <p className="text-muted-foreground mt-2 font-medium">No incoming student requests detected at this time.</p>
                </div>
              ) : (
                <AnimatePresence mode="popLayout">
                  {requests.map((req, i) => (
                    <motion.div
                      key={req.id}
                      layout
                      initial={{ opacity: 0, y: 20 }}
                      animate={{ opacity: 1, y: 0 }}
                      exit={{ opacity: 0, scale: 0.95 }}
                      transition={{ delay: i * 0.05 }}
                      className="inst-card p-8 md:p-10 flex flex-col md:flex-row md:items-start justify-between gap-10 group"
                    >
                      <div className="flex flex-1 gap-8 items-start">
                        <div className="w-20 h-20 rounded-[2rem] bg-secondary border border-border flex items-center justify-center text-2xl font-black text-muted-foreground/30 relative overflow-hidden shadow-sm shrink-0">
                          {req.student?.avatar_url ? (
                            <img src={req.student.avatar_url} alt="" className="w-full h-full object-cover" />
                          ) : req.student?.full_name?.[0]}
                        </div>
                        <div className="flex-1">
                          <div className="flex items-center gap-4 mb-2">
                            <h4 className="text-2xl font-black tracking-tight text-foreground">{req.student?.full_name}</h4>
                            <span className={`text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-lg ${
                              req.status === 'pending' ? 'bg-amber-500/10 text-amber-600' :
                              req.status === 'accepted' ? 'bg-green-500/10 text-green-600' :
                              'bg-lendi-red/10 text-lendi-red'
                            }`}>
                              {req.status}
                            </span>
                          </div>
                          <div className="flex items-center gap-4 text-[11px] font-bold text-muted-foreground/60 uppercase tracking-widest mb-6">
                            <div className="flex items-center gap-1.5"><Clock size={12} /> {new Date(req.created_at).toLocaleDateString()}</div>
                            <div className="h-3 w-px bg-border" />
                            <div>{req.student?.department}</div>
                            <div className="h-3 w-px bg-border" />
                            <div className="text-lendi-blue">{req.student?.rank}</div>
                          </div>

                          <div className="mb-6">
                            <p className="text-[10px] font-black uppercase tracking-widest text-amber-600 mb-2">Inquiry Focus</p>
                            <p className="text-lg font-bold text-foreground leading-snug">{req.topic}</p>
                          </div>

                          <div className="p-6 rounded-2xl bg-secondary border border-border text-sm text-muted-foreground leading-relaxed italic max-w-2xl relative">
                            <div className="absolute -top-3 left-4 bg-background px-2 text-[10px] font-black uppercase text-muted-foreground/30">Student Context</div>
                            &quot;{req.message || 'No additional context provided.'}&quot;
                          </div>
                        </div>
                      </div>

                      <div className="flex md:flex-col gap-3 min-w-[200px]">
                        {req.status === 'pending' ? (
                          <>
                            <Button
                              onClick={() => handleAction(req.id, 'accepted')}
                              className="h-14 rounded-2xl font-black text-xs uppercase tracking-widest shadow-sm bg-green-600 hover:bg-green-700"
                            >
                              <Check size={18} className="mr-2" />
                              Approve
                            </Button>
                            <Button
                              onClick={() => handleAction(req.id, 'declined')}
                              variant="secondary"
                              className="h-14 rounded-2xl font-black text-xs uppercase tracking-widest border-border"
                            >
                              <X size={18} className="mr-2" />
                              Decline
                            </Button>
                          </>
                        ) : req.status === 'accepted' ? (
                          <Button
                            variant="outline"
                            className="h-14 rounded-2xl font-black text-xs uppercase tracking-widest border-lendi-blue text-lendi-blue hover:bg-lendi-blue/5"
                          >
                            <MessageSquare size={18} className="mr-2" />
                            Open Comms
                          </Button>
                        ) : (
                          <p className="text-center text-[10px] font-black uppercase text-muted-foreground/30 py-4 italic">Protocol Terminated</p>
                        )}
                      </div>
                    </motion.div>
                  ))}
                </AnimatePresence>
              )}
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
