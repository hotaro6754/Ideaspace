"use client";

import { useState, useEffect } from "react";
import { AppShell } from "@/components/layout/app-shell";
import { Avatar } from "@/components/ui/avatar";
import { Badge } from "@/components/ui/badge";
import { Skeleton } from "@/components/ui/skeleton";
import { EmptyState } from "@/components/ui/empty-state";
import { RankBadge } from "@/components/ui/RankBadge";
import { PointsCounter } from "@/components/ui/PointsCounter";
import { Trophy, Medal, Crown, TrendingUp } from "lucide-react";
import { cn } from "@/lib/utils";
import type { RankTier } from "@/types";
import { motion, AnimatePresence } from "framer-motion";
import Link from "next/link";

interface LeaderboardEntry {
  _id: string;
  user: { _id: string; name: string; username: string; rankTier: RankTier; points: number; avatarUrl?: string; bio?: string };
  points: number;
  rank: number;
  rankTier: string;
  ideasShipped: number;
  proofsSubmitted: number;
  workshopsAttended: number;
}

export default function LeaderboardPage() {
  const [entries, setEntries] = useState<LeaderboardEntry[]>([]);
  const [loading, setLoading] = useState(true);
  const [period, setPeriod] = useState<"alltime" | "monthly" | "weekly">("alltime");

  useEffect(() => {
    setLoading(true);
    fetch(`/api/leaderboard?period=${period}`)
      .then(r => r.json())
      .then(d => setEntries(d.data ?? []))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, [period]);

  const getRankIcon = (rank: number) => {
    if (rank === 1) return <Crown className="h-6 w-6 text-brand-warning drop-shadow-md" />;
    if (rank === 2) return <Medal className="h-6 w-6 text-slate-300 drop-shadow-md" />;
    if (rank === 3) return <Medal className="h-6 w-6 text-amber-700 drop-shadow-md" />;
    return <span className="text-lg font-bold text-text-muted w-6 text-center">#{rank}</span>;
  };

  return (
    <AppShell>
      <div className="max-w-5xl mx-auto pb-20">
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10 pb-6 border-b border-border-default">
          <div>
            <h1 className="text-4xl md:text-5xl font-bold tracking-tight text-text-primary font-display">Campus Leaderboard</h1>
            <p className="text-lg text-text-secondary mt-2">Top builders ranked by verified proof and contributions.</p>
          </div>
          <div className="flex items-center bg-surface-raised border border-border-default p-1 rounded-lg">
            {(["alltime", "monthly", "weekly"] as const).map(p => (
              <button
                key={p}
                onClick={() => setPeriod(p)}
                className={cn(
                  "px-4 py-2 rounded-md text-sm font-bold transition-all capitalize cursor-pointer",
                  period === p ? "bg-surface-float text-text-primary border border-border-strong shadow-sm" : "text-text-muted hover:text-text-secondary"
                )}
              >
                {p === "alltime" ? "All Time" : p}
              </button>
            ))}
          </div>
        </div>

        {loading ? (
          <div className="space-y-4">
            {[1, 2, 3, 4, 5].map(i => <Skeleton key={i} className="h-24 w-full rounded-[var(--radius-xl)]" />)}
          </div>
        ) : entries.length > 0 ? (
          <div className="space-y-3">
            <AnimatePresence>
              {entries.map((entry, i) => (
                <motion.div
                  key={entry._id}
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: i * 0.05, type: "spring", stiffness: 300, damping: 30 }}
                >
                  <Link href={`/profile/${entry.user.username}`}>
                    <div className={cn(
                      "flex flex-col md:flex-row md:items-center gap-4 md:gap-6 p-4 md:p-6 rounded-[var(--radius-xl)] border transition-all cursor-pointer group",
                      entry.rank <= 3 
                        ? "bg-surface-raised border-border-strong shadow-lg hover:shadow-xl hover:-translate-y-1" 
                        : "bg-surface-overlay border-border-default hover:border-border-strong hover:-translate-y-1"
                    )}>
                      <div className="w-12 flex justify-center shrink-0 items-center">
                        {getRankIcon(entry.rank)}
                      </div>
                      
                      <div className="flex items-center gap-4 flex-1">
                        <Avatar name={entry.user.name} tier={entry.user.rankTier as RankTier} size="lg" />
                        <div className="min-w-0">
                          <div className="flex items-center gap-3">
                            <span className="font-bold text-text-primary text-lg truncate group-hover:text-brand-primary transition-colors">
                              {entry.user.name}
                            </span>
                            <RankBadge tier={entry.user.rankTier as RankTier} />
                          </div>
                          <span className="text-sm text-text-muted">@{entry.user.username}</span>
                        </div>
                      </div>
                      
                      <div className="hidden lg:flex items-center gap-8 text-sm text-text-muted">
                        <div className="text-center"><span className="block text-text-primary font-bold text-lg">{entry.ideasShipped}</span>Shipped</div>
                        <div className="text-center"><span className="block text-text-primary font-bold text-lg">{entry.proofsSubmitted}</span>Proofs</div>
                      </div>
                      
                      <div className="text-right md:w-32">
                        <PointsCounter value={entry.points} className="text-3xl font-bold text-brand-secondary font-display group-hover:scale-105 transition-transform inline-block" />
                        <span className="block text-[10px] text-text-muted uppercase tracking-widest font-bold mt-1">Points</span>
                      </div>
                    </div>
                  </Link>
                </motion.div>
              ))}
            </AnimatePresence>
          </div>
        ) : (
          <EmptyState
            icon={<Trophy className="h-12 w-12 text-brand-primary mb-4" />}
            title="No rankings yet"
            description="Earn points by forging ideas, submitting proof, and collaborating."
          />
        )}
      </div>
    </AppShell>
  );
}
