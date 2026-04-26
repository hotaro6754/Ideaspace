"use client"

import { motion } from "framer-motion"
import Link from "next/link"
import {
  Brain,
  Rocket,
  Shield,
  Layers,
  Users,
  Search,
  ChevronRight,
  TrendingUp,
  Fingerprint,
  Zap,
} from "lucide-react"

export default function FeaturesBento() {
  return (
    <section id="features" className="py-32 relative overflow-hidden bg-background">
      <div className="max-w-7xl mx-auto px-6">
        {/* Section Header */}
        <div className="flex flex-col md:flex-row md:items-end justify-between mb-20 gap-8">
          <div>
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-synk/10 border border-synk/20 text-[10px] font-black text-synk uppercase tracking-widest mb-6">
              The Infrastructure
            </div>
            <h2 className="text-5xl md:text-7xl font-black text-foreground tracking-tighter leading-[0.9]">
              Engineered for <br />
              <span className="text-gradient-sentinel">High Performance.</span>
            </h2>
          </div>
          <p className="text-lg text-muted-foreground font-medium max-w-sm mb-2">
            A suite of institutional tools designed to accelerate research and foster collaboration.
          </p>
        </div>

        {/* Bento Grid 2.0 */}
        <div className="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-6">
          {/* Card 1: Collaborative Research (Large) */}
          <div className="md:col-span-4 lg:col-span-4 p-10 rounded-[40px] bg-card border border-border card-hover group relative overflow-hidden">
            <div className="flex flex-col h-full justify-between relative z-10">
              <div>
                <div className="w-14 h-14 rounded-2xl bg-lendi/10 flex items-center justify-center text-lendi mb-8 border border-lendi/20">
                  <Layers size={28} />
                </div>
                <h3 className="text-3xl font-black text-foreground mb-4 tracking-tight">Cross-Department Synapse</h3>
                <p className="text-lg text-muted-foreground font-medium max-w-lg leading-snug">
                  Break down institutional silos. Our Synapse protocol allows real-time data
                  sharing and collaborative editing between Mechanical, CSE, and ECE labs.
                </p>
              </div>
              <div className="mt-16 flex items-center gap-6">
                <div className="flex -space-x-4">
                  {[1, 2, 3, 4, 5].map((i) => (
                    <div key={i} className="w-12 h-12 rounded-full border-4 border-card bg-secondary flex items-center justify-center text-[11px] font-black group-hover:scale-110 transition-transform cursor-pointer">
                      {String.fromCharCode(64 + i)}
                    </div>
                  ))}
                </div>
                <div className="flex flex-col">
                  <span className="text-xs font-black text-foreground uppercase tracking-widest leading-none mb-1">
                    Node Distribution
                  </span>
                  <span className="text-[10px] text-muted-foreground font-bold uppercase tracking-wider">
                    24 Active Research Bails
                  </span>
                </div>
              </div>
            </div>
            {/* Abstract Background Element */}
            <div className="absolute -right-10 -bottom-10 w-96 h-96 bg-lendi/5 rounded-full blur-[100px] pointer-events-none group-hover:bg-lendi/10 transition-colors" />
          </div>

          {/* Card 2: Career Logic */}
          <div className="md:col-span-2 lg:col-span-2 p-10 rounded-[40px] bg-card border border-border card-hover group relative overflow-hidden">
             <div className="absolute top-0 right-0 w-32 h-32 bg-synk/5 rounded-full blur-2xl -mr-16 -mt-16" />
            <div className="w-14 h-14 rounded-2xl bg-synk/10 flex items-center justify-center text-synk mb-8 border border-synk/20">
              <TrendingUp size={28} />
            </div>
            <h3 className="text-2xl font-black text-foreground mb-4 tracking-tight">Innovation Resume</h3>
            <p className="text-base text-muted-foreground font-medium leading-relaxed mb-6">
              Forget static PDFs. Generate a verified, live history of every project,
              contribution, and mentor review.
            </p>
            <div className="p-4 rounded-2xl bg-secondary/50 border border-border">
               <div className="flex justify-between items-center mb-2">
                 <span className="text-[10px] font-black uppercase text-muted-foreground">Reputation Score</span>
                 <span className="text-sm font-black text-synk">942</span>
               </div>
               <div className="h-1.5 w-full bg-border rounded-full overflow-hidden">
                 <div className="h-full w-[85%] bg-synk" />
               </div>
            </div>
          </div>

          {/* Card 3: AI Pairing */}
          <div className="md:col-span-2 lg:col-span-2 p-10 rounded-[40px] bg-card border border-border card-hover group">
            <div className="w-14 h-14 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 mb-8 border border-indigo-500/20">
              <Brain size={28} />
            </div>
            <h3 className="text-2xl font-black text-foreground mb-4 tracking-tight">AI Matching</h3>
            <p className="text-base text-muted-foreground font-medium leading-relaxed">
              Proprietary algorithms that analyze 40+ academic signals to suggest the perfect
              team composition for any problem statement.
            </p>
          </div>

          {/* Card 4: Governance (The Steward) */}
          <div className="md:col-span-4 lg:col-span-4 p-10 rounded-[40px] bg-foreground text-background dark:bg-white dark:text-black card-hover group relative overflow-hidden">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-12 h-full relative z-10">
              <div className="flex-1">
                <div className="w-14 h-14 rounded-2xl bg-background/10 dark:bg-black/10 flex items-center justify-center mb-8 border border-background/20 dark:border-black/20">
                  <Shield size={28} />
                </div>
                <h3 className="text-3xl font-black mb-4 tracking-tight">Institutional Governance</h3>
                <p className="text-lg font-medium max-w-sm leading-snug opacity-80">
                  Secure access for faculty to monitor, validate, and issue credits for student
                  led innovation tracks.
                </p>
              </div>
              <div className="w-full md:w-auto flex flex-col gap-3">
                 {[
                   { label: "Role-Based Access", icon: Fingerprint },
                   { label: "Audit Logging", icon: Search },
                   { label: "Protocol Compliance", icon: Zap },
                 ].map(item => (
                   <div key={item.label} className="flex items-center gap-4 px-6 py-4 rounded-2xl bg-background/5 dark:bg-black/5 border border-background/10 dark:border-black/10">
                     <item.icon size={20} />
                     <span className="text-sm font-black uppercase tracking-widest">{item.label}</span>
                   </div>
                 ))}
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
