"use client";

import { useEffect, useState } from "react";
import { useParams } from "next/navigation";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import { Calendar, MapPin, Clock, Users, Loader2, Image as ImageIcon, ArrowLeft, Camera, Sparkles } from "lucide-react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";
import { toast } from "sonner";

export default function EventDetailPage() {
  const { id } = useParams();
  const [event, setEvent] = useState<any>(null);
  const [media, setMedia] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchEvent = async () => {
      const { data } = await supabase.from('events').select(`*, profiles:organizer_id (full_name)`).eq('id', id).single();
      const { data: mediaData } = await supabase.from('event_media').select('*').eq('event_id', id);
      if (data) setEvent(data);
      if (mediaData) setMedia(mediaData);
      setLoading(false);
    };
    fetchEvent();
  }, [id]);

  if (loading) return <div className="h-screen flex items-center justify-center bg-black"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>;
  if (!event) return <div className="h-screen flex items-center justify-center bg-black">Event not found.</div>;

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Event Intelligence" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-6xl mx-auto">
          <Link href="/events" className="flex items-center gap-2 text-white/20 hover:text-white transition-colors mb-10 group">
            <ArrowLeft className="w-4 h-4 group-hover:-translate-x-1 transition-transform" />
            <span className="text-[10px] font-black uppercase tracking-widest">Back to Calendar</span>
          </Link>

          <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 mb-20">
            <div className="lg:col-span-8">
              <h1 className="text-6xl font-black font-plus-jakarta tracking-tightest mb-8 leading-[0.9]">{event.title}</h1>
              <div className="flex flex-wrap items-center gap-6 mb-12">
                <div className="flex items-center gap-3 px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-white/60"><MapPin className="w-4 h-4 text-lendi-blue" />{event.location}</div>
                <div className="flex items-center gap-3 px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-white/60"><Clock className="w-4 h-4 text-lendi-blue" />{new Date(event.start_time).toLocaleString()}</div>
                <div className="flex items-center gap-3 px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-xs font-bold text-green-400"><Sparkles className="w-4 h-4" />+{event.points_reward} XP</div>
              </div>
              <p className="text-xl text-white/40 leading-relaxed font-medium mb-12">{event.description}</p>
              <Button className="rounded-2xl h-14 px-12 font-black bg-white text-black hover:bg-white/90 shadow-2xl">Secure My Spot</Button>
            </div>
            <div className="lg:col-span-4">
              <div className="glass rounded-[3rem] p-10 border border-white/10 bg-white/[0.02]">
                <h3 className="text-[10px] font-black uppercase tracking-widest text-white/20 mb-8">Organizer</h3>
                <div className="flex items-center gap-4 mb-8">
                  <div className="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-xl font-black">{event.profiles?.full_name?.[0]}</div>
                  <div><p className="text-sm font-black">{event.profiles?.full_name}</p><p className="text-[10px] font-bold text-white/20 uppercase tracking-widest">Club Lead</p></div>
                </div>
                <Button variant="glass" className="w-full rounded-xl h-12 text-[10px] font-black uppercase tracking-widest border-white/10 hover:border-lendi-blue/30 transition-all">Contact Organizer</Button>
              </div>
            </div>
          </div>

          <section>
            <div className="flex items-center justify-between mb-10">
              <div className="flex items-center gap-4">
                <div className="w-10 h-10 rounded-xl bg-lendi-blue/10 flex items-center justify-center text-lendi-blue"><Camera className="w-5 h-5" /></div>
                <h2 className="text-3xl font-black font-plus-jakarta tracking-tight">Mission Retrospective</h2>
              </div>
              <Button variant="glass" className="rounded-xl h-10 px-6 text-[8px] font-black uppercase tracking-widest border-white/5 text-white/40 hover:text-white">Upload Media</Button>
            </div>
            {media.length === 0 ? (
              <div className="h-64 rounded-[3rem] border-2 border-dashed border-white/5 flex flex-col items-center justify-center text-center p-10">
                <ImageIcon className="w-10 h-10 text-white/10 mb-4" />
                <p className="text-sm font-bold text-white/20 uppercase tracking-widest tracking-[0.2em]">Gallery initialization pending...</p>
              </div>
            ) : (
              <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                {media.map((m, i) => (
                  <motion.div key={m.id} initial={{ opacity: 0, scale: 0.9 }} animate={{ opacity: 1, scale: 1 }} transition={{ delay: i * 0.1 }} className="aspect-square rounded-3xl overflow-hidden glass border border-white/5 relative group cursor-pointer" >
                    <img src={m.media_url} alt={m.caption} className="w-full h-full object-cover opacity-50 group-hover:opacity-80 transition-opacity" />
                    <div className="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                      <p className="text-[8px] font-black uppercase tracking-widest text-white">{m.caption}</p>
                    </div>
                  </motion.div>
                ))}
              </div>
            )}
          </section>
        </div>
      </main>
    </div>
  );
}
