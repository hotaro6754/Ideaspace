"use client"

import { useState } from "react"
import { motion } from "framer-motion"
import { ArrowRight, Zap, CheckCircle2, Mail } from "lucide-react"

const perks = [
  "Access all 48 open problem statements",
  "GitHub & Supabase auto-connected",
  "AI-powered team matching",
  "Track your XP from day one",
]

export default function CtaSection() {
  const [email, setEmail] = useState("")
  const [submitted, setSubmitted] = useState(false)

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    if (email) setSubmitted(true)
  }

  return (
    <section id="join" className="relative py-24 lg:py-36 overflow-hidden">
      {/* Background */}
      <div className="absolute inset-0 bg-gradient-to-b from-background via-lendi/5 to-background" />
      <div className="absolute inset-0 blueprint-grid opacity-40" />

      {/* Large glow center */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[500px] bg-lendi/10 rounded-full blur-[150px] pointer-events-none" />
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[300px] bg-synk/5 rounded-full blur-[100px] pointer-events-none" />

      {/* Decorative rings */}
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] rounded-full border border-lendi/8 pointer-events-none" />
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[900px] h-[900px] rounded-full border border-lendi/5 pointer-events-none" />
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[1200px] h-[1200px] rounded-full border border-lendi/[0.03] pointer-events-none" />

      <div className="relative z-10 max-w-4xl mx-auto px-6 text-center">
        {/* Badge */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5 }}
          className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-lendi/30 bg-lendi/10 text-xs font-bold text-lendi-light mb-8 uppercase tracking-widest"
        >
          <Zap size={12} className="text-synk fill-synk" />
          Early access is open — 412 students have joined
        </motion.div>

        {/* Heading */}
        <motion.h2
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.75, delay: 0.1, ease: "easeOut" }}
          className="text-5xl sm:text-6xl lg:text-7xl font-bold text-foreground text-balance leading-tight mb-5 font-sans"
        >
          Join the
          <br />
          <span className="gradient-text-hero">Forge.</span>
        </motion.h2>

        <motion.p
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.25 }}
          className="text-lg text-muted-foreground max-w-xl mx-auto text-pretty leading-relaxed mb-10 font-medium"
        >
          Start building today with your LIET credentials.
          The next big idea is one sync away.
        </motion.p>

        {/* Perks */}
        <motion.div
          initial={{ opacity: 0, y: 16 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5, delay: 0.35 }}
          className="flex flex-wrap items-center justify-center gap-x-6 gap-y-2 mb-10"
        >
          {perks.map((perk) => (
            <div key={perk} className="flex items-center gap-1.5 text-sm text-muted-foreground font-bold">
              <CheckCircle2 size={13} className="text-synk shrink-0" />
              {perk}
            </div>
          ))}
        </motion.div>

        {/* Email form */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6, delay: 0.45 }}
        >
          {submitted ? (
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              className="inline-flex items-center gap-3 px-8 py-4 rounded-2xl border border-synk/30 bg-synk/10 text-synk font-bold uppercase tracking-widest"
            >
              <CheckCircle2 size={20} />
              Protocol Initiated. Check your email.
            </motion.div>
          ) : (
            <form onSubmit={handleSubmit} className="flex flex-col sm:flex-row items-center justify-center gap-3 max-w-md mx-auto">
              <div className="relative flex-1 w-full">
                <Mail size={15} className="absolute left-3.5 top-1/2 -translate-y-1/2 text-muted-foreground pointer-events-none" />
                <input
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="rollno@lendi.org"
                  required
                  className="w-full pl-10 pr-4 py-3.5 rounded-xl bg-secondary/60 border border-border text-sm text-foreground placeholder:text-muted-foreground/60 focus:outline-none focus:border-lendi/50 focus:ring-1 focus:ring-lendi/30 transition-all font-bold"
                />
              </div>
              <button
                type="submit"
                className="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-lendi text-white font-bold text-sm glow-btn-primary whitespace-nowrap uppercase tracking-widest"
              >
                Access Forge
                <ArrowRight size={15} />
              </button>
            </form>          )}
        </motion.div>

        <motion.p
          initial={{ opacity: 0 }}
          whileInView={{ opacity: 1 }}
          viewport={{ once: true }}
          transition={{ delay: 0.7 }}
          className="mt-4 text-[10px] font-black uppercase tracking-[0.4em] text-muted-foreground/40"
        >
          Free for all LIET students. Domain Restricted.
        </motion.p>
      </div>
    </section>
  )
}
