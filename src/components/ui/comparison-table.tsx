"use client"

import { Badge } from "@/components/ui/badge"
import { Card } from "@/components/ui/card"
import { Check, X } from "lucide-react"

export interface ComparisonRow {
  label: string
  ideaspace: string
  traditional: string
}

interface ComparisonTableProps {
  rows: ComparisonRow[]
  ideaspaceLabel?: string
  traditionalLabel?: string
}

export function ComparisonTable({
  rows,
  ideaspaceLabel = "IdeaSpace",
  traditionalLabel = "WhatsApp / Forms",
}: ComparisonTableProps) {
  return (
    <Card variant="glass" className="overflow-hidden">
      <div className="grid grid-cols-3 text-xs uppercase tracking-wider text-text-muted font-semibold border-b border-border">
        <div className="px-6 py-4">Criteria</div>
        <div className="px-6 py-4 text-text-primary">{ideaspaceLabel}</div>
        <div className="px-6 py-4">{traditionalLabel}</div>
      </div>
      {rows.map((row) => (
        <div key={row.label} className="grid grid-cols-3 border-b border-border last:border-b-0">
          <div className="px-6 py-4 text-sm text-text-secondary">{row.label}</div>
          <div className="px-6 py-4 text-sm text-text-primary flex items-center gap-2">
            <Check className="h-4 w-4 text-green" />
            {row.ideaspace}
          </div>
          <div className="px-6 py-4 text-sm text-text-muted flex items-center gap-2">
            <X className="h-4 w-4 text-red" />
            {row.traditional}
          </div>
        </div>
      ))}
    </Card>
  )
}