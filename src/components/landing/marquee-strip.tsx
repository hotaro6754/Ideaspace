"use client"

import { motion } from "framer-motion"

const partners = [
  "Lendi Research Cell",
  "IIC Lendi",
  "Microsoft Azure",
  "GitHub Education",
  "AICTE Idea Lab",
  "Lendi Alumni Association",
]

export default function MarqueeStrip() {
  return (
    <div className="relative py-14 border-y border-border bg-muted/20 overflow-hidden">
      <div className="flex whitespace-nowrap">
        <motion.div
          animate={{ x: [0, -1035] }}
          transition={{ duration: 40, repeat: Infinity, ease: "linear" }}
          className="flex items-center gap-20 px-10"
        >
          {[...partners, ...partners, ...partners].map((p, i) => (
            <div key={i} className="flex items-center gap-6 group">
               <div className="w-2 h-2 rounded-full bg-lendi-blue/20 group-hover:bg-lendi-blue transition-colors" />
               <span className="text-xs font-black text-muted-foreground uppercase tracking-[0.3em] group-hover:text-foreground transition-all">
                 {p}
               </span>
            </div>
          ))}
        </motion.div>
      </div>

      {/* Side Fades for Professional Polish */}
      <div className="absolute inset-y-0 left-0 w-64 bg-gradient-to-r from-background to-transparent z-10" />
      <div className="absolute inset-y-0 right-0 w-64 bg-gradient-to-l from-background to-transparent z-10" />
    </div>
  )
}
