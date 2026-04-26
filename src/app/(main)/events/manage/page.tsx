"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import { Calendar, Plus, MapPin, Clock, Users, Loader2, Image as ImageIcon, Trash2 } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

export default function EventManagementPage() {
  const [events, setEvents] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [isAdding, setIsAdding] = useState(false);
  const [newEvent, setNewEvent] = useState({
    title: "",
    description: "",
    event_type: "workshop",
    location: "",
    start_time: "",
    end_time: "",
    points_reward: 20
  });

  const fetchData = async () => {
    setLoading(true);
    const { data } = await supabase.from('events').select('*').order('start_time', { ascending: false });
    if (data) setEvents(data);
    setLoading(false);
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleCreate = async () => {
    if (!newEvent.title || !newEvent.start_time) return;
    const { data: { user } } = await supabase.auth.getUser();
    const { error } = await supabase.from('events').insert({ ...newEvent, organizer_id: user?.id });

    if (error) {
      toast.error(error.message);
    } else {
      toast.success("Event Broadcast successfully!");
      setIsAdding(false);
      fetchData();
    }
  };

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Event Ops" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-5xl mx-auto">
          <div className="flex justify-between items-end mb-12">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2">Club Terminal</h1>
              <p className="text-white/40 font-medium">Broadcast workshops, hackathons, and community gatherings.</p>
            </div>
            <Button onClick={() => setIsAdding(true)} className="rounded-2xl h-14 px-8 bg-lendi-blue hover:bg-lendi-blue/80 flex gap-2 font-black text-[10px] uppercase tracking-widest shadow-2xl shadow-lendi-blue/20">
              <Plus className="w-4 h-4" />
              Spawn Event
            </Button>
          </div>

          <AnimatePresence>
            {isAdding && (
              <motion.div initial={{ opacity: 0, y: -20 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: -20 }} className="glass rounded-[3rem] p-10 border border-white/10 mb-12 relative overflow-hidden" >
                <div className="absolute top-0 right-0 w-64 h-64 bg-lendi-blue/5 blur-[100px] rounded-full" />
                <div className="grid grid-cols-1 md:grid-cols-2 gap-8 relative z-10">
                  <div className="space-y-6">
                    <div>
                      <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Event Title</label>
                      <input type="text" placeholder="e.g. Next.js Masterclass" value={newEvent.title} onChange={e => setNewEvent({...newEvent, title: e.target.value})} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm focus:outline-none focus:border-lendi-blue/50" />
                    </div>
                    <div>
                      <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Description</label>
                      <textarea placeholder="Agenda, speakers, prerequisites..." value={newEvent.description} onChange={e => setNewEvent({...newEvent, description: e.target.value})} rows={4} className="w-full bg-white/5 border border-white/5 rounded-[2rem] p-6 text-sm focus:outline-none focus:border-lendi-blue/50 resize-none" />
                    </div>
                  </div>
                  <div className="space-y-6">
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Location</label>
                        <input type="text" placeholder="e.g. Lab 4, Block B" value={newEvent.location} onChange={e => setNewEvent({...newEvent, location: e.target.value})} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm focus:outline-none focus:border-lendi-blue/50" />
                      </div>
                      <div>
                        <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Type</label>
                        <select value={newEvent.event_type} onChange={e => setNewEvent({...newEvent, event_type: e.target.value})} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm focus:outline-none focus:border-lendi-blue/50 appearance-none text-white/60">
                          <option value="workshop">Workshop</option>
                          <option value="hackathon">Hackathon</option>
                          <option value="meetup">Meetup</option>
                        </select>
                      </div>
                    </div>
                    <div className="grid grid-cols-2 gap-4">
                      <div>
                        <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Start Time</label>
                        <input type="datetime-local" value={newEvent.start_time} onChange={e => setNewEvent({...newEvent, start_time: e.target.value})} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm focus:outline-none focus:border-lendi-blue/50" />
                      </div>
                      <div>
                        <label className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-3 block">Reward XP</label>
                        <input type="number" value={newEvent.points_reward} onChange={e => setNewEvent({...newEvent, points_reward: parseInt(e.target.value)})} className="w-full bg-white/5 border border-white/5 rounded-2xl px-6 h-14 text-sm focus:outline-none focus:border-lendi-blue/50" />
                      </div>
                    </div>
                    <div className="flex items-center gap-6 pt-4">
                      <Button onClick={handleCreate} className="flex-1 h-16 rounded-2xl font-black bg-white text-black hover:bg-white/90">Broadcast Event</Button>
                      <Button onClick={() => setIsAdding(false)} variant="glass" className="h-16 rounded-2xl px-10 text-white/20 hover:text-white">Cancel</Button>
                    </div>
                  </div>
                </div>
              </motion.div>
            )}
          </AnimatePresence>

          {loading ? (
            <div className="flex justify-center py-20"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>
          ) : (
            <div className="grid grid-cols-1 gap-6">
              {events.map((e) => (
                <div key={e.id} className="glass rounded-[2.5rem] p-8 border border-white/5 flex items-center justify-between group">
                  <div className="flex gap-6 items-center">
                    <div className="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/20">
                      <Calendar className="w-6 h-6" />
                    </div>
                    <div>
                      <h3 className="text-xl font-black font-plus-jakarta mb-1 group-hover:text-lendi-blue transition-colors">{e.title}</h3>
                      <div className="flex items-center gap-4">
                        <p className="text-[10px] font-black uppercase tracking-widest text-white/20 flex items-center gap-2"><MapPin className="w-3 h-3" />{e.location}</p>
                        <p className="text-[10px] font-black uppercase tracking-widest text-white/20 flex items-center gap-2"><Clock className="w-3 h-3" />{new Date(e.start_time).toLocaleString()}</p>
                      </div>
                    </div>
                  </div>
                  <div className="flex items-center gap-6">
                    <div className="text-right">
                      <p className="text-xl font-black font-plus-jakarta text-green-400">+{e.points_reward}</p>
                      <p className="text-[8px] font-bold text-white/20 uppercase tracking-widest">Attendance XP</p>
                    </div>
                    <Button variant="glass" className="w-12 h-12 rounded-xl p-0 hover:bg-red-500/20 text-red-500/40 hover:text-red-500 border-white/5 hover:border-red-500/20"><Trash2 className="w-5 h-5" /></Button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
