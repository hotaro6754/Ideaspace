"use client";

import Link from "next/link";
import { cn } from "@/lib/utils";
import { Lightbulb } from "lucide-react";
import { Button } from "./button";

export interface EmptyStateProps {
  icon?: React.ReactNode;
  title: string;
  description: string;
  action?: {
    label: string;
    href: string;
  };
  className?: string;
}

export function EmptyState({ icon, title, description, action, className }: EmptyStateProps) {
  return (
    <div className={cn("flex flex-col items-center justify-center text-center py-20 px-6", className)}>
      <div className="h-16 w-16 rounded-2xl bg-bg-tertiary border border-border flex items-center justify-center mb-6 text-text-muted">
        {icon ?? <Lightbulb className="h-8 w-8" />}
      </div>
      <h3 className="text-lg font-bold text-text-primary mb-2">{title}</h3>
      <p className="text-sm text-text-secondary max-w-md mb-6">{description}</p>
      {action && (
        <Link href={action.href}>
          <Button variant="gradient">{action.label}</Button>
        </Link>
      )}
    </div>
  );
}
