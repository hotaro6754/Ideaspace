"use client"

import { motion } from "framer-motion"
import { ArrowRight, ChevronRight, Users, Lightbulb, Rocket, GitBranch, Star, Zap } from "lucide-react"
import Link from "next/link"

const stats = [
  { value: "128", label: "Active Projects" },
  { value: "2,400+", label: "LIET Students" },
  { value: "42", label: "Ideas Launched" },
]

const mockProjects = [
  { title: "Smart Traffic AI", tag: "IoT", members: 4, stars: 23, color: "text-synk" },
  { title: "BioMed Blockchain", tag: "Web3", members: 3, stars: 18, color: "text-lendi-light" },
  { title: "Campus EV Grid", tag: "Sustainability", members: 5, stars: 31, color: "text-emerald-400" },
]

export default function Hero() {
  return (
    <section className="relative min-h-screen flex items-center justify-center overflow-hidden pt-16">
      {/* Background layers */}
      <div className="absolute inset-0 bg-background" />
      <div className="absolute inset-0 blueprint-grid" />

      {/* Glow orbs */}
      <div className="absolute top-[-10%] right-[-5%] w-[700px] h-[700px] rounded-full bg-lendi/10 blur-[130px] animate-glow-pulse pointer-events-none" />
      <div className="absolute bottom-[-5%] left-[-10%] w-[500px] h-[500px] rounded-full bg-synk/8 blur-[110px] animate-glow-pulse-fast delay-1000 pointer-events-none" />
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[400px] rounded-full bg-lendi/5 blur-[150px] pointer-events-none" />

      {/* Scanline */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="animate-scan" />
      </div>

      {/* Corner decorations */}
      <div className="absolute top-24 left-8 w-16 h-16 border-l-2 border-t-2 border-lendi/20 rounded-tl-sm pointer-events-none" />
      <div className="absolute top-24 right-8 w-16 h-16 border-r-2 border-t-2 border-lendi/20 rounded-tr-sm pointer-events-none" />
      <div className="absolute bottom-16 left-8 w-16 h-16 border-l-2 border-b-2 border-synk/20 rounded-bl-sm pointer-events-none" />
      <div className="absolute bottom-16 right-8 w-16 h-16 border-r-2 border-b-2 border-synk/20 rounded-br-sm pointer-events-none" />

      <div className="relative z-10 max-w-7xl mx-auto px-6 py-24 lg:py-32 grid lg:grid-cols-2 gap-16 items-center">
        {/* Left: Content */}
        <div className="flex flex-col items-start text-left">
          {/* Badge */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.5 }}
            className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-synk/25 bg-synk/8 text-xs font-medium text-synk mb-6"
          >
            <span className="w-1.5 h-1.5 rounded-full bg-synk animate-pulse" />
            LIET Innovation Platform — Open for 2025
            <ChevronRight size={13} className="opacity-60" />
          </motion.div>

          {/* Main heading */}
          <motion.h1
            initial={{ opacity: 0, y: 35 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.75, delay: 0.1, ease: "easeOut" }}
            className="text-5xl sm:text-6xl xl:text-7xl font-bold leading-[1.08] text-foreground text-balance mb-5 font-sans"
          >
            Build the Next
            <br />
            <span className="gradient-text-hero">
              Big Idea.
            </span>
          </motion.h1>

          {/* Subtext */}
          <motion.p
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.65, delay: 0.3 }}
            className="text-base sm:text-lg text-muted-foreground max-w-lg text-pretty leading-relaxed mb-8"
          >
            Connecting technical builders with visionary innovation tracks.
            Form teams, tackle real-world problems, and ship what matters at LIET.
          </motion.p>

          {/* CTAs */}
          <motion.div
            initial={{ opacity: 0, y: 20 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.6, delay: 0.45 }}
            className="flex flex-wrap gap-3 mb-12"
          >
            <Link
              href="/login"
              className="flex items-center gap-2 px-7 py-3.5 bg-lendi text-white rounded-xl font-semibold text-sm glow-btn-primary"
            >
              <Zap size={16} className="fill-white text-white" />
              Join the Forge
              <ArrowRight size={15} />
            </Link>
            <Link
              href="/login"
              className="flex items-center gap-2 px-7 py-3.5 rounded-xl font-semibold text-sm border border-border text-foreground hover:border-lendi/40 hover:bg-lendi/8 transition-all duration-300"
            >
              Explore Projects
            </Link>
          </motion.div>

          {/* Stats */}
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            transition={{ duration: 0.7, delay: 0.65 }}
            className="flex items-center gap-8"
          >
            {stats.map((stat, i) => (
              <div key={stat.label} className="flex items-center gap-8">
                <div>
                  <div className="text-2xl font-bold text-foreground">{stat.value}</div>
                  <div className="text-xs text-muted-foreground mt-0.5 uppercase tracking-widest">{stat.label}</div>
                </div>
                {i < stats.length - 1 && (
                  <div className="w-px h-9 bg-border" />
                )}
              </div>
            ))}
          </motion.div>
        </div>

        {/* Right: Mock dashboard UI */}
        <motion.div
          initial={{ opacity: 0, x: 60, y: 20 }}
          animate={{ opacity: 1, x: 0, y: 0 }}
          transition={{ duration: 0.9, delay: 0.3, ease: "easeOut" }}
          className="hidden lg:block"
        >
          <div className="relative animate-float">
            {/* Main dashboard card */}
            <div className="glass-card rounded-2xl p-1 border border-white/8 shadow-[0_30px_80px_rgba(0,0,0,0.5),0_0_60px_rgba(0,74,153,0.15)]">
              {/* Window chrome */}
              <div className="flex items-center gap-2 px-4 py-3 border-b border-border/50">
                <div className="flex gap-1.5">
                  <div className="w-3 h-3 rounded-full bg-red-500/60" />
                  <div className="w-3 h-3 rounded-full bg-yellow-500/60" />
                  <div className="w-3 h-3 rounded-full bg-green-500/60" />
                </div>
                <div className="flex-1 text-center">
                  <div className="inline-flex items-center gap-2 bg-secondary/60 rounded-md px-3 py-1 text-xs text-muted-foreground">
                    <div className="w-1.5 h-1.5 rounded-full bg-synk animate-pulse" />
                    ideasync.liet.ac.in/dashboard
                  </div>
                </div>
              </div>

              {/* Dashboard content */}
              <div className="p-4 space-y-3">
                {/* Header row */}
                <div className="flex items-center justify-between mb-4">
                  <div>
                    <div className="text-sm font-semibold text-foreground">My Projects</div>
                    <div className="text-xs text-muted-foreground">3 active this week</div>
                  </div>
                  <div className="flex items-center gap-2 px-3 py-1.5 bg-lendi/20 border border-lendi/25 rounded-lg text-xs text-lendi-light font-medium">
                    <Rocket size={11} />
                    New Project
                  </div>
                </div>

                {/* Project cards */}
                {mockProjects.map((proj, i) => (
                  <motion.div
                    key={proj.title}
                    initial={{ opacity: 0, x: 20 }}
                    animate={{ opacity: 1, x: 0 }}
                    transition={{ delay: 0.6 + i * 0.1 }}
                    className="flex items-center justify-between p-3 rounded-xl bg-secondary/40 border border-border/40 hover:border-border/70 transition-colors group cursor-pointer"
                  >
                    <div className="flex items-center gap-3">
                      <div className={`w-2 h-2 rounded-full ${proj.color.replace('text-', 'bg-')}`} />
                      <div>
                        <div className="text-xs font-medium text-foreground">{proj.title}</div>
                        <div className="text-[11px] text-muted-foreground mt-0.5">{proj.tag}</div>
                      </div>
                    </div>
                    <div className="flex items-center gap-3 text-xs text-muted-foreground">
                      <div className="flex items-center gap-1">
                        <Users size={10} />
                        {proj.members}
                      </div>
                      <div className="flex items-center gap-1">
                        <Star size={10} className="text-yellow-500" />
                        {proj.stars}
                      </div>
                    </div>
                  </motion.div>
                ))}

                {/* Bottom XP bar */}
                <div className="mt-4 p-3 rounded-xl bg-lendi/8 border border-lendi/20">
                  <div className="flex items-center justify-between mb-2">
                    <div className="flex items-center gap-2">
                      <div className="w-6 h-6 rounded-full bg-gradient-to-br from-lendi to-synk flex items-center justify-center text-[10px] font-bold text-white">
                        L
                      </div>
                      <div>
                        <div className="text-xs font-medium text-foreground">Level 7 Builder</div>
                        <div className="text-[11px] text-muted-foreground">1,240 / 1,500 XP</div>
                      </div>
                    </div>
                    <div className="flex items-center gap-1 text-xs text-synk font-medium">
                      <GitBranch size={11} />
                      42 commits
                    </div>
                  </div>
                  <div className="w-full h-1.5 bg-secondary rounded-full overflow-hidden">
                    <motion.div
                      initial={{ width: 0 }}
                      animate={{ width: "83%" }}
                      transition={{ delay: 1, duration: 1.2, ease: "easeOut" }}
                      className="h-full bg-gradient-to-r from-lendi to-synk rounded-full"
                    />
                  </div>
                </div>
              </div>
            </div>

            {/* Floating badge top-right */}
            <motion.div
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 1, duration: 0.5 }}
              className="absolute -top-4 -right-4 glass-card rounded-xl px-3 py-2 border border-synk/25 shadow-[0_0_20px_rgba(6,182,212,0.2)] animate-float-delayed"
            >
              <div className="flex items-center gap-2 text-xs">
                <div className="w-2 h-2 rounded-full bg-synk animate-pulse" />
                <span className="text-synk font-medium">Live Sync Active</span>
              </div>
            </motion.div>

            {/* Floating badge bottom-left */}
            <motion.div
              initial={{ opacity: 0, scale: 0.8 }}
              animate={{ opacity: 1, scale: 1 }}
              transition={{ delay: 1.2, duration: 0.5 }}
              className="absolute -bottom-4 -left-6 glass-card rounded-xl px-3 py-2.5 border border-lendi/25 shadow-[0_0_20px_rgba(0,74,153,0.2)]"
            >
              <div className="flex items-center gap-2">
                <div className="w-6 h-6 rounded-full bg-gradient-to-br from-lendi to-synk flex items-center justify-center">
                  <Lightbulb size={12} className="text-white" />
                </div>
                <div>
                  <div className="text-[11px] font-medium text-foreground">New match found!</div>
                  <div className="text-[10px] text-muted-foreground">AI Mentor suggestion</div>
                </div>
              </div>
            </motion.div>
          </div>
        </motion.div>
      </div>

      {/* Bottom fade */}
      <div className="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-background to-transparent pointer-events-none" />
    </section>
  )
}
