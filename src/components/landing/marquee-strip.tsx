"use client"

import { motion } from "framer-motion"

const partners = [
  "Lendi Research Cell",
  "IIC Lendi",
  "Microsoft Azure",
  "GitHub Education",
  "AICTE Idea Lab",
  "Lendi Alumni Association",
  "Lendi Research Cell",
  "IIC Lendi",
  "Microsoft Azure",
  "GitHub Education",
  "AICTE Idea Lab",
  "Lendi Alumni Association",
]

export default function MarqueeStrip() {
  return (
    <div className="relative py-12 border-y border-border bg-card/30 overflow-hidden">
      <div className="flex whitespace-nowrap">
        <motion.div
          animate={{ x: [0, -1000] }}
          transition={{ duration: 30, repeat: Infinity, ease: "linear" }}
          className="flex items-center gap-16 px-8"
        >
          {partners.map((p, i) => (
            <div key={i} className="flex items-center gap-4 group">
               <div className="w-1.5 h-1.5 rounded-full bg-lendi opacity-20" />
               <span className="text-sm font-black text-muted-foreground uppercase tracking-[0.2em] group-hover:text-lendi transition-colors">
                 {p}
               </span>
            </div>
          ))}
        </motion.div>
      </div>

      {/* Side Fades */}
      <div className="absolute inset-y-0 left-0 w-32 bg-gradient-to-r from-background to-transparent z-10" />
      <div className="absolute inset-y-0 right-0 w-32 bg-gradient-to-l from-background to-transparent z-10" />
    </div>
  )
}
