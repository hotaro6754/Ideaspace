"use client"

import { useState, useEffect } from "react"
import { motion, AnimatePresence } from "framer-motion"
import { Menu, X, ArrowRight, ShieldCheck, GraduationCap } from "lucide-react"
import Link from "next/link"

const navLinks = [
  { href: "#features", label: "Students" },
  { href: "#faculty", label: "Faculty" },
  { href: "#alumni", label: "Alumni" },
  { href: "#challenges", label: "Challenges" },
]

export default function Navbar() {
  const [scrolled, setScrolled] = useState(false)
  const [mobileOpen, setMobileOpen] = useState(false)

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 20)
    window.addEventListener("scroll", handleScroll, { passive: true })
    return () => window.removeEventListener("scroll", handleScroll)
  }, [])

  return (
    <motion.header
      initial={{ y: -100 }}
      animate={{ y: 0 }}
      transition={{ duration: 0.6, ease: [0.22, 1, 0.36, 1] }}
      className={`fixed top-6 left-1/2 -translate-x-1/2 z-50 w-[calc(100%-3rem)] max-w-5xl transition-all duration-500 ${
        scrolled ? "glass-nav py-2 rounded-3xl shadow-premium border border-white/10" : "bg-transparent py-4"
      }`}
    >
      <div className="px-6 flex items-center justify-between">
        {/* Logo */}
        <Link href="/" className="flex items-center gap-3 group">
          <div className="w-10 h-10 rounded-xl bg-lendi-blue flex items-center justify-center shadow-lendi transition-transform group-hover:rotate-6">
            <GraduationCap size={20} className="text-white" />
          </div>
          <div className="flex flex-col">
            <span className="font-black text-foreground text-lg tracking-tight-inst leading-none uppercase">
              Idea<span className="text-lendi-light">Sync</span>
            </span>
            <span className="text-[9px] text-muted-foreground uppercase tracking-[0.3em] font-black mt-1">
              Lendi Institute
            </span>
          </div>
        </Link>

        {/* Desktop nav */}
        <nav className="hidden lg:flex items-center gap-2 bg-secondary/30 p-1.5 rounded-2xl border border-white/5">
          {navLinks.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              className="px-5 py-2 text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:text-lendi-blue transition-all duration-300 rounded-xl hover:bg-white"
            >
              {link.label}
            </Link>
          ))}
        </nav>

        {/* CTA */}
        <div className="hidden md:flex items-center gap-6">
          <Link
            href="/login"
            className="text-xs font-black uppercase tracking-widest text-muted-foreground hover:text-foreground transition-colors"
          >
            Access Hub
          </Link>
          <Link
            href="/login"
            className="flex items-center gap-2 px-6 py-3 bg-lendi-blue text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:scale-105 shadow-lendi active:scale-95 transition-all"
          >
            Get Started
            <ArrowRight size={14} />
          </Link>
        </div>

        {/* Mobile toggle */}
        <button
          onClick={() => setMobileOpen((v) => !v)}
          className="lg:hidden p-2 text-muted-foreground hover:text-foreground transition-colors"
        >
          {mobileOpen ? <X size={24} /> : <Menu size={24} />}
        </button>
      </div>

      {/* Mobile menu */}
      <AnimatePresence>
        {mobileOpen && (
          <motion.div
            initial={{ opacity: 0, y: -20 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: -20 }}
            className="lg:hidden mt-4 bg-card/95 backdrop-blur-3xl rounded-3xl border border-border shadow-premium overflow-hidden"
          >
            <div className="px-6 py-8 flex flex-col gap-4 text-center">
              {navLinks.map((link) => (
                <Link
                  key={link.href}
                  href={link.href}
                  onClick={() => setMobileOpen(false)}
                  className="px-4 py-4 text-xs font-black uppercase tracking-[0.2em] text-muted-foreground hover:text-lendi-blue rounded-2xl hover:bg-lendi-blue/5 transition-all"
                >
                  {link.label}
                </Link>
              ))}
              <div className="mt-4 pt-6 border-t border-border flex flex-col gap-4">
                <Link
                  href="/login"
                  className="w-full py-4 text-xs font-black uppercase tracking-widest text-foreground"
                  onClick={() => setMobileOpen(false)}
                >
                  Sign In
                </Link>
                <Link
                  href="/login"
                  className="flex items-center justify-center gap-2 w-full py-5 bg-lendi-blue text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-lendi"
                  onClick={() => setMobileOpen(false)}
                >
                  Get Started <ArrowRight size={18} />
                </Link>
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </motion.header>
  )
}
