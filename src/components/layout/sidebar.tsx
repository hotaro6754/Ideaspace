"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { cn } from "@/lib/utils";
import { useState } from "react";
import { signOut, useSession } from "next-auth/react";
import { motion, AnimatePresence } from "framer-motion";
import { Avatar } from "@/components/ui/avatar";
import {
  Lightbulb,
  Trophy,
  Flame,
  Archive,
  ShieldCheck,
  Hammer,
  Target,
  Bell,
  Menu,
  X,
  Zap,
  Plus,
  ChevronsLeft,
  ChevronsRight,
  LogOut,
  Settings,
  LayoutDashboard,
  User,
} from "lucide-react";

const NAV_ITEMS = [
  { name: "Feed", href: "/feed", icon: Lightbulb },
  { name: "Leaderboard", href: "/leaderboard", icon: Trophy },
  { name: "The Forge", href: "/forge", icon: Hammer },
  { name: "Proof Wall", href: "/wall", icon: ShieldCheck },
  { name: "Bounties", href: "/bounties", icon: Target },
  { name: "Archive", href: "/archive", icon: Archive },
  { name: "Notifications", href: "/notifications", icon: Bell },
];

const ADMIN_ITEMS = [
  { name: "Admin", href: "/admin", icon: LayoutDashboard },
];

