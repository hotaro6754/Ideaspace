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
  BarChart3
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
  { icon: Vote, label: "Polls", href: "/polls" },
  { icon: MessageSquare, label: "Comms", href: "/messages" },
  { icon: Trophy, label: "Leaderboard", href: "/leaderboard" },
  { icon: BarChart3, label: "Analytics", href: "/admin/analytics" },
  { icon: Settings, label: "Settings", href: "/settings" },
];

export const Sidebar = () => {
  const pathname = usePathname();

  return (
    <aside className="w-72 h-screen border-r border-white/5 flex flex-col bg-black/50 backdrop-blur-3xl sticky top-0 shrink-0">
      <div className="p-8">
        <div className="flex items-center gap-3 group cursor-pointer">
          <div className="w-10 h-10 rounded-2xl bg-lendi-blue flex items-center justify-center shadow-2xl shadow-lendi-blue/40 rotate-3 group-hover:rotate-0 transition-transform duration-500">
            <Zap className="w-6 h-6 text-white fill-white" />
          </div>
          <div>
            <h1 className="text-xl font-black font-plus-jakarta tracking-tighter">IdeaSync</h1>
            <p className="text-[8px] font-black uppercase tracking-[0.4em] text-lendi-blue opacity-50">Sentinel V1</p>
          </div>
        </div>
      </div>

      <nav className="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar pb-10">
        {NAV_ITEMS.map((item) => {
          const isActive = pathname === item.href;
          return (
            <Link key={item.href} href={item.href}>
              <motion.div
                whileHover={{ x: 5 }}
                className={`flex items-center gap-3 px-6 py-3.5 rounded-2xl transition-all duration-300 group ${
                  isActive
                  ? "bg-white/5 border border-white/5 text-white shadow-xl shadow-black/20"
                  : "text-white/20 hover:text-white"
                }`}
              >
                <item.icon className={`w-4 h-4 ${isActive ? "text-lendi-blue" : "group-hover:text-lendi-blue"} transition-colors`} />
                <span className="text-[10px] font-black uppercase tracking-widest">{item.label}</span>
                {isActive && (
                  <motion.div
                    layoutId="active-pill"
                    className="ml-auto w-1.5 h-1.5 rounded-full bg-lendi-blue shadow-[0_0_10px_rgba(0,74,153,1)]"
                  />
                )}
              </motion.div>
            </Link>
          );
        })}
      </nav>

      <div className="p-6">
        <Link href="/roadmap">
          <div className="p-5 rounded-[2rem] bg-white/5 border border-white/5 relative overflow-hidden group cursor-pointer">
            <div className="absolute -top-10 -right-10 w-24 h-24 bg-lendi-blue/10 blur-2xl rounded-full group-hover:bg-lendi-blue/20 transition-colors" />
            <h4 className="text-[9px] font-black uppercase tracking-widest text-white/40 mb-2">System Status</h4>
            <p className="text-xs font-bold text-white">V1.0 Operational</p>
            <div className="mt-4 h-1 w-full bg-white/5 rounded-full overflow-hidden">
              <div className="h-full w-full bg-lendi-blue shadow-[0_0_10px_rgba(0,74,153,0.5)]" />
            </div>
            <p className="text-[7px] font-bold text-white/20 mt-2 uppercase tracking-tighter">VIEW PLATFORM ROADMAP</p>
          </div>
        </Link>
      </div>
    </aside>
  );
};
