"use client";

import { useEffect, useState } from "react";
import { Search, Bell, User, ChevronDown, Loader2, Zap } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { supabase } from "@/lib/supabase";
import { ThemeToggle } from "@/components/ui/ThemeToggle";

interface HeaderProps {
  title: string;
}

export const Header = ({ title }: HeaderProps) => {
  const [showNotifs, setShowNotifs] = useState(false);
  const [notifs, setNotifs] = useState<any[]>([]);
  const [unreadCount, setUnreadCount] = useState(0);

  useEffect(() => {
    const fetchNotifs = async () => {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return;
      const { data } = await supabase.from('notifs').select('*, profiles:actor_id (full_name)').eq('user_id', user.id).order('created_at', { ascending: false }).limit(5);
      if (data) {
        setNotifs(data);
        setUnreadCount(data.filter(n => !n.is_read).length);
      }
    };
    fetchNotifs();

    const channel = supabase
      .channel('header-notifs')
      .on('postgres_changes', { event: 'INSERT', schema: 'public', table: 'notifs' }, () => {
        fetchNotifs();
      })
      .subscribe();

    return () => { supabase.removeChannel(channel); };
  }, []);

  return (
    <header className="h-20 border-b border-white/5 flex items-center justify-between px-10 glass sticky top-0 z-50">
      <div className="flex items-center gap-6">
        <h2 className="text-sm font-black uppercase tracking-[0.3em] text-white/40">{title}</h2>
        <div className="h-4 w-px bg-white/10" />
        <div className="flex items-center gap-2 px-3 py-1 rounded-full bg-green-500/10 border border-green-500/20">
          <div className="w-1 h-1 rounded-full bg-green-500 animate-pulse" />
          <span className="text-[8px] font-black uppercase text-green-400">Node: Lendi-Main</span>
        </div>
      </div>

      <div className="flex items-center gap-8">
        <div className="relative group hidden md:block">
          <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20 group-focus-within:text-lendi-blue transition-colors" />
          <input type="text" placeholder="Search Global Feed..." className="bg-white/5 border border-white/5 rounded-2xl pl-12 pr-6 h-11 w-64 text-xs focus:outline-none focus:border-lendi-blue/50 transition-all" />
        </div>

        <div className="flex items-center gap-4 relative">
          <button
            onClick={() => setShowNotifs(!showNotifs)}
            className="p-2.5 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors relative group"
          >
            <Bell className={`w-4 h-4 transition-colors ${showNotifs ? "text-lendi-blue" : "text-white/40 group-hover:text-white"}`} />
            {unreadCount > 0 && <div className="absolute top-2.5 right-2.5 w-1.5 h-1.5 bg-lendi-blue rounded-full shadow-[0_0_10px_rgba(0,74,153,1)]" />}
          </button>

          <AnimatePresence>
            {showNotifs && (
              <motion.div initial={{ opacity: 0, y: 10, scale: 0.95 }} animate={{ opacity: 1, y: 0, scale: 1 }} exit={{ opacity: 0, y: 10, scale: 0.95 }} className="absolute top-14 right-0 w-80 glass rounded-3xl border border-white/10 shadow-2xl p-6 overflow-hidden z-[100]" >
                <h4 className="text-[10px] font-black uppercase tracking-widest text-white/30 mb-4 ml-1">Live Notifications</h4>
                <div className="space-y-4">
                  {notifs.length === 0 ? (
                    <p className="text-[10px] font-bold text-white/10 italic text-center py-4">No recent activity found.</p>
                  ) : notifs.map(n => (
                    <div key={n.id} className="flex gap-3 p-3 rounded-2xl hover:bg-white/5 transition-colors cursor-pointer group/n">
                      <div className="w-8 h-8 rounded-lg bg-lendi-blue/10 flex items-center justify-center text-lendi-blue group-hover/n:bg-lendi-blue group-hover/n:text-white transition-colors"><Zap className="w-3.5 h-3.5" /></div>
                      <div>
                        <p className="text-[10px] font-bold text-white/80 leading-snug"><span className="text-white font-black">{n.profiles?.full_name}</span> {n.content}</p>
                        <p className="text-[8px] font-black uppercase text-white/20 mt-1">{new Date(n.created_at).toLocaleTimeString()}</p>
                      </div>
                    </div>
                  ))}
                </div>
                <button className="w-full mt-6 pt-4 border-t border-white/5 text-[8px] font-black uppercase tracking-widest text-white/20 hover:text-white transition-colors">Clear Protocol Cache</button>
              </motion.div>
            )}
          </AnimatePresence>

          <div className="flex items-center gap-3 pl-4 border-l border-white/10 group cursor-pointer">
            <ThemeToggle />
            <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-lendi-blue to-purple-600 p-px ml-2">
              <div className="w-full h-full rounded-[11px] bg-black flex items-center justify-center"><User className="w-4 h-4 text-white/40" /></div>
            </div>
            <ChevronDown className="w-3 h-3 text-white/20 group-hover:text-white transition-colors" />
          </div>
        </div>
      </div>
    </header>
  );
};
