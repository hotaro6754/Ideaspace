"use client";

import { motion } from "framer-motion";
import { cn } from "@/lib/utils";

type RankTier = "Spark" | "Builder" | "Innovator" | "Legend";

interface RankBadgeProps {
  tier: RankTier;
  className?: string;
}

const tierConfig: Record<RankTier, { color: string; label: string }> = {
  Spark: { color: "bg-surface-overlay text-text-secondary border-border-default", label: "Spark" },
  Builder: { color: "bg-brand-primary/10 text-brand-primary border-brand-primary/30", label: "Builder" },
  Innovator: { color: "bg-brand-secondary/15 text-brand-secondary border-brand-secondary/40", label: "Innovator" },
  Legend: { color: "bg-brand-warning/20 text-brand-warning border-brand-warning/50 relative overflow-hidden", label: "Legend" },
};

export function RankBadge({ tier, className }: RankBadgeProps) {
  const config = tierConfig[tier];

  return (
    <div
      className={cn(
        "inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-widest border",
        config.color,
        className
      )}
    >
      {tier === "Legend" && (
        <motion.div
          className="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-[-20deg]"
          initial={{ x: "-150%" }}
          animate={{ x: "150%" }}
          transition={{ repeat: Infinity, duration: 2, ease: "linear", repeatDelay: 1 }}
        />
      )}
      <span className="relative z-10">{config.label}</span>
    </div>
  );
}
