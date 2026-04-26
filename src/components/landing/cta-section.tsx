"use client"

import { motion } from "framer-motion"
import { ArrowRight, Zap } from "lucide-react"
import Link from "next/link"

export default function CtaSection() {
  return (
    <section className="py-32 px-6 relative overflow-hidden bg-background">
      <div className="absolute inset-0 soft-grid opacity-20" />

      <div className="max-w-5xl mx-auto relative z-10">
        <motion.div
          initial={{ opacity: 0, y: 30 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="p-12 md:p-24 rounded-[3rem] bg-gradient-to-br from-lendi-blue to-lendi-dark text-white text-center shadow-premium relative overflow-hidden group"
        >
          {/* Decorative Elements */}
          <div className="absolute top-0 right-0 p-32 opacity-10 group-hover:scale-110 transition-transform duration-1000">
            <Zap size={300} fill="currentColor" />
          </div>
          <div className="absolute -bottom-24 -left-24 w-64 h-64 bg-white/5 rounded-full blur-3xl" />

          <h3 className="text-4xl md:text-6xl font-black tracking-tight-inst mb-8 leading-none relative z-10">
            Ready to shape the <br /> future of Lendi?
          </h3>
          <p className="text-lg md:text-xl text-white/80 font-medium mb-12 max-w-2xl mx-auto leading-relaxed relative z-10">
            Join thousands of innovators at LIET. Access the institutional hub today and start your journey towards excellence.
          </p>

          <div className="flex flex-wrap justify-center gap-6 relative z-10">
            <Link href="/login">
              <motion.button
                whileHover={{ scale: 1.05 }}
                whileTap={{ scale: 0.98 }}
                className="px-10 py-5 bg-white text-lendi-blue rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl"
              >
                Launch Hub Access
              </motion.button>
            </Link>
            <Link href="/login">
              <motion.button
                whileHover={{ scale: 1.05, backgroundColor: "rgba(255, 255, 255, 0.1)" }}
                whileTap={{ scale: 0.98 }}
                className="px-10 py-5 bg-white/5 border-2 border-white/20 text-white rounded-2xl font-black text-xs uppercase tracking-widest backdrop-blur-sm transition-colors"
              >
                Contact Faculty Desk
              </motion.button>
            </Link>
          </div>
        </motion.div>
      </div>
    </section>
  )
}
