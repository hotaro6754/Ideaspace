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
} from "lucide-react"

export default function FeaturesBento() {
  return (
    <section id="features" className="py-24 lg:py-32 relative overflow-hidden bg-secondary/30">
      <div className="max-w-7xl mx-auto px-6">
        {/* Section Header */}
        <div className="mb-16">
          <div className="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-lendi mb-4">
            <span className="w-8 h-px bg-lendi" />
            Core Capabilities
          </div>
          <h2 className="text-4xl md:text-5xl font-black text-foreground mb-6 tracking-tight">
            Designed for institutional <br />
            <span className="gradient-text">excellence.</span>
          </h2>
        </div>

        {/* Bento Grid */}
        <div className="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-6 gap-4">
          {/* Card 1: Collaborative Research (Large) */}
          <div className="md:col-span-4 lg:col-span-4 p-8 rounded-3xl bg-card border border-border card-hover group relative overflow-hidden">
            <div className="flex flex-col h-full justify-between relative z-10">
              <div>
                <div className="w-12 h-12 rounded-2xl bg-lendi/10 flex items-center justify-center text-lendi mb-6">
                  <Layers size={24} />
                </div>
                <h3 className="text-2xl font-bold text-foreground mb-3">Multi-Disciplinary Collaboration</h3>
                <p className="text-muted-foreground font-medium max-w-md">
                  Bridge departments and year groups. Students from CSE, ECE, and Mechanical can collaborate
                  seamlessly on complex research projects with shared workspaces.
                </p>
              </div>
              <div className="mt-12 flex items-center gap-4">
                <div className="flex -space-x-3">
                  {[1, 2, 3, 4].map((i) => (
                    <div key={i} className="w-10 h-10 rounded-full border-2 border-background bg-secondary flex items-center justify-center text-[10px] font-bold">
                      {String.fromCharCode(64 + i)}
                    </div>
                  ))}
                </div>
                <span className="text-xs font-bold text-muted-foreground uppercase tracking-widest">
                  +12 Departments Active
                </span>
              </div>
            </div>
            {/* Abstract Background Element */}
            <div className="absolute -right-20 -bottom-20 w-80 h-80 bg-lendi/5 rounded-full blur-3xl pointer-events-none group-hover:bg-lendi/10 transition-colors" />
          </div>

          {/* Card 2: AI Mentor */}
          <div className="md:col-span-2 lg:col-span-2 p-8 rounded-3xl bg-card border border-border card-hover group">
            <div className="w-12 h-12 rounded-2xl bg-synk/10 flex items-center justify-center text-synk mb-6">
              <Brain size={24} />
            </div>
            <h3 className="text-xl font-bold text-foreground mb-3">AI Academic Pairing</h3>
            <p className="text-sm text-muted-foreground font-medium leading-relaxed">
              Smart algorithms suggest mentors and project partners based on research history,
              skills, and publication interests.
            </p>
          </div>

          {/* Card 3: For Faculty */}
          <div id="faculty" className="md:col-span-2 lg:col-span-2 p-8 rounded-3xl bg-lendi text-white card-hover group relative overflow-hidden">
            <div className="relative z-10 flex flex-col h-full justify-between">
              <div>
                <div className="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center mb-6">
                  <Shield size={24} />
                </div>
                <h3 className="text-xl font-bold mb-3 text-white">Faculty Stewardship</h3>
                <p className="text-sm text-white/80 font-medium leading-relaxed">
                  Manage research bails, track student progress, and issue problem statements
                  directly to the student body with administrative oversight.
                </p>
              </div>
              <Link href="/login" className="mt-8 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest hover:translate-x-1 transition-transform">
                Admin Terminal <ChevronRight size={14} />
              </Link>
            </div>
            <div className="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-2xl -mr-16 -mt-16" />
          </div>

          {/* Card 4: Alumni Network */}
          <div id="alumni" className="md:col-span-2 lg:col-span-4 p-8 rounded-3xl bg-card border border-border card-hover group">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-8 h-full">
              <div className="flex-1">
                <div className="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 mb-6">
                  <Users size={24} />
                </div>
                <h3 className="text-2xl font-bold text-foreground mb-3">Alumni Mentorship Gateway</h3>
                <p className="text-muted-foreground font-medium max-w-sm">
                  Lendi alumni now at top global firms can mentor students, review projects, and post
                  internship opportunities exclusively for current students.
                </p>
              </div>
              <div className="w-full md:w-auto p-6 rounded-2xl bg-secondary/50 border border-border">
                <div className="flex items-center gap-3 mb-4">
                  <div className="w-10 h-10 rounded-full bg-emerald-500/20 flex items-center justify-center">
                    <TrendingUp size={18} className="text-emerald-600" />
                  </div>
                  <span className="text-sm font-bold">92% Career Success</span>
                </div>
                <div className="space-y-2">
                  <div className="h-2 w-32 bg-border rounded-full overflow-hidden">
                    <div className="h-full w-4/5 bg-emerald-500" />
                  </div>
                  <div className="h-2 w-24 bg-border rounded-full overflow-hidden">
                    <div className="h-full w-2/3 bg-emerald-500" />
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Card 5: Smart Search */}
          <div className="md:col-span-2 lg:col-span-2 p-8 rounded-3xl bg-card border border-border card-hover group">
            <div className="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500 mb-6">
              <Search size={24} />
            </div>
            <h3 className="text-xl font-bold text-foreground mb-3">Institutional Discovery</h3>
            <p className="text-sm text-muted-foreground font-medium leading-relaxed">
              Global Command+K search to find any project, research paper, student, or faculty
              member across the entire Lendi network.
            </p>
          </div>

          {/* Card 6: Career Growth */}
          <div className="md:col-span-2 lg:col-span-4 p-8 rounded-3xl bg-card border border-border card-hover group flex items-center justify-between overflow-hidden relative">
            <div className="relative z-10">
              <div className="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 mb-6">
                <Rocket size={24} />
              </div>
              <h3 className="text-2xl font-bold text-foreground mb-3">Industry-Ready Portfolio</h3>
              <p className="text-muted-foreground font-medium max-w-sm">
                Every project and participation generates a verified "Innovation Resume"
                shareable with recruiters to showcase hands-on excellence.
              </p>
            </div>
            <div className="hidden lg:flex flex-col gap-3 relative z-10 translate-x-4">
               {[1, 2, 3].map(i => (
                 <div key={i} className="w-48 h-12 rounded-xl bg-secondary border border-border border-dashed flex items-center px-4">
                   <div className="w-2 h-2 rounded-full bg-indigo-400 mr-3" />
                   <div className="h-2 w-24 bg-border rounded-full" />
                 </div>
               ))}
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}
