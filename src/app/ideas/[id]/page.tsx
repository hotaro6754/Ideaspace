"use client";

import { useState, useEffect, use } from "react";
import Link from "next/link";
import { useSession } from "next-auth/react";
import { AppShell } from "@/components/layout/app-shell";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Avatar } from "@/components/ui/avatar";
import { Card } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";
import { StatusChip } from "@/components/ui/StatusChip";
import { HealthScoreRing } from "@/components/ui/HealthScoreRing";
import { CollaboratorStack } from "@/components/ui/CollaboratorStack";
import { ProofWall } from "@/components/ideas/ProofWall";
import { JoinRequestManager } from "@/components/ideas/JoinRequestManager";
import { CORE_TRACKS } from "@/types";
import { Flame, Eye, GitBranch, ExternalLink, ArrowLeft, Calendar, Code2, Shield } from "lucide-react";
import { formatDate } from "@/lib/utils";
import { motion } from "framer-motion";

export default function IdeaDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = use(params);
  const { data: session } = useSession();
  const [idea, setIdea] = useState<any | null>(null);
  const [proofs, setProofs] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.all([
      fetch(`/api/ideas/${id}`).then(r => r.json()),
      fetch(`/api/ideas/${id}/proof`).then(r => r.json()),
    ])
    .then(([ideaRes, proofsRes]) => {
      setIdea(ideaRes.data);
      setProofs(proofsRes.data || []);
    })
    .catch(console.error)
    .finally(() => setLoading(false));
  }, [id]);

  const getTrackLabel = (slug: string) => CORE_TRACKS.find(t => t.slug === slug)?.label ?? slug;

  if (loading) {
    return (
      <AppShell>
        <div className="max-w-5xl mx-auto space-y-6">
          <Skeleton className="h-8 w-48" />
          <Skeleton className="h-12 w-3/4" />
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div className="lg:col-span-2 space-y-4">
              <Skeleton className="h-40" />
              <Skeleton className="h-40" />
            </div>
            <Skeleton className="h-80" />
          </div>
        </div>
      </AppShell>
    );
  }

  if (!idea) {
    return (
      <AppShell>
        <div className="max-w-5xl mx-auto text-center py-20">
          <h2 className="text-2xl font-bold text-text-primary mb-2">Idea not found</h2>
          <p className="text-text-secondary mb-6">This idea may have been removed or does not exist.</p>
          <Link href="/dashboard"><Button variant="gradient">Back to Feed</Button></Link>
        </div>
      </AppShell>
    );
  }

  const isCollaborator = session?.user?.id 
    ? idea.owner._id === session.user.id || idea.collaborators.some((c: any) => c._id === session.user.id)
    : false;

  return (
    <AppShell>
      <div className="max-w-6xl mx-auto pb-20">
        <Link href="/dashboard" className="inline-flex items-center gap-2 text-sm text-text-secondary hover:text-text-primary transition-colors mb-6">
          <ArrowLeft className="h-4 w-4" /> Back to Dashboard
        </Link>

        {idea.coverImage && (
          <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} className="w-full h-48 md:h-64 lg:h-80 rounded-[var(--radius-xl)] overflow-hidden mb-8 border border-border-default">
            <img src={idea.coverImage} alt={idea.title} className="w-full h-full object-cover" />
          </motion.div>
        )}

        <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} className="mb-8">
          <div className="flex items-center gap-3 mb-4">
            <StatusChip status={idea.status} />
            <Badge variant="secondary">{getTrackLabel(idea.track)}</Badge>
          </div>
          <h1 className="text-4xl md:text-5xl font-bold tracking-tight text-text-primary font-display mb-3">{idea.title}</h1>
          {idea.tagline && <p className="text-xl text-text-secondary mb-4">{idea.tagline}</p>}
          
          <div className="flex items-center gap-4 text-sm text-text-muted mt-6">
            <span className="flex items-center gap-1"><Calendar className="h-4 w-4" />{formatDate(idea.createdAt)}</span>
            <span className="flex items-center gap-1"><Eye className="h-4 w-4" />{idea.views} views</span>
            <span className="flex items-center gap-1 text-brand-warning"><Flame className="h-4 w-4" />{idea.upvotes} upvotes</span>
          </div>
        </motion.div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-12">
          {/* Main content */}
          <div className="lg:col-span-2 space-y-8">
            <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.1 }}>
              <Card variant="glass" className="p-8">
                <h2 className="text-xl font-bold text-text-primary mb-4 font-display flex items-center gap-2">
                  <Code2 className="h-5 w-5 text-brand-primary" /> The Problem
                </h2>
                <p className="text-text-secondary leading-relaxed whitespace-pre-wrap text-lg">{idea.problem}</p>
              </Card>
            </motion.div>

            <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.2 }}>
              <Card variant="glass" className="p-8">
                <h2 className="text-xl font-bold text-text-primary mb-4 font-display flex items-center gap-2">
                  <Shield className="h-5 w-5 text-brand-success" /> The Solution
                </h2>
                <p className="text-text-secondary leading-relaxed whitespace-pre-wrap text-lg">{idea.solution}</p>
              </Card>
            </motion.div>

            <motion.div initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: 0.3 }} className="pt-8">
              <ProofWall 
                ideaId={idea._id} 
                proofs={proofs} 
                isCollaborator={isCollaborator} 
                onProofSubmitted={(newProof) => setProofs([newProof, ...proofs])}
              />
            </motion.div>
          </div>

          {/* Sidebar */}
          <div className="space-y-6">
            <Card variant="glass" className="p-6 flex flex-col items-center text-center">
              <h3 className="text-sm font-bold text-text-muted uppercase tracking-widest mb-6">Health Score</h3>
              <HealthScoreRing score={idea.healthScore} size={120} strokeWidth={8} className="mb-4" />
              <p className="text-sm text-text-secondary">
                {idea.healthScore >= 80 ? "Excellent health!" : idea.healthScore >= 60 ? "Ready to build." : "Needs more details before publishing."}
              </p>
            </Card>

            <Card variant="glass" className="p-6">
              <h3 className="text-sm font-bold text-text-muted uppercase tracking-widest mb-4">Team</h3>
              <div className="mb-4">
                <h4 className="text-xs font-bold text-text-muted mb-2">Creator</h4>
                <Link href={`/profile/${idea.owner.username}`} className="flex items-center gap-3 hover:bg-surface-overlay p-2 -mx-2 rounded-lg transition-colors">
                  <Avatar name={idea.owner.name} tier={idea.owner.rankTier} size="md" />
                  <div>
                    <div className="font-bold text-text-primary text-sm">{idea.owner.name}</div>
                    <div className="text-xs text-text-muted">@{idea.owner.username} • {idea.owner.rankTier}</div>
                  </div>
                </Link>
              </div>

              {idea.collaborators.length > 0 && (
                <div>
                  <h4 className="text-xs font-bold text-text-muted mb-3">Collaborators</h4>
                  <CollaboratorStack collaborators={idea.collaborators} max={5} />
                </div>
              )}

              {/* Join Team Workflow Integration */}
              {session?.user && (
                <JoinRequestManager 
                  ideaId={idea._id}
                  isOwner={idea.owner._id === session.user.id}
                  isCollaborator={isCollaborator}
                  onUpdate={() => window.location.reload()}
                />
              )}
            </Card>

            <Card variant="glass" className="p-6">
              <h3 className="text-sm font-bold text-text-muted uppercase tracking-widest mb-4">Tags & Skills</h3>
              <div className="space-y-4">
                {idea.tags?.length > 0 && (
                  <div>
                    <div className="flex flex-wrap gap-2">
                      {idea.tags.map((tag: string) => (
                        <Badge key={tag} variant="outline" className="text-xs">{tag}</Badge>
                      ))}
                    </div>
                  </div>
                )}
                {idea.skillsNeeded?.length > 0 && (
                  <div>
                    <h4 className="text-xs font-bold text-text-muted mb-2 mt-4">Looking for</h4>
                    <div className="flex flex-wrap gap-2">
                      {idea.skillsNeeded.map((skill: string) => (
                        <Badge key={skill} variant="secondary" className="text-xs">{skill}</Badge>
                      ))}
                    </div>
                  </div>
                )}
              </div>
            </Card>

            {(idea.githubUrl || idea.demoUrl) && (
              <Card variant="glass" className="p-6">
                <h3 className="text-sm font-bold text-text-muted uppercase tracking-widest mb-4">Links</h3>
                <div className="flex flex-col gap-3">
                  {idea.githubUrl && (
                    <a href={idea.githubUrl} target="_blank" rel="noopener noreferrer">
                      <Button variant="outline" className="w-full justify-start"><GitBranch className="mr-3 h-4 w-4" /> GitHub Repository</Button>
                    </a>
                  )}
                  {idea.demoUrl && (
                    <a href={idea.demoUrl} target="_blank" rel="noopener noreferrer">
                      <Button variant="primary" className="w-full justify-start"><ExternalLink className="mr-3 h-4 w-4" /> Live Demo</Button>
                    </a>
                  )}
                </div>
              </Card>
            )}
          </div>
        </div>
      </div>
    </AppShell>
  );
}
