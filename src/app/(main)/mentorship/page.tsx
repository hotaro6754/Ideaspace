"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import {
  Users,
  Search,
  ChevronRight,
  Star,
  ShieldCheck,
  Briefcase,
  MessageSquare,
  Loader2,
  Sparkles,
  X,
  Target
} from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

export default function MentorshipHub() {
  const [mentors, setMentors] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [selectedMentor, setSelectedMentor] = useState<any>(null);
  const [requestTopic, setRequestTopic] = useState("");
  const [requestMessage, setRequestMessage] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    const fetchMentors = async () => {
      const { data } = await supabase
        .from('profiles')
        .select('*')
        .eq('role', 'alumni')
        .order('full_name');
      if (data) setMentors(data);
      setLoading(false);
    };
    fetchMentors();
  }, []);

  const handleRequest = async () => {
    if (!requestTopic || !requestMessage) return;
    setIsSubmitting(true);
    const { data: { user } } = await supabase.auth.getUser();
    if (!user) return;

    const { error } = await supabase.from('mentorship_requests').insert({
      mentor_id: selectedMentor.id,
      student_id: user.id,
      topic: requestTopic,
      message: requestMessage,
      status: 'pending'
    });

    if (error) {
      toast.error(error.message);
    } else {
      toast.success("Guidance request successfully transmitted");
      setSelectedMentor(null);
      setRequestTopic("");
      setRequestMessage("");
    }
    setIsSubmitting(false);
  };

  const filteredMentors = mentors.filter(m =>
    m.full_name?.toLowerCase().includes(search.toLowerCase()) ||
    m.department?.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Mentorship Network" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 border border-amber-500/20 text-[10px] font-black text-amber-600 uppercase tracking-widest">
                <Users size={12} />
                Alumni Directory
              </div>
              <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst">Find Your Mentor</h1>
              <p className="text-muted-foreground font-medium max-w-xl text-balance">
                Connect with Lendi alumni across global tech sectors for personalized institutional guidance and career pathing.
              </p>
            </div>

            <div className="relative group">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/40 group-focus-within:text-lendi-blue transition-colors" />
              <input
                type="text"
                placeholder="Search mentors by tech or industry..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
                className="bg-card border border-border rounded-2xl pl-12 pr-6 h-14 w-full md:w-80 text-sm font-bold focus:outline-none focus:border-lendi-blue transition-all shadow-sm"
              />
            </div>
          </div>

          {loading ? (
            <div className="h-[400px] flex flex-col items-center justify-center gap-6">
              <div className="w-12 h-12 border-4 border-lendi-blue border-t-transparent rounded-full animate-spin" />
              <p className="text-muted-foreground font-black uppercase tracking-[0.3em] text-[10px]">Syncing Alumni Network...</p>
            </div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 pb-24">
              {filteredMentors.map((mentor, i) => (
                <motion.div
                  key={mentor.id}
                  initial={{ opacity: 0, scale: 0.95 }}
                  whileInView={{ opacity: 1, scale: 1 }}
                  viewport={{ once: true }}
                  transition={{ delay: i * 0.05 }}
                  className="inst-card p-10 bg-card shadow-sm hover:border-lendi-blue transition-all group relative overflow-hidden"
                >
                  <div className="flex flex-col items-center text-center space-y-6 relative z-10">
                    <div className="w-24 h-24 rounded-3xl bg-secondary border-4 border-white shadow-premium flex items-center justify-center text-3xl font-black text-muted-foreground/30 relative overflow-hidden group-hover:rotate-3 transition-transform duration-500">
                      {mentor.avatar_url ? (
                        <img src={mentor.avatar_url} alt="" className="w-full h-full object-cover" />
                      ) : mentor.full_name?.[0]}
                      <div className="absolute inset-0 bg-lendi-blue/5 opacity-0 group-hover:opacity-100 transition-opacity" />
                    </div>

                    <div>
                      <h3 className="text-xl font-black tracking-tight mb-1 group-hover:text-lendi-blue transition-colors">{mentor.full_name}</h3>
                      <p className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground/60">{mentor.department} • Class of {mentor.year}</p>
                    </div>

                    <div className="flex gap-4 w-full pt-2">
                       <div className="flex-1 p-3 rounded-2xl bg-secondary border border-border">
                         <p className="text-[9px] font-black text-muted-foreground uppercase tracking-widest mb-1">Mentees</p>
                         <p className="text-sm font-bold text-foreground">12 Active</p>
                       </div>
                       <div className="flex-1 p-3 rounded-2xl bg-secondary border border-border">
                         <p className="text-[9px] font-black text-muted-foreground uppercase tracking-widest mb-1">Score</p>
                         <div className="flex items-center justify-center gap-1">
                           <Star size={12} className="text-amber-500 fill-amber-500" />
                           <span className="text-sm font-bold text-foreground">4.9</span>
                         </div>
                       </div>
                    </div>

                    <p className="text-xs text-muted-foreground font-medium leading-relaxed italic line-clamp-2">
                      &quot;{mentor.bio || "Institutional alumni dedicated to student innovation and career excellence at Lendi."}&quot;
                    </p>

                    <Button
                      onClick={() => setSelectedMentor(mentor)}
                      className="w-full h-12 rounded-xl text-[10px] font-black uppercase tracking-widest gap-2 shadow-sm"
                    >
                      Request Guidance
                      <ChevronRight size={14} />
                    </Button>
                  </div>

                  {/* Decorative background logo */}
                  <div className="absolute -bottom-6 -right-6 opacity-5 text-lendi-blue pointer-events-none group-hover:scale-110 transition-transform duration-700">
                    <ShieldCheck size={120} />
                  </div>
                </motion.div>
              ))}
            </div>
          )}
        </div>
      </main>

      <AnimatePresence>
        {selectedMentor && (
          <div className="fixed inset-0 z-[200] flex items-center justify-center p-6">
            <motion.div initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }} onClick={() => setSelectedMentor(null)} className="absolute inset-0 bg-black/60 backdrop-blur-md" />
            <motion.div
              initial={{ opacity: 0, scale: 0.95, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.95, y: 20 }}
              className="w-full max-w-xl inst-card p-10 bg-card z-[210] relative"
            >
              <div className="flex items-center justify-between mb-10 pb-6 border-b border-border">
                <div className="flex items-center gap-4 text-left">
                  <div className="w-14 h-14 rounded-2xl bg-secondary flex items-center justify-center text-xl font-black text-lendi-blue shadow-sm border border-border">
                    {selectedMentor.full_name?.[0]}
                  </div>
                  <div>
                    <h2 className="text-2xl font-black tracking-tight uppercase leading-tight">Request Protocol</h2>
                    <p className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mt-1.5">Target: {selectedMentor.full_name}</p>
                  </div>
                </div>
                <button onClick={() => setSelectedMentor(null)} className="p-2 hover:bg-secondary rounded-xl transition-colors">
                  <X size={24} />
                </button>
              </div>

              <div className="space-y-8">
                <div className="space-y-3">
                  <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Inquiry Domain</label>
                  <div className="relative">
                    <Target className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/40" />
                    <input
                      type="text"
                      placeholder="e.g. Distributed Systems, AI Ethics, MBA Path..."
                      value={requestTopic}
                      onChange={(e) => setRequestTopic(e.target.value)}
                      className="w-full bg-secondary border border-border rounded-xl pl-12 pr-6 h-14 text-sm font-bold focus:outline-none focus:border-lendi-blue transition-all shadow-sm"
                    />
                  </div>
                </div>

                <div className="space-y-3">
                  <label className="text-[10px] font-black uppercase tracking-widest text-muted-foreground ml-1">Guidance Context</label>
                  <textarea
                    placeholder="Provide a brief overview of your innovation track and the specific guidance you require..."
                    value={requestMessage}
                    onChange={(e) => setRequestMessage(e.target.value)}
                    className="w-full bg-secondary border border-border rounded-2xl p-6 min-h-[140px] text-sm font-medium focus:outline-none focus:border-lendi-blue transition-all resize-none shadow-sm placeholder:text-muted-foreground/30"
                  />
                </div>

                <Button
                  disabled={isSubmitting || !requestTopic || !requestMessage}
                  onClick={handleRequest}
                  className="w-full h-16 rounded-[2rem] font-black uppercase tracking-widest text-xs shadow-lendi mt-4 gap-3 bg-amber-600 hover:bg-amber-700"
                >
                  {isSubmitting ? <Loader2 className="w-5 h-5 animate-spin" /> : (
                    <>
                      Initialize Connection
                      <Sparkles size={16} />
                    </>
                  )}
                </Button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
}
