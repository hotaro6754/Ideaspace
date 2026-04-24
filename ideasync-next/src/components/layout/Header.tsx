"use client";

import { Search, Bell, User, ChevronDown } from "lucide-react";
import { motion } from "framer-motion";

interface HeaderProps {
  title: string;
}

export const Header = ({ title }: HeaderProps) => {
  return (
    <header className="h-20 border-b border-white/5 flex items-center justify-between px-10 glass sticky top-0 z-50">
      <div className="flex items-center gap-6">
        <h2 className="text-sm font-black uppercase tracking-[0.3em] text-white/40">{title}</h2>
        <div className="h-4 w-px bg-white/10" />
        <div className="flex items-center gap-2 px-3 py-1 rounded-full bg-green-500/10 border border-green-500/20">
          <div className="w-1 h-1 rounded-full bg-green-500 animate-pulse" />
          <span className="text-[8px] font-black uppercase text-green-400">Node: Lendi-Main</span>
        </div>
      </div>

      <div className="flex items-center gap-8">
        <div className="relative group hidden md:block">
          <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20 group-focus-within:text-lendi-blue transition-colors" />
          <input
            type="text"
            placeholder="Search Global Feed..."
            className="bg-white/5 border border-white/5 rounded-2xl pl-12 pr-6 h-11 w-64 text-xs focus:outline-none focus:border-lendi-blue/50 transition-all placeholder:text-white/10"
          />
        </div>

        <div className="flex items-center gap-4">
          <button className="p-2.5 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition-colors relative">
            <Bell className="w-4 h-4 text-white/40" />
            <div className="absolute top-2.5 right-2.5 w-1.5 h-1.5 bg-lendi-blue rounded-full shadow-[0_0_10px_rgba(0,74,153,1)]" />
          </button>

          <div className="flex items-center gap-3 pl-4 border-l border-white/10 group cursor-pointer">
            <div className="w-9 h-9 rounded-xl bg-gradient-to-br from-lendi-blue to-purple-600 p-px">
              <div className="w-full h-full rounded-[11px] bg-black flex items-center justify-center">
                <User className="w-4 h-4 text-white/40" />
              </div>
            </div>
            <ChevronDown className="w-3 h-3 text-white/20 group-hover:text-white transition-colors" />
          </div>
        </div>
      </div>
    </header>
  );
};
