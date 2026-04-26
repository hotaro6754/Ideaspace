"use client"

import { motion } from "framer-motion"

const partners = [
  "Lendi Research Cell",
  "IIC Lendi",
  "NPTEL Local Chapter",
  "AICTE Idea Lab",
  "Lendi Alumni Network",
  "Lendi Innovation Council",
]

export default function MarqueeStrip() {
  return (
    <div className="relative py-16 border-y border-border bg-card/20 backdrop-blur-sm overflow-hidden">
      <div className="flex whitespace-nowrap overflow-hidden">
        <motion.div
          animate={{ x: [0, -1200] }}
          transition={{ duration: 40, repeat: Infinity, ease: "linear" }}
          className="flex items-center gap-24 px-12"
        >
          {[...partners, ...partners].map((p, i) => (
            <div key={i} className="flex items-center gap-6 group">
               <div className="w-2 h-2 rounded-full bg-lendi shadow-[0_0_10px_rgba(0,74,153,0.5)]" />
               <span className="text-sm font-black text-muted-foreground uppercase tracking-[0.4em] group-hover:text-lendi transition-colors duration-500">
                 {p}
               </span>
            </div>
          ))}
        </motion.div>
      </div>

      {/* Cinematic Fades */}
      <div className="absolute inset-y-0 left-0 w-64 bg-gradient-to-r from-background to-transparent z-10" />
      <div className="absolute inset-y-0 right-0 w-64 bg-gradient-to-l from-background to-transparent z-10" />
    </div>
  )
}
