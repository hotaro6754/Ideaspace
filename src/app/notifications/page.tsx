"use client";

import { useState, useEffect } from "react";
import { AppShell } from "@/components/layout/app-shell";
import { Badge } from "@/components/ui/badge";
import { EmptyState } from "@/components/ui/empty-state";
import { Skeleton } from "@/components/ui/skeleton";
import { Bell, Check, MessageCircle, Flame, ShieldCheck, Award, Calendar, Info } from "lucide-react";
import { formatRelativeTime, cn } from "@/lib/utils";
import Link from "next/link";
import { motion } from "framer-motion";

interface NotificationData {
  _id: string;
  type: string;
  title: string;
  body: string;
  linkUrl?: string;
  isRead: boolean;
  createdAt: string;
}

const typeIcons: Record<string, React.ElementType> = {
  collaborator_request: MessageCircle,
  idea_upvoted: Flame,
  proof_verified: ShieldCheck,
  bounty_awarded: Award,
  workshop_reminder: Calendar,
  system_announcement: Info,
};

export default function NotificationsPage() {
  const [notifications, setNotifications] = useState<NotificationData[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch("/api/notifications")
      .then(r => r.json())
      .then(d => setNotifications(d.data ?? []))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  return (
    <AppShell>
      <div className="max-w-3xl mx-auto">
        <div className="flex items-center justify-between mb-8">
          <div>
            <h1 className="text-3xl font-bold tracking-tight text-text-primary font-display">Notifications</h1>
            <p className="text-text-secondary mt-1">Stay updated on your ideas and collaborations.</p>
          </div>
        </div>

        {loading ? (
          <div className="space-y-2">
            {[1, 2, 3, 4, 5].map(i => <Skeleton key={i} className="h-16" />)}
          </div>
        ) : notifications.length > 0 ? (
          <div className="space-y-1">
            {notifications.map((n, i) => {
              const Icon = typeIcons[n.type] ?? Bell;
              const content = (
                <motion.div
                  initial={{ opacity: 0, x: -10 }}
                  animate={{ opacity: 1, x: 0 }}
                  transition={{ delay: i * 0.03 }}
                  className={cn(
                    "flex items-start gap-4 p-4 rounded-[var(--radius-md)] border transition-colors hover:bg-bg-secondary/50",
                    n.isRead ? "border-transparent" : "border-border bg-bg-secondary/30"
                  )}
                >
                  <div className={cn("h-9 w-9 rounded-lg flex items-center justify-center shrink-0", n.isRead ? "bg-bg-tertiary" : "bg-accent/10")}>
                    <Icon className={cn("h-4 w-4", n.isRead ? "text-text-muted" : "text-accent")} />
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="flex items-start justify-between gap-2">
                      <p className={cn("text-sm font-medium", n.isRead ? "text-text-secondary" : "text-text-primary")}>{n.title}</p>
                      {!n.isRead && <span className="h-2 w-2 rounded-full bg-accent shrink-0 mt-1.5" />}
                    </div>
                    <p className="text-xs text-text-muted mt-0.5">{n.body}</p>
                    <p className="text-[10px] text-text-muted mt-1">{formatRelativeTime(n.createdAt)}</p>
                  </div>
                </motion.div>
              );

              return n.linkUrl ? (
                <Link key={n._id} href={n.linkUrl}>{content}</Link>
              ) : (
                <div key={n._id}>{content}</div>
              );
            })}
          </div>
        ) : (
          <EmptyState icon={<Bell className="h-8 w-8" />} title="No notifications" description="You're all caught up! Notifications appear when someone interacts with your work." />
        )}
      </div>
    </AppShell>
  );
}
