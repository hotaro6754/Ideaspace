"use client";

import { useState, useEffect } from "react";
import { AppShell } from "@/components/layout/app-shell";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Avatar } from "@/components/ui/avatar";
import { EmptyState } from "@/components/ui/empty-state";
import { Skeleton } from "@/components/ui/skeleton";
import { Archive as ArchiveIcon, GitFork, CheckCircle, PauseCircle, XCircle, RefreshCw } from "lucide-react";
import { formatRelativeTime } from "@/lib/utils";
import type { RankTier } from "@/types";
import { motion } from "framer-motion";

interface ArchiveData {
  _id: string;
  title: string;
  outcome: string;
  whatWorked: string;
  whatFailed: string;
  lessons: string;
  forkCount: number;
  author: { name: string; username: string; rankTier: RankTier };
  idea: { title: string; slug: string; track: string };
  createdAt: string;
}

const outcomeIcons: Record<string, React.ElementType> = {
  shipped: CheckCircle,
  paused: PauseCircle,
  failed: XCircle,
  pivoted: RefreshCw,
};

const outcomeColors: Record<string, string> = {
  shipped: "success",
  paused: "warning",
  failed: "danger",
  pivoted: "info",
};

export default function ArchivePage() {
  const [archives, setArchives] = useState<ArchiveData[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch("/api/archive")
      .then(r => r.json())
      .then(d => setArchives(d.data ?? []))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  return (
    <AppShell>
      <div className="max-w-4xl mx-auto">
        <div className="mb-8">
          <h1 className="text-3xl font-bold tracking-tight text-text-primary font-display">The Archive</h1>
          <p className="text-text-secondary mt-1">Postmortems and lessons from shipped, paused, and failed projects.</p>
        </div>

        {loading ? (
          <div className="space-y-4">
            {[1, 2, 3].map(i => <Skeleton key={i} className="h-40" />)}
          </div>
        ) : archives.length > 0 ? (
          <div className="space-y-4">
            {archives.map((a, i) => {
              const OutcomeIcon = outcomeIcons[a.outcome] ?? CheckCircle;
              return (
                <motion.div key={a._id} initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.08 }}>
                  <Card variant="spotlight" className="p-6">
                    <div className="flex justify-between items-start mb-4">
                      <Badge variant={outcomeColors[a.outcome] as "success" | "warning" | "danger" | "info"} className="gap-1 capitalize">
                        <OutcomeIcon className="h-3 w-3" /> {a.outcome}
                      </Badge>
                      <div className="flex items-center gap-1 text-xs text-text-muted">
                        <GitFork className="h-3.5 w-3.5" /> {a.forkCount} forks
                      </div>
                    </div>
                    <h3 className="text-lg font-bold text-text-primary mb-1 font-display">{a.title}</h3>
                    <p className="text-xs text-text-muted mb-4">From: {a.idea.title}</p>
                    <div className="space-y-3 text-sm">
                      <div><span className="text-text-muted font-medium">What worked: </span><span className="text-text-secondary">{a.whatWorked.slice(0, 150)}...</span></div>
                      <div><span className="text-text-muted font-medium">Lessons: </span><span className="text-text-secondary">{a.lessons.slice(0, 150)}...</span></div>
                    </div>
                    <div className="flex items-center gap-2 mt-4 pt-3 border-t border-border">
                      <Avatar name={a.author.name} tier={a.author.rankTier} size="sm" />
                      <span className="text-xs text-text-muted">{a.author.name}</span>
                      <span className="text-xs text-text-muted ml-auto">{formatRelativeTime(a.createdAt)}</span>
                    </div>
                  </Card>
                </motion.div>
              );
            })}
          </div>
        ) : (
          <EmptyState icon={<ArchiveIcon className="h-8 w-8" />} title="No postmortems yet" description="Postmortems will appear here when ideas are archived." />
        )}
      </div>
    </AppShell>
  );
}
