"use client";

import { useEffect, useState, useRef } from "react";
import { motion, useSpring, useTransform } from "framer-motion";
import { cn } from "@/lib/utils";

interface PointsCounterProps {
  value: number;
  className?: string;
  prefix?: string;
}

export function PointsCounter({ value, className, prefix = "" }: PointsCounterProps) {
  const [mounted, setMounted] = useState(false);
  const springValue = useSpring(0, { stiffness: 300, damping: 30 });
  const displayValue = useTransform(springValue, (current) => Math.round(current));
  
  useEffect(() => {
    setMounted(true);
    springValue.set(value);
  }, [value, springValue]);

  return (
    <span className={cn("font-display font-bold tabular-nums", className)}>
      {prefix}
      {mounted ? <motion.span>{displayValue}</motion.span> : <span>0</span>}
    </span>
  );
}
