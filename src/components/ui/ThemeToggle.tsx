"use client";

import { useTheme } from "next-themes";
import { useEffect, useState } from "react";
import { Sun, Moon } from "lucide-react";
import { motion } from "framer-motion";

export const ThemeToggle = () => {
  const { theme, setTheme } = useTheme();
  const [mounted, setMounted] = useState(false);

  useEffect(() => setMounted(true), []);

  if (!mounted) return null;

  return (
    <button
      onClick={() => setTheme(theme === "dark" ? "light" : "dark")}
      className="p-2.5 rounded-xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all group relative overflow-hidden"
    >
      <motion.div
        initial={false}
        animate={{ y: theme === "dark" ? 0 : 40 }}
        className="flex flex-col items-center"
      >
        <Moon className="w-4 h-4 text-white/40 group-hover:text-lendi-blue transition-colors mb-6" />
        <Sun className="w-4 h-4 text-white/40 group-hover:text-yellow-500 transition-colors" />
      </motion.div>
    </button>
  );
};
