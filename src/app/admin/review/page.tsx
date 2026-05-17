"use client";

import { useState, useEffect } from "react";
import { AppShell } from "@/components/layout/app-shell";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import { EmptyState } from "@/components/ui/empty-state";
import { Avatar } from "@/components/ui/avatar";
import { ShieldCheck, Check, X, Clock, Eye, MessageSquare } from "lucide-react";
import { formatDate } from "@/lib/utils";
import toast from "react-hot-toast";
import { motion, AnimatePresence } from "framer-motion";

interface ReviewItem {
  _id: string;
  targetId: string;
  targetType: "proof" | "idea" | "bounty_submission";
  reviewType: "milestone_verification" | "external_proof" | "content_report";
  status: "pending" | "in_review" | "approved" | "rejected";
  submittedBy: { name: string; username: string; avatarUrl?: string };
  createdAt: string;
}

export default function AdminReviewPage() {
  const [items, setItems] = useState<ReviewItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [processingId, setProcessingId] = useState<string | null>(null);

  useEffect(() => {
    fetchQueue();
  }, []);

  const fetchQueue = async () => {
    setLoading(true);
    try {
      const res = await fetch("/api/admin/review");
      if (res.ok) {
        const result = await res.json();
        setItems(result.data ?? []);
      }
    } catch {
      toast.error("Failed to fetch review queue");
    } finally {
      setLoading(false);
    }
  };

  const handleAction = async (id: string, status: "approved" | "rejected") => {
    setProcessingId(id);
    try {
      const res = await fetch(`/api/admin/review/${id}`, {
        method: "PATCH",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ status, reviewNotes: `Handled by admin at ${new Date().toISOString()}` }),
      });

      if (res.ok) {
        toast.success(`Item ${status} successfully`);
        setItems(items.filter(item => item._id !== id));
      } else {
        toast.error("Action failed");
      }
    } catch {
      toast.error("Something went wrong");
    } finally {
      setProcessingId(null);
    }
  };

  const typeLabels = {
    proof: "Proof Evidence",
    idea: "Idea Verification",
    bounty_submission: "Bounty Submission",
  };

  const typeIcons = {
    proof: ShieldCheck,
    idea: Eye,
    bounty_submission: Check,
  };

  return (
    <AppShell>
      <div className="max-w-6xl mx-auto">
        <div className="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
          <div>
            <h1 className="text-3xl font-bold tracking-tight text-text-primary font-display">Review Queue</h1>
            <p className="text-text-secondary mt-1">Verify milestones, proof of work, and report submissions.</p>
          </div>
          <Badge variant="cyan" className="h-fit">{items.length} Pending Items</Badge>
        </div>

        {loading ? (
          <div className="space-y-4">
            {[1, 2, 3].map(i => <Skeleton key={i} className="h-24 w-full" />)}
          </div>
        ) : items.length > 0 ? (
          <div className="rounded-[var(--radius-lg)] border border-border overflow-hidden bg-bg-secondary/20">
            <table className="w-full text-left">
              <thead>
                <tr className="bg-bg-secondary/80 border-b border-border">
                  <th className="p-4 text-xs font-bold text-text-muted uppercase tracking-wider">Submitter</th>
                  <th className="p-4 text-xs font-bold text-text-muted uppercase tracking-wider">Type</th>
                  <th className="p-4 text-xs font-bold text-text-muted uppercase tracking-wider">Submitted</th>
                  <th className="p-4 text-xs font-bold text-text-muted uppercase tracking-wider text-right">Actions</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-border">
                <AnimatePresence mode="popLayout">
                  {items.map((item) => {
                    const Icon = typeIcons[item.targetType];
                    return (
                      <motion.tr
                        key={item._id}
                        layout
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0, x: -20 }}
                        className="group hover:bg-bg-secondary/40 transition-colors"
                      >
                        <td className="p-4">
                          <div className="flex items-center gap-3">
                            <Avatar name={item.submittedBy.name} size="sm" />
                            <div>
                              <div className="text-sm font-bold text-text-primary">{item.submittedBy.name}</div>
                              <div className="text-xs text-text-muted">@{item.submittedBy.username}</div>
                            </div>
                          </div>
                        </td>
                        <td className="p-4">
                          <div className="flex items-center gap-2">
                            <div className="h-8 w-8 rounded-lg bg-bg-tertiary border border-border flex items-center justify-center">
                              <Icon className="h-4 w-4 text-accent" />
                            </div>
                            <div>
                              <div className="text-sm font-medium text-text-primary">{typeLabels[item.targetType]}</div>
                              <div className="text-[10px] text-text-muted uppercase tracking-tighter">{item.reviewType.replace(/_/g, " ")}</div>
                            </div>
                          </div>
                        </td>
                        <td className="p-4">
                          <div className="flex items-center gap-1.5 text-xs text-text-secondary">
                            <Clock className="h-3.5 w-3.5" />
                            {formatDate(item.createdAt)}
                          </div>
                        </td>
                        <td className="p-4 text-right">
                          <div className="flex items-center justify-end gap-2">
                            <Button
                              variant="secondary"
                              size="sm"
                              className="h-8 w-8 p-0 rounded-full"
                              onClick={() => toast("Viewing target detail coming soon...")}
                            >
                              <Eye className="h-3.5 w-3.5" />
                            </Button>
                            <Button
                              variant="secondary"
                              size="sm"
                              className="h-8 w-8 p-0 rounded-full hover:bg-[#E5484D]/10 hover:text-[#E5484D] hover:border-[#E5484D]/30"
                              onClick={() => handleAction(item._id, "rejected")}
                              disabled={processingId === item._id}
                            >
                              <X className="h-3.5 w-3.5" />
                            </Button>
                            <Button
                              variant="secondary"
                              size="sm"
                              className="h-8 w-8 p-0 rounded-full hover:bg-[#2EA86A]/10 hover:text-[#2EA86A] hover:border-[#2EA86A]/30"
                              onClick={() => handleAction(item._id, "approved")}
                              disabled={processingId === item._id}
                            >
                              <Check className="h-3.5 w-3.5" />
                            </Button>
                          </div>
                        </td>
                      </motion.tr>
                    );
                  })}
                </AnimatePresence>
              </tbody>
            </table>
          </div>
        ) : (
          <EmptyState
            icon={<ShieldCheck className="h-8 w-8" />}
            title="Queue is empty"
            description="No items pending review. Good job!"
          />
        )}
      </div>
    </AppShell>
  );
}
