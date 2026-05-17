"use client";

import { useState } from "react";
import { signIn } from "next-auth/react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Github, Mail, ArrowRight, ShieldCheck, Chrome } from "lucide-react";
import { motion } from "framer-motion";
import Link from "next/link";
import { toast } from "@/components/ui/ToastSystem";

export default function LoginPage() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [loading, setLoading] = useState<string | null>(null);

  const handleCredentialsLogin = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading("credentials");
    try {
      const result = await signIn("credentials", {
        email,
        password,
        redirect: true,
        callbackUrl: "/dashboard",
      });
      if (result?.error) {
        toast.error("Sign in failed", result.error);
      }
    } catch {
      toast.error("Error", "Failed to sign in");
    } finally {
      setLoading(null);
    }
  };

  const handleGitHubLogin = async () => {
    setLoading("github");
    try {
      await signIn("github", { callbackUrl: "/dashboard" });
    } catch {
      toast.error("Error", "GitHub login failed");
      setLoading(null);
    }
  };

  const handleGoogleLogin = async () => {
    setLoading("google");
    try {
      await signIn("google", { callbackUrl: "/dashboard" });
    } catch {
      toast.error("Error", "Google login failed");
      setLoading(null);
    }
  };

  return (
    <div className="min-h-screen bg-bg flex flex-col items-center justify-center p-6">
      <motion.div
        initial={{ opacity: 0, y: 20 }}
        animate={{ opacity: 1, y: 0 }}
        className="w-full max-w-md"
      >
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center h-16 w-16 rounded-2xl bg-surface-raised border border-border-default mb-4 shadow-xl">
            <ShieldCheck className="h-8 w-8 text-brand-primary" />
          </div>
          <h1 className="text-3xl font-bold tracking-tight text-text-primary font-display">Welcome Back</h1>
          <p className="text-text-secondary mt-2">Sign in to IdeaSpace to continue building.</p>
        </div>

        <Card variant="glass" className="p-8">
          <div className="flex flex-col gap-3 mb-6">
            <Button
              variant="secondary"
              className="w-full h-12 gap-3 font-bold hover:bg-surface-overlay transition-all"
              onClick={handleGitHubLogin}
              disabled={loading !== null}
            >
              <Github className="h-5 w-5" /> {loading === "github" ? "Connecting..." : "Sign in with GitHub"}
            </Button>
            
            <Button
              variant="secondary"
              className="w-full h-12 gap-3 font-bold hover:bg-surface-overlay transition-all"
              onClick={handleGoogleLogin}
              disabled={loading !== null}
            >
              <Chrome className="h-5 w-5" /> {loading === "google" ? "Connecting..." : "Sign in with Google"}
            </Button>
          </div>

          <div className="relative mb-6">
            <div className="absolute inset-0 flex items-center">
              <div className="w-full border-t border-border-default"></div>
            </div>
            <div className="relative flex justify-center text-xs uppercase">
              <span className="bg-surface-base px-2 text-text-muted font-bold tracking-widest">Or email</span>
            </div>
          </div>

          {/* Credentials Login */}
          <form onSubmit={handleCredentialsLogin} className="space-y-4">
            <Input
              label="Institutional Email"
              type="email"
              placeholder="name@lendi.org"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              required
              icon={<Mail className="h-4 w-4" />}
            />
            <Input
              label="Password"
              type="password"
              placeholder="••••••••"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              required
            />
            <Button
              variant="gradient"
              className="w-full h-12 font-bold mt-2"
              type="submit"
              disabled={loading !== null}
            >
              {loading === "credentials" ? "Signing In..." : (
                <>Sign In <ArrowRight className="ml-2 h-4 w-4" /></>
              )}
            </Button>
          </form>
        </Card>

        <p className="text-center mt-8 text-sm text-text-muted">
          Don't have an account?{" "}
          <Link href="/auth/register" className="text-brand-primary font-bold hover:underline">
            Request Access
          </Link>
        </p>

        <div className="mt-12 flex items-center justify-center gap-6 opacity-40 grayscale hover:grayscale-0 transition-all duration-500">
          <div className="text-[10px] font-bold uppercase tracking-[0.2em] text-text-muted">Trusted by</div>
          <div className="h-4 w-px bg-border-default"></div>
          <div className="text-xs font-display font-black text-text-primary">LIET</div>
          <div className="text-xs font-display font-black text-text-primary">ACM</div>
          <div className="text-xs font-display font-black text-text-primary">GDSC</div>
        </div>
      </motion.div>
    </div>
  );
}
