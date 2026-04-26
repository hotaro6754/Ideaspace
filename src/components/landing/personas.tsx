"use client"

import { motion } from "framer-motion"
import { GraduationCap, Briefcase, Award } from "lucide-react"

const personas = [
  {
    title: "For Students",
    icon: GraduationCap,
    description: "Launch your career with hands-on projects, industry challenges, and peer collaboration.",
    points: [
      "Find project partners across departments",
      "Solve real-world challenges from faculty",
      "Build a verified innovation portfolio"
    ],
    color: "lendi"
  },
  {
    title: "For Faculty",
    icon: Award,
    description: "Bridge the gap between curriculum and innovation by guiding student research.",
    points: [
      "Post project problem statements",
      "Track and mentor student groups",
      "Manage academic innovation bails"
    ],
    color: "synk"
  },
  {
    title: "For Alumni",
    icon: Briefcase,
    description: "Give back to the Lendi community by mentoring the next generation of builders.",
    points: [
      "Mentor high-potential student projects",
      "Post internship and job opportunities",
      "Stay connected with Lendi's growth"
    ],
    color: "indigo-500"
  }
]

export default function Personas() {
  return (
    <section className="py-24 relative overflow-hidden bg-background">
      <div className="max-w-7xl mx-auto px-6">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-5xl font-black text-foreground mb-4">
            A Platform for Every Stakeholder
          </h2>
          <p className="text-muted-foreground font-medium max-w-2xl mx-auto">
            IdeaSync is built to serve the entire Lendi ecosystem, creating a
            synergistic environment for innovation.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {personas.map((p, i) => (
            <motion.div
              key={p.title}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: i * 0.1 }}
              className="p-8 rounded-3xl border border-border bg-card/50 card-hover flex flex-col h-full"
            >
              <div className={`w-14 h-14 rounded-2xl bg-${p.color}/10 flex items-center justify-center text-${p.color} mb-6`}>
                <p.icon size={28} />
              </div>
              <h3 className="text-2xl font-bold mb-4">{p.title}</h3>
              <p className="text-muted-foreground font-medium mb-8 flex-1">
                {p.description}
              </p>
              <ul className="space-y-3">
                {p.points.map((point) => (
                  <li key={point} className="flex items-center gap-3 text-sm font-semibold text-foreground">
                    <div className={`w-1.5 h-1.5 rounded-full bg-${p.color}`} />
                    {point}
                  </li>
                ))}
              </ul>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  )
}
