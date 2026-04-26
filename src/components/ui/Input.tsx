"use client";

import * as React from "react";
import { cn } from "@/lib/utils";
import { motion } from "framer-motion";

export interface InputProps
  extends React.InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    error?: string;
  }

const Input = React.forwardRef<HTMLInputElement, InputProps>(
  ({ className, type, label, error, ...props }, ref) => {
    return (
      <div className="w-full space-y-2">
        {label && (
          <label className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground ml-1">
            {label}
          </label>
        )}
        <motion.div
          whileFocus={{ scale: 1.01 }}
          className="relative"
        >
          <input
            type={type}
            className={cn(
              "flex h-12 w-full rounded-2xl border border-border bg-card px-5 py-2 text-sm font-medium ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground/40 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-lendi-blue/20 focus-visible:border-lendi-blue transition-all duration-300",
              error && "border-lendi-red focus-visible:ring-lendi-red/20",
              className
            )}
            ref={ref}
            {...props}
          />
        </motion.div>
        {error && (
          <p className="text-[10px] font-bold text-lendi-red ml-1 uppercase tracking-wider italic">
            {error}
          </p>
        )}
      </div>
    );
  }
);
Input.displayName = "Input";

export { Input };
