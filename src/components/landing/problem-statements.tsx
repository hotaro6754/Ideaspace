"use client"

import { motion } from "framer-motion"
import { ArrowUpRight, Users, Flame } from "lucide-react"

const containerVariants = {
  hidden: {},
  visible: { transition: { staggerChildren: 0.09 } },
}

const itemVariants = {
  hidden: { opacity: 0, y: 28 },
  visible: { opacity: 1, y: 0, transition: { duration: 0.55, ease: "easeOut" } },
}

type Problem = {
  domain: string
  domainColor: string
  title: string
  description: string
  teams: number
  difficulty: number
  hot: boolean
}

const problems: Problem[] = [
  {
    domain: "IoT",
    domainColor: "bg-synk/15 text-synk border-synk/25",
    title: "Smart Traffic Management System",
    description:
      "Design an adaptive traffic control system using edge computing and ML at LIET campus gates to reduce congestion by 40%.",
    teams: 12,
    difficulty: 4,
    hot: true,
  },
  {
    domain: "Blockchain",
    domainColor: "bg-purple-500/15 text-purple-300 border-purple-500/25",
    title: "Immutable Academic Credentials",
    description:
      "Build a decentralized ledger for degree and certificate verification, eliminating document fraud campus-wide.",
    teams: 8,
    difficulty: 4,
    hot: false,
  },
  {
    domain: "ML / AI",
    domainColor: "bg-lendi/15 text-lendi-light border-lendi/25",
    title: "AI-Powered Attendance System",
    description:
      "Face recognition-based smart attendance with real-time anomaly detection and automated faculty reports.",
    teams: 15,
    difficulty: 3,
    hot: true,
  },
  {
    domain: "Sustainability",
    domainColor: "bg-emerald-500/15 text-emerald-400 border-emerald-500/25",
    title: "Campus Energy Optimizer",
    description:
      "Real-time energy monitoring across all LIET buildings with AI-driven consumption forecasting and alerts.",
    teams: 6,
    difficulty: 3,
    hot: false,
  },
  {
    domain: "Cybersecurity",
    domainColor: "bg-red-500/15 text-red-400 border-red-500/25",
    title: "Secure Research Data Gateway",
    description:
      "End-to-end encrypted platform for cross-department research data sharing with role-based access control and audit trails.",
    teams: 5,
    difficulty: 5,
    hot: false,
  },
]

function DifficultyDots({ level }: { level: number }) {
  return (
    <div className="flex items-center gap-1">
      {Array.from({ length: 5 }).map((_, i) => (
        <div
          key={i}
          className={`w-1.5 h-1.5 rounded-full transition-colors ${
            i < level ? "bg-synk" : "bg-border"
          }`}
        />
      ))}
    </div>
  )
}

function SectionLabel({ text }: { text: string }) {
  return (
    <div className="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-synk">
      <span className="w-4 h-px bg-synk" />
      {text}
    </div>
  )
}

export default function ProblemStatements() {
  return (
    <section id="challenges" className="relative py-24 lg:py-32 overflow-hidden">
      <div className="absolute top-0 left-1/4 w-[600px] h-[400px] bg-synk/5 rounded-full blur-[120px] pointer-events-none" />

      <div className="relative z-10 max-w-7xl mx-auto px-6">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="mb-14"
        >
          <SectionLabel text="Open Challenges" />
          <h2 className="mt-4 text-4xl sm:text-5xl font-bold text-foreground text-balance leading-tight font-sans">
            Real problems.
            <br />
            <span className="gradient-text">Real impact.</span>
          </h2>
          <p className="mt-4 text-muted-foreground text-lg max-w-lg text-pretty font-medium">
            Tackle industry-grade challenges issued by LIET faculty and partner
            organizations. Solve it. Ship it. Own it.
          </p>
        </motion.div>

        {/* Bento grid */}
        <motion.div
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-80px" }}
          className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
        >
          {/* Card 0 — wide */}
          <motion.div
            variants={itemVariants as any}
            className="lg:col-span-2 glass-card card-hover-glow border-shine rounded-2xl p-6 group cursor-pointer relative overflow-hidden"
          >
            <ProblemCard problem={problems[0]} large />
          </motion.div>

          {/* Card 1 */}
          <motion.div
            variants={itemVariants as any}
            className="glass-card card-hover-glow border-shine rounded-2xl p-6 group cursor-pointer"
          >
            <ProblemCard problem={problems[1]} />
          </motion.div>

          {/* Card 2 */}
          <motion.div
            variants={itemVariants as any}
            className="glass-card card-hover-glow border-shine rounded-2xl p-6 group cursor-pointer"
          >
            <ProblemCard problem={problems[2]} />
          </motion.div>

          {/* Card 3 */}
          <motion.div
            variants={itemVariants as any}
            className="glass-card card-hover-glow border-shine rounded-2xl p-6 group cursor-pointer"
          >
            <ProblemCard problem={problems[3]} />
          </motion.div>

          {/* Card 4 — wide */}
          <motion.div
            variants={itemVariants as any}
            className="glass-card card-hover-glow border-shine rounded-2xl p-6 group cursor-pointer"
          >
            <ProblemCard problem={problems[4]} />
          </motion.div>
        </motion.div>

        {/* View all CTA */}
        <motion.div
          initial={{ opacity: 0, y: 16 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5, delay: 0.3 }}
          className="mt-10 text-center"
        >
          <button className="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-border text-sm font-bold text-muted-foreground hover:border-lendi/40 hover:text-foreground hover:bg-lendi/8 transition-all duration-300">
            View all 48 challenges
            <ArrowUpRight size={15} />
          </button>
        </motion.div>
      </div>
    </section>
  )
}

function ProblemCard({ problem, large = false }: { problem: Problem; large?: boolean }) {
  return (
    <>
      <div className="flex items-start justify-between mb-4">
        <span className={`text-xs font-semibold px-2.5 py-1 rounded-full border ${problem.domainColor}`}>
          {problem.domain}
        </span>
        <div className="flex items-center gap-2">
          {problem.hot && (
            <div className="flex items-center gap-1 text-orange-400 text-xs font-bold uppercase tracking-widest">
              <Flame size={12} />
              Hot
            </div>
          )}
          <div className="w-7 h-7 rounded-full bg-secondary/60 border border-border/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <ArrowUpRight size={13} className="text-muted-foreground" />
          </div>
        </div>
      </div>
      <h3 className={`font-bold text-foreground mb-2 text-balance leading-snug font-sans ${large ? "text-xl" : "text-base"}`}>
        {problem.title}
      </h3>
      <p className="text-muted-foreground text-sm leading-relaxed text-pretty line-clamp-3 font-medium">
        {problem.description}
      </p>
      <div className="mt-5 flex items-center justify-between">
        <div className="flex items-center gap-1.5 text-xs text-muted-foreground font-bold">
          <Users size={12} />
          <span>{problem.teams} teams working</span>
        </div>
        <DifficultyDots level={problem.difficulty} />
      </div>
    </>
  )
}
