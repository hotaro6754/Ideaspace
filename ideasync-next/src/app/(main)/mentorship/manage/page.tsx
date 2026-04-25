"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import { Check, X, User, ShieldCheck, Mail, Loader2, Sparkles, MessageSquare, Clock } from "lucide-react";
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
      .select(`*, student:student_id (full_name, rank, roll_number, department)`)
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
      toast.success(`Request ${status}`);
      fetchRequests();

      const req = requests.find(r => r.id === id);
      if (status === 'accepted') {
        await supabase.from('notifs').insert({
          user_id: req.student_id,
          title: "Mentorship Accepted!",
          content: "Your guidance request has been accepted by the mentor.",
          type: 'success'
        });
      }
    }
  };

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Mentor Terminal" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-5xl mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-8">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2 flex items-center gap-4">
                Guidance Requests
                <div className="px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-[10px] font-black text-orange-500 uppercase tracking-widest">
                  Secure Channel
                </div>
              </h1>
              <p className="text-white/40 font-medium">Manage incoming mentorship requests and evaluate student potential.</p>
            </div>
          </div>

          {loading ? (
            <div className="flex justify-center py-20"><Loader2 className="w-10 h-10 animate-spin text-orange-500" /></div>
          ) : (
            <div className="space-y-6">
              {requests.length === 0 ? (
                <div className="text-center py-20 glass rounded-[3rem] border border-white/5">
                  <Sparkles className="w-12 h-12 text-white/10 mx-auto mb-4" />
                  <p className="text-white/20 font-bold uppercase tracking-widest text-xs">No pending transmissions</p>
                </div>
              ) : (
                <AnimatePresence mode="popLayout">
                  {requests.map((req, i) => (
                    <motion.div
                      key={req.id}
                      layout
                      initial={{ opacity: 0, x: -20 }}
                      animate={{ opacity: 1, x: 0 }}
                      exit={{ opacity: 0, scale: 0.95 }}
                      transition={{ delay: i * 0.05 }}
                      className="glass rounded-[2.5rem] p-8 border border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-8 group"
                    >
                      <div className="flex gap-6 items-start">
                        <div className="w-16 h-16 rounded-2xl bg-white/5 p-px flex items-center justify-center relative border border-white/10 group-hover:border-orange-500/30 transition-all">
                          <div className="w-full h-full rounded-[15px] bg-black flex items-center justify-center text-2xl font-black text-white/40">
                            {req.student?.full_name?.[0]}
                          </div>
                        </div>
                        <div>
                          <div className="flex items-center gap-3 mb-1">
                            <h4 className="text-lg font-black">{req.student?.full_name}</h4>
                            <span className={`text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md ${
                              req.status === 'pending' ? 'bg-orange-500/10 text-orange-500' :
                              req.status === 'accepted' ? 'bg-green-500/10 text-green-500' :
                              'bg-red-500/10 text-red-500'
                            }`}>
                              {req.status}
                            </span>
                          </div>
                          <p className="text-xs text-white/40 font-medium mb-4 flex items-center gap-2">
                            <Clock className="w-3 h-3" />
                            {new Date(req.created_at).toLocaleDateString()} • {req.student?.department}
                          </p>
                          <div className="mb-4">
                            <p className="text-[10px] font-black uppercase tracking-widest text-orange-500 mb-1">Inquiry Topic</p>
                            <p className="text-sm font-bold text-white">{req.topic}</p>
                          </div>
                          <div className="p-5 rounded-2xl bg-white/[0.02] border border-white/5 text-xs text-white/60 leading-relaxed max-w-xl italic">
                            "{req.message || 'No additional message provided.'}"
                          </div>
                        </div>
                      </div>

                      {req.status === 'pending' && (
                        <div className="flex md:flex-col gap-3 min-w-[160px]">
                          <Button
                            onClick={() => handleAction(req.id, 'accepted')}
                            className="bg-orange-500 hover:bg-orange-600 text-black rounded-xl h-12 px-8 flex gap-2 font-black text-[10px] uppercase tracking-widest"
                          >
                            <Check className="w-4 h-4" />
                            Approve
                          </Button>
                          <Button
                            onClick={() => handleAction(req.id, 'declined')}
                            variant="glass"
                            className="rounded-xl h-12 px-8 flex gap-2 font-black text-white/40 hover:text-red-400 text-[10px] uppercase tracking-widest"
                          >
                            <X className="w-4 h-4" />
                            Decline
                          </Button>
                        </div>
                      )}

                      {req.status === 'accepted' && (
                        <Button variant="glass" className="rounded-xl h-12 px-6 flex gap-2 font-black text-orange-500 border-orange-500/20 text-[10px] uppercase tracking-widest">
                          <MessageSquare className="w-4 h-4" />
                          Start Comms
                        </Button>
                      )}
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
