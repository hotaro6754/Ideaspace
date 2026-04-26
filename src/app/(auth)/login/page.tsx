"use client";

import { useState } from "react";
import { Button } from "@/components/ui/Button";
import { Input } from "@/components/ui/Input";
import { BackgroundGradient } from "@/components/ui/BackgroundGradient";
import { signIn, validateLendiEmail } from "@/lib/auth";
import { logger } from "@/lib/logger";
import { motion } from "framer-motion";
import Link from "next/link";

export default function LoginPage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsLoading(true);
    setError(null);

    try {
      if (!validateLendiEmail(email)) {
        throw new Error("Please use your @lendi.org or @liethub.org email.");
      }
      await signIn(email, password);
      window.location.href = "/dashboard";
    } catch (err: any) {
      setError(err.message);
      logger.error("Login", "Login failed", err);
    } finally {
      setIsLoading(false);
    }
  };

  return (
    <div className="relative min-h-screen flex items-center justify-center overflow-hidden text-white font-inter p-6">
      <BackgroundGradient />

      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.8, ease: [0.2, 0, 0, 1] }}
        className="w-full max-w-md glass p-8 rounded-3xl relative z-10 shadow-2xl border border-white/10"
      >
        <div className="text-center mb-8">
          <motion.h1
            className="text-4xl font-bold font-plus-jakarta mb-2 tracking-tight"
            initial={{ scale: 0.9 }}
            animate={{ scale: 1 }}
          >
            IdeaSync
          </motion.h1>
          <p className="text-white/50 text-sm">
            Lendi College's Innovation Operating System
          </p>
        </div>

        <form onSubmit={handleLogin} className="space-y-4">
          <div className="space-y-2">
            <label className="text-xs font-medium text-white/40 uppercase tracking-widest ml-1">
              College Email
            </label>
            <Input
              type="email"
              placeholder="rollno@lendi.org"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              className="bg-white/5 border-white/10 focus:border-lendi-blue"
            />
          </div>

          <div className="space-y-2">
            <label className="text-xs font-medium text-white/40 uppercase tracking-widest ml-1">
              Password
            </label>
            <Input
              type="password"
              placeholder="••••••••"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
              className="bg-white/5 border-white/10 focus:border-lendi-blue"
            />
          </div>

          {error && (
            <motion.p
              initial={{ opacity: 0 }}
              animate={{ opacity: 1 }}
              className="text-lendi-red text-xs mt-2 text-center"
            >
              {error}
            </motion.p>
          )}

          <Button
            type="submit"
            className="w-full h-12 text-base font-semibold mt-4 shadow-lg shadow-lendi-blue/20"
            disabled={isLoading}
          >
            {isLoading ? "Identifying..." : "Enter Workspace"}
          </Button>
        </form>

        <div className="mt-8 text-center space-y-4">
          <p className="text-xs text-white/30">
            Don't have access?{" "}
            <Link href="/register" className="text-white hover:text-lendi-blue transition-colors underline-offset-4 underline">
              Create an account
            </Link>
          </p>

          <div className="pt-4 border-t border-white/5 flex justify-center gap-6">
            <button className="text-[10px] text-white/20 hover:text-white transition-colors uppercase tracking-widest">
              Security
            </button>
            <button className="text-[10px] text-white/20 hover:text-white transition-colors uppercase tracking-widest">
              Privacy
            </button>
            <button className="text-[10px] text-white/20 hover:text-white transition-colors uppercase tracking-widest">
              Support
            </button>
          </div>
        </div>
      </motion.div>
    </div>
  );
}
