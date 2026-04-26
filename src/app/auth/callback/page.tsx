"use client";

import { useEffect } from "react";
import { supabase } from "@/lib/supabase";
import { validateLendiEmail, extractEmailMetadata } from "@/lib/auth";
import { useRouter } from "next/navigation";
import { toast } from "sonner";

export default function AuthCallback() {
  const router = useRouter();

  useEffect(() => {
    const handleAuth = async () => {
      const { data: { session }, error } = await supabase.auth.getSession();

      if (error || !session) {
        toast.error("Authentication session failed.");
        router.push("/login");
        return;
      }

      const email = session.user?.email;
      if (email && !validateLendiEmail(email)) {
        await supabase.auth.signOut();
        toast.error("Access restricted to verified institutional accounts (@lendi.edu.in, @lendi.org).");
        router.push("/login");
        return;
      }

      // Check if profile exists and update metadata
      const { data: profile } = await supabase
        .from('profiles')
        .select('*')
        .eq('id', session.user.id)
        .single();

      if (!profile) {
        // Redirect to onboarding for new OAuth users
        router.push("/onboarding");
      } else {
        router.push("/dashboard");
      }
    };

    handleAuth();
  }, [router]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-[#020617] mesh-gradient">
      <div className="flex flex-col items-center gap-6">
        <div className="w-12 h-12 border-4 border-lendi border-t-transparent rounded-full animate-spin shadow-2xl shadow-lendi/20" />
        <div className="text-center">
          <p className="text-[10px] font-black text-white uppercase tracking-[0.4em] animate-pulse mb-2">Syncing Protocol</p>
          <p className="text-xs text-white/40 font-medium">Establishing secure link to Lendi network...</p>
        </div>
      </div>
    </div>
  );
}
