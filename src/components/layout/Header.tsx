"use client";

import { useEffect, useState } from "react";
import { Search, Bell, User, ChevronDown, Loader2, Zap, Command } from "lucide-react";
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
    <header className="h-20 border-b border-border flex items-center justify-between px-10 bg-card/50 backdrop-blur-xl sticky top-0 z-50">
      <div className="flex items-center gap-6">
        <h2 className="text-xs font-black uppercase tracking-[0.4em] text-muted-foreground">{title}</h2>
        <div className="h-4 w-px bg-border" />
        <div className="flex items-center gap-2 px-3 py-1 rounded-full bg-synk/10 border border-synk/20">
          <div className="w-1.5 h-1.5 rounded-full bg-synk animate-pulse" />
          <span className="text-[10px] font-black uppercase tracking-widest text-synk">Lendi-Main</span>
        </div>
      </div>

      <div className="flex items-center gap-8">
        <div className="relative group hidden lg:block">
          <div className="absolute left-4 top-1/2 -translate-y-1/2 flex items-center gap-2 pointer-events-none">
            <Search className="w-4 h-4 text-muted-foreground/40 group-focus-within:text-lendi transition-colors" />
          </div>
          <input
            type="text"
            placeholder="Search Research Hub..."
            className="bg-secondary/50 border border-border rounded-2xl pl-12 pr-12 h-12 w-80 text-sm font-bold focus:outline-none focus:border-lendi transition-all placeholder:text-muted-foreground/30"
          />
          <div className="absolute right-4 top-1/2 -translate-y-1/2 px-1.5 py-0.5 rounded-md border border-border bg-card text-[10px] font-black text-muted-foreground uppercase tracking-widest flex items-center gap-1">
            <Command size={10} /> K
          </div>
        </div>

        <div className="flex items-center gap-4 relative">
          <button
            onClick={() => setShowNotifs(!showNotifs)}
            className="p-3 rounded-2xl bg-secondary/50 border border-border hover:border-lendi transition-all relative group"
          >
            <Bell className={`w-5 h-5 transition-colors ${showNotifs ? "text-lendi" : "text-muted-foreground/60 group-hover:text-foreground"}`} />
            {unreadCount > 0 && <div className="absolute top-3 right-3 w-2 h-2 bg-lendi rounded-full shadow-[0_0_10px_rgba(0,74,153,1)]" />}
          </button>

          <AnimatePresence>
            {showNotifs && (
              <motion.div
                initial={{ opacity: 0, y: 10, scale: 0.95 }}
                animate={{ opacity: 1, y: 0, scale: 1 }}
                exit={{ opacity: 0, y: 10, scale: 0.95 }}
                className="absolute top-16 right-0 w-96 bg-card/90 backdrop-blur-2xl rounded-[32px] border border-border shadow-[0_30px_80px_-15px_rgba(0,0,0,0.5)] p-8 overflow-hidden z-[100]"
              >
                <div className="flex justify-between items-center mb-6">
                  <h4 className="text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground">Institutional Intel</h4>
                  <span className="text-[10px] font-bold text-lendi cursor-pointer hover:underline">Mark all read</span>
                </div>
                <div className="space-y-6">
                  {notifs.length === 0 ? (
                    <div className="py-10 text-center flex flex-col items-center gap-4">
                       <div className="w-12 h-12 rounded-full bg-secondary flex items-center justify-center text-muted-foreground/20"><Bell size={24} /></div>
                       <p className="text-[11px] font-bold text-muted-foreground uppercase tracking-widest">No active protocols found.</p>
                    </div>
                  ) : notifs.map(n => (
                    <div key={n.id} className="flex gap-4 p-4 rounded-2xl hover:bg-secondary transition-colors cursor-pointer group/n border border-transparent hover:border-border">
                      <div className="w-10 h-10 rounded-xl bg-lendi/10 flex items-center justify-center text-lendi group-hover/n:bg-lendi group-hover/n:text-white transition-colors border border-lendi/20"><Zap className="w-4 h-4" /></div>
                      <div>
                        <p className="text-xs font-bold text-foreground leading-snug mb-1">
                          <span className="font-black">{n.profiles?.full_name}</span> {n.content}
                        </p>
                        <p className="text-[9px] font-black uppercase text-muted-foreground/40">{new Date(n.created_at).toLocaleTimeString()}</p>
                      </div>
                    </div>
                  ))}
                </div>
                <button className="w-full mt-8 py-4 bg-secondary/50 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground hover:text-foreground transition-colors border border-border">System Diagnostic</button>
              </motion.div>
            )}
          </AnimatePresence>

          <div className="flex items-center gap-3 pl-6 border-l border-border group cursor-pointer">
            <ThemeToggle />
            <div className="w-10 h-10 rounded-xl bg-gradient-to-br from-lendi to-synk p-px ml-2">
              <div className="w-full h-full rounded-[11px] bg-card flex items-center justify-center overflow-hidden">
                 <User className="w-5 h-5 text-muted-foreground/40 group-hover:text-foreground transition-colors" />
              </div>
            </div>
            <ChevronDown className="w-3.5 h-3.5 text-muted-foreground/40 group-hover:text-foreground transition-colors" />
          </div>
        </div>
      </div>
    </header>
  );
};
