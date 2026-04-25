"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import {
  Sparkles,
  Search,
  MessageSquare,
  ArrowUpRight,
  Loader2,
  Award,
  CheckCircle2,
  Clock,
  Send,
  X
} from "lucide-react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";
import { toast } from "sonner";

export default function MentorshipPage() {
  const [mentors, setMentors] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");
  const [selectedMentor, setSelectedMentor] = useState<any>(null);
  const [requestTopic, setRequestTopic] = useState("");
  const [requestMessage, setRequestMessage] = useState("");
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    const fetchMentors = async () => {
      setLoading(true);
      // In a real app, role would be 'alumni'. For now, let's look for anyone with higher points or explicit role.
      const { data } = await supabase
        .from('profiles')
        .select('*')
        .order('points', { ascending: false });

      // Filter for those who might be mentors (points > 500 or explicitly alumni)
      if (data) {
        setMentors(data.filter(p => p.role === 'alumni' || p.points > 500));
      }
      setLoading(false);
    };
    fetchMentors();
  }, []);

  const handleRequest = async () => {
    if (!requestTopic) {
      toast.error("Please specify a topic");
      return;
    }

    setIsSubmitting(true);
    const { data: userData } = await supabase.auth.getUser();

    if (!userData.user) return;

    const { error } = await supabase
      .from('mentorship_requests')
      .insert({
        student_id: userData.user.id,
        mentor_id: selectedMentor.id,
        topic: requestTopic,
        message: requestMessage
      });

    if (error) {
      toast.error(error.message);
    } else {
      toast.success("Guidance request dispatched!");
      setSelectedMentor(null);
      setRequestTopic("");
      setRequestMessage("");
    }
    setIsSubmitting(false);
  };

  const filteredMentors = mentors.filter(m =>
    m.full_name?.toLowerCase().includes(searchQuery.toLowerCase()) ||
    m.interests?.some((i: string) => i.toLowerCase().includes(searchQuery.toLowerCase()))
  );

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Mentorship Hub" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-6">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2 flex items-center gap-4">
                Elite Mentorship
                <div className="px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-[10px] font-black text-orange-500 uppercase tracking-widest">
                  Active Alumni
                </div>
              </h1>
              <p className="text-white/40 font-medium tracking-tight">Connect with industry veterans and LIET alumni to accelerate your innovation journey.</p>
            </div>
            <div className="relative group">
              <Search className="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20 group-focus-within:text-lendi-blue transition-colors" />
              <input
                type="text"
                placeholder="Search by specialty..."
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                className="bg-white/5 border border-white/5 rounded-2xl pl-14 pr-6 h-14 w-80 text-sm focus:outline-none focus:border-lendi-blue/50 transition-all placeholder:text-white/10"
              />
            </div>
          </div>

          {loading ? (
            <div className="flex justify-center py-20"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8 pb-20">
              <AnimatePresence mode="popLayout">
                {filteredMentors.map((mentor, i) => (
                  <motion.div
                    key={mentor.id}
                    layout
                    initial={{ opacity: 0, y: 20 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ delay: i * 0.05 }}
                    className="glass rounded-[2.5rem] p-8 border border-white/5 hover:border-orange-500/30 transition-all group relative overflow-hidden flex flex-col"
                  >
                    <div className="absolute top-0 right-0 w-32 h-32 bg-orange-500/5 blur-3xl rounded-full" />

                    <div className="flex items-start justify-between mb-8 relative z-10">
                      <div className="flex gap-4">
                        <div className="w-16 h-16 rounded-2xl bg-white/5 p-px flex items-center justify-center relative border border-white/10">
                          <div className="w-full h-full rounded-[15px] bg-black flex items-center justify-center text-2xl font-black text-white/40">
                            {mentor.full_name?.[0]}
                          </div>
                          <div className="absolute -top-2 -right-2 bg-orange-500 text-black p-1 rounded-lg shadow-lg">
                            <Award className="w-4 h-4" />
                          </div>
                        </div>
                        <div>
                          <h3 className="text-xl font-black font-plus-jakarta">{mentor.full_name}</h3>
                          <p className="text-[10px] font-black uppercase tracking-widest text-orange-500/60">
                            {mentor.role === 'alumni' ? 'Alumnus' : 'Vanguard'} • {mentor.department}
                          </p>
                        </div>
                      </div>
                    </div>

                    <div className="flex-1 mb-8 relative z-10">
                      <p className="text-xs text-white/40 leading-relaxed line-clamp-3 mb-6 font-medium">
                        Expert in navigating {mentor.interests?.[0]} and enterprise architecture. Previously spearheaded large-scale initiatives at LIET.
                      </p>

                      <div className="flex flex-wrap gap-2">
                        {mentor.interests?.slice(0, 3).map((skill: string) => (
                          <span key={skill} className="px-3 py-1 rounded-lg bg-orange-500/5 border border-orange-500/10 text-[9px] font-black text-orange-500 uppercase">
                            {skill}
                          </span>
                        ))}
                      </div>
                    </div>

                    <div className="flex gap-3 relative z-10">
                      <Button
                        onClick={() => setSelectedMentor(mentor)}
                        className="flex-1 rounded-xl h-12 text-[10px] font-black uppercase tracking-widest bg-orange-500 hover:bg-orange-600 text-black border-none"
                      >
                        Request Guidance
                      </Button>
                      <Link href={`/profile/${mentor.id}`}>
                        <Button variant="glass" className="w-12 h-12 rounded-xl p-0">
                          <ArrowUpRight className="w-4 h-4" />
                        </Button>
                      </Link>
                    </div>
                  </motion.div>
                ))}
              </AnimatePresence>
            </div>
          )}
        </div>
      </main>

      {/* Request Modal */}
      <AnimatePresence>
        {selectedMentor && (
          <div className="fixed inset-0 z-[100] flex items-center justify-center p-6">
            <motion.div
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              exit={{ opacity: 0 }}
              onClick={() => setSelectedMentor(null)}
              className="absolute inset-0 bg-black/80 backdrop-blur-xl"
            />
            <motion.div
              initial={{ opacity: 0, scale: 0.9, y: 20 }}
              animate={{ opacity: 1, scale: 1, y: 0 }}
              exit={{ opacity: 0, scale: 0.9, y: 20 }}
              className="w-full max-w-xl glass rounded-[3rem] p-10 border border-white/10 relative z-10 overflow-hidden"
            >
              <div className="absolute top-0 right-0 w-64 h-64 bg-orange-500/5 blur-[100px] rounded-full" />

              <div className="flex items-center justify-between mb-10">
                <div className="flex items-center gap-4">
                  <div className="w-12 h-12 rounded-xl bg-orange-500/10 flex items-center justify-center">
                    <Sparkles className="w-6 h-6 text-orange-500" />
                  </div>
                  <div>
                    <h2 className="text-2xl font-black font-plus-jakarta tracking-tight">Guidance Request</h2>
                    <p className="text-[10px] font-black uppercase tracking-widest text-white/20">Target: {selectedMentor.full_name}</p>
                  </div>
                </div>
                <button onClick={() => setSelectedMentor(null)} className="w-10 h-10 rounded-xl bg-white/5 flex items-center justify-center hover:bg-white/10 transition-colors">
                  <X className="w-5 h-5" />
                </button>
              </div>

              <div className="space-y-6">
                <div>
                  <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Primary Topic</label>
                  <input
                    type="text"
                    placeholder="e.g. Career in AI Research, System Design..."
                    value={requestTopic}
                    onChange={(e) => setRequestTopic(e.target.value)}
                    className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm focus:outline-none focus:border-orange-500/50 transition-all"
                  />
                </div>

                <div>
                  <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Message / Context</label>
                  <textarea
                    placeholder="Provide brief context on what you hope to achieve..."
                    value={requestMessage}
                    onChange={(e) => setRequestMessage(e.target.value)}
                    rows={4}
                    className="w-full bg-white/5 border border-white/5 rounded-[1.5rem] p-6 text-sm focus:outline-none focus:border-orange-500/50 transition-all resize-none"
                  />
                </div>

                <Button
                  disabled={isSubmitting}
                  onClick={handleRequest}
                  className="w-full h-16 rounded-2xl bg-orange-500 hover:bg-orange-600 text-black text-xs font-black uppercase tracking-[0.2em] shadow-2xl shadow-orange-500/20"
                >
                  {isSubmitting ? <Loader2 className="w-5 h-5 animate-spin" /> : "Dispatch Request"}
                </Button>
              </div>
            </motion.div>
          </div>
        )}
      </AnimatePresence>
    </div>
  );
}
