"use client";

import { useEffect, useRef, useState } from "react";
import { Header } from "@/components/layout/Header";
import { FeedCard } from "@/components/dashboard/FeedCard";
import { TrendingWidget } from "@/components/dashboard/TrendingWidget";
import { StatsWidget } from "@/components/dashboard/StatsWidget";
import { CreatePostModal } from "@/components/dashboard/CreatePostModal";
import { useInfiniteFeed } from "@/hooks/useInfiniteFeed";
import { motion, AnimatePresence } from "framer-motion";
import { Loader2, Plus, Sparkles, Activity } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { SplineScene } from "@/components/ui/SplineScene";

export default function DashboardPage() {
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [showToast, setShowToast] = useState(false);

  const {
    data,
    fetchNextPage,
    hasNextPage,
    isFetchingNextPage,
    status,
    refetch
  } = useInfiniteFeed();

  const loadMoreRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    const observer = new IntersectionObserver(
      (entries) => {
        if (entries[0].isIntersecting && hasNextPage && !isFetchingNextPage) {
          fetchNextPage();
        }
      },
      { threshold: 1.0 }
    );
    if (loadMoreRef.current) observer.observe(loadMoreRef.current);
    return () => observer.disconnect();
  }, [hasNextPage, isFetchingNextPage, fetchNextPage]);

  const handlePostSuccess = () => {
    refetch();
    setShowToast(true);
    setTimeout(() => setShowToast(false), 3000);
  };

  return (
    <div className="flex flex-col h-full overflow-hidden relative">
      <Header title="Lendi Innovation Feed" />

      <main className="flex-1 overflow-y-auto p-6 md:p-10 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">

          <div className="flex items-center justify-between mb-10">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2">Good morning, Innovator</h1>
              <p className="text-white/40 font-medium">Here's what's happening across the campus today.</p>
            </div>
            <Button onClick={() => setIsModalOpen(true)} className="rounded-full px-8 h-14 font-black shadow-2xl shadow-lendi-blue/20 flex gap-2 group transition-all hover:scale-105 active:scale-95"><Plus className="w-5 h-5 group-hover:rotate-90 transition-transform" />Submit Idea</Button>
          </div>

          <div className="grid grid-cols-1 xl:grid-cols-12 gap-8">
            <div className="xl:col-span-8 space-y-6">
              <div className="h-[400px] rounded-[3rem] overflow-hidden border border-white/5 relative group bg-black/40 mb-10">
                <div className="absolute inset-0 z-0">
                  <SplineScene />
                </div>
                <div className="absolute inset-0 p-12 flex flex-col justify-end z-10 pointer-events-none">
                   <div className="max-w-md pointer-events-auto">
                      <div className="px-3 py-1 rounded-full bg-lendi-blue/20 border border-lendi-blue/40 text-[8px] font-black text-lendi-blue uppercase tracking-widest mb-4 w-fit">Featured Prototype</div>
                      <h2 className="text-4xl font-black font-plus-jakarta mb-4 leading-tight">Quantum Mesh Network V1.2</h2>
                      <p className="text-sm text-white/60 mb-8 font-medium leading-relaxed">The Lendi IoT sector is testing a new decentralized routing protocol across Block A. Phase 1 active.</p>
                      <Button variant="glass" className="rounded-xl h-10 px-6 font-black uppercase text-[10px] tracking-widest border-white/10 hover:border-lendi-blue/30 transition-all">Inspect Node</Button>
                   </div>
                </div>
                <div className="absolute top-8 right-8 z-20">
                   <div className="flex items-center gap-3 px-4 py-2 rounded-xl bg-black/60 backdrop-blur-xl border border-white/10">
                      <div className="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse" />
                      <span className="text-[10px] font-black uppercase tracking-widest text-white/60">Live Uplink</span>
                   </div>
                </div>
              </div>

              <div className="glass rounded-[2.5rem] p-8 border border-white/5 bg-white/[0.01] mb-10 relative overflow-hidden group">
                <div className="flex items-center justify-between mb-8">
                  <div className="flex items-center gap-3">
                    <Activity className="w-5 h-5 text-lendi-blue" />
                    <h3 className="font-black text-xs uppercase tracking-widest text-white/40">Contribution Pulse</h3>
                  </div>
                  <span className="text-[10px] font-black text-green-400 bg-green-500/10 px-3 py-1 rounded-full uppercase">84% Activity Load</span>
                </div>
                <div className="flex gap-2">
                  {Array.from({ length: 24 }).map((_, i) => (
                    <div key={i} className="flex-1 h-12 rounded-lg bg-white/5 relative group/bar overflow-hidden">
                      <motion.div initial={{ height: 0 }} animate={{ height: `${Math.random() * 80 + 20}%` }} transition={{ delay: i * 0.05, duration: 1 }} className="absolute bottom-0 left-0 right-0 bg-lendi-blue/20 border-t border-lendi-blue/40 group-hover/bar:bg-lendi-blue transition-colors" />
                    </div>
                  ))}
                </div>
                <p className="text-[8px] font-black uppercase text-center mt-6 tracking-[0.4em] opacity-10">Real-time Node Activity Stream</p>
              </div>

              {status === "pending" ? (
                <div className="h-64 flex flex-col items-center justify-center gap-4 border-2 border-dashed border-white/5 rounded-[2.5rem]"><Loader2 className="w-8 h-8 animate-spin text-lendi-blue" /><p className="text-white/20 italic font-medium">Synthesizing data streams...</p></div>
              ) : (
                <>
                  {data?.pages.map((page, i) => (
                    <div key={i} className="space-y-6">{page.map((item, j) => (<FeedCard key={item.id} item={item} index={j} />))}</div>
                  ))}
                  <div ref={loadMoreRef} className="py-10 flex justify-center">
                    {isFetchingNextPage && <div className="flex items-center gap-3"><Loader2 className="w-5 h-5 animate-spin text-lendi-blue" /><span className="text-xs font-black uppercase tracking-widest text-white/20">Loading next batch...</span></div>}
                  </div>
                </>
              )}
            </div>

            <div className="xl:col-span-4 space-y-8">
              <div className="sticky top-0 space-y-8">
                <StatsWidget />
                <TrendingWidget />
                <div className="p-8 rounded-[2rem] bg-gradient-to-br from-lendi-blue to-purple-600 relative overflow-hidden group shadow-2xl shadow-lendi-blue/20">
                  <div className="absolute top-0 right-0 p-10 opacity-10 group-hover:scale-110 transition-transform"><Plus className="w-24 h-24 text-white rotate-45" /></div>
                  <h4 className="text-2xl font-black mb-2 relative z-10">Hackathon V3.0</h4>
                  <p className="text-white/80 text-sm mb-6 relative z-10 leading-relaxed font-medium">The biggest coding event at Lendi is back. 48 hours of pure creation.</p>
                  <Button className="w-full bg-white text-lendi-blue hover:bg-white/90 font-black rounded-2xl relative z-10 transition-all active:scale-95 shadow-xl">Register Now</Button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>
      <CreatePostModal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} onSuccess={handlePostSuccess} />
      <AnimatePresence>
        {showToast && (
          <motion.div initial={{ opacity: 0, y: 50 }} animate={{ opacity: 1, y: 0 }} exit={{ opacity: 0, y: 20 }} className="fixed bottom-10 left-1/2 -translate-x-1/2 glass px-8 py-4 rounded-2xl border border-white/10 z-[200] flex items-center gap-3 shadow-2xl" >
            <div className="p-2 bg-green-500/20 rounded-lg"><Sparkles className="w-4 h-4 text-green-400" /></div>
            <p className="text-sm font-bold tracking-tight">Transmission Broadcasted</p>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}
