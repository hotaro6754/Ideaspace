"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion } from "framer-motion";
import { Calendar, MapPin, Clock, Users, ArrowRight, Loader2, Sparkles, Plus } from "lucide-react";
import { Button } from "@/components/ui/Button";

export default function EventsPage() {
  const [events, setEvents] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchEvents = async () => {
      const { data } = await supabase.from('events').select('*').order('start_time', { ascending: true });
      if (data) setEvents(data);
      setLoading(false);
    };
    fetchEvents();
  }, []);

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black">
      <Header title="Campus Timeline" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div className="space-y-4">
              <div className="flex items-center gap-3">
                <div className="px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-[0.2em]">IIC Official</div>
                <div className="w-1.5 h-1.5 rounded-full bg-white/20" />
                <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.2em]">Innovation Hub</p>
              </div>
              <h1 className="text-6xl font-black font-plus-jakarta tracking-tightest leading-[0.9]">Experience<br/>Tomorrow.</h1>
              <p className="text-white/40 font-medium max-w-lg">Join high-impact workshops, hackathons, and seminars designed to accelerate your innovation journey.</p>
            </div>
            <Button className="rounded-2xl h-14 px-10 font-black shadow-2xl shadow-lendi-blue/20 flex gap-2"><Plus className="w-5 h-5" />Host Event</Button>
          </div>

          {loading ? (
            <div className="flex justify-center py-20"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>
          ) : events.length === 0 ? (
            <div className="glass rounded-[3rem] p-20 border-2 border-dashed border-white/5 flex flex-col items-center justify-center text-center">
              <div className="p-5 rounded-full bg-white/5 mb-6 text-white/10"><Calendar className="w-12 h-12" /></div>
              <h3 className="text-xl font-bold text-white/20 italic">No scheduled transmissions found.</h3>
            </div>
          ) : (
            <div className="grid grid-cols-1 xl:grid-cols-2 gap-8">
              {events.map((event, i) => (
                <motion.div key={event.id} initial={{ opacity: 0, scale: 0.98 }} animate={{ opacity: 1, scale: 1 }} transition={{ delay: i * 0.1 }} className="glass rounded-[2.5rem] p-1 border border-white/5 group hover:border-white/10 transition-all overflow-hidden" >
                  <div className="bg-white/[0.02] rounded-[2.2rem] p-8">
                    <div className="flex justify-between items-start mb-10">
                      <div className="flex items-center gap-4">
                        <div className="w-16 h-16 rounded-2xl bg-white/5 flex flex-col items-center justify-center border border-white/5">
                          <p className="text-[10px] font-black uppercase text-white/20 leading-none mb-1">{new Date(event.start_time).toLocaleString('default', { month: 'short' })}</p>
                          <p className="text-2xl font-black font-plus-jakarta leading-none">{new Date(event.start_time).getDate()}</p>
                        </div>
                        <div>
                          <h3 className="text-2xl font-black tracking-tight mb-1 group-hover:text-lendi-blue transition-colors">{event.title}</h3>
                          <div className="flex items-center gap-4 text-[10px] font-black uppercase tracking-widest text-white/20">
                            <span className="flex items-center gap-1.5"><MapPin className="w-3.5 h-3.5" />{event.location}</span>
                            <span className="flex items-center gap-1.5"><Clock className="w-3.5 h-3.5" />{new Date(event.start_time).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                          </div>
                        </div>
                      </div>
                      <div className="px-4 py-2 rounded-xl bg-lendi-blue/10 border border-lendi-blue/20">
                        <p className="text-[10px] font-black text-lendi-blue uppercase">+{event.points_reward} XP</p>
                      </div>
                    </div>
                    <p className="text-white/40 text-sm leading-relaxed mb-10 font-medium line-clamp-2">{event.description}</p>
                    <div className="flex items-center justify-between pt-8 border-t border-white/5">
                      <div className="flex items-center gap-2"><div className="w-2 h-2 rounded-full bg-green-500 animate-pulse" /><span className="text-[10px] font-black uppercase tracking-widest text-white/40">Registration Open</span></div>
                      <Button variant="glass" className="rounded-xl px-8 h-12 flex gap-2 group/btn font-black text-xs uppercase tracking-widest">Secure Seat <ArrowRight className="w-4 h-4 group-hover/btn:translate-x-1 transition-transform" /></Button>
                    </div>
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
