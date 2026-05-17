"use client";

import { useState, useEffect } from "react";
import { AppShell } from "@/components/layout/app-shell";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Avatar } from "@/components/ui/avatar";
import { EmptyState } from "@/components/ui/empty-state";
import { Skeleton } from "@/components/ui/skeleton";
import { ShieldCheck, CheckCircle, ExternalLink, Github, Video, FileText } from "lucide-react";
import { formatRelativeTime } from "@/lib/utils";
import type { RankTier } from "@/types";
import { motion } from "framer-motion";

interface ProofData {
  _id: string;
  title: string;
  description?: string;
  type: string;
  evidenceUrl: string;
  isVerified: boolean;
  pointsAwarded: number;
  createdAt: string;
  submittedBy: { name: string; username: string; rankTier: RankTier; avatarUrl?: string };
  idea: { title: string; slug: string; track: string };
}

const typeIcons: Record<string, React.ElementType> = {
  github_commit: Github,
  demo_link: ExternalLink,
  presentation: Video,
  build_log: FileText,
  external_validation: CheckCircle,
  media_upload: FileText,
};

const typeLabels: Record<string, string> = {
  github_commit: "GitHub",
  demo_link: "Demo",
  presentation: "Presentation",
  build_log: "Build Log",
  external_validation: "Validation",
  media_upload: "Media",
};

export default function ProofWallPage() {
  const [proofs, setProofs] = useState<ProofData[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch("/api/wall")
      .then(r => r.json())
      .then(d => setProofs(d.data ?? []))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  return (
    <AppShell>
      <div className="max-w-6xl mx-auto">
        <div className="mb-8">
          <h1 className="text-3xl font-bold tracking-tight text-text-primary font-display">Proof Wall</h1>
          <p className="text-text-secondary mt-1">Verified evidence from campus builders. Demos, commits, and outcomes.</p>
        </div>

        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[1, 2, 3, 4, 5, 6].map(i => <Skeleton key={i} className="h-40" />)}
          </div>
        ) : proofs.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {proofs.map((proof, i) => {
              const TypeIcon = typeIcons[proof.type] ?? FileText;
              return (
                <motion.div
                  key={proof._id}
                  initial={{ opacity: 0, y: 20 }}
                  animate={{ opacity: 1, y: 0 }}
                  transition={{ delay: i * 0.06 }}
                >
                  <Card variant="spotlight" className="p-5 h-full flex flex-col">
                    <div className="flex justify-between items-start mb-3">
                      <div className="flex items-center gap-2">
                        <div className="h-8 w-8 rounded-lg bg-bg-tertiary border border-border flex items-center justify-center">
                          <TypeIcon className="h-4 w-4 text-accent" />
                        </div>
                        <Badge variant="secondary" className="text-[9px]">{typeLabels[proof.type] ?? proof.type}</Badge>
                      </div>
                      {proof.isVerified && (
                        <Badge variant="success" className="gap-1">
                          <CheckCircle className="h-3 w-3" /> Verified
                        </Badge>
                      )}
                    </div>
                    <h3 className="text-sm font-bold text-text-primary mb-1 line-clamp-2">{proof.title}</h3>
                    <p className="text-xs text-text-muted mb-3">{proof.idea.title}</p>
                    {proof.description && (
                      <p className="text-xs text-text-secondary line-clamp-2 mb-3 flex-1">{proof.description}</p>
                    )}
                    <div className="flex items-center justify-between pt-3 border-t border-border mt-auto">
                      <div className="flex items-center gap-2">
                        <Avatar name={proof.submittedBy.name} tier={proof.submittedBy.rankTier} size="sm" />
                        <span className="text-xs text-text-muted">{proof.submittedBy.name}</span>
                      </div>
                      <span className="text-[10px] text-text-muted">{formatRelativeTime(proof.createdAt)}</span>
                    </div>
                  </Card>
                </motion.div>
              );
            })}
          </div>
        ) : (
          <EmptyState
            icon={<ShieldCheck className="h-8 w-8" />}
            title="No verified proofs yet"
            description="Evidence will appear here as teams submit and verify their work."
          />
        )}
      </div>
    </AppShell>
  );
}
