"use client"

import { useState, useEffect } from "react"
import { motion, AnimatePresence } from "framer-motion"
import { Menu, X, ArrowRight, ShieldCheck, UserCircle2 } from "lucide-react"
import Link from "next/link"

const navLinks = [
  { href: "#features", label: "Research" },
  { href: "#challenges", label: "Challenges" },
  { href: "#talent", label: "Personnel" },
  { href: "/roadmap", label: "Roadmap" },
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
      transition={{ duration: 0.8, ease: [0.2, 0, 0, 1] }}
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-500 ${
        scrolled ? "bg-card/60 backdrop-blur-2xl py-3 border-b border-border shadow-lg shadow-black/5" : "bg-transparent py-6"
      }`}
    >
      <div className="max-w-7xl mx-auto px-6 flex items-center justify-between">
        {/* Institutional Branding */}
        <Link href="/" className="flex items-center gap-3 group">
          <div className="w-11 h-11 rounded-2xl bg-lendi flex items-center justify-center shadow-2xl shadow-lendi/20 group-hover:scale-105 transition-transform duration-500">
            <ShieldCheck size={24} className="text-white" />
          </div>
          <div className="flex flex-col">
            <span className="font-black text-foreground text-2xl tracking-tighter leading-none">
              Idea<span className="text-lendi">Sync</span>
            </span>
            <span className="text-[10px] font-black text-muted-foreground uppercase tracking-[0.3em] mt-1 opacity-60">
              Institutional Hub
            </span>
          </div>
        </Link>

        {/* Intelligence Nav */}
        <nav className="hidden lg:flex items-center gap-2">
          {navLinks.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              className="px-6 py-2.5 text-[11px] font-black uppercase tracking-[0.2em] text-muted-foreground hover:text-lendi transition-colors duration-300 rounded-xl hover:bg-lendi/5"
            >
              {link.label}
            </Link>
          ))}
        </nav>

        {/* Portal CTAs */}
        <div className="hidden md:flex items-center gap-6">
          <Link
            href="/login"
            className="flex items-center gap-2 text-[11px] font-black uppercase tracking-[0.2em] text-muted-foreground hover:text-foreground transition-all group"
          >
            <UserCircle2 size={16} className="text-muted-foreground/40 group-hover:text-lendi transition-colors" />
            Sign In
          </Link>
          <Link
            href="/login"
            className="flex items-center gap-2 px-8 py-3.5 bg-foreground text-background dark:bg-white dark:text-black text-xs font-black uppercase tracking-[0.2em] rounded-[18px] hover:scale-[1.05] active:scale-95 transition-all shadow-xl shadow-black/10"
          >
            Get Access
            <ArrowRight size={14} className="opacity-60" />
          </Link>
        </div>

        {/* Protocol Toggle */}
        <button
          onClick={() => setMobileOpen((v) => !v)}
          className="lg:hidden p-3 rounded-2xl bg-secondary/50 border border-border text-muted-foreground hover:text-foreground transition-colors"
          aria-label="Toggle navigation"
        >
          {mobileOpen ? <X size={24} /> : <Menu size={24} />}
        </button>
      </div>

      {/* Mobile Command Center */}
      <AnimatePresence>
        {mobileOpen && (
          <motion.div
            initial={{ opacity: 0, height: 0 }}
            animate={{ opacity: 1, height: "auto" }}
            exit={{ opacity: 0, height: 0 }}
            className="lg:hidden bg-card/95 backdrop-blur-2xl border-t border-border mt-3 shadow-2xl"
          >
            <div className="px-6 py-8 flex flex-col gap-3">
              {navLinks.map((link) => (
                <Link
                  key={link.href}
                  href={link.href}
                  onClick={() => setMobileOpen(false)}
                  className="px-5 py-4 text-sm font-black uppercase tracking-[0.3em] text-muted-foreground hover:text-lendi rounded-2xl hover:bg-lendi/5 border border-transparent hover:border-lendi/10"
                >
                  {link.label}
                </Link>
              ))}
              <div className="mt-6 pt-6 border-t border-border space-y-4">
                <Link
                  href="/login"
                  className="flex items-center justify-center w-full py-5 text-xs font-black uppercase tracking-[0.3em] text-foreground border border-border rounded-2xl"
                  onClick={() => setMobileOpen(false)}
                >
                  Sign In
                </Link>
                <Link
                  href="/login"
                  className="flex items-center justify-center gap-3 w-full py-5 bg-lendi text-white text-xs font-black uppercase tracking-[0.3em] rounded-2xl shadow-xl shadow-lendi/20"
                  onClick={() => setMobileOpen(false)}
                >
                  Establish Link <ArrowRight size={18} />
                </Link>
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </motion.header>
  )
}
