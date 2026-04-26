"use client"

import { useEffect, useRef } from "react"
import { motion } from "framer-motion"
import { ArrowRight, ChevronRight, GraduationCap, Building2, Users2, Sparkles, ShieldCheck } from "lucide-react"
import Link from "next/link"
import gsap from "gsap"

const stats = [
  { value: "250+", label: "Research Projects", icon: Sparkles },
  { value: "4,500+", label: "Campus Members", icon: Users2 },
  { value: "120+", label: "Academic Awards", icon: GraduationCap },
]

export default function Hero() {
  const containerRef = useRef<HTMLDivElement>(null)
  const titleRef = useRef<HTMLDivElement>(null)
  const statsRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    if (!containerRef.current) return

    const ctx = gsap.context(() => {
      const tl = gsap.timeline({ defaults: { ease: "power4.out" } });

      tl.from(".hero-badge", {
        y: 20,
        opacity: 0,
        duration: 0.8
      })
      .from(".hero-title-line", {
        y: 80,
        opacity: 0,
        duration: 1.2,
        stagger: 0.15
      }, "-=0.4")
      .from(".hero-subtext", {
        y: 30,
        opacity: 0,
        duration: 1
      }, "-=0.8")
      .from(".hero-cta", {
        y: 20,
        opacity: 0,
        duration: 0.8,
        stagger: 0.1
      }, "-=0.6")
      .from(".hero-stat-card", {
        y: 40,
        opacity: 0,
        scale: 0.95,
        duration: 1,
        stagger: 0.1
      }, "-=0.6")
    }, containerRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={containerRef} className="relative min-h-screen flex items-center justify-center pt-32 pb-20 overflow-hidden bg-background soft-grid">
      {/* Background Orbs - Subtle and Professional */}
      <div className="absolute top-[10%] left-[-5%] w-[40vw] h-[40vw] bg-lendi-blue/5 rounded-full blur-[120px] pointer-events-none" />
      <div className="absolute bottom-[10%] right-[-5%] w-[35vw] h-[35vw] bg-synk/5 rounded-full blur-[100px] pointer-events-none" />

      <div className="relative z-10 max-w-7xl mx-auto px-6 flex flex-col items-center text-center">
        {/* Institutional Badge */}
        <div className="hero-badge inline-flex items-center gap-2.5 px-5 py-2 rounded-full bg-white border border-border shadow-premium text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-12">
          <ShieldCheck size={14} className="text-lendi-blue" />
          Lendi Institute of Engineering & Technology
          <ChevronRight size={12} className="opacity-30" />
        </div>

        {/* Massive Typographic Headline */}
        <div ref={titleRef} className="space-y-1 mb-10 max-w-5xl">
          <h1 className="hero-title-line text-5xl md:text-8xl font-black tracking-tight-inst leading-[0.95] text-foreground">
            Academic Excellence.
          </h1>
          <h1 className="hero-title-line text-5xl md:text-8xl font-black tracking-tight-inst leading-[0.95] gradient-text-hero">
            Powered by Innovation.
          </h1>
        </div>

        {/* Professional Subtext */}
        <p className="hero-subtext text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto mb-12 font-medium leading-relaxed text-balance">
          The comprehensive institutional hub for LIET. Connecting students, faculty, and alumni through a unified ecosystem for research, challenges, and professional growth.
        </p>

        {/* CTAs */}
        <div className="flex flex-wrap justify-center gap-6 mb-24 w-full">
          <Link href="/login" className="hero-cta">
            <motion.div
              whileHover={{ scale: 1.05 }}
              whileTap={{ scale: 0.98 }}
              className="flex items-center gap-3 px-10 py-5 bg-lendi-blue text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-lendi"
            >
              Access the Hub
              <ArrowRight size={18} />
            </motion.div>
          </Link>
          <Link href="/login" className="hero-cta">
            <motion.div
              whileHover={{ scale: 1.05, backgroundColor: "rgba(0, 74, 153, 0.05)" }}
              whileTap={{ scale: 0.98 }}
              className="flex items-center gap-3 px-10 py-5 bg-white border-2 border-border rounded-2xl font-black text-xs uppercase tracking-widest text-lendi-blue transition-colors"
            >
              Institutional Roadmap
            </motion.div>
          </Link>
        </div>

        {/* Professional Stats Grid */}
        <div ref={statsRef} className="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-5xl mx-auto">
          {stats.map((stat) => (
            <div key={stat.label} className="hero-stat-card p-8 rounded-3xl border border-border bg-card shadow-premium flex flex-col gap-4 text-left group hover:border-lendi-blue transition-colors">
              <div className="w-12 h-12 rounded-2xl bg-secondary flex items-center justify-center text-lendi-blue group-hover:bg-lendi-blue group-hover:text-white transition-all duration-500 shadow-sm">
                <stat.icon size={24} />
              </div>
              <div>
                <div className="text-4xl font-black text-foreground tracking-tight mb-1">{stat.value}</div>
                <div className="text-[10px] font-black text-muted-foreground uppercase tracking-[0.2em]">{stat.label}</div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
