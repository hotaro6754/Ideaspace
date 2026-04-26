"use client"

import { useEffect, useRef } from "react"
import { motion } from "framer-motion"
import { ArrowRight, ChevronRight, GraduationCap, Building2, Users2, Sparkles } from "lucide-react"
import Link from "next/link"
import gsap from "gsap"

const stats = [
  { value: "150+", label: "Research Projects", icon: Sparkles },
  { value: "4,000+", label: "Campus Members", icon: Users2 },
  { value: "85+", label: "Industry Partners", icon: Building2 },
]

export default function Hero() {
  const containerRef = useRef<HTMLDivElement>(null)
  const titleRef = useRef<HTMLDivElement>(null)
  const statsRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    if (!containerRef.current) return

    const ctx = gsap.context(() => {
      gsap.from(titleRef.current, {
        y: 60,
        opacity: 0,
        duration: 1.2,
        ease: "power4.out",
        stagger: 0.2
      })

      gsap.from(".hero-element", {
        y: 40,
        opacity: 0,
        duration: 1,
        ease: "power3.out",
        stagger: 0.15,
        delay: 0.5
      })

      if (statsRef.current) {
        gsap.from(Array.from(statsRef.current.children), {
          scale: 0.9,
          opacity: 0,
          duration: 0.8,
          ease: "back.out(1.7)",
          stagger: 0.1,
          delay: 1
        })
      }
    }, containerRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={containerRef} className="relative min-h-screen flex items-center justify-center pt-24 pb-16 overflow-hidden">
      {/* Background */}
      <div className="absolute inset-0 soft-grid opacity-40 pointer-events-none" />
      <div className="absolute top-[10%] left-[10%] w-[500px] h-[500px] bg-lendi/5 rounded-full blur-[120px] pointer-events-none" />
      <div className="absolute bottom-[10%] right-[10%] w-[400px] h-[400px] bg-synk/5 rounded-full blur-[100px] pointer-events-none" />

      <div className="relative z-10 max-w-7xl mx-auto px-6 flex flex-col items-center text-center">
        {/* Institutional Badge */}
        <div className="hero-element inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-secondary border border-border text-xs font-bold text-muted-foreground mb-8">
          <GraduationCap size={14} className="text-lendi" />
          Lendi Institute of Engineering & Technology
          <ChevronRight size={12} className="opacity-50" />
        </div>

        {/* Massive Typographic Headline */}
        <div ref={titleRef} className="space-y-2 mb-8">
          <h1 className="text-6xl md:text-8xl font-black tracking-tight leading-[0.9] text-foreground">
            Connect. Innovate.
          </h1>
          <h1 className="text-6xl md:text-8xl font-black tracking-tight leading-[0.9] gradient-text-hero">
            Impact the Future.
          </h1>
        </div>

        {/* Professional Subtext */}
        <p className="hero-element text-lg md:text-xl text-muted-foreground max-w-2xl mx-auto mb-10 font-medium leading-relaxed">
          The premier institutional operating system for students, faculty, and alumni.
          Bridge the gap between academic research and industry application at LIET.
        </p>

        {/* CTAs */}
        <div className="hero-element flex flex-wrap justify-center gap-4 mb-20">
          <Link
            href="/login"
            className="group flex items-center gap-2 px-8 py-4 bg-lendi text-white rounded-2xl font-bold text-base glow-btn shadow-xl shadow-lendi/20"
          >
            Access Hub
            <ArrowRight size={18} className="group-hover:translate-x-1 transition-transform" />
          </Link>
          <Link
            href="/login"
            className="flex items-center gap-2 px-8 py-4 bg-white dark:bg-white/5 border border-border rounded-2xl font-bold text-base hover:bg-secondary transition-colors"
          >
            Explore Projects
          </Link>
        </div>

        {/* Professional Stats Grid */}
        <div ref={statsRef} className="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-4xl mx-auto">
          {stats.map((stat) => (
            <div key={stat.label} className="p-6 rounded-2xl border border-border bg-card/50 backdrop-blur-sm card-hover text-left flex flex-col gap-3">
              <div className="w-10 h-10 rounded-xl bg-lendi/10 flex items-center justify-center text-lendi">
                <stat.icon size={20} />
              </div>
              <div>
                <div className="text-3xl font-black text-foreground">{stat.value}</div>
                <div className="text-sm font-bold text-muted-foreground uppercase tracking-wider">{stat.label}</div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
