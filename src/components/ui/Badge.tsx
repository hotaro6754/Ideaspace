"use client";

import { cn } from "@/lib/utils";

interface BadgeProps extends React.HTMLAttributes<HTMLDivElement> {
  variant?: "default" | "secondary" | "outline" | "lendi";
}

export const Badge = ({ className, variant = "default", ...props }: BadgeProps) => {
  const variants = {
    default: "bg-secondary text-muted-foreground border-border",
    secondary: "bg-muted text-muted-foreground border-transparent",
    outline: "bg-transparent text-muted-foreground border-border",
    lendi: "bg-lendi-blue/10 text-lendi-blue border-lendi-blue/20",
  };

  return (
    <div
      className={cn(
        "inline-flex items-center rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-widest transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2",
        variants[variant],
        className
      )}
      {...props}
    />
  );
};
