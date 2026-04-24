"use client";

import { useEffect, useRef, useState } from "react";
import { Header } from "@/components/layout/Header";
import { FeedCard } from "@/components/dashboard/FeedCard";
import { TrendingWidget } from "@/components/dashboard/TrendingWidget";
import { StatsWidget } from "@/components/dashboard/StatsWidget";
import { CreatePostModal } from "@/components/dashboard/CreatePostModal";
import { useInfiniteFeed } from "@/hooks/useInfiniteFeed";
import { motion, AnimatePresence } from "framer-motion";
import { Loader2, Plus, Sparkles } from "lucide-react";
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

    if (loadMoreRef.current) {
      observer.observe(loadMoreRef.current);
    }

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
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2">
                Good morning, Innovator
              </h1>
              <p className="text-white/40 font-medium">Here's what's happening across the campus today.</p>
            </div>
            <Button
              onClick={() => setIsModalOpen(true)}
              className="rounded-full px-8 h-14 font-black shadow-2xl shadow-lendi-blue/20 flex gap-2 group"
            >
              <Plus className="w-5 h-5 group-hover:rotate-90 transition-transform" />
              Submit Idea
            </Button>
          </div>

          <div className="grid grid-cols-1 xl:grid-cols-12 gap-8">

            <div className="xl:col-span-8 space-y-6">
              {status === "pending" ? (
                <div className="h-64 flex flex-col items-center justify-center gap-4 border-2 border-dashed border-white/5 rounded-[2.5rem]">
                  <Loader2 className="w-8 h-8 animate-spin text-lendi-blue" />
                  <p className="text-white/20 italic font-medium">Synthesizing personalized data streams...</p>
                </div>
              ) : data?.pages[0].length === 0 ? (
                <div className="h-64 flex flex-col items-center justify-center gap-4 border-2 border-dashed border-white/5 rounded-[2.5rem] p-10 text-center">
                  <p className="text-white/20 italic font-medium max-w-sm">
                    No active transmissions in your sector. Try following more tracks or users.
                  </p>
                  <Button variant="glass" className="rounded-full">Explore Tracks</Button>
                </div>
              ) : (
                <>
                  {data?.pages.map((page, i) => (
                    <div key={i} className="space-y-6">
                      {page.map((item, j) => (
                        <FeedCard key={item.id} item={item} index={j} />
                      ))}
                    </div>
                  ))}

                  <div ref={loadMoreRef} className="py-10 flex justify-center">
                    {isFetchingNextPage && (
                      <div className="flex items-center gap-3">
                        <Loader2 className="w-5 h-5 animate-spin text-lendi-blue" />
                        <span className="text-xs font-black uppercase tracking-widest text-white/20">Loading next batch...</span>
                      </div>
                    )}
                  </div>
                </>
              )}
            </div>

            <div className="xl:col-span-4 space-y-8">
              <div className="sticky top-0 space-y-8">
                <StatsWidget />
                <TrendingWidget />

                <div className="p-8 rounded-[2rem] bg-gradient-to-br from-lendi-blue to-purple-600 relative overflow-hidden group shadow-2xl shadow-lendi-blue/20">
                  <div className="absolute top-0 right-0 p-10 opacity-10 group-hover:scale-110 transition-transform">
                    <Plus className="w-24 h-24 text-white rotate-45" />
                  </div>
                  <h4 className="text-2xl font-black mb-2 relative z-10">Hackathon V3.0</h4>
                  <p className="text-white/80 text-sm mb-6 relative z-10 leading-relaxed font-medium">
                    The biggest coding event at Lendi is back. 48 hours of pure creation.
                  </p>
                  <Button className="w-full bg-white text-lendi-blue hover:bg-white/90 font-black rounded-2xl relative z-10">
                    Register Now
                  </Button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </main>

      <CreatePostModal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        onSuccess={handlePostSuccess}
      />

      {/* Success Toast */}
      <AnimatePresence>
        {showToast && (
          <motion.div
            initial={{ opacity: 0, y: 50 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 20 }}
            className="fixed bottom-10 left-1/2 -translate-x-1/2 glass px-8 py-4 rounded-2xl border border-white/10 z-[200] flex items-center gap-3 shadow-2xl"
          >
            <div className="p-2 bg-green-500/20 rounded-lg">
              <Sparkles className="w-4 h-4 text-green-400" />
            </div>
            <p className="text-sm font-bold tracking-tight">Transmission Broadcasted Successfully</p>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
}
