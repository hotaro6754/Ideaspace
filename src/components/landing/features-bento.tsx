"use client"

import { motion, Variants } from "framer-motion"
import {
  GitMerge,
  Brain,
  Map,
  BarChart3,
  Users,
  Network,
  Sparkles,
} from "lucide-react"

const containerVariants: Variants = {
  hidden: {},
  visible: { transition: { staggerChildren: 0.08 } },
}

const itemVariants: Variants = {
  hidden: { opacity: 0, y: 30 },
  visible: { opacity: 1, y: 0, transition: { duration: 0.6, ease: "easeOut" } },
}

function SectionLabel({ text }: { text: string }) {
  return (
    <div className="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-synk">
      <span className="w-4 h-px bg-synk" />
      {text}
    </div>
  )
}

export default function FeaturesBento() {
  return (
    <section id="features" className="relative py-24 lg:py-32 overflow-hidden">
      <div className="absolute inset-0 blueprint-grid-sm opacity-40" />
      <div className="absolute top-1/3 right-0 w-[400px] h-[400px] bg-lendi/6 rounded-full blur-[100px] pointer-events-none" />

      <div className="relative z-10 max-w-7xl mx-auto px-6">
        {/* Header */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="mb-14"
        >
          <SectionLabel text="Platform Capabilities" />
          <h2 className="mt-4 text-4xl sm:text-5xl font-bold text-foreground text-balance leading-tight font-sans">
            Everything you need to
            <br />
            <span className="gradient-text">build and collaborate.</span>
          </h2>
          <p className="mt-4 text-muted-foreground text-lg max-w-xl text-pretty font-medium">
            A unified workspace for LIET students to ideate, team up, and ship
            solutions that make a difference.
          </p>
        </motion.div>

        {/* Bento Grid */}
        <motion.div
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-80px" }}
          className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
        >
          {/* Card 1 — Large (col-span-2) */}
          <motion.div
            variants={itemVariants as any}
            className="lg:col-span-2 glass-card card-hover-glow border-shine rounded-2xl p-6 min-h-[220px] group cursor-pointer relative overflow-hidden"
          >
            <div className="absolute inset-0 bg-gradient-to-br from-lendi/5 via-transparent to-synk/5 opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
            <div className="flex items-start justify-between mb-4">
              <div className="w-10 h-10 rounded-xl bg-synk/15 border border-synk/25 flex items-center justify-center">
                <Network size={20} className="text-synk" />
              </div>
              <span className="text-xs font-medium bg-synk/10 text-synk border border-synk/20 px-2.5 py-1 rounded-full">
                Live
              </span>
            </div>
            <h3 className="text-xl font-semibold text-foreground mb-2">Real-time Sync</h3>
            <p className="text-muted-foreground text-sm leading-relaxed max-w-md">
              Co-edit project briefs, pitch decks, and technical specs simultaneously.
              Powered by CRDTs — no merge conflicts, ever. See your teammates' cursors live.
            </p>
            {/* Live indicator strip */}
            <div className="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-transparent via-synk/40 to-transparent" />
            <div className="mt-5 flex items-center gap-3">
              {["CS", "EC", "ME", "AI"].map((dept) => (
                <div key={dept} className="w-7 h-7 rounded-full bg-lendi/20 border border-lendi/30 text-[10px] font-bold text-lendi-light flex items-center justify-center">
                  {dept}
                </div>
              ))}
              <span className="text-xs text-muted-foreground font-medium">+18 collaborating now</span>
            </div>
          </motion.div>

          {/* Card 2 — AI Mentor */}
          <motion.div
            variants={itemVariants as any}
            className="glass-card card-hover-glow-cyan border-shine rounded-2xl p-6 group cursor-pointer relative overflow-hidden"
          >
            <div className="absolute inset-0 bg-gradient-to-b from-synk/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500" />
            <div className="w-10 h-10 rounded-xl bg-lendi/15 border border-lendi/25 flex items-center justify-center mb-4">
              <Brain size={20} className="text-lendi-light" />
            </div>
            <h3 className="text-lg font-semibold text-foreground mb-2">AI Mentor</h3>
            <p className="text-muted-foreground text-sm leading-relaxed">
              Smart pairing suggestions based on your skills, interests, and project history.
              The AI analyzes 40+ signals.
            </p>
            <div className="mt-4 p-3 rounded-xl bg-lendi/8 border border-lendi/15">
              <div className="flex items-center gap-2 text-xs text-lendi-light">
                <Sparkles size={12} />
                <span className="font-medium">Match found: 94% compatibility</span>
              </div>
              <div className="text-[11px] text-muted-foreground mt-1 font-medium">
                Riya S. · ML Expert · CSE Branch
              </div>
            </div>
          </motion.div>

          {/* Card 3 — Innovation Tracks */}
          <motion.div
            variants={itemVariants as any}
            className="glass-card card-hover-glow border-shine rounded-2xl p-6 group cursor-pointer"
          >
            <div className="w-10 h-10 rounded-xl bg-synk/15 border border-synk/25 flex items-center justify-center mb-4">
              <Map size={20} className="text-synk" />
            </div>
            <h3 className="text-lg font-semibold text-foreground mb-2">Innovation Tracks</h3>
            <p className="text-muted-foreground text-sm leading-relaxed">
              Curated challenge tracks across 12 domains — from IoT and AI to Sustainability
              and Blockchain.
            </p>
            <div className="mt-4 flex flex-wrap gap-1.5">
              {["IoT", "AI/ML", "Web3", "Green", "Cyber"].map((tag) => (
                <span key={tag} className="text-[11px] px-2 py-0.5 rounded-full bg-secondary/60 text-muted-foreground border border-border/60 font-bold">
                  {tag}
                </span>
              ))}
            </div>
          </motion.div>

          {/* Card 4 — Talent Matching */}
          <motion.div
            variants={itemVariants as any}
            className="glass-card card-hover-glow border-shine rounded-2xl p-6 group cursor-pointer"
          >
            <div className="w-10 h-10 rounded-xl bg-lendi/15 border border-lendi/25 flex items-center justify-center mb-4">
              <Users size={20} className="text-lendi-light" />
            </div>
            <h3 className="text-lg font-semibold text-foreground mb-2">Talent Matching</h3>
            <p className="text-muted-foreground text-sm leading-relaxed">
              Algorithm-driven team assembly. Balance skills, year of study, and specialization
              for optimal team composition.
            </p>
            <div className="mt-4 flex items-center gap-2">
              <div className="flex-1 h-1.5 bg-secondary rounded-full overflow-hidden">
                <div className="h-full w-[78%] bg-gradient-to-r from-lendi to-synk rounded-full" />
              </div>
              <span className="text-xs text-synk font-bold">78% match rate</span>
            </div>
          </motion.div>

          {/* Card 5 — Version Control (col-span-2) */}
          <motion.div
            variants={itemVariants as any}
            className="lg:col-span-2 glass-card card-hover-glow border-shine rounded-2xl p-6 group cursor-pointer relative overflow-hidden"
          >
            <div className="absolute right-6 top-0 bottom-0 w-[200px] opacity-[0.04] group-hover:opacity-[0.07] transition-opacity">
              <svg viewBox="0 0 200 200" className="w-full h-full" fill="none">
                <circle cx="100" cy="50" r="8" stroke="#004a99" strokeWidth="2" />
                <circle cx="60" cy="120" r="8" stroke="#004a99" strokeWidth="2" />
                <circle cx="140" cy="120" r="8" stroke="#004a99" strokeWidth="2" />
                <circle cx="100" cy="180" r="8" stroke="#06b6d4" strokeWidth="2" />
                <line x1="100" y1="58" x2="60" y2="112" stroke="#004a99" strokeWidth="1.5" strokeDasharray="4 4" />
                <line x1="100" y1="58" x2="140" y2="112" stroke="#004a99" strokeWidth="1.5" strokeDasharray="4 4" />
                <line x1="60" y1="128" x2="100" y2="172" stroke="#06b6d4" strokeWidth="1.5" strokeDasharray="4 4" />
                <line x1="140" y1="128" x2="100" y2="172" stroke="#06b6d4" strokeWidth="1.5" strokeDasharray="4 4" />
              </svg>
            </div>
            <div className="flex items-start justify-between mb-4">
              <div className="w-10 h-10 rounded-xl bg-lendi/15 border border-lendi/25 flex items-center justify-center">
                <GitMerge size={20} className="text-lendi-light" />
              </div>
              <div className="flex items-center gap-1.5 text-xs text-muted-foreground font-bold">
                <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse" />
                GitHub connected
              </div>
            </div>
            <h3 className="text-xl font-semibold text-foreground mb-2">Project Versioning</h3>
            <p className="text-muted-foreground text-sm leading-relaxed max-w-md">
              GitHub-native integration built into every project. Push commits, open PRs, and
              track contribution history — all visible inside IdeaSync.
            </p>
            <div className="mt-5 flex items-center gap-6 text-xs text-muted-foreground font-bold">
              <span className="flex items-center gap-1.5"><span className="w-1.5 h-1.5 rounded-full bg-green-500" /> 3 branches</span>
              <span className="flex items-center gap-1.5"><span className="w-1.5 h-1.5 rounded-full bg-lendi-light" /> 127 commits</span>
              <span className="flex items-center gap-1.5"><span className="w-1.5 h-1.5 rounded-full bg-synk" /> 14 open PRs</span>
            </div>
          </motion.div>

          {/* Card 6 — Analytics */}
          <motion.div
            variants={itemVariants as any}
            className="glass-card card-hover-glow border-shine rounded-2xl p-6 group cursor-pointer"
          >
            <div className="w-10 h-10 rounded-xl bg-synk/15 border border-synk/25 flex items-center justify-center mb-4">
              <BarChart3 size={20} className="text-synk" />
            </div>
            <h3 className="text-lg font-semibold text-foreground mb-2">Analytics Hub</h3>
            <p className="text-muted-foreground text-sm leading-relaxed">
              Monitor project velocity, team activity scores, and milestone completion
              with live dashboards.
            </p>
            {/* Mini bar chart */}
            <div className="mt-4 flex items-end gap-1.5 h-10">
              {[40, 65, 35, 80, 55, 90, 70].map((h, i) => (
                <div
                  key={i}
                  className="flex-1 rounded-sm transition-all duration-300 group-hover:opacity-90"
                  style={{
                    height: `${h}%`,
                    background: i === 5
                      ? "linear-gradient(to top, #004a99, #06b6d4)"
                      : "rgba(0,74,153,0.25)",
                  }}
                />
              ))}
            </div>
          </motion.div>
        </motion.div>
      </div>
    </section>
  )
}
