"use client";

import { useState, useEffect } from "react";
import { FeedItem } from "@/services/FeedService";
import { motion, AnimatePresence } from "framer-motion";
import {
  Rocket,
  Shield,
  FileText,
  Star,
  MessageSquare,
  Share2,
  ArrowUpRight,
  Zap,
  ChevronRight,
  Clock,
  Target
} from "lucide-react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";
import { supabase } from "@/lib/supabase";

interface FeedCardProps {
  item: FeedItem;
  index: number;
}

export const FeedCard = ({ item, index }: FeedCardProps) => {
  const isProject = item.type === "project";
  const isBounty = item.type === "bounty";
  const isNews = item.type === "news";

  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      transition={{ delay: index * 0.05 }}
      className="inst-card p-8 bg-card shadow-sm hover:border-lendi-blue transition-all group relative overflow-hidden"
    >
      <div className="flex items-center justify-between mb-6">
        <div className="flex items-center gap-3">
          <div className={`p-2 rounded-lg ${
            isBounty ? "bg-red-500/10 text-red-600" :
            isNews ? "bg-amber-500/10 text-amber-600" :
            "bg-lendi-blue/10 text-lendi-blue"
          }`}>
            {isBounty ? <Shield size={18} /> : isNews ? <FileText size={18} /> : <Rocket size={18} />}
          </div>
          <span className="text-[10px] font-black uppercase tracking-widest text-muted-foreground/40">{item.type} protocol</span>
        </div>
        <div className="flex items-center gap-2 text-[10px] font-bold text-muted-foreground/30 uppercase tracking-widest">
          <Clock size={12} />
          {new Date(item.created_at).toLocaleDateString()}
        </div>
      </div>

      <div className="space-y-3 mb-8">
        <h3 className="text-2xl font-black tracking-tight group-hover:text-lendi-blue transition-colors">
          {item.title}
        </h3>
        <p className="text-muted-foreground text-sm leading-relaxed font-medium line-clamp-2 text-balance">
          {item.description}
        </p>
      </div>

      <div className="flex items-center justify-between pt-6 border-t border-border">
        <div className="flex items-center gap-3">
          <div className="w-9 h-9 rounded-xl bg-secondary border border-border flex items-center justify-center text-sm font-black text-muted-foreground/30">
            {item.author_name?.[0]}
          </div>
          <div>
            <p className="text-xs font-black text-foreground">{item.author_name}</p>
            <p className="text-[10px] font-bold text-muted-foreground/40 uppercase tracking-widest">
              {isBounty ? 'Faculty Desk' : isNews ? 'Institutional Hub' : 'Mission Lead'}
            </p>
          </div>
        </div>

        <div className="flex items-center gap-3">
          {isProject && (
            <div className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-secondary border border-border text-[10px] font-black text-muted-foreground uppercase">
              <Star size={12} className="text-amber-500 fill-amber-500" />
              {item.upvotes || 0}
            </div>
          )}
          {isBounty && (
             <div className="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-lendi-blue/5 border border-lendi-blue/10 text-[10px] font-black text-lendi-blue uppercase">
               <Zap size={12} fill="currentColor" />
               +{item.reward_amount} XP
             </div>
          )}
          <Link href={isProject ? `/projects/${item.id}` : isBounty ? '/bounties' : (item.external_url || '#')}>
            <Button variant="outline" size="sm" className="rounded-xl px-4 text-[10px] uppercase font-black tracking-widest gap-2">
              Inspect
              <ChevronRight size={14} />
            </Button>
          </Link>
        </div>
      </div>

      {/* Subtle aesthetic backdrop for news */}
      {isNews && (
        <div className="absolute top-0 right-0 p-8 opacity-[0.02] text-amber-500 pointer-events-none">
          <FileText size={160} />
        </div>
      )}
    </motion.div>
  );
};
