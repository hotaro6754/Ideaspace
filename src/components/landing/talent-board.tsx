"use client"

import { motion } from "framer-motion"
import { Flame, Star, Trophy, Zap, Shield, Code2, Cpu, Globe, Lock } from "lucide-react"

const containerVariants = {
  hidden: {},
  visible: { transition: { staggerChildren: 0.07 } },
}

const rowVariants = {
  hidden: { opacity: 0, x: -20 },
  visible: { opacity: 1, x: 0, transition: { duration: 0.5, ease: "easeOut" } },
}

type Builder = {
  rank: number
  initials: string
  name: string
  dept: string
  year: string
  xp: number
  maxXp: number
  level: number
  streak: number
  badges: string[]
  gradient: string
}

const builders: Builder[] = [
  {
    rank: 1,
    initials: "AK",
    name: "Arjun Kumar",
    dept: "CSE",
    year: "3rd Year",
    xp: 4820,
    maxXp: 5000,
    level: 12,
    streak: 14,
    badges: ["gold", "fire", "code"],
    gradient: "from-yellow-500/30 to-orange-500/20",
  },
  {
    rank: 2,
    initials: "PS",
    name: "Priya Sharma",
    dept: "ECE",
    year: "4th Year",
    xp: 4310,
    maxXp: 5000,
    level: 11,
    streak: 9,
    badges: ["silver", "shield", "globe"],
    gradient: "from-slate-400/20 to-slate-500/10",
  },
  {
    rank: 3,
    initials: "RV",
    name: "Rahul Verma",
    dept: "CSE",
    year: "3rd Year",
    xp: 3950,
    maxXp: 5000,
    level: 10,
    streak: 7,
    badges: ["bronze", "cpu", "code"],
    gradient: "from-amber-700/20 to-amber-800/10",
  },
  {
    rank: 4,
    initials: "SM",
    name: "Sneha Mishra",
    dept: "IT",
    year: "2nd Year",
    xp: 3400,
    maxXp: 5000,
    level: 9,
    streak: 11,
    badges: ["fire", "globe"],
    gradient: "from-lendi/15 to-transparent",
  },
  {
    rank: 5,
    initials: "KR",
    name: "Kiran Reddy",
    dept: "MECH",
    year: "4th Year",
    xp: 2980,
    maxXp: 5000,
    level: 8,
    streak: 5,
    badges: ["shield", "cpu"],
    gradient: "from-synk/10 to-transparent",
  },
]

const achievementBadges = [
  { icon: Trophy, label: "Top Innovator", color: "text-yellow-400", bg: "bg-yellow-400/10 border-yellow-400/25" },
  { icon: Flame, label: "7-Day Streak", color: "text-orange-400", bg: "bg-orange-400/10 border-orange-400/25" },
  { icon: Code2, label: "100 Commits", color: "text-lendi-light", bg: "bg-lendi/10 border-lendi/25" },
  { icon: Cpu, label: "AI Builder", color: "text-synk", bg: "bg-synk/10 border-synk/25" },
  { icon: Shield, label: "Security Pro", color: "text-red-400", bg: "bg-red-400/10 border-red-400/25" },
  { icon: Globe, label: "Open Source", color: "text-emerald-400", bg: "bg-emerald-400/10 border-emerald-400/25" },
  { icon: Star, label: "5-Star Project", color: "text-purple-400", bg: "bg-purple-400/10 border-purple-400/25" },
  { icon: Lock, label: "Hackathon Win", color: "text-pink-400", bg: "bg-pink-400/10 border-pink-400/25" },
]

const rankColors = ["text-yellow-400", "text-slate-400", "text-amber-600"]
const rankShadows = [
  "shadow-[0_0_20px_rgba(234,179,8,0.15)]",
  "shadow-[0_0_20px_rgba(148,163,184,0.1)]",
  "shadow-[0_0_20px_rgba(180,83,9,0.1)]",
]

