"use client"

import { useState, useEffect } from "react"
import { motion, AnimatePresence } from "framer-motion"
import { Menu, X, ArrowRight, ShieldCheck } from "lucide-react"
import Link from "next/link"

const navLinks = [
  { href: "#features", label: "For Students" },
  { href: "#faculty", label: "For Faculty" },
  { href: "#alumni", label: "For Alumni" },
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
      transition={{ duration: 0.5, ease: [0.2, 0, 0, 1] }}
      className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
        scrolled ? "glass-nav py-3" : "bg-transparent py-5"
      }`}
    >
      <div className="max-w-7xl mx-auto px-6 flex items-center justify-between">
        {/* Logo */}
        <Link href="/" className="flex items-center gap-2 group">
          <div className="w-9 h-9 rounded-xl bg-lendi flex items-center justify-center shadow-lg shadow-lendi/20">
            <ShieldCheck size={20} className="text-white" />
          </div>
          <div className="flex flex-col">
            <span className="font-bold text-foreground text-xl tracking-tight leading-none">
              Idea<span className="text-lendi-light">Sync</span>
            </span>
            <span className="text-[10px] text-muted-foreground uppercase tracking-widest font-bold mt-0.5">
              Lendi Institute
            </span>
          </div>
        </Link>

        {/* Desktop nav */}
        <nav className="hidden lg:flex items-center gap-1">
          {navLinks.map((link) => (
            <Link
              key={link.href}
              href={link.href}
              className="px-5 py-2 text-sm font-medium text-muted-foreground hover:text-lendi transition-colors duration-200 rounded-full hover:bg-lendi/5"
            >
              {link.label}
            </Link>
          ))}
        </nav>

        {/* CTA */}
        <div className="hidden md:flex items-center gap-4">
          <Link
            href="/login"
            className="text-sm font-semibold text-muted-foreground hover:text-foreground transition-colors duration-200"
          >
            Sign In
          </Link>
          <Link
            href="/login"
            className="flex items-center gap-2 px-5 py-2.5 bg-foreground text-background dark:bg-white dark:text-black text-sm font-bold rounded-full hover:scale-105 transition-transform active:scale-95"
          >
            Get Started
            <ArrowRight size={14} />
          </Link>
        </div>

        {/* Mobile toggle */}
        <button
          onClick={() => setMobileOpen((v) => !v)}
          className="lg:hidden p-2 text-muted-foreground hover:text-foreground transition-colors"
          aria-label="Toggle navigation"
        >
          {mobileOpen ? <X size={24} /> : <Menu size={24} />}
        </button>
      </div>

      {/* Mobile menu */}
      <AnimatePresence>
        {mobileOpen && (
          <motion.div
            initial={{ opacity: 0, height: 0 }}
            animate={{ opacity: 1, height: "auto" }}
            exit={{ opacity: 0, height: 0 }}
            className="lg:hidden glass-nav border-t border-border mt-3"
          >
            <div className="px-6 py-6 flex flex-col gap-2">
              {navLinks.map((link) => (
                <Link
                  key={link.href}
                  href={link.href}
                  onClick={() => setMobileOpen(false)}
                  className="px-4 py-3 text-base font-medium text-muted-foreground hover:text-lendi rounded-xl hover:bg-lendi/5"
                >
                  {link.label}
                </Link>
              ))}
              <div className="mt-4 pt-4 border-t border-border space-y-3">
                <Link
                  href="/login"
                  className="flex items-center justify-center w-full py-4 text-base font-semibold text-foreground"
                  onClick={() => setMobileOpen(false)}
                >
                  Sign In
                </Link>
                <Link
                  href="/login"
                  className="flex items-center justify-center gap-2 w-full py-4 bg-lendi text-white text-base font-bold rounded-xl"
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
