"use client";

import { useState, useEffect } from "react";
import { useInfiniteQuery } from "@tanstack/react-query";
import { useInView } from "react-intersection-observer";
import Link from "next/link";
import { AppShell } from "@/components/layout/app-shell";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { EmptyState } from "@/components/ui/empty-state";
import { IdeaCard } from "@/components/ui/IdeaCard";
import { SkeletonCard } from "@/components/ui/SkeletonCard";
import { Search, TrendingUp, Clock, Activity, Plus } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";

const TRACKS = [
  { slug: "ai", label: "AI & Intelligent Systems" },
  { slug: "web3", label: "Web3 & Decentralization" },
  { slug: "sustainability", label: "Sustainability" },
  { slug: "fintech", label: "FinTech" },
  { slug: "edtech", label: "EdTech" },
];

export default function DashboardPage() {
  const [search, setSearch] = useState("");
  const [debouncedSearch, setDebouncedSearch] = useState("");
  const [activeTrack, setActiveTrack] = useState<string | null>(null);
  const [sortBy, setSortBy] = useState<"newest" | "trending" | "health">("newest");
  
  const { ref: loadMoreRef, inView } = useInView();

  useEffect(() => {
    const timer = setTimeout(() => setDebouncedSearch(search), 300);
    return () => clearTimeout(timer);
  }, [search]);

  const fetchIdeas = async ({ pageParam = 1 }) => {
    const params = new URLSearchParams({
      page: pageParam.toString(),
      limit: "10",
      sort: sortBy,
    });
    if (activeTrack) params.append("track", activeTrack);
    
    const res = await fetch(`/api/ideas?${params.toString()}`);
    if (!res.ok) throw new Error("Network response was not ok");
    return res.json();
  };

  const {
    data,
    error,
    fetchNextPage,
    hasNextPage,
    isFetching,
    isFetchingNextPage,
    status,
  } = useInfiniteQuery({
    queryKey: ["ideas", activeTrack, sortBy, debouncedSearch],
    queryFn: fetchIdeas,
    getNextPageParam: (lastPage) => {
      if (lastPage.meta.page < lastPage.meta.pages) {
        return lastPage.meta.page + 1;
      }
      return undefined;
    },
    initialPageParam: 1,
  });

  useEffect(() => {
    if (inView && hasNextPage) {
      fetchNextPage();
    }
  }, [inView, fetchNextPage, hasNextPage]);

  // Flatten the pages into a single array
  const ideas = data?.pages.flatMap((page) => page.data) ?? [];
  const filteredIdeas = debouncedSearch 
    ? ideas.filter((idea) => 
        idea.title.toLowerCase().includes(debouncedSearch.toLowerCase()) ||
        idea.problem.toLowerCase().includes(debouncedSearch.toLowerCase())
      )
    : ideas;

  return (
    <AppShell>
      <div className="flex flex-col gap-6 max-w-6xl mx-auto pb-20">
        {/* Header */}
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-border-default">
          <div>
            <h1 className="text-3xl md:text-4xl font-bold tracking-tight text-text-primary font-display">Campus Dashboard</h1>
            <p className="text-text-secondary mt-1">Discover, collaborate, and elevate ideas into reality.</p>
          </div>
          <Link href="/ideas/new">
            <Button variant="gradient" className="glow-accent">
              <Plus className="mr-2 h-4 w-4" /> Forge Idea
            </Button>
          </Link>
        </div>

        {/* Search & Sort */}
        <div className="flex flex-col md:flex-row gap-4">
          <div className="flex-1">
            <Input
              icon={<Search className="h-4 w-4 text-text-muted" />}
              placeholder="Search ideas..."
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              className="bg-surface-raised border-border-default"
            />
          </div>
          <div className="flex items-center bg-surface-raised border border-border-default p-1 rounded-lg">
            {[
              { key: "newest", label: "Latest", icon: Clock },
              { key: "trending", label: "Trending", icon: TrendingUp },
              { key: "health", label: "Health Score", icon: Activity },
            ].map(s => (
              <button
                key={s.key}
                onClick={() => setSortBy(s.key as any)}
                className={`flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-bold transition-colors cursor-pointer ${
                  sortBy === s.key 
                    ? "bg-surface-float text-text-primary border border-border-strong shadow-sm" 
                    : "text-text-muted hover:text-text-secondary"
                }`}
              >
                <s.icon className="h-3.5 w-3.5" /> {s.label}
              </button>
            ))}
          </div>
        </div>

        {/* Track Filters */}
        <div className="flex flex-wrap gap-2">
          <Badge
            variant={activeTrack === null ? "default" : "secondary"}
            className="cursor-pointer font-bold px-3 py-1 text-sm"
            onClick={() => setActiveTrack(null)}
          >
            All Tracks
          </Badge>
          {TRACKS.map(t => (
            <Badge
              key={t.slug}
              variant={activeTrack === t.slug ? "default" : "secondary"}
              className="cursor-pointer font-bold px-3 py-1 text-sm"
              onClick={() => setActiveTrack(t.slug)}
            >
              {t.label}
            </Badge>
          ))}
        </div>

        {/* Idea Grid */}
        {status === "pending" ? (
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {[1, 2, 3, 4].map(i => <SkeletonCard key={i} />)}
          </div>
        ) : status === "error" ? (
          <EmptyState
            title="Failed to load ideas"
            description="There was an error connecting to the server."
            action={{ label: "Try Again", onClick: () => window.location.reload() }}
          />
        ) : filteredIdeas.length > 0 ? (
          <>
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
              <AnimatePresence>
                {filteredIdeas.map((idea: any, index: number) => (
                  <Link href={`/ideas/${idea.slug}`} key={idea._id} className="block h-full">
                    <IdeaCard idea={idea} />
                  </Link>
                ))}
              </AnimatePresence>
            </div>
            
            {/* Infinite Scroll Trigger */}
            <div ref={loadMoreRef} className="flex justify-center py-8">
              {isFetchingNextPage ? (
                <div className="flex items-center gap-2 text-text-muted font-bold text-sm animate-pulse">
                  <Activity className="h-4 w-4 animate-spin" /> Loading more ideas...
                </div>
              ) : hasNextPage ? (
                <div className="text-text-muted font-bold text-sm">Scroll to load more</div>
              ) : (
                <div className="text-text-muted font-bold text-sm">You've reached the end of the line.</div>
              )}
            </div>
          </>
        ) : (
          <EmptyState
            title="No ideas found"
            description={search ? "Try adjusting your search or filters." : "Be the first to forge a new idea!"}
            action={!search ? { label: "Forge Idea", href: "/ideas/new" } : undefined}
          />
        )}
      </div>
    </AppShell>
  );
}
