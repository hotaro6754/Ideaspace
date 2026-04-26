"use client";

import { useEffect, useRef, useState } from "react";
import { Header } from "@/components/layout/Header";
import { FeedCard } from "@/components/dashboard/FeedCard";
import { TrendingWidget } from "@/components/dashboard/TrendingWidget";
import { StatsWidget } from "@/components/dashboard/StatsWidget";
import { CreateProjectModal } from "@/components/projects/CreateProjectModal";
import { useInfiniteFeed } from "@/hooks/useInfiniteFeed";
import { motion, AnimatePresence } from "framer-motion";
import { Loader2, Plus, Sparkles, Activity, Target, Zap } from "lucide-react";
import { Button } from "@/components/ui/Button";

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
    <div className="flex flex-col h-full overflow-hidden relative bg-background">
      <Header title="Lendi Innovation Feed" />

      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">

          <div className="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-8">
            <div className="space-y-1">
              <h1 className="text-4xl font-black tracking-tight-inst mb-2 text-foreground">Innovator Hub</h1>
              <p className="text-muted-foreground font-medium flex items-center gap-2">
                <Target size={16} className="text-lendi-blue" />
                Aggregated research and activity across LIET sectors.
              </p>
            </div>
            <Button onClick={() => setIsModalOpen(true)} className="rounded-2xl px-10 h-14 font-black shadow-lendi flex gap-3 group text-xs uppercase tracking-widest">
              <Plus size={18} className="group-hover:rotate-90 transition-transform" />
              Spawn Mission
            </Button>
          </div>

          <div className="grid grid-cols-1 xl:grid-cols-12 gap-10">
            <div className="xl:col-span-8 space-y-10">
              <div className="inst-card p-10 bg-card shadow-sm mb-12 relative overflow-hidden group">
                <div className="flex items-center justify-between mb-10">
                  <div className="flex items-center gap-3">
                    <div className="p-2 rounded-lg bg-lendi-blue/10 text-lendi-blue shadow-sm">
                      <Activity size={18} />
                    </div>
                    <h3 className="font-black text-[10px] uppercase tracking-[0.3em] text-muted-foreground">Contribution Velocity</h3>
                  </div>
                  <div className="px-3 py-1 rounded-full bg-green-500/10 border border-green-500/20 flex items-center gap-2">
                    <div className="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse shadow-[0_0_8px_rgba(34,197,94,0.6)]" />
                    <span className="text-[9px] font-black text-green-600 uppercase tracking-widest">92% Sector Load</span>
                  </div>
                </div>

                <div className="flex gap-2.5 h-20 items-end px-2">
                  {Array.from({ length: 32 }).map((_, i) => (
                    <div key={i} className="flex-1 h-full rounded-md bg-secondary relative group/bar overflow-hidden border border-border/50">
                      <motion.div
                        initial={{ height: 0 }}
                        animate={{ height: `${Math.random() * 70 + 20}%` }}
                        transition={{ delay: i * 0.02, duration: 1.5, ease: "circOut" }}
                        className="absolute bottom-0 left-0 right-0 bg-lendi-blue/30 border-t-2 border-lendi-blue group-hover/bar:bg-lendi-blue group-hover/bar:border-white transition-all duration-300"
                      />
                    </div>
                  ))}
                </div>

                <div className="mt-8 flex justify-between items-center text-[8px] font-black uppercase tracking-[0.3em] text-muted-foreground/30">
                   <span>Lendi-Cloud-Sentinel-Uplink</span>
                   <span>Real-time Innovation Stream</span>
                </div>
              </div>

              {status === "pending" ? (
                <div className="h-[400px] flex flex-col items-center justify-center gap-6 inst-card border-dashed border-2 bg-muted/20">
                  <div className="w-12 h-12 border-4 border-lendi-blue border-t-transparent rounded-full animate-spin" />
                  <p className="text-muted-foreground font-black uppercase tracking-[0.3em] text-[10px]">Synthesizing Data Nodes...</p>
                </div>
              ) : (
                <div className="space-y-8">
                  {data?.pages.map((page, i) => (
                    <div key={i} className="space-y-8">
                      {page.map((item, j) => (
                        <FeedCard key={item.id} item={item} index={j} />
                      ))}
                    </div>
                  ))}
                  <div ref={loadMoreRef} className="py-12 flex justify-center">
                    {isFetchingNextPage && (
                      <div className="flex flex-col items-center gap-4">
                        <Loader2 className="w-6 h-6 animate-spin text-lendi-blue opacity-50" />
                        <span className="text-[10px] font-black uppercase tracking-widest text-muted-foreground/30">Expanding Knowledge Graph</span>
                      </div>
                    )}
                  </div>
                </div>
              )}
            </div>

            <div className="xl:col-span-4 space-y-10">
              <div className="sticky top-28 space-y-10">
                <StatsWidget />
                <TrendingWidget />

                <div className="inst-card p-10 bg-gradient-to-br from-lendi-blue to-lendi-dark text-white relative overflow-hidden group shadow-lendi border-none">
                  <div className="absolute top-0 right-0 p-12 opacity-10 group-hover:scale-110 transition-transform duration-1000 rotate-12">
                    <Zap size={200} fill="currentColor" />
                  </div>
                  <div className="relative z-10">
                    <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 border border-white/30 text-[9px] font-black uppercase tracking-widest mb-6">
                      Upcoming Event
                    </div>
                    <h4 className="text-3xl font-black mb-3 tracking-tight-inst leading-tight">Institutional <br />Hackathon v4.0</h4>
                    <p className="text-white/70 text-sm mb-8 leading-relaxed font-medium text-balance">The flagship LIET innovation track. 48 hours of uninterrupted technical development.</p>
                    <Button className="w-full bg-white text-lendi-blue hover:bg-white/90 font-black rounded-2xl h-14 text-xs uppercase tracking-widest shadow-xl">Secure Registration</Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </main>

      <CreateProjectModal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} onSuccess={handlePostSuccess} />

      <AnimatePresence>
        {showToast && (
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 20 }}
            className="fixed bottom-12 left-1/2 -translate-x-1/2 inst-card bg-white dark:bg-card px-10 py-5 rounded-[2rem] border border-border shadow-premium z-[200] flex items-center gap-4"
          >
            <div className="w-10 h-10 rounded-2xl bg-lendi-blue/10 flex items-center justify-center text-lendi-blue">
              <Sparkles size={20} />
            </div>
            <p className="text-sm font-black uppercase tracking-widest text-foreground">Mission Broadcast Success</p>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}
