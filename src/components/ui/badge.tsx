"use client";

import { cn } from "@/lib/utils";

export interface BadgeProps extends React.HTMLAttributes<HTMLSpanElement> {
  variant?: "default" | "secondary" | "success" | "warning" | "danger" | "info" | "outline" | "cyan" | "gold" | "ember";
}

export function Badge({ className, variant = "default", children, ...props }: BadgeProps) {
  const variants = {
    default: "bg-brand-primary/15 text-brand-primary border-brand-primary/20",
    secondary: "bg-surface-overlay text-text-secondary border-border-default",
    success: "bg-brand-success/15 text-brand-success border-brand-success/20",
    warning: "bg-brand-warning/15 text-brand-warning border-brand-warning/20",
    danger: "bg-brand-danger/15 text-brand-danger border-brand-danger/20",
    info: "bg-brand-primary/15 text-brand-primary border-brand-primary/20",
    outline: "bg-transparent text-text-secondary border-border-default",
    cyan: "bg-brand-accent/15 text-brand-accent border-brand-accent/20",
    gold: "bg-brand-warning/15 text-brand-warning border-brand-warning/20",
    ember: "bg-brand-danger/15 text-brand-danger border-brand-danger/20",
  };

  return (
    <span
      className={cn(
        "inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-semibold uppercase tracking-wider border",
        variants[variant],
        className
      )}
      {...props}
    >
      {children}
    </span>
  );
}
