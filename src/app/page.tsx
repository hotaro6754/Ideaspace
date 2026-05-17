"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { motion, useScroll, useTransform } from "framer-motion";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Card } from "@/components/ui/card";
import {
  Terminal,
  Zap,
  Shield,
  Layers,
  Rocket,
  ArrowRight,
  Users,
  Trophy,
  CheckCircle,
  XCircle,
  Flame,
  Code2,
  Lightbulb,
  Target,
} from "lucide-react";

export default function LandingPage() {
  const { scrollYProgress } = useScroll();
  const heroOpacity = useTransform(scrollYProgress, [0, 0.2], [1, 0]);
  const heroScale = useTransform(scrollYProgress, [0, 0.2], [1, 0.95]);
  const [stats, setStats] = useState({ totalIdeas: 0, activeBuilds: 0, shipped: 0, totalUsers: 0, totalProofs: 0, totalWorkshops: 0 });

  useEffect(() => {
    fetch("/api/stats").then(r => r.json()).then(d => {
      if (d.data) setStats(d.data);
    }).catch(() => {});
  }, []);

  const pipelineSteps = [
    {
      step: "01",
      title: "Post",
      description: "Define the problem, solution, and skills needed. Your idea gets a live health score.",
      chips: ["Draft", "Publish"],
      icon: Lightbulb,
    },
    {
      step: "02",
      title: "Build",
      description: "Recruit teammates, run sprints, and ship working proof. Track progress publicly.",
      chips: ["Sprint", "Ship"],
      icon: Layers,
    },
    {
      step: "03",
      title: "Verify",
      description: "Attach demos, repos, and outcomes. Faculty verify evidence. Earn rank points.",
      chips: ["Evidence", "Verified"],
      icon: Shield,
    },
  ];

  const comparisonRows = [
    { label: "Ownership clarity", ideaspace: "Roles + verified owners", traditional: "Unclear or missing" },
    { label: "Proof of work", ideaspace: "Required for rank", traditional: "Optional or absent" },
    { label: "Progress visibility", ideaspace: "Health score + updates", traditional: "Manual check-ins" },
    { label: "Team matching", ideaspace: "Skills + tier matching", traditional: "Random DMs" },
    { label: "Postmortems", ideaspace: "Archive with lessons", traditional: "Lost in chats" },
  ];

  const features = [
    { icon: Lightbulb, title: "Idea Feed", desc: "Browse high-signal projects. Filter by track, evaluate completeness, and join teams.", color: "from-[#1FB7A6]/10" },
    { icon: Trophy, title: "Leaderboard", desc: "Gamified progression. Earn points through builds, proofs, and verified outcomes.", color: "from-[#F2B24B]/10" },
    { icon: Flame, title: "The Forge", desc: "Join intensive workshops and hackathons. Learn from peers and ship MVPs fast.", color: "from-[#22D3EE]/10" },
    { icon: Users, title: "Collaboration", desc: "Find the missing skills in your team. Request to join with 1-click.", color: "from-[#FF6B4A]/10" },
    { icon: Shield, title: "Proof Wall", desc: "Every build carries evidence. Demos, commits, and outcomes are visible by default.", color: "from-[#2EA86A]/10" },
    { icon: Target, title: "Bounties", desc: "Complete campus challenges for reward points. Build, research, or design.", color: "from-[#8B5CF6]/10" },
  ];

  return (
    <div className="min-h-screen bg-bg-primary overflow-hidden relative">
      <div className="aurora-bg" />
      <div className="fixed inset-0 dot-grid opacity-30 pointer-events-none" />

      {/* Header */}
      <header className="fixed top-0 left-0 right-0 z-50 h-16 flex items-center justify-between px-6 lg:px-12 glass border-b border-border">
        <div className="flex items-center gap-3">
          <div className="w-8 h-8 rounded-lg gradient-accent flex items-center justify-center shadow-lg shadow-accent/20">
            <Zap className="h-4 w-4 text-white" />
          </div>
          <span className="text-xl font-bold tracking-tight text-white font-display">IdeaSpace</span>
        </div>
        <nav className="hidden md:flex items-center gap-8">
          <Link href="#features" className="text-sm font-medium text-text-secondary hover:text-white transition-colors">Features</Link>
          <Link href="#process" className="text-sm font-medium text-text-secondary hover:text-white transition-colors">How it Works</Link>
          <Link href="#compare" className="text-sm font-medium text-text-secondary hover:text-white transition-colors">Compare</Link>
        </nav>
        <div className="flex items-center gap-4">
          <Link href="/auth/login" className="text-sm font-medium text-text-secondary hover:text-white transition-colors hidden sm:block">
            Sign In
          </Link>
          <Link href="/auth/register">
            <Button variant="gradient" size="sm" className="rounded-full">
              Get Started <ArrowRight className="ml-1 h-3.5 w-3.5" />
            </Button>
          </Link>
        </div>
      </header>

      {/* Hero */}
      <motion.section
        style={{ opacity: heroOpacity, scale: heroScale }}
        className="relative pt-36 pb-16 lg:pt-44 lg:pb-28 px-6 flex flex-col items-center text-center z-10 min-h-[85vh] justify-center"
      >
        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.1 }}
          className="mb-6 inline-flex items-center gap-2 px-3 py-1 rounded-full border border-border-bright bg-white/[0.03] backdrop-blur-md"
        >
          <span className="flex h-2 w-2 rounded-full bg-accent animate-pulse" />
          <span className="text-xs font-semibold text-text-secondary tracking-wide uppercase">Lendi Campus Innovation Platform</span>
        </motion.div>

        <motion.h1
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.2 }}
          className="text-5xl md:text-7xl lg:text-8xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-b from-white via-white to-white/40 mb-8 max-w-5xl leading-[1.05] font-display"
        >
          Build in public.<br />Prove your work.<br />
          <span className="gradient-accent-text">Earn your rank.</span>
        </motion.h1>

        <motion.p
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.3 }}
          className="text-lg md:text-xl text-text-secondary max-w-2xl mb-12 leading-relaxed"
        >
          Post ideas, recruit collaborators, run workshops, and build a verifiable proof-of-work trail.
          The campus innovation platform that proves the work.
        </motion.p>

        <motion.div
          initial={{ opacity: 0, y: 20 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.5, delay: 0.4 }}
          className="flex flex-col sm:flex-row items-center gap-4"
        >
          <Link href="/auth/register">
            <Button size="lg" variant="gradient" className="rounded-full h-14 px-8 text-base shadow-[0_0_40px_rgba(31,183,166,0.3)] animate-pulse-glow">
              Join IdeaSpace
            </Button>
          </Link>
          <Link href="/feed">
            <Button size="lg" variant="outline" className="rounded-full h-14 px-8 text-base">
              <Terminal className="mr-2 h-5 w-5" /> Browse Ideas
            </Button>
          </Link>
        </motion.div>

        {/* Hero glow orbs */}
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full max-w-5xl pointer-events-none -z-10">
          <motion.div
            animate={{ y: [0, -20, 0], rotate: [0, 5, 0] }}
            transition={{ duration: 8, repeat: Infinity, ease: "easeInOut" }}
            className="absolute top-[20%] left-[10%] w-64 h-64 rounded-full bg-accent/20 blur-[100px]"
          />
          <motion.div
            animate={{ y: [0, 30, 0], rotate: [0, -10, 0] }}
            transition={{ duration: 10, repeat: Infinity, ease: "easeInOut", delay: 1 }}
            className="absolute bottom-[20%] right-[10%] w-80 h-80 rounded-full bg-[#FF6B4A]/10 blur-[120px]"
          />
        </div>
      </motion.section>

      {/* Stats Strip */}
      <section className="py-12 border-y border-border bg-bg-secondary/30 backdrop-blur-sm relative z-20">
        <div className="max-w-7xl mx-auto px-6 grid grid-cols-2 md:grid-cols-4 gap-8">
          {[
            { label: "Ideas Posted", value: stats.totalIdeas || "0" },
            { label: "Active Builds", value: stats.activeBuilds || "0" },
            { label: "Projects Shipped", value: stats.shipped || "0" },
            { label: "Campus Builders", value: stats.totalUsers || "0" },
          ].map((stat, i) => (
            <motion.div
              initial={{ opacity: 0, y: 20 }}
              whileInView={{ opacity: 1, y: 0 }}
              viewport={{ once: true }}
              transition={{ delay: i * 0.1 }}
              key={i}
              className="text-center"
            >
              <div className="text-3xl md:text-4xl font-black text-white mb-2 tracking-tight font-display">{stat.value}</div>
              <div className="text-sm text-text-muted font-semibold uppercase tracking-wider">{stat.label}</div>
            </motion.div>
          ))}
        </div>
      </section>

      {/* Proof Pipeline */}
      <section id="process" className="py-24 px-6 relative z-20">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <Badge variant="cyan" className="mb-4">How It Works</Badge>
            <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-white mb-4 font-display">
              Post. Build. <span className="gradient-accent-text">Verify.</span>
            </h2>
            <p className="text-text-secondary text-lg max-w-2xl mx-auto">
              A clear path from idea to evidence. Designed to keep momentum, trust, and accountability.
            </p>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {pipelineSteps.map((step, i) => (
              <motion.div
                key={step.step}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.15 }}
              >
                <Card variant="glass" className="p-6 h-full hover:border-border-bright transition-colors">
                  <div className="flex items-center justify-between mb-4">
                    <div className="h-10 w-10 rounded-lg bg-bg-tertiary border border-border flex items-center justify-center">
                      <step.icon className="h-5 w-5 text-accent" />
                    </div>
                    <span className="text-xs font-mono text-text-muted">{step.step}</span>
                  </div>
                  <h3 className="text-xl font-bold text-white mb-2 font-display">{step.title}</h3>
                  <p className="text-text-secondary text-sm leading-relaxed">{step.description}</p>
                  <div className="mt-4 flex flex-wrap gap-2">
                    {step.chips.map((chip) => (
                      <Badge key={chip} variant="secondary" className="text-[10px]">{chip}</Badge>
                    ))}
                  </div>
                </Card>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Features Grid */}
      <section id="features" className="py-24 px-6 relative z-20">
        <div className="max-w-7xl mx-auto">
          <div className="text-center mb-16">
            <Badge variant="gold" className="mb-4">Platform Features</Badge>
            <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-white mb-4 font-display">
              Everything you need to <span className="gradient-ember-text">ship.</span>
            </h2>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {features.map((feat, i) => (
              <motion.div
                key={feat.title}
                initial={{ opacity: 0, y: 20 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: i * 0.08 }}
              >
                <Card variant="spotlight" className="p-6 h-full group">
                  <div className={`absolute inset-0 bg-gradient-to-br ${feat.color} to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-[var(--radius-lg)]`} />
                  <div className="relative z-10">
                    <div className="h-12 w-12 rounded-xl bg-bg-tertiary border border-border flex items-center justify-center mb-5">
                      <feat.icon className="h-6 w-6 text-accent" />
                    </div>
                    <h3 className="text-lg font-bold text-white mb-2 font-display">{feat.title}</h3>
                    <p className="text-text-secondary text-sm leading-relaxed">{feat.desc}</p>
                  </div>
                </Card>
              </motion.div>
            ))}
          </div>
        </div>
      </section>

      {/* Comparison */}
      <section id="compare" className="py-20 px-6 relative z-20">
        <div className="max-w-5xl mx-auto">
          <div className="text-center mb-12">
            <Badge variant="ember" className="mb-4">Reality Check</Badge>
            <h2 className="text-3xl md:text-4xl font-bold tracking-tight text-white font-display">IdeaSpace vs ad-hoc tools</h2>
            <p className="text-text-secondary max-w-2xl mx-auto mt-3">
              WhatsApp groups and Google Forms move fast but lose evidence. IdeaSpace keeps progress visible and verified.
            </p>
          </div>
          <div className="rounded-[var(--radius-lg)] border border-border overflow-hidden">
            <table className="w-full">
              <thead>
                <tr className="bg-bg-secondary/80">
                  <th className="text-left text-sm font-semibold text-text-muted p-4 w-1/3">Capability</th>
                  <th className="text-left text-sm font-semibold text-accent p-4 w-1/3">IdeaSpace</th>
                  <th className="text-left text-sm font-semibold text-text-muted p-4 w-1/3">WhatsApp / Forms</th>
                </tr>
              </thead>
              <tbody>
                {comparisonRows.map((row, i) => (
                  <tr key={i} className="border-t border-border hover:bg-bg-secondary/30 transition-colors">
                    <td className="p-4 text-sm text-text-primary font-medium">{row.label}</td>
                    <td className="p-4 text-sm text-[#2EA86A]">
                      <span className="flex items-center gap-2"><CheckCircle className="h-4 w-4" />{row.ideaspace}</span>
                    </td>
                    <td className="p-4 text-sm text-text-muted">
                      <span className="flex items-center gap-2"><XCircle className="h-4 w-4 text-[#E5484D]" />{row.traditional}</span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {/* Final CTA */}
      <section className="py-24 px-6 relative z-20">
        <div className="max-w-3xl mx-auto text-center">
          <h2 className="text-4xl md:text-5xl font-bold tracking-tight text-white mb-6 font-display">
            Ready to build?
          </h2>
          <p className="text-lg text-text-secondary mb-10 max-w-xl mx-auto">
            Join the campus builders who are turning ideas into shipped products with verifiable proof.
          </p>
          <Link href="/auth/register">
            <Button size="lg" variant="gradient" className="rounded-full h-14 px-10 text-base shadow-[0_0_50px_rgba(31,183,166,0.3)]">
              Create Your Profile <ArrowRight className="ml-2 h-5 w-5" />
            </Button>
          </Link>
        </div>
      </section>

      {/* Footer */}
      <footer className="border-t border-border bg-bg-secondary/50 pt-16 pb-8 px-6 relative z-20">
        <div className="max-w-7xl mx-auto">
          <div className="flex flex-col md:flex-row justify-between items-center mb-12">
            <div className="flex items-center gap-3 mb-6 md:mb-0">
              <div className="w-10 h-10 rounded-lg gradient-accent flex items-center justify-center shadow-lg shadow-accent/20">
                <Zap className="h-5 w-5 text-white" />
              </div>
              <span className="text-2xl font-bold tracking-tight text-white font-display">IdeaSpace</span>
            </div>
            <Link href="/auth/register">
              <Button variant="gradient" className="rounded-full">Join the Platform</Button>
            </Link>
          </div>
          <div className="flex flex-col md:flex-row justify-between items-center text-sm text-text-muted border-t border-border pt-8">
            <p>© {new Date().getFullYear()} Lendi Institute of Engineering & Technology.</p>
            <p className="mt-2 md:mt-0">Build in public. Prove your work. Earn your rank.</p>
          </div>
        </div>
      </footer>
    </div>
  );
}
