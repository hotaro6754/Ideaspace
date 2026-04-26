"use client"

import { useState, useEffect } from "react"
import { motion, AnimatePresence } from "framer-motion"
import { Zap, Menu, X, ArrowRight } from "lucide-react"
import Link from "next/link"

const navLinks = [
  { href: "#features", label: "Features" },
  { href: "#challenges", label: "Challenges" },
  { href: "#talent", label: "Talent Board" },
  { href: "#integrations", label: "Integrations" },
]

export default function Navbar() {
  const [scrolled, setScrolled] = useState(false)
  const [mobileOpen, setMobileOpen] = useState(false)

  useEffect(() => {
    const handleScroll = () => setScrolled(window.scrollY > 30)
    window.addEventListener("scroll", handleScroll, { passive: true })
    return () => window.removeEventListener("scroll", handleScroll)
  }, [])

  return (
    <motion.header
      initial={{ y: -80, opacity: 0 }}
      animate={{ y: 0, opacity: 1 }}
      transition={{ duration: 0.6, ease: "easeOut" }}
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-500 ${
        scrolled ? "glass-nav shadow-[0_1px_0_rgba(21,32,64,0.8)]" : "bg-transparent"
      }`}
    >
      <div className="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
        {/* Logo */}
        <Link href="/" className="flex items-center gap-2.5 group">
          <div className="w-8 h-8 rounded-lg bg-lendi flex items-center justify-center animate-pulse-ring">
            <Zap size={16} className="text-white fill-white" />
          </div>
          <span className="font-semibold text-foreground text-lg tracking-tight">
            Idea<span className="text-synk">Sync</span>
          </span>
          <span className="hidden sm:block text-xs text-muted-foreground border border-border rounded-full px-2 py-0.5 ml-1">
            LIET
          </span>
        </Link>

        {/* Desktop nav */}
        <nav className="hidden md:flex items-center gap-1">
          {navLinks.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              className="px-4 py-2 text-sm text-muted-foreground hover:text-foreground transition-colors duration-200 rounded-lg hover:bg-white/[0.04]"
            >
              {link.label}
            </Link>
          ))}
        </nav>

        {/* CTA */}
        <div className="hidden md:flex items-center gap-3">
          <Link
            href="/login"
            className="text-sm text-muted-foreground hover:text-foreground transition-colors duration-200"
          >
            Sign In
          </Link>
          <Link
            href="/login"
            className="flex items-center gap-2 px-4 py-2 bg-lendi text-white text-sm font-medium rounded-lg glow-btn-primary"
          >
            Join the Forge
            <ArrowRight size={14} />
          </Link>
        </div>

        {/* Mobile toggle */}
        <button
          onClick={() => setMobileOpen((v) => !v)}
          className="md:hidden p-2 text-muted-foreground hover:text-foreground transition-colors"
          aria-label="Toggle navigation"
        >
          {mobileOpen ? <X size={22} /> : <Menu size={22} />}
        </button>
      </div>

      {/* Mobile menu */}
      <AnimatePresence>
        {mobileOpen && (
          <motion.div
            initial={{ height: 0, opacity: 0 }}
            animate={{ height: "auto", opacity: 1 }}
            exit={{ height: 0, opacity: 0 }}
            transition={{ duration: 0.25 }}
            className="md:hidden overflow-hidden glass-nav border-t border-border/50"
          >
            <div className="px-6 py-4 flex flex-col gap-1">
              {navLinks.map((link) => (
                <Link
                  key={link.href}
                  href={link.href}
                  onClick={() => setMobileOpen(false)}
                  className="px-3 py-3 text-sm text-muted-foreground hover:text-foreground transition-colors rounded-lg hover:bg-white/[0.04]"
                >
                  {link.label}
                </Link>
              ))}
              <div className="mt-2 pt-2 border-t border-border/50">
                <Link
                  href="/login"
                  className="flex items-center justify-center gap-2 w-full px-4 py-3 bg-lendi text-white text-sm font-medium rounded-lg"
                  onClick={() => setMobileOpen(false)}
                >
                  Join the Forge <ArrowRight size={14} />
                </Link>
              </div>
            </div>
          </motion.div>
        )}
      </AnimatePresence>
    </motion.header>
  )
}
