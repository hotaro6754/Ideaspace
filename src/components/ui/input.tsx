"use client";

import { forwardRef } from "react";
import { cn } from "@/lib/utils";

export interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  error?: boolean;
  icon?: React.ReactNode;
  label?: string;
  helperText?: string;
}

export const Input = forwardRef<HTMLInputElement, InputProps>(
  ({ className, error, icon, label, helperText, ...props }, ref) => {
    return (
      <div className="w-full">
        {label && (
          <label className="block text-sm font-medium text-text-primary mb-1.5">
            {label}
          </label>
        )}
        <div className="relative">
          {icon && (
            <div className="absolute left-3 top-1/2 -translate-y-1/2 text-text-muted">
              {icon}
            </div>
          )}
          <input
            ref={ref}
            className={cn(
              "w-full h-10 rounded-[var(--radius-md)] border bg-bg-secondary/80 backdrop-blur-sm px-3 text-sm text-text-primary placeholder:text-text-muted/60 transition-all duration-200",
              "focus:outline-none focus:ring-2 focus:ring-accent/40 focus:border-accent",
              "hover:border-border-bright",
              icon && "pl-10",
              error
                ? "border-red focus:ring-red/40 focus:border-red"
                : "border-border",
              className
            )}
            {...props}
          />
        </div>
        {helperText && (
          <p className={cn("mt-1.5 text-xs", error ? "text-red" : "text-text-muted")}>
            {helperText}
          </p>
        )}
      </div>
    );
  }
);

Input.displayName = "Input";
