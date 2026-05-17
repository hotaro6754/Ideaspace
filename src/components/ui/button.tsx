"use client";

import { forwardRef } from "react";
import { cn } from "@/lib/utils";
import { Loader2 } from "lucide-react";

export interface ButtonProps extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: "primary" | "secondary" | "ghost" | "danger" | "gradient" | "outline";
  size?: "sm" | "md" | "lg";
  loading?: boolean;
}

export const Button = forwardRef<HTMLButtonElement, ButtonProps>(
  ({ className, variant = "primary", size = "md", loading, children, disabled, ...props }, ref) => {
    const baseStyles =
      "inline-flex items-center justify-center font-semibold transition-all duration-200 press-effect rounded-[var(--radius-md)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-accent focus-visible:ring-offset-2 focus-visible:ring-offset-surface-base disabled:opacity-50 disabled:pointer-events-none cursor-pointer";

    const variants = {
      primary:
        "bg-brand-primary text-text-primary hover:brightness-110 shadow-sm shadow-brand-primary/20",
      secondary:
        "bg-surface-overlay text-text-primary border border-border-default hover:bg-surface-float hover:border-border-strong",
      ghost:
        "text-text-secondary hover:text-text-primary hover:bg-white/[0.04]",
      danger:
        "bg-brand-danger text-text-primary hover:brightness-110 shadow-sm shadow-brand-danger/20",
      gradient:
        "bg-gradient-to-r from-brand-primary to-brand-secondary text-text-primary hover:brightness-110 shadow-lg shadow-brand-primary/25",
      outline:
        "border border-border-default text-text-primary hover:bg-surface-overlay hover:border-border-strong",
    };

    const sizes = {
      sm: "h-8 px-3 text-xs gap-1.5",
      md: "h-10 px-4 text-sm gap-2",
      lg: "h-12 px-6 text-base gap-2.5",
    };

    return (
      <button
        ref={ref}
        className={cn(baseStyles, variants[variant], sizes[size], className)}
        disabled={disabled || loading}
        {...props}
      >
        {loading && <Loader2 className="h-4 w-4 animate-spin" />}
        {children}
      </button>
    );
  }
);

Button.displayName = "Button";
