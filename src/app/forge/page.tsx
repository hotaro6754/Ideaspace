"use client";

import { useState, useEffect } from "react";
import { AppShell } from "@/components/layout/app-shell";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Avatar } from "@/components/ui/avatar";
import { EmptyState } from "@/components/ui/empty-state";
import { Skeleton } from "@/components/ui/skeleton";
import { Hammer, Calendar, MapPin, Users, Clock, Wifi } from "lucide-react";
import { formatDate } from "@/lib/utils";
import type { RankTier } from "@/types";
import { motion } from "framer-motion";

interface WorkshopData {
  _id: string;
  title: string;
  description: string;
  host: { _id: string; name: string; username: string; rankTier: RankTier; avatarUrl?: string };
  scheduledAt: string;
  durationMins: number;
  location: string;
  isOnline: boolean;
  maxAttendees: number;
  rsvpList: string[];
  demandSignals: number;
  track: string;
  status: string;
}

export default function ForgePage() {
  const [workshops, setWorkshops] = useState<WorkshopData[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch("/api/workshops")
      .then(r => r.json())
      .then(d => setWorkshops(d.data ?? []))
      .catch(() => {})
      .finally(() => setLoading(false));
  }, []);

  const statusColors: Record<string, string> = {
    upcoming: "info",
    live: "success",
    completed: "secondary",
    cancelled: "danger",
  };

  return (
    <AppShell>
      <div className="max-w-6xl mx-auto">
        <div className="mb-8">
          <h1 className="text-3xl font-bold tracking-tight text-text-primary font-display">The Forge</h1>
          <p className="text-text-secondary mt-1">Workshops, hackathons, and intensive build sessions.</p>
        </div>

        {loading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {[1, 2, 3, 4].map(i => <Skeleton key={i} className="h-48" />)}
          </div>
        ) : workshops.length > 0 ? (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {workshops.map((ws, i) => (
              <motion.div
                key={ws._id}
                initial={{ opacity: 0, y: 20 }}
                animate={{ opacity: 1, y: 0 }}
                transition={{ delay: i * 0.08 }}
              >
                <Card variant="spotlight" className="p-6 h-full flex flex-col">
                  <div className="flex justify-between items-start mb-4">
                    <Badge variant={statusColors[ws.status] as "info" | "success" | "secondary" | "danger"} className="capitalize">
                      {ws.status}
                    </Badge>
                    <Badge variant="secondary">{ws.track.replace(/-/g, " ")}</Badge>
                  </div>
                  <h3 className="text-lg font-bold text-text-primary mb-2 font-display">{ws.title}</h3>
                  <p className="text-sm text-text-secondary line-clamp-2 mb-4 flex-1">{ws.description}</p>
                  <div className="space-y-2 mb-4">
                    <div className="flex items-center gap-2 text-sm text-text-muted">
                      <Calendar className="h-4 w-4" />
                      <span>{formatDate(ws.scheduledAt)}</span>
                      <span className="text-text-muted">•</span>
                      <Clock className="h-4 w-4" />
                      <span>{ws.durationMins} min</span>
                    </div>
                    <div className="flex items-center gap-2 text-sm text-text-muted">
                      {ws.isOnline ? <Wifi className="h-4 w-4" /> : <MapPin className="h-4 w-4" />}
                      <span>{ws.location}</span>
                    </div>
                  </div>
                  <div className="flex items-center justify-between pt-3 border-t border-border">
                    <div className="flex items-center gap-2">
                      <Avatar name={ws.host.name} tier={ws.host.rankTier} size="sm" />
                      <span className="text-xs font-medium text-text-primary">{ws.host.name}</span>
                    </div>
                    <div className="flex items-center gap-1 text-xs text-text-muted">
                      <Users className="h-3.5 w-3.5" />
                      <span>{ws.rsvpList.length}/{ws.maxAttendees}</span>
                    </div>
                  </div>
                </Card>
              </motion.div>
            ))}
          </div>
        ) : (
          <EmptyState
            icon={<Hammer className="h-8 w-8" />}
            title="No workshops scheduled"
            description="Check back soon for upcoming workshops and hackathons."
          />
        )}
      </div>
    </AppShell>
  );
}