export function Sidebar() {
  const pathname = usePathname();
  const { data: session } = useSession();
  const [mobileOpen, setMobileOpen] = useState(false);
  const [collapsed, setCollapsed] = useState(false);

  const user = session?.user as Record<string, unknown> | undefined;
  const isAdmin = user?.role === "admin" || user?.role === "faculty";

  return (
    <>
      {/* Mobile Top Bar */}
      <div className="md:hidden fixed top-0 left-0 right-0 z-50 h-14 glass-strong flex items-center justify-between px-4">
        <Link href="/feed" className="flex items-center gap-2 font-bold text-sm tracking-tight">
          <div className="w-7 h-7 rounded-lg gradient-accent flex items-center justify-center shadow-lg shadow-accent/20">
            <Zap className="h-3.5 w-3.5 text-white" />
          </div>
          <span className="font-display">IdeaSpace</span>
        </Link>
        <div className="flex items-center gap-1">
          <Link href="/ideas/new" className="p-2 rounded-lg hover:bg-white/[0.04] transition-colors" aria-label="New idea">
            <Plus className="h-4 w-4 text-text-secondary" />
          </Link>
          <Link href="/notifications" className="relative p-2 rounded-lg hover:bg-white/[0.04] transition-colors" aria-label="Notifications">
            <Bell className="h-4 w-4 text-text-secondary" />
            <span className="absolute top-1.5 right-1.5 h-1.5 w-1.5 rounded-full bg-accent" />
          </Link>
          <button
            onClick={() => setMobileOpen(!mobileOpen)}
            className="p-2 rounded-lg hover:bg-white/[0.04] transition-colors"
            aria-label="Toggle menu"
          >
            {mobileOpen ? <X className="h-5 w-5" /> : <Menu className="h-5 w-5" />}
          </button>
        </div>
      </div>

      {/* Mobile Overlay */}
      <AnimatePresence>
        {mobileOpen && (
          <motion.div
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            className="md:hidden fixed inset-0 z-40 bg-black/60 backdrop-blur-sm"
            onClick={() => setMobileOpen(false)}
          />
        )}
      </AnimatePresence>

      {/* Sidebar */}
      <aside
        className={cn(
          "fixed inset-y-0 left-0 z-50 flex flex-col border-r border-border bg-bg-primary transition-all duration-300 ease-in-out",
          "md:translate-x-0",
          collapsed ? "md:w-[68px]" : "md:w-[260px]",
          mobileOpen ? "translate-x-0 w-[260px]" : "-translate-x-full md:translate-x-0"
        )}
      >
        {/* Logo */}
        <div className="flex h-16 shrink-0 items-center justify-between px-4 border-b border-border">
          <Link
            href="/feed"
            className={cn("flex items-center gap-2.5 font-bold tracking-tight transition-all font-display", collapsed && "md:justify-center")}
          >
            <div className="w-8 h-8 rounded-lg gradient-accent flex items-center justify-center shadow-lg shadow-accent/20 shrink-0">
              <Zap className="h-4 w-4 text-white" />
            </div>
            {!collapsed && <span className="text-lg">IdeaSpace</span>}
          </Link>
          <button
            onClick={() => setCollapsed(!collapsed)}
            className="hidden md:flex p-1.5 rounded-md text-text-muted hover:text-text-secondary hover:bg-white/[0.04] transition-colors"
            aria-label="Toggle sidebar"
          >
            {collapsed ? <ChevronsRight className="h-4 w-4" /> : <ChevronsLeft className="h-4 w-4" />}
          </button>
        </div>

        {/* Quick Action */}
        {!collapsed ? (
          <div className="px-3 pt-4 pb-2">
            <Link href="/ideas/new">
              <button className="w-full flex items-center gap-2 h-9 px-3 rounded-lg bg-accent/10 border border-accent/20 text-accent text-sm font-medium hover:bg-accent/15 transition-all cursor-pointer">
                <Plus className="h-4 w-4" />
                New Idea
              </button>
            </Link>
          </div>
        ) : (
          <div className="px-3 pt-4 pb-2 flex justify-center">
            <Link href="/ideas/new">
              <button className="h-9 w-9 flex items-center justify-center rounded-lg bg-accent/10 border border-accent/20 text-accent hover:bg-accent/15 transition-all cursor-pointer" aria-label="New idea">
                <Plus className="h-4 w-4" />
              </button>
            </Link>
          </div>
        )}

        {/* Navigation */}
        <nav className="flex-1 overflow-y-auto px-3 py-4 space-y-1">
          {!collapsed && (
            <div className="mb-3 px-3 text-[10px] font-semibold uppercase tracking-[0.15em] text-text-muted">
              Platform
            </div>
          )}

          {NAV_ITEMS.map((item) => {
            const isActive = pathname === item.href || (item.href !== "/feed" && pathname.startsWith(item.href));
            return (
              <Link
                key={item.name}
                href={item.href}
                onClick={() => setMobileOpen(false)}
                className={cn(
                  "group relative flex items-center rounded-lg text-sm font-medium transition-all duration-200",
                  collapsed ? "justify-center h-10 w-10 mx-auto" : "px-3 py-2.5 gap-3",
                  isActive
                    ? "text-text-primary"
                    : "text-text-secondary hover:text-text-primary hover:bg-white/[0.03]"
                )}
              >
                {isActive && (
                  <motion.div
                    layoutId="activeNav"
                    className="absolute inset-0 bg-accent/[0.08] border border-accent/15 rounded-lg"
                    transition={{ type: "spring", bounce: 0.15, duration: 0.5 }}
                  />
                )}
                <item.icon
                  className={cn(
                    "shrink-0 h-[18px] w-[18px] transition-colors relative z-10",
                    isActive ? "text-accent" : "text-text-muted group-hover:text-text-secondary"
                  )}
                />
                {!collapsed && <span className="relative z-10">{item.name}</span>}
                {isActive && !collapsed && (
                  <div className="ml-auto w-1.5 h-1.5 rounded-full bg-accent relative z-10" />
                )}
              </Link>
            );
          })}

          {isAdmin && (
            <>
              <div className="my-4 border-t border-border" />
              {!collapsed && (
                <div className="mb-3 px-3 text-[10px] font-semibold uppercase tracking-[0.15em] text-text-muted">
                  Admin
                </div>
              )}
              {ADMIN_ITEMS.map((item) => {
                const isActive = pathname.startsWith(item.href);
                return (
                  <Link
                    key={item.name}
                    href={item.href}
                    onClick={() => setMobileOpen(false)}
                    className={cn(
                      "group relative flex items-center rounded-lg text-sm font-medium transition-all duration-200",
                      collapsed ? "justify-center h-10 w-10 mx-auto" : "px-3 py-2.5 gap-3",
                      isActive
                        ? "text-text-primary"
                        : "text-text-secondary hover:text-text-primary hover:bg-white/[0.03]"
                    )}
                  >
                    <item.icon
                      className={cn(
                        "shrink-0 h-[18px] w-[18px] transition-colors",
                        isActive ? "text-accent" : "text-text-muted group-hover:text-text-secondary"
                      )}
                    />
                    {!collapsed && <span>{item.name}</span>}
                  </Link>
                );
              })}
            </>
          )}

          <div className="my-4 border-t border-border" />

          {!collapsed && (
            <div className="mb-3 px-3 text-[10px] font-semibold uppercase tracking-[0.15em] text-text-muted">
              Account
            </div>
          )}

          <Link
            href={user?.username ? `/profile/${user.username}` : "/profile/me"}
            onClick={() => setMobileOpen(false)}
            className={cn(
              "group flex items-center rounded-lg text-sm font-medium text-text-secondary transition-all hover:text-text-primary hover:bg-white/[0.03]",
              collapsed ? "justify-center h-10 w-10 mx-auto" : "px-3 py-2.5 gap-3"
            )}
          >
            <User className="shrink-0 h-[18px] w-[18px] text-text-muted group-hover:text-text-secondary" />
            {!collapsed && <span>Profile</span>}
          </Link>
        </nav>

        {/* User Card */}
        <div className="p-3 border-t border-border">
          {collapsed ? (
            <div className="flex justify-center">
              <Avatar
                name={user?.name as string ?? "User"}
                tier={(user?.rankTier as string ?? "Bronze") as "Bronze" | "Silver" | "Gold" | "Platinum" | "Elite"}
                size="sm"
              />
            </div>
          ) : (
            <div className="flex items-center gap-3 rounded-xl border border-border bg-bg-secondary/50 p-3 hover:border-border-bright transition-colors">
              <Avatar
                name={user?.name as string ?? "User"}
                tier={(user?.rankTier as string ?? "Bronze") as "Bronze" | "Silver" | "Gold" | "Platinum" | "Elite"}
                size="sm"
              />
              <div className="flex-1 flex flex-col min-w-0">
                <span className="truncate text-sm font-medium text-text-primary">
                  {user?.name as string ?? "Builder"}
                </span>
                <span className="truncate text-[10px] text-text-muted font-mono">
                  {user?.rankTier as string ?? "Bronze"} • {user?.points as number ?? 0} pts
                </span>
              </div>
              <button
                onClick={() => signOut({ callbackUrl: "/" })}
                className="text-text-muted hover:text-[#E5484D] cursor-pointer transition-colors shrink-0"
                aria-label="Sign out"
              >
                <LogOut className="h-4 w-4" />
              </button>
            </div>
          )}
        </div>
      </aside>
    </>
  );
}
