"use client"

import { motion } from "framer-motion"
import { GraduationCap, Briefcase, Award, MoveRight, ArrowRight } from "lucide-react"
import Link from "next/link"

const personas = [
  {
    id: "student",
    title: "The Innovator",
    role: "Students",
    icon: GraduationCap,
    description: "Convert academic excellence into industrial-grade products. Build a verified history of contributions and team leadership.",
    highlights: ["Cross-dept teaming", "Industry-led bounties", "Real-time XP tracking"],
    cta: "Join the Forge",
    color: "from-lendi to-blue-600",
    shadow: "shadow-lendi/20"
  },
  {
    id: "faculty",
    title: "The Steward",
    role: "Faculty",
    icon: Award,
    description: "Guide the next generation of researchers. Post institutional challenges and monitor progress across departments.",
    highlights: ["Manage research tracks", "Direct student mentorship", "Academic gatekeeping"],
    cta: "Admin Console",
    color: "from-synk to-cyan-600",
    shadow: "shadow-synk/20"
  },
  {
    id: "alumni",
    title: "The Guardian",
    role: "Alumni",
    icon: Briefcase,
    description: "Bridge the gap between Lendi and the corporate world. Hire, mentor, and sponsor innovation from the outside in.",
    highlights: ["Direct hiring pipeline", "Strategic mentorship", "Corporate sponsorship"],
    cta: "Network Access",
    color: "from-indigo-600 to-purple-600",
    shadow: "shadow-indigo-500/20"
  }
]

export default function Personas() {
  return (
    <section className="py-32 relative overflow-hidden bg-background mesh-gradient">
      <div className="max-w-7xl mx-auto px-6">
        <div className="flex flex-col md:flex-row md:items-end justify-between mb-20 gap-8">
          <div className="max-w-2xl">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lendi/10 border border-lendi/20 text-[10px] font-black text-lendi uppercase tracking-widest mb-6">
              The Ecosystem
            </div>
            <h2 className="text-5xl md:text-7xl font-black text-foreground tracking-tighter leading-[0.9]">
              Unified for <br />
              <span className="text-gradient-sentinel">Synergistic Progress.</span>
            </h2>
          </div>
          <p className="text-lg text-muted-foreground font-medium max-w-sm mb-2">
            A single infrastructure designed to connect every node in the Lendi institutional network.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {personas.map((p, i) => (
            <motion.div
              key={p.id}
              initial={{ opacity: 0, y: 30 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: i * 0.1, duration: 0.8, ease: [0.2, 0, 0, 1] }}
              className={`p-10 rounded-[40px] border border-border bg-card/50 backdrop-blur-xl card-hover flex flex-col h-full group relative overflow-hidden`}
            >
              {/* Top Accent Line */}
              <div className={`absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r ${p.color}`} />

              <div className="flex items-center justify-between mb-10">
                <div className="w-16 h-16 rounded-[24px] bg-secondary border border-border flex items-center justify-center text-foreground group-hover:scale-110 transition-transform duration-500">
                  <p.icon size={32} />
                </div>
                <div className="text-right">
                  <span className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground block mb-1">Sector</span>
                  <span className="text-sm font-black text-foreground">{p.role}</span>
                </div>
              </div>

              <h3 className="text-3xl font-black mb-4 tracking-tight">{p.title}</h3>
              <p className="text-muted-foreground font-medium text-lg leading-snug mb-10 flex-1">
                {p.description}
              </p>

              <div className="space-y-4 mb-10">
                {p.highlights.map((h) => (
                  <div key={h} className="flex items-center gap-3">
                    <div className={`w-2 h-2 rounded-full bg-gradient-to-r ${p.color}`} />
                    <span className="text-xs font-bold text-foreground uppercase tracking-widest">{h}</span>
                  </div>
                ))}
              </div>

              <Link
                href="/login"
                className={`flex items-center justify-center gap-3 w-full py-5 bg-foreground text-background dark:bg-white dark:text-black rounded-2xl font-black text-sm uppercase tracking-widest hover:scale-[1.02] active:scale-95 transition-all ${p.shadow}`}
              >
                {p.cta}
                <ArrowRight size={18} />
              </Link>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  )
}
