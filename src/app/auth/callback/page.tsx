"use client";

import { useEffect } from "react";
import { supabase } from "@/lib/supabase";
import { validateLendiEmail } from "@/lib/auth";
import { useRouter } from "next/navigation";
import { toast } from "sonner";

export default function AuthCallback() {
  const router = useRouter();

  useEffect(() => {
    const handleAuth = async () => {
      const { data: { session }, error } = await supabase.auth.getSession();

      if (error || !session) {
        router.push("/login");
        return;
      }

      const email = session.user?.email;
      if (email && !validateLendiEmail(email)) {
        await supabase.auth.signOut();
        toast.error("Access restricted to Lendi College email addresses only.");
        router.push("/login");
        return;
      }

      router.push("/dashboard");
    };

    handleAuth();
  }, [router]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-background">
      <div className="flex flex-col items-center gap-4">
        <div className="w-8 h-8 border-4 border-lendi border-t-transparent rounded-full animate-spin" />
        <p className="text-muted-foreground animate-pulse">Verifying credentials...</p>
      </div>
    </div>
  );
}
