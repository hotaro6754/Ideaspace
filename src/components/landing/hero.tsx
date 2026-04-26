"use client"

import { useEffect, useRef } from "react"
import { motion } from "framer-motion"
import { ArrowRight, ChevronRight, GraduationCap, Building2, Users2, Sparkles, MoveRight } from "lucide-react"
import Link from "next/link"
import gsap from "gsap"

export default function Hero() {
  const containerRef = useRef<HTMLDivElement>(null)
  const textRef = useRef<HTMLDivElement>(null)
  const statsRef = useRef<HTMLDivElement>(null)
  const mockupRef = useRef<HTMLDivElement>(null)

  useEffect(() => {
    if (!containerRef.current) return

    const ctx = gsap.context(() => {
      // Title Animation
      gsap.from(".hero-title-part", {
        y: 80,
        opacity: 0,
        duration: 1.4,
        ease: "power4.out",
        stagger: 0.1,
      })

      // Subtitle & Buttons
      gsap.from(".hero-reveal", {
        y: 40,
        opacity: 0,
        duration: 1.2,
        ease: "power3.out",
        stagger: 0.1,
        delay: 0.6
      })

      // Stat Cards
      if (statsRef.current) {
        gsap.from(Array.from(statsRef.current.children), {
          y: 30,
          opacity: 0,
          duration: 1,
          ease: "back.out(1.5)",
          stagger: 0.15,
          delay: 1.2
        })
      }

      // Mockup Float & Entry
      gsap.from(mockupRef.current, {
        scale: 0.9,
        opacity: 0,
        y: 100,
        duration: 2,
        ease: "expo.out",
        delay: 0.8
      })
    }, containerRef)

    return () => ctx.revert()
  }, [])

  return (
    <section ref={containerRef} className="relative min-h-[110vh] flex flex-col items-center justify-center pt-32 pb-20 overflow-hidden mesh-gradient">
      <div className="absolute inset-0 sentinel-grid opacity-[0.15] pointer-events-none" />

      <div className="relative z-10 max-w-7xl mx-auto px-6 flex flex-col items-center text-center">
        {/* Elite Badge */}
        <div className="hero-reveal inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white/5 border border-white/10 glass-reflective text-[11px] font-black uppercase tracking-[0.3em] text-muted-foreground mb-10">
          <GraduationCap size={16} className="text-lendi" />
          The Institutional Standard
          <ChevronRight size={14} className="opacity-40" />
        </div>

        {/* Cinematic Headline */}
        <div ref={textRef} className="mb-10">
          <h1 className="text-7xl md:text-[120px] font-black tracking-tighter leading-[0.85] text-foreground mb-2">
            <span className="hero-title-part block">Architect the</span>
            <span className="hero-title-part block text-gradient-sentinel">Next Era.</span>
          </h1>
        </div>

        {/* Narrative Subtext */}
        <p className="hero-reveal text-xl md:text-2xl text-muted-foreground max-w-2xl mx-auto mb-12 font-medium leading-tight">
          Lendi’s unified operating system for high-stakes collaboration.
          Where academic research meets industrial-grade execution.
        </p>

        {/* Cinematic CTAs */}
        <div className="hero-reveal flex flex-wrap justify-center gap-5 mb-24">
          <Link
            href="/login"
            className="group btn-sentinel px-10 py-5 bg-lendi text-white rounded-2xl font-black text-lg shadow-2xl shadow-lendi/30"
          >
            Enter Workspace
            <MoveRight size={22} className="ml-3 group-hover:translate-x-1.5 transition-transform" />
          </Link>
          <Link
            href="/login"
            className="btn-sentinel px-10 py-5 bg-card border border-border rounded-2xl font-black text-lg hover:bg-secondary transition-colors"
          >
            Institutional Roadmap
          </Link>
        </div>

        {/* Product Preview Mockup */}
        <div ref={mockupRef} className="relative w-full max-w-5xl mx-auto px-4 mb-24">
          <div className="glass-reflective rounded-[40px] p-2 border border-white/10 shadow-[0_40px_100px_-20px_rgba(0,0,0,0.6)]">
            <div className="bg-[#020617] rounded-[32px] aspect-[16/10] flex items-center justify-center overflow-hidden relative">
               {/* Internal dashboard mockup visual */}
               <div className="absolute inset-0 bg-gradient-to-br from-lendi/20 via-transparent to-synk/10" />
               <div className="w-[80%] h-[70%] border border-white/5 bg-white/5 rounded-2xl relative">
                  <div className="absolute top-4 left-4 flex gap-2">
                    <div className="w-2 h-2 rounded-full bg-red-500/50" />
                    <div className="w-2 h-2 rounded-full bg-yellow-500/50" />
                    <div className="w-2 h-2 rounded-full bg-green-500/50" />
                  </div>
                  <div className="mt-12 px-6 space-y-4">
                    <div className="h-6 w-1/3 bg-white/10 rounded-lg animate-pulse" />
                    <div className="grid grid-cols-3 gap-4">
                       <div className="h-32 bg-white/5 rounded-xl" />
                       <div className="h-32 bg-white/5 rounded-xl" />
                       <div className="h-32 bg-white/5 rounded-xl" />
                    </div>
                  </div>
               </div>
            </div>
          </div>
          {/* Floating Accents */}
          <div className="absolute -top-10 -right-10 w-40 h-40 bg-lendi/20 rounded-full blur-3xl animate-pulse" />
          <div className="absolute -bottom-10 -left-10 w-40 h-40 bg-synk/20 rounded-full blur-3xl animate-pulse delay-700" />
        </div>

        {/* Elite Institutional Stats */}
        <div ref={statsRef} className="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-5xl mx-auto">
          {[
            { value: "4.2k+", label: "Verified Personnel", icon: Users2, color: "text-lendi" },
            { value: "128", label: "Active Research Bails", icon: Sparkles, color: "text-synk" },
            { value: "94%", label: "Industry Integration", icon: Building2, color: "text-indigo-400" },
          ].map((stat) => (
            <div key={stat.label} className="p-8 rounded-[32px] border border-border bg-card/40 backdrop-blur-md card-hover text-left flex flex-col gap-4">
              <div className={`w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center ${stat.color}`}>
                <stat.icon size={24} />
              </div>
              <div>
                <div className="text-4xl font-black text-foreground tracking-tighter mb-1">{stat.value}</div>
                <div className="text-[10px] font-black text-muted-foreground uppercase tracking-[0.2em]">{stat.label}</div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  )
}
