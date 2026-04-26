"use client";

import { motion } from "framer-motion";
import {
  LayoutDashboard,
  Rocket,
  Shield,
  Users,
  MessageSquare,
  Trophy,
  Settings,
  Zap,
  Briefcase,
  Sparkles,
  BookOpen,
  Vote,
  BarChart3,
  UserCog,
  GraduationCap
} from "lucide-react";
import Link from "next/link";
import { usePathname } from "next/navigation";

const NAV_ITEMS = [
  { icon: LayoutDashboard, label: "Feed", href: "/dashboard" },
  { icon: Rocket, label: "Missions", href: "/projects" },
  { icon: Briefcase, label: "Manage", href: "/manage" },
  { icon: Shield, label: "Bounties", href: "/bounties" },
  { icon: Users, label: "Talent", href: "/talent" },
  { icon: Sparkles, label: "Mentors", href: "/mentorship" },
  { icon: BookOpen, label: "Resources", href: "/resources" },
  { icon: MessageSquare, label: "Comms", href: "/messages" },
  { icon: Trophy, label: "Hall of Fame", href: "/leaderboard" },
  { icon: BarChart3, label: "Intelligence", href: "/admin/analytics" },
  { icon: UserCog, label: "Registry", href: "/admin/users" },
  { icon: Settings, label: "Settings", href: "/settings" },
];

export const Sidebar = () => {
  const pathname = usePathname();

  return (
    <aside className="w-72 h-screen border-r border-border flex flex-col bg-card sticky top-0 shrink-0">
      <div className="p-10">
        <Link href="/" className="flex items-center gap-4 group cursor-pointer">
          <div className="w-11 h-11 rounded-2xl bg-lendi-blue flex items-center justify-center shadow-lendi transition-transform group-hover:rotate-6">
            <GraduationCap size={22} className="text-white" />
          </div>
          <div>
            <h1 className="text-xl font-black tracking-tight-inst uppercase leading-none">Idea<span className="text-lendi-blue">Sync</span></h1>
            <p className="text-[9px] font-black uppercase tracking-[0.3em] text-muted-foreground mt-1">Lendi Institute</p>
          </div>
        </Link>
      </div>

      <nav className="flex-1 px-6 space-y-1.5 overflow-y-auto custom-scrollbar pb-10">
        {NAV_ITEMS.map((item) => {
          const isActive = pathname === item.href;
          return (
            <Link key={item.href} href={item.href}>
              <motion.div
                whileHover={{ x: 4 }}
                className={`flex items-center gap-4 px-6 py-3.5 rounded-2xl transition-all duration-300 group ${
                  isActive
                  ? "bg-secondary text-lendi-blue shadow-sm border border-border"
                  : "text-muted-foreground hover:text-foreground"
                }`}
              >
                <item.icon className={`w-4 h-4 ${isActive ? "text-lendi-blue" : "group-hover:text-lendi-blue"} transition-colors`} />
                <span className="text-[10px] font-black uppercase tracking-widest leading-none">{item.label}</span>
                {isActive && (
                  <motion.div
                    layoutId="active-pill"
                    className="ml-auto w-1.5 h-1.5 rounded-full bg-lendi-blue shadow-lendi"
                  />
                )}
              </motion.div>
            </Link>
          );
        })}
      </nav>

      <div className="p-8">
        <Link href="/roadmap">
          <div className="p-6 rounded-[2rem] bg-secondary border border-border relative overflow-hidden group cursor-pointer hover:border-lendi-blue transition-colors shadow-sm">
            <div className="absolute -top-10 -right-10 w-24 h-24 bg-lendi-blue/5 blur-2xl rounded-full group-hover:bg-lendi-blue/10 transition-colors" />
            <h4 className="text-[9px] font-black uppercase tracking-widest text-muted-foreground/50 mb-2">Protocol Status</h4>
            <p className="text-xs font-bold text-foreground">V1.0 Operational</p>
            <div className="mt-4 h-1.5 w-full bg-border rounded-full overflow-hidden">
              <div className="h-full w-full bg-lendi-blue shadow-sm" />
            </div>
            <p className="text-[8px] font-black text-muted-foreground/30 mt-3 uppercase tracking-tighter">View Institutional Roadmap</p>
          </div>
        </Link>
      </div>
    </aside>
  );
};
