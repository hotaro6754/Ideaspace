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
  Briefcase
} from "lucide-react";
import Link from "next/link";
import { usePathname } from "next/navigation";

const NAV_ITEMS = [
  { icon: LayoutDashboard, label: "Feed", href: "/dashboard" },
  { icon: Rocket, label: "Missions", href: "/projects" },
  { icon: Briefcase, label: "Manage", href: "/manage" },
  { icon: Shield, label: "Bounties", href: "/bounties" },
  { icon: Users, label: "Talent", href: "/talent" },
  { icon: MessageSquare, label: "Comms", href: "/messages" },
  { icon: Trophy, label: "Leaderboard", href: "/leaderboard" },
];

export const Sidebar = () => {
  const pathname = usePathname();

  return (
    <aside className="w-80 h-screen border-r border-white/5 flex flex-col bg-black/50 backdrop-blur-3xl sticky top-0">
      <div className="p-10">
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

      <nav className="flex-1 px-6 space-y-2 overflow-y-auto custom-scrollbar">
        {NAV_ITEMS.map((item) => {
          const isActive = pathname === item.href;
          return (
            <Link key={item.href} href={item.href}>
              <motion.div
                whileHover={{ x: 5 }}
                className={`flex items-center gap-4 px-6 py-4 rounded-2xl transition-all duration-300 group ${
                  isActive
                  ? "bg-white/5 border border-white/5 text-white"
                  : "text-white/30 hover:text-white"
                }`}
              >
                <item.icon className={`w-5 h-5 ${isActive ? "text-lendi-blue" : "group-hover:text-lendi-blue"} transition-colors`} />
                <span className="text-xs font-black uppercase tracking-widest">{item.label}</span>
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

      <div className="p-8">
        <div className="p-6 rounded-[2rem] bg-white/5 border border-white/5 relative overflow-hidden group">
          <div className="absolute -top-10 -right-10 w-24 h-24 bg-lendi-blue/10 blur-2xl rounded-full group-hover:bg-lendi-blue/20 transition-colors" />
          <h4 className="text-[10px] font-black uppercase tracking-widest text-white/40 mb-2">Current Status</h4>
          <p className="text-sm font-bold text-white">Seedling</p>
          <div className="mt-4 h-1 w-full bg-white/5 rounded-full overflow-hidden">
            <div className="h-full w-1/3 bg-lendi-blue shadow-[0_0_10px_rgba(0,74,153,0.5)]" />
          </div>
          <p className="text-[8px] font-bold text-white/20 mt-2 uppercase tracking-tighter">840 XP TO INNOVATOR</p>
        </div>
      </div>
    </aside>
  );
};
