"use client";

import { useEffect, useState, useRef } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import { Send, Hash, Users, MessageSquare, Search, Plus, Shield, Zap, Loader2, Paperclip, Smile, AtSign } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

export default function CommsHub() {
  const [channels, setChannels] = useState<any[]>([]);
  const [activeChannel, setActiveChannel] = useState<any>(null);
  const [messages, setMessages] = useState<any[]>([]);
  const [inputText, setInputText] = useState("");
  const [loading, setLoading] = useState(true);
  const [currentUser, setCurrentUser] = useState<any>(null);
  const scrollRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const fetchChannels = async () => {
      const { data: { user } } = await supabase.auth.getUser();
      setCurrentUser(user);

      const { data } = await supabase.from('channels').select('*').order('type', { ascending: false });
      if (data) {
        setChannels(data);
        setActiveChannel(data[0]);
      }
      setLoading(false);
    };
    fetchChannels();
  }, []);

  useEffect(() => {
    if (!activeChannel) return;

    const fetchMessages = async () => {
      const { data } = await supabase
        .from('chat_messages')
        .select('*, profiles:sender_id (full_name, role, points)')
        .eq('channel_id', activeChannel.id)
        .order('created_at', { ascending: true });
      if (data) setMessages(data);
    };
    fetchMessages();

    const channel = supabase
      .channel(`chat:${activeChannel.id}`)
      .on('postgres_changes', {
        event: 'INSERT',
        schema: 'public',
        table: 'chat_messages',
        filter: `channel_id=eq.${activeChannel.id}`
      }, async (payload) => {
        // Fetch profile for the new message
        const { data: profile } = await supabase.from('profiles').select('full_name').eq('id', payload.new.sender_id).single();
        setMessages(prev => [...prev, { ...payload.new, profiles: profile }]);
      })
      .subscribe();

    return () => {
      supabase.removeChannel(channel);
    };
  }, [activeChannel]);

  useEffect(() => {
    if (scrollRef.current) {
      scrollRef.current.scrollTop = scrollRef.current.scrollHeight;
    }
  }, [messages]);

  const handleSendMessage = async () => {
    if (!inputText || !activeChannel) return;

    if (!currentUser) return;

    const { error } = await supabase.from('chat_messages').insert({
      channel_id: activeChannel.id,
      sender_id: currentUser.id,
      content: inputText
    });

    if (!error) setInputText("");
  };

  const simulateFileUpload = () => {
    toast.info("Secure file transmission uplink initializing...");
    setTimeout(() => {
      toast.success("Artifact encrypted and shared in sector.");
    }, 1500);
  };

  if (loading) return <div className="h-screen flex items-center justify-center bg-black"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>;

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Secure Comms Terminal" />
      <div className="flex-1 flex overflow-hidden">
        {/* Sidebar */}
        <div className="w-80 border-r border-white/5 bg-white/[0.01] flex flex-col">
          <div className="p-6 border-b border-white/5">
            <div className="relative group">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white/20" />
              <input type="text" placeholder="Filter transmissions..." className="w-full bg-white/5 border border-white/5 rounded-xl pl-10 pr-4 h-10 text-[10px] font-black uppercase tracking-widest focus:outline-none focus:border-lendi-blue/50" />
            </div>
          </div>
          <div className="flex-1 overflow-y-auto p-4 space-y-8 custom-scrollbar">
            <section>
              <div className="flex items-center justify-between px-2 mb-4">
                <h3 className="text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Active Sectors</h3>
                <Plus className="w-3 h-3 text-white/20 hover:text-white cursor-pointer" />
              </div>
              <div className="space-y-1">
                {channels.map(channel => (
                  <button key={channel.id} onClick={() => setActiveChannel(channel)} className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all group ${activeChannel?.id === channel.id ? "bg-lendi-blue/10 text-lendi-blue shadow-lg shadow-lendi-blue/5" : "text-white/30 hover:bg-white/5 hover:text-white"}`} >
                    {channel.type === 'public' ? <Hash className="w-4 h-4 opacity-40" /> : <Shield className="w-4 h-4 opacity-40 text-lendi-blue" />}
                    <span className="text-[11px] font-black uppercase tracking-widest">{channel.name}</span>
                  </button>
                ))}
              </div>
            </section>
          </div>
          <div className="p-6 border-t border-white/5">
            <div className="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5">
               <div className="w-10 h-10 rounded-xl bg-lendi-blue flex items-center justify-center text-xl font-black">{currentUser?.email?.[0]}</div>
               <div className="flex-1 overflow-hidden">
                 <p className="text-[10px] font-black uppercase tracking-widest truncate">{currentUser?.email}</p>
                 <div className="flex items-center gap-1.5 mt-0.5">
                   <div className="w-1 h-1 rounded-full bg-green-500 animate-pulse" />
                   <span className="text-[8px] font-black uppercase text-green-500 tracking-tighter">Connection Active</span>
                 </div>
               </div>
            </div>
          </div>
        </div>

        {/* Chat Area */}
        <div className="flex-1 flex flex-col bg-black/50 backdrop-blur-3xl relative">
          <div className="absolute inset-0 bg-gradient-to-b from-lendi-blue/5 to-transparent pointer-events-none" />

          <div className="h-16 border-b border-white/5 flex items-center justify-between px-8 relative z-10">
            <div className="flex items-center gap-3">
              <div className="p-2 rounded-lg bg-white/5 border border-white/5">
                {activeChannel?.type === 'public' ? <Hash className="w-4 h-4 text-white/20" /> : <Shield className="w-4 h-4 text-lendi-blue" />}
              </div>
              <div>
                <h2 className="text-sm font-black uppercase tracking-widest">{activeChannel?.name}</h2>
                <p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter mt-0.5">Sector Authorization: Level 1</p>
              </div>
            </div>
            <div className="flex items-center gap-4">
              <div className="flex -space-x-2">
                {[1,2,3].map(i => (
                  <div key={i} className="w-7 h-7 rounded-lg bg-white/10 border-2 border-black flex items-center justify-center text-[10px] font-black">U</div>
                ))}
              </div>
              <div className="h-6 w-px bg-white/10" />
              <Search className="w-4 h-4 text-white/20 hover:text-white cursor-pointer" />
            </div>
          </div>

          <div ref={scrollRef} className="flex-1 overflow-y-auto p-10 space-y-8 custom-scrollbar relative z-10">
            {messages.map((m, i) => {
              const isMe = m.sender_id === currentUser?.id;
              return (
                <motion.div initial={{ opacity: 0, y: 10 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.05 }} key={m.id} className={`flex flex-col ${isMe ? 'items-end' : 'items-start'}`}>
                  <div className="flex items-center gap-3 mb-2">
                    {!isMe && <div className="w-8 h-8 rounded-xl bg-white/5 border border-white/10 text-[10px] font-black flex items-center justify-center text-white/40">{m.profiles?.full_name?.[0] || 'U'}</div>}
                    <div className={isMe ? 'text-right' : 'text-left'}>
                      <span className="text-[10px] font-black uppercase tracking-widest text-white/40">{m.profiles?.full_name || 'System'}</span>
                      <span className="text-[8px] font-bold text-white/10 ml-2 uppercase">{new Date(m.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                    </div>
                  </div>
                  <div className={`max-w-xl p-5 rounded-[2rem] text-sm font-medium leading-relaxed glass border border-white/5 ${isMe ? 'bg-lendi-blue/20 border-lendi-blue/20 text-white rounded-tr-none shadow-xl shadow-lendi-blue/5' : 'bg-white/5 text-white/70 rounded-tl-none'}`}>
                    {m.content}
                  </div>
                </motion.div>
              );
            })}
          </div>

          <div className="p-8 relative z-10">
            <div className="glass rounded-[2.5rem] p-4 flex items-center gap-4 border border-white/10 bg-black/40 backdrop-blur-3xl shadow-2xl">
              <button onClick={simulateFileUpload} className="p-4 rounded-2xl hover:bg-white/5 text-white/20 hover:text-lendi-blue transition-all">
                <Paperclip className="w-5 h-5" />
              </button>
              <div className="h-8 w-px bg-white/5" />
              <input
                type="text"
                placeholder={`Dispatch transmission to #${activeChannel?.name}...`}
                value={inputText}
                onChange={(e) => setInputText(e.target.value)}
                onKeyDown={(e) => e.key === 'Enter' && handleSendMessage()}
                className="flex-1 bg-transparent border-none focus:outline-none text-sm font-medium placeholder:text-white/10 px-4"
              />
              <div className="flex items-center gap-2">
                <button className="p-3 rounded-xl hover:bg-white/5 text-white/20 hover:text-yellow-500 transition-all hidden sm:block">
                  <Smile className="w-5 h-5" />
                </button>
                <button className="p-3 rounded-xl hover:bg-white/5 text-white/20 hover:text-lendi-blue transition-all hidden sm:block">
                  <AtSign className="w-5 h-5" />
                </button>
                <Button onClick={handleSendMessage} disabled={!inputText} className="rounded-2xl w-14 h-14 p-0 bg-lendi-blue hover:bg-lendi-blue/80 shadow-2xl shadow-lendi-blue/20 transition-all flex items-center justify-center">
                  <Send className="w-5 h-5" />
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
