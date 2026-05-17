"use client";

import { useState, useEffect } from "react";
import { AppShell } from "@/components/layout/app-shell";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Avatar } from "@/components/ui/avatar";
import { EmptyState } from "@/components/ui/empty-state";
import { Skeleton } from "@/components/ui/skeleton";
import { Target, Clock, Users, Award } from "lucide-react";
import type { RankTier } from "@/types";
import { motion } from "framer-motion";

interface BountyData {
  _id: string;
  title: string;
  description: string;
  postedBy: { name: string; username: string; rankTier: RankTier };
  rewardPoints: number;
  kind: string;
  participationMode: string;
  deadlineAt: string;
  status: string;
  track: string;
}

export default function BountiesPage() {
  const [bounties, setBounties] = useState<BountyData[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch("/api/bounties")
      .then(r => r.json())
      .then(d => setBounties(d.data ?? []))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  const daysUntil = (date: string) => {
    const d = Math.ceil((new Date(date).getTime() - Date.now()) / 86400000);
    return d > 0 ? `${d}d left` : "Expired";
  };

  const kindColors: Record<string, string> = { build: "default", research: "info", design: "cyan", mentor: "gold", judge: "ember" };

  return (
    <AppShell>
      <div className="max-w-6xl mx-auto">
        <div className="mb-8">
          <h1 className="text-3xl font-bold tracking-tight text-text-primary font-display">Bounties</h1>
          <p className="text-text-secondary mt-1">Complete challenges, earn reward points.</p>
        </div>

        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3].map(i => <Skeleton key={i} className="h-48" />)}
          </div>
        ) : bounties.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {bounties.map((b, i) => (
              <motion.div key={b._id} initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.08 }}>
                <Card variant="spotlight" className="p-6 h-full flex flex-col">
                  <div className="flex justify-between items-start mb-3">
                    <Badge variant={(kindColors[b.kind] ?? "secondary") as "default" | "info" | "cyan" | "gold" | "ember" | "secondary"} className="capitalize">{b.kind}</Badge>
                    <Badge variant={b.status === "open" ? "success" : "secondary"} className="capitalize">{b.status}</Badge>
                  </div>
                  <h3 className="text-lg font-bold text-text-primary mb-2 font-display line-clamp-2">{b.title}</h3>
                  <p className="text-sm text-text-secondary line-clamp-3 mb-4 flex-1">{b.description}</p>
                  <div className="flex items-center gap-4 text-xs text-text-muted mb-4">
                    <span className="flex items-center gap-1"><Award className="h-3.5 w-3.5 text-accent" />{b.rewardPoints} pts</span>
                    <span className="flex items-center gap-1"><Clock className="h-3.5 w-3.5" />{daysUntil(b.deadlineAt)}</span>
                    <span className="flex items-center gap-1"><Users className="h-3.5 w-3.5" />{b.participationMode}</span>
                  </div>
                  <div className="flex items-center gap-2 pt-3 border-t border-border">
                    <Avatar name={b.postedBy.name} tier={b.postedBy.rankTier} size="sm" />
                    <span className="text-xs text-text-muted">{b.postedBy.name}</span>
                  </div>
                </Card>
              </motion.div>
            ))}
          </div>
        ) : (
          <EmptyState icon={<Target className="h-8 w-8" />} title="No bounties available" description="Check back soon for new challenges." />
        )}
      </div>
    </AppShell>
  );
}
