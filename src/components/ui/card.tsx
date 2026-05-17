"use client";

import { cn } from "@/lib/utils";

export interface CardProps extends React.HTMLAttributes<HTMLDivElement> {
  variant?: "default" | "glass" | "spotlight" | "elevated";
}

export function Card({ className, variant = "default", children, ...props }: CardProps) {
  const variants = {
    default: "bg-surface-raised border border-border-default",
    glass: "glass-card",
    spotlight: "bg-surface-raised/60 border border-border-default card-spotlight hover:border-border-strong transition-all duration-300",
    elevated: "bg-surface-float border border-border-strong shadow-lg",
  };

  return (
    <div
      className={cn(
        "rounded-[var(--radius-lg)] overflow-hidden",
        variants[variant],
        className
      )}
      {...props}
    >
      {children}
    </div>
  );
}
