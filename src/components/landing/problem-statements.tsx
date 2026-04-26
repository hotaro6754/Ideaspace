"use client"

import { motion, Variants } from "framer-motion"
import { ArrowUpRight, Flame, Microscope, Cpu, Link as LinkIcon, Wind, ShieldAlert } from "lucide-react"

const containerVariants: Variants = {
  hidden: {},
  visible: { transition: { staggerChildren: 0.1 } },
}

const itemVariants: Variants = {
  hidden: { opacity: 0, y: 30 },
  visible: {
    opacity: 1,
    y: 0,
    transition: {
      duration: 0.8,
      ease: [0.2, 0, 0, 1]
    }
  },
}

type Problem = {
  domain: string
  icon: any
  title: string
  description: string
  teams: number
  difficulty: string
  status: "active" | "hot" | "full"
}

const problems: Problem[] = [
  {
    domain: "Edge Computing",
    icon: Cpu,
    title: "Adaptive Campus Grid Management",
    description: "Architecting a decentralized power distribution model for LIET labs using low-latency edge nodes and predictive ML.",
    teams: 14,
    difficulty: "Elite",
    status: "hot",
  },
  {
    domain: "Applied Cryptography",
    icon: LinkIcon,
    title: "Immutable Academic Credentials",
    description: "Developing a sovereign identity layer for verified degree issuance and cross-institutional certificate validation.",
    teams: 8,
    difficulty: "Expert",
    status: "active",
  },
  {
    domain: "Computer Vision",
    icon: Microscope,
    title: "Neural Attendance Protocol",
    description: "High-accuracy biometric verification system for campus-wide auditing with privacy-preserving local processing.",
    teams: 18,
    difficulty: "Advanced",
    status: "hot",
  },
  {
    domain: "Sustainability",
    icon: Wind,
    title: "Eco-Logic Consumption Forecaster",
    description: "Real-time auditing of renewable energy generation at Lendi with AI-driven load balancing for smart buildings.",
    teams: 6,
    difficulty: "Expert",
    status: "active",
  },
  {
    domain: "Cyber Intelligence",
    icon: ShieldAlert,
    title: "Intrusion Detection Gateway",
    description: "Securing institutional research data through an automated behavioral analysis firewall for cross-dept networks.",
    teams: 5,
    difficulty: "Elite",
    status: "active",
  },
]

export default function ProblemStatements() {
  return (
    <section id="challenges" className="py-32 relative overflow-hidden bg-background mesh-gradient">
      <div className="max-w-7xl mx-auto px-6">
        <div className="flex flex-col md:flex-row md:items-end justify-between mb-20 gap-8">
          <div>
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lendi/10 border border-lendi/20 text-[10px] font-black text-lendi uppercase tracking-widest mb-6">
              Research Tracks
            </div>
            <h2 className="text-5xl md:text-7xl font-black text-foreground tracking-tighter leading-[0.9]">
              High Stakes <br />
              <span className="text-gradient-sentinel">Open Challenges.</span>
            </h2>
          </div>
          <p className="text-lg text-muted-foreground font-medium max-w-sm mb-2">
            Directly from Lendi labs. Solve real institutional problems and earn verified reputation points.
          </p>
        </div>

        <motion.div
          variants={containerVariants}
          initial="hidden"
          whileInView="visible"
          viewport={{ once: true, margin: "-100px" }}
          className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
        >
          {problems.map((p, i) => (
            <motion.div
              key={p.title}
              variants={itemVariants}
              className={`p-8 rounded-[36px] bg-card/40 backdrop-blur-xl border border-border card-hover flex flex-col group ${i === 0 ? 'md:col-span-2' : ''}`}
            >
              <div className="flex items-start justify-between mb-8">
                 <div className="flex items-center gap-3">
                    <div className="w-10 h-10 rounded-xl bg-secondary flex items-center justify-center text-foreground border border-border group-hover:bg-lendi group-hover:text-white transition-colors">
                      <p.icon size={20} />
                    </div>
                    <div>
                      <span className="text-[10px] font-black text-muted-foreground uppercase tracking-widest block leading-none mb-1">{p.domain}</span>
                      <span className="text-xs font-bold text-foreground uppercase tracking-wider">{p.difficulty}</span>
                    </div>
                 </div>
                 {p.status === 'hot' && (
                   <span className="px-3 py-1 rounded-full bg-red-500/10 border border-red-500/20 text-[9px] font-black text-red-500 uppercase tracking-widest flex items-center gap-1.5">
                     <Flame size={12} className="fill-red-500" /> Active
                   </span>
                 )}
              </div>

              <h3 className="text-2xl font-black mb-4 tracking-tight leading-snug">{p.title}</h3>
              <p className="text-muted-foreground font-medium text-base leading-snug mb-10 flex-1">
                {p.description}
              </p>

              <div className="flex items-center justify-between pt-8 border-t border-border/50 mt-auto">
                <div className="flex items-center gap-2">
                   <div className="flex -space-x-2">
                     {[1, 2, 3].map(j => (
                       <div key={j} className="w-6 h-6 rounded-full border-2 border-card bg-secondary" />
                     ))}
                   </div>
                   <span className="text-[10px] font-black text-muted-foreground uppercase tracking-widest">{p.teams} Teams Synced</span>
                </div>
                <button className="w-10 h-10 rounded-full bg-secondary border border-border flex items-center justify-center text-muted-foreground group-hover:bg-lendi group-hover:text-white transition-all">
                  <ArrowUpRight size={18} />
                </button>
              </div>
            </motion.div>
          ))}
        </motion.div>

        <div className="mt-20 text-center">
           <button className="px-10 py-5 rounded-2xl border border-border bg-card/50 backdrop-blur-sm font-black text-xs uppercase tracking-[0.3em] hover:bg-secondary transition-all">
             Audit all 124 Tracks
           </button>
        </div>
      </div>
    </section>
  )
}
