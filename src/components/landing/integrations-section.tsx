"use client"

import { motion } from "framer-motion"
import { GitBranch, Database, CheckCircle2, RefreshCw, Shield, Zap } from "lucide-react"

function SectionLabel({ text }: { text: string }) {
  return (
    <div className="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-synk">
      <span className="w-4 h-px bg-synk" />
      {text}
    </div>
  )
}

const githubStats = [
  { label: "Repositories", value: "128" },
  { label: "Commits (30d)", value: "2.4k" },
  { label: "Open PRs", value: "47" },
  { label: "Contributors", value: "312" },
]

const supabaseStats = [
  { label: "Tables", value: "24" },
  { label: "Realtime Subs", value: "180" },
  { label: "Rows (total)", value: "1.2M" },
  { label: "Uptime", value: "99.9%" },
]

export default function IntegrationsSection() {
  return (
    <section id="integrations" className="relative py-24 lg:py-32 overflow-hidden">
      <div className="absolute inset-0 blueprint-grid opacity-30" />
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[300px] bg-lendi/5 rounded-full blur-[100px] pointer-events-none" />

      <div className="relative z-10 max-w-7xl mx-auto px-6">
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.6 }}
          className="mb-14 text-center"
        >
          <SectionLabel text="Integrations" />
          <h2 className="mt-4 text-4xl sm:text-5xl font-bold text-foreground text-balance leading-tight font-sans">
            Connected to your
            <br />
            <span className="gradient-text">workflow.</span>
          </h2>
          <p className="mt-4 text-muted-foreground text-lg max-w-xl mx-auto text-pretty font-medium">
            IdeaSync plugs directly into the tools you already use.
            GitHub for code, Supabase for data — everything synced in real-time.
          </p>
        </motion.div>

        {/* Integration cards + center node */}
        <div className="grid md:grid-cols-3 gap-6 items-center">
          {/* GitHub Card */}
          <motion.div
            initial={{ opacity: 0, x: -40 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="glass-card border-shine rounded-2xl p-6 border border-border/60 card-hover-glow group cursor-pointer"
          >
            <div className="flex items-center justify-between mb-5">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-secondary/60 border border-border flex items-center justify-center">
                  <svg viewBox="0 0 24 24" className="w-5 h-5 text-foreground fill-current">
                    <path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0 1 12 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z" />
                  </svg>
                </div>
                <div>
                  <div className="text-sm font-bold text-foreground">GitHub</div>
                  <div className="text-xs text-muted-foreground font-bold uppercase tracking-widest">liet-ideasync</div>
                </div>
              </div>
              <div className="flex items-center gap-1.5 text-xs text-emerald-400 font-bold uppercase tracking-widest">
                <CheckCircle2 size={13} />
                Connected
              </div>
            </div>

            <div className="grid grid-cols-2 gap-3 mb-5">
              {githubStats.map((stat) => (
                <div key={stat.label} className="p-2.5 rounded-lg bg-secondary/40 border border-border/40">
                  <div className="text-base font-bold text-foreground">{stat.value}</div>
                  <div className="text-[11px] text-muted-foreground mt-0.5 font-bold uppercase tracking-widest">{stat.label}</div>
                </div>
              ))}
            </div>

            <div className="flex items-center gap-2 text-xs text-muted-foreground font-bold uppercase tracking-widest">
              <RefreshCw size={11} className="animate-spin-slow" />
              Last synced 2 minutes ago
            </div>
          </motion.div>

          {/* Center connection node */}
          <motion.div
            initial={{ opacity: 0, scale: 0.7 }}
            whileInView={{ opacity: 1, scale: 1 }}
            viewport={{ once: true }}
            transition={{ duration: 0.7, delay: 0.2 }}
            className="flex flex-col items-center justify-center gap-4"
          >
            {/* Connection lines (top) */}
            <div className="hidden md:flex flex-col items-center gap-1">
              {[...Array(4)].map((_, i) => (
                <motion.div
                  key={i}
                  initial={{ opacity: 0 }}
                  whileInView={{ opacity: [0, 1, 0] }}
                  viewport={{ once: false }}
                  transition={{ delay: i * 0.15, duration: 1.2, repeat: Infinity, repeatDelay: 1 }}
                  className="w-0.5 h-3 rounded-full bg-synk/50"
                />
              ))}
            </div>

            {/* IdeaSync core */}
            <div className="relative">
              <div className="w-20 h-20 rounded-2xl bg-gradient-to-br from-lendi to-synk flex items-center justify-center shadow-[0_0_40px_rgba(0,74,153,0.5)] animate-pulse-ring">
                <Zap size={28} className="text-white fill-white" />
              </div>
              {/* Orbit ring */}
              <div className="absolute inset-[-10px] rounded-3xl border border-lendi/20 animate-spin-slow" />
              <div className="absolute inset-[-18px] rounded-[28px] border border-synk/10 animate-spin-slow" style={{ animationDirection: "reverse", animationDuration: "15s" }} />
            </div>

            <div className="text-center font-bold uppercase tracking-widest">
              <div className="text-sm text-foreground">IdeaSync Core</div>
              <div className="text-[10px] text-muted-foreground mt-0.5">Data bridge active</div>
            </div>

            {/* Connection lines (bottom) */}
            <div className="hidden md:flex flex-col items-center gap-1">
              {[...Array(4)].map((_, i) => (
                <motion.div
                  key={i}
                  initial={{ opacity: 0 }}
                  whileInView={{ opacity: [0, 1, 0] }}
                  viewport={{ once: false }}
                  transition={{ delay: 0.6 + i * 0.15, duration: 1.2, repeat: Infinity, repeatDelay: 1 }}
                  className="w-0.5 h-3 rounded-full bg-lendi/50"
                />
              ))}
            </div>
          </motion.div>

          {/* Supabase Card */}
          <motion.div
            initial={{ opacity: 0, x: 40 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="glass-card border-shine rounded-2xl p-6 border border-border/60 card-hover-glow-cyan group cursor-pointer"
          >
            <div className="flex items-center justify-between mb-5">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-center">
                  <Database size={18} className="text-emerald-400" />
                </div>
                <div>
                  <div className="text-sm font-bold text-foreground">Supabase</div>
                  <div className="text-[10px] text-muted-foreground font-bold uppercase tracking-widest">ideasync-prod</div>
                </div>
              </div>
              <div className="flex items-center gap-1.5 text-xs text-emerald-400 font-bold uppercase tracking-widest">
                <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
                Live
              </div>
            </div>

            <div className="grid grid-cols-2 gap-3 mb-5">
              {supabaseStats.map((stat) => (
                <div key={stat.label} className="p-2.5 rounded-lg bg-secondary/40 border border-border/40">
                  <div className="text-base font-bold text-foreground">{stat.value}</div>
                  <div className="text-[11px] text-muted-foreground mt-0.5 font-bold uppercase tracking-widest">{stat.label}</div>
                </div>
              ))}
            </div>

            <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-widest">
              <Shield size={11} className="text-synk" />
              <span className="text-muted-foreground">Row-level security</span>
            </div>
          </motion.div>
        </div>

        {/* Bottom integration strip */}
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          transition={{ duration: 0.5, delay: 0.4 }}
          className="mt-10 flex flex-wrap items-center justify-center gap-3"
        >
          {["OAuth 2.0", "Webhooks", "REST API", "Realtime WS", "Edge Functions", "Storage"].map((tech) => (
            <div key={tech} className="flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-border/60 bg-secondary/30 text-[10px] font-bold text-muted-foreground uppercase tracking-widest">
              <div className="w-1.5 h-1.5 rounded-full bg-synk/60" />
              {tech}
            </div>
          ))}
        </motion.div>
      </div>
    </section>
  )
}
