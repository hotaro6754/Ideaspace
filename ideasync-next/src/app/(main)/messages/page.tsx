"use client";

import { useEffect, useState, useRef } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import { Send, Hash, Users, MessageSquare, Search, Plus, Shield, Zap, Loader2 } from "lucide-react";
import { Button } from "@/components/ui/Button";

export default function CommsHub() {
  const [channels, setChannels] = useState<any[]>([]);
  const [activeChannel, setActiveChannel] = useState<any>(null);
  const [messages, setMessages] = useState<any[]>([]);
  const [inputText, setInputText] = useState("");
  const [loading, setLoading] = useState(true);
  const scrollRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const fetchChannels = async () => {
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
        .select('*, profiles:sender_id (full_name)')
        .eq('channel_id', activeChannel.id)
        .order('created_at', { ascending: true });
      if (data) setMessages(data);
    };
    fetchMessages();

    // Realtime subscription
    const channel = supabase
      .channel(`chat:${activeChannel.id}`)
      .on('postgres_changes', {
        event: 'INSERT',
        schema: 'public',
        table: 'chat_messages',
        filter: `channel_id=eq.${activeChannel.id}`
      }, (payload) => {
        setMessages(prev => [...prev, payload.new]);
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

    const { data: { user } } = await supabase.auth.getUser();
    if (!user) return;

    const { error } = await supabase.from('chat_messages').insert({
      channel_id: activeChannel.id,
      sender_id: user.id,
      content: inputText
    });

    if (!error) setInputText("");
  };

  if (loading) return <div className="h-screen flex items-center justify-center bg-black"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>;

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black">
      <Header title="Encrypted Comms" />
      <div className="flex-1 flex overflow-hidden">
        <div className="w-80 border-r border-white/5 bg-white/[0.01] flex flex-col">
          <div className="p-6 border-b border-white/5">
            <div className="relative group">
              <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-white/20" />
              <input type="text" placeholder="Find transmission..." className="w-full bg-white/5 border border-white/5 rounded-xl pl-10 pr-4 h-10 text-[10px] font-bold focus:outline-none focus:border-lendi-blue/50" />
            </div>
          </div>
          <div className="flex-1 overflow-y-auto p-4 space-y-8 custom-scrollbar">
            <section>
              <div className="flex items-center justify-between px-2 mb-4">
                <h3 className="text-[10px] font-black uppercase tracking-[0.2em] text-white/20">Sectors</h3>
                <Plus className="w-3 h-3 text-white/20 hover:text-white cursor-pointer" />
              </div>
              <div className="space-y-1">
                {channels.map(channel => (
                  <button key={channel.id} onClick={() => setActiveChannel(channel)} className={`w-full flex items-center gap-3 px-4 py-3 rounded-xl transition-all group ${activeChannel?.id === channel.id ? "bg-lendi-blue/10 text-lendi-blue" : "text-white/30 hover:bg-white/5 hover:text-white"}`} >
                    {channel.type === 'public' ? <Hash className="w-4 h-4 opacity-40" /> : <Shield className="w-4 h-4 opacity-40" />}
                    <span className="text-[11px] font-black uppercase tracking-widest">{channel.name}</span>
                  </button>
                ))}
              </div>
            </section>
          </div>
        </div>
        <div className="flex-1 flex flex-col bg-black/50 backdrop-blur-3xl">
          <div className="h-16 border-b border-white/5 flex items-center justify-between px-8">
            <div className="flex items-center gap-3">
              <Hash className="w-5 h-5 text-white/20" />
              <h2 className="text-sm font-black uppercase tracking-widest">{activeChannel?.name}</h2>
            </div>
          </div>
          <div ref={scrollRef} className="flex-1 overflow-y-auto p-10 space-y-8 custom-scrollbar">
            {messages.map((m) => (
              <div key={m.id} className="flex flex-col items-start">
                <div className="flex items-center gap-3 mb-2">
                  <div className="w-6 h-6 rounded-lg bg-white/5 text-[10px] font-black flex items-center justify-center text-white/20">{m.profiles?.full_name?.[0] || 'U'}</div>
                  <span className="text-[10px] font-black uppercase tracking-widest text-white/40">{m.profiles?.full_name || 'System'}</span>
                </div>
                <div className="max-w-xl p-5 rounded-[2rem] text-sm font-medium leading-relaxed bg-white/5 border border-white/5 text-white/70 rounded-tl-none">{m.content}</div>
              </div>
            ))}
          </div>
          <div className="p-8">
            <div className="glass rounded-[2rem] p-4 flex gap-4 border border-white/10">
              <input type="text" placeholder={`Message #${activeChannel?.name}...`} value={inputText} onChange={(e) => setInputText(e.target.value)} onKeyDown={(e) => e.key === 'Enter' && handleSendMessage()} className="flex-1 bg-transparent border-none focus:outline-none text-sm font-medium placeholder:text-white/10 px-4" />
              <Button onClick={handleSendMessage} disabled={!inputText} className="rounded-2xl w-14 h-14 p-0 shadow-2xl shadow-lendi-blue/20"><Send className="w-5 h-5 mx-auto" /></Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