function BadgeIcon({ badge }: { badge: string }) {
  const map: Record<string, React.ReactNode> = {
    gold: <Trophy size={11} className="text-yellow-400" />,
    silver: <Trophy size={11} className="text-slate-400" />,
    bronze: <Trophy size={11} className="text-amber-600" />,
    fire: <Flame size={11} className="text-orange-400" />,
    code: <Code2 size={11} className="text-lendi-light" />,
    shield: <Shield size={11} className="text-red-400" />,
    cpu: <Cpu size={11} className="text-synk" />,
    globe: <Globe size={11} className="text-emerald-400" />,
  }
  return (
    <div className="w-5 h-5 rounded-full bg-secondary/60 border border-border/60 flex items-center justify-center">
      {map[badge]}
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

export default function TalentBoard() {
  return (
    <section id="talent" className="relative py-24 lg:py-32 overflow-hidden">
      <div className="absolute bottom-0 right-0 w-[500px] h-[500px] bg-lendi/6 rounded-full blur-[120px] pointer-events-none" />

      <div className="relative z-10 max-w-7xl mx-auto px-6">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="mb-14"
        >
          <SectionLabel text="Talent Board" />
          <h2 className="mt-4 text-4xl sm:text-5xl font-bold text-foreground text-balance leading-tight font-sans">
            Gamified Talent Board.
          </h2>
          <p className="mt-4 text-muted-foreground text-lg max-w-xl text-pretty font-medium">
            Rise through the ranks. Earn XP by contributing code, solving challenges, and
            collaborating. Build your legacy at LIET.
          </p>
        </motion.div>

        <div className="grid lg:grid-cols-3 gap-6">
          {/* Leaderboard — 2 columns */}
          <motion.div
            initial={{ opacity: 0, y: 24 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="lg:col-span-2 glass-card rounded-2xl overflow-hidden border border-border/60"
          >
            {/* Header */}
            <div className="flex items-center justify-between px-6 py-4 border-b border-border/50">
              <div className="flex items-center gap-2 font-bold uppercase tracking-widest text-xs">
                <Trophy size={16} className="text-yellow-400" />
                <span className="text-foreground">Top Builders — May 2025</span>
              </div>
              <div className="flex items-center gap-1.5 text-xs text-muted-foreground font-bold uppercase tracking-widest">
                <span className="w-1.5 h-1.5 rounded-full bg-synk animate-pulse" />
                Live ranking
              </div>
            </div>

            {/* Rows */}
            <motion.div
              variants={containerVariants}
              initial="hidden"
              whileInView="visible"
              viewport={{ once: true }}
              className="divide-y divide-border/30"
            >
              {builders.map((builder) => (
                <motion.div
                  key={builder.rank}
                  variants={rowVariants as any}
                  className={`flex items-center gap-4 px-6 py-4 bg-gradient-to-r ${builder.gradient} hover:bg-white/[0.02] transition-colors group cursor-pointer`}
                >
                  {/* Rank */}
                  <div className={`w-7 text-center text-sm font-bold ${builder.rank <= 3 ? rankColors[builder.rank - 1] : "text-muted-foreground"} ${builder.rank <= 3 ? rankShadows[builder.rank - 1] : ""}`}>
                    {builder.rank <= 3 ? (
                      <span className="text-base">{["1", "2", "3"][builder.rank - 1]}</span>
                    ) : (
                      builder.rank
                    )}
                  </div>

                  {/* Avatar */}
                  <div className={`w-9 h-9 rounded-full bg-gradient-to-br from-lendi to-synk flex items-center justify-center text-xs font-bold text-white shrink-0`}>
                    {builder.initials}
                  </div>

                  {/* Info */}
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2">
                      <span className="text-sm font-bold text-foreground truncate">{builder.name}</span>
                      <span className="text-[11px] text-muted-foreground shrink-0 font-bold">{builder.dept} · {builder.year}</span>
                    </div>
                    <div className="flex items-center gap-2 mt-1">
                      <div className="flex-1 max-w-[120px] h-1 bg-secondary rounded-full overflow-hidden">
                        <div
                          className="h-full bg-gradient-to-r from-lendi to-synk rounded-full transition-all duration-1000"
                          style={{ width: `${(builder.xp / builder.maxXp) * 100}%` }}
                        />
                      </div>
                      <span className="text-[11px] text-muted-foreground font-bold">{builder.xp.toLocaleString()} XP</span>
                    </div>
                  </div>

                  {/* Level + streak + badges */}
                  <div className="flex items-center gap-3 shrink-0 font-bold uppercase tracking-widest">
                    <div className="flex items-center gap-1 text-[10px]">
                      <Zap size={11} className="text-synk" />
                      <span className="text-synk font-black">Lv.{builder.level}</span>
                    </div>
                    <div className="flex items-center gap-1 text-[10px] text-orange-400">
                      <Flame size={11} />
                      <span>{builder.streak}d</span>
                    </div>
                    <div className="hidden sm:flex items-center gap-1">
                      {builder.badges.map((b) => (
                        <BadgeIcon key={b} badge={b} />
                      ))}
                    </div>
                  </div>
                </motion.div>
              ))}
            </motion.div>

            <div className="px-6 py-3 border-t border-border/40 text-center">
              <button className="text-xs text-muted-foreground hover:text-synk transition-colors font-bold uppercase tracking-widest">
                View full leaderboard (2,400+ students) →
              </button>
            </div>
          </motion.div>

          {/* Achievement Badges */}
          <motion.div
            initial={{ opacity: 0, y: 24 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6, delay: 0.15 }}
            className="glass-card rounded-2xl p-6 border border-border/60"
          >
            <div className="flex items-center gap-2 mb-5 font-bold uppercase tracking-widest text-xs">
              <Star size={15} className="text-yellow-400" />
              <span className="text-foreground">Achievement Badges</span>
            </div>
            <p className="text-xs text-muted-foreground mb-5 leading-relaxed font-medium">
              Collect badges by completing milestones, contributing consistently, and winning
              hackathons.
            </p>

            <div className="grid grid-cols-2 gap-3">
              {achievementBadges.map((badge, i) => (
                <motion.div
                  key={badge.label}
                  initial={{ opacity: 0, scale: 0.85 }}
                  whileInView={{ opacity: 1, scale: 1 }}
                  viewport={{ once: true }}
                  transition={{ delay: 0.1 + i * 0.05, duration: 0.4 }}
                  className={`flex flex-col items-center gap-2 p-3 rounded-xl border ${badge.bg} cursor-pointer hover:scale-105 transition-transform duration-200`}
                >
                  <badge.icon size={20} className={badge.color} />
                  <span className="text-[11px] font-bold text-center text-foreground/80 leading-tight">
                    {badge.label}
                  </span>
                </motion.div>
              ))}
            </div>

            <div className="mt-5 p-3 rounded-xl bg-lendi/8 border border-lendi/20">
              <div className="text-xs font-bold text-lendi-light mb-1 uppercase tracking-widest">Your next badge</div>
              <div className="flex items-center gap-2">
                <Code2 size={14} className="text-lendi-light" />
                <div className="flex-1">
                  <div className="text-[11px] text-foreground font-bold">100 Commits</div>
                  <div className="flex items-center gap-1.5 mt-1">
                    <div className="flex-1 h-1 bg-secondary rounded-full overflow-hidden">
                      <div className="h-full w-[67%] bg-lendi rounded-full" />
                    </div>
                    <span className="text-[10px] text-muted-foreground font-bold">67/100</span>
                  </div>
                </div>
              </div>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  )
}
