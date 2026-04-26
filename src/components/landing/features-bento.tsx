"use client"

import { motion } from "framer-motion"
import { Rocket, Shield, Users, Trophy, Sparkles, BookOpen } from "lucide-react"

const features = [
  {
    title: "Project Missions",
    desc: "Collaborate on institutional research and development through structured quality gates.",
    icon: Rocket,
    size: "large",
    color: "bg-blue-500/10 text-blue-600",
  },
  {
    title: "Faculty Bounties",
    desc: "Solve real-world institutional challenges posted by Lendi departments.",
    icon: Shield,
    size: "small",
    color: "bg-red-500/10 text-red-600",
  },
  {
    title: "Alumni Network",
    desc: "Connect with the LIET diaspora for mentorship and professional opportunities.",
    icon: Users,
    size: "small",
    color: "bg-purple-500/10 text-purple-600",
  },
  {
    title: "Academic Rewards",
    desc: "Earn XP and build an institutional reputation through verified contributions.",
    icon: Trophy,
    size: "medium",
    color: "bg-amber-500/10 text-amber-600",
  },
  {
    title: "Resource Hub",
    desc: "Access verified academic content and collaborative learning materials.",
    icon: BookOpen,
    size: "medium",
    color: "bg-emerald-500/10 text-emerald-600",
  },
];

export default function FeaturesBento() {
  return (
    <section id="features" className="py-32 px-6 bg-muted/30 relative">
      <div className="max-w-7xl mx-auto">
        <div className="text-center mb-24 max-w-3xl mx-auto">
          <h2 className="text-[10px] font-black uppercase tracking-[0.4em] text-lendi-blue mb-4">Core Capabilities</h2>
          <h3 className="text-4xl md:text-5xl font-black tracking-tight mb-6">Designed for Institutional Excellence.</h3>
          <p className="text-lg text-muted-foreground font-medium leading-relaxed">
            IdeaSync provides the digital infrastructure to transform academic potential into industry-ready experience through Lendi's verified innovation tracks.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 auto-rows-[240px]">
          {features.map((f, i) => (
            <motion.div
              key={f.title}
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: i * 0.1 }}
              className={`inst-card p-8 flex flex-col justify-between group ${
                f.size === "large" ? "md:col-span-2 md:row-span-2" :
                f.size === "medium" ? "md:row-span-1" : ""
              }`}
            >
              <div className={`w-14 h-14 rounded-2xl flex items-center justify-center ${f.color} shadow-sm transition-transform group-hover:scale-110 duration-500`}>
                <f.icon size={28} />
              </div>

              <div>
                <h4 className="text-xl font-black mb-3 tracking-tight">{f.title}</h4>
                <p className="text-muted-foreground text-sm font-medium leading-relaxed">
                  {f.desc}
                </p>
              </div>
            </motion.div>
          ))}
        </div>
      </div>
    </section>
  )
}
