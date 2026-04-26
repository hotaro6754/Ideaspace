"use client"

import { useState } from "react"
import { motion } from "framer-motion"
import { ArrowRight, CheckCircle2, Mail, GraduationCap } from "lucide-react"

const benefits = [
  "Institutional Research Hub access",
  "GitHub & Industry tools integration",
  "Peer & Alumni mentorship matching",
  "Verified Innovation Resume",
]

export default function CtaSection() {
  const [email, setEmail] = useState("")
  const [submitted, setSubmitted] = useState(false)

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    if (email) setSubmitted(true)
  }

  return (
    <section id="join" className="relative py-24 lg:py-36 overflow-hidden bg-background">
      {/* Background Decor */}
      <div className="absolute inset-0 soft-grid opacity-20 pointer-events-none" />
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[400px] bg-lendi/5 rounded-full blur-[120px] pointer-events-none" />

      <div className="relative z-10 max-w-4xl mx-auto px-6 text-center">
        {/* Badge */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-secondary border border-border text-[10px] font-black text-muted-foreground mb-8 uppercase tracking-[0.2em]"
        >
          <GraduationCap size={14} className="text-lendi" />
          Institutional Access is Open
        </motion.div>

        {/* Heading */}
        <motion.h2
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-5xl md:text-7xl font-black text-foreground tracking-tighter leading-none mb-6"
        >
          Join the Hub. <br />
          <span className="gradient-text-hero">Elevate your Career.</span>
        </motion.h2>

        <motion.p
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="text-lg text-muted-foreground max-w-xl mx-auto font-medium leading-relaxed mb-12"
        >
          Secure your workspace with your institutional credentials.
          The future of Lendi innovation starts here.
        </motion.p>

        {/* Benefits */}
        <div className="flex flex-wrap items-center justify-center gap-x-8 gap-y-3 mb-12">
          {benefits.map((benefit) => (
            <div key={benefit} className="flex items-center gap-2 text-sm font-bold text-foreground">
              <CheckCircle2 size={16} className="text-lendi" />
              {benefit}
            </div>
          ))}
        </div>

        {/* Email form */}
        <div className="max-w-md mx-auto">
          {submitted ? (
            <motion.div
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              className="inline-flex items-center gap-3 px-8 py-4 rounded-2xl bg-lendi/10 border border-lendi/20 text-lendi font-bold text-sm uppercase tracking-widest"
            >
              <CheckCircle2 size={20} />
              Verification link sent to your email.
            </motion.div>
          ) : (
            <form onSubmit={handleSubmit} className="flex flex-col sm:flex-row items-center justify-center gap-3">
              <div className="relative flex-1 w-full">
                <Mail size={16} className="absolute left-4 top-1/2 -translate-y-1/2 text-muted-foreground pointer-events-none" />
                <input
                  type="email"
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  placeholder="rollno@lendi.edu.in"
                  required
                  className="w-full pl-12 pr-4 py-4 rounded-2xl bg-card border border-border text-sm text-foreground placeholder:text-muted-foreground focus:outline-none focus:border-lendi focus:ring-1 focus:ring-lendi transition-all font-bold"
                />
              </div>
              <button
                type="submit"
                className="w-full sm:w-auto flex items-center justify-center gap-2 px-8 py-4 rounded-2xl bg-lendi text-white font-bold text-sm glow-btn whitespace-nowrap shadow-xl shadow-lendi/20"
              >
                Access Now
                <ArrowRight size={18} />
              </button>
            </form>
          )}
        </div>

        <p className="mt-8 text-[10px] font-black uppercase tracking-[0.4em] text-muted-foreground/30">
          Exclusive for LIET Students, Faculty & Alumni.
        </p>
      </div>
    </section>
  )
}
