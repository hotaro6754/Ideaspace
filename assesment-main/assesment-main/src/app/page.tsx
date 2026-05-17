"use client"

import { AuthForm } from "@/components/auth/AuthForm"
import { useScrollReveal } from "@/hooks/use-scroll-reveal"
import { useUser, useDoc, useFirestore, useAuth } from "@/firebase"
import { doc } from "firebase/firestore"
import { Button } from "@/components/ui/button"
import { LogOut, LayoutDashboard, ShieldCheck, Fingerprint, Lock } from "lucide-react"
import { useRouter } from "next/navigation"
import { signOut } from "firebase/auth"
import { useMemoFirebase } from "@/firebase"
import { Loader2 } from "lucide-react"

export default function Home() {
  const containerRef = useScrollReveal()
  const router = useRouter()
  const auth = useAuth()
  const db = useFirestore()
  const { user, isUserLoading } = useUser()

  const userDocRef = useMemoFirebase(() => {
    if (!user) return null
    return doc(db, "users", user.uid)
  }, [db, user])
  const { data: userProfile, isLoading: profileLoading } = useDoc(userDocRef)

  const handleLogout = async () => {
    await signOut(auth)
  }

  return (
    <main ref={containerRef} className="min-h-screen flex flex-col items-center justify-center p-4 bg-background relative overflow-hidden">
      <div className="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none -z-10">
        <div className="absolute top-[-10%] left-[-10%] w-[40rem] h-[40rem] bg-primary/10 rounded-full blur-[100px] animate-pulse" />
        <div className="absolute bottom-[-10%] right-[-10%] w-[40rem] h-[40rem] bg-accent/10 rounded-full blur-[120px] animate-pulse" style={{animationDelay: '2s'}} />
      </div>

      <div className="reveal-up space-y-12 w-full max-w-md z-10">
        <div className="text-center space-y-4">
          <div className="inline-flex items-center justify-center p-3 rounded-2xl bg-primary/10 mb-4 glow-primary">
            <ShieldCheck className="w-12 h-12 text-primary" />
          </div>
          <h1 className="text-5xl font-extrabold tracking-tighter gradient-text">
            Zero-Trust
          </h1>
          <p className="text-muted-foreground text-lg tracking-wide uppercase font-bold text-[10px]">
            Secure Assessment Gateway
          </p>
        </div>

        {!isUserLoading && user ? (
          <div className="glass-card-hover p-8 rounded-[2.5rem] space-y-8 text-center animate-in fade-in zoom-in duration-500">
            <div className="space-y-2">
              <h2 className="text-2xl font-bold flex items-center justify-center gap-2">
                {profileLoading ? <Loader2 className="w-5 h-5 animate-spin"/> : <Fingerprint className="text-emerald-500 w-6 h-6" />}
                {profileLoading ? "Verifying..." : (userProfile?.username || "Authenticated Identity")}
              </h2>
              <p className="text-sm text-muted-foreground">Clearance level established.</p>
            </div>
            
            <div className="grid grid-cols-1 gap-4">
              <Button 
                onClick={() => router.push(`/dashboard/${userProfile?.role || 'student'}`)}
                className="w-full btn-premium py-7 text-lg rounded-2xl flex items-center gap-2 glow-primary"
              >
                <LayoutDashboard className="w-5 h-5" /> Access Dashboard
              </Button>
              <Button 
                variant="ghost" 
                onClick={handleLogout}
                className="w-full py-6 text-destructive hover:bg-destructive/10 hover:text-destructive rounded-2xl flex items-center gap-2"
              >
                <LogOut className="w-5 h-5" /> Sever Connection
              </Button>
            </div>
          </div>
        ) : (
          <div className="glass-card p-1 rounded-[2.5rem] glow-primary">
            <div className="bg-background rounded-[2.4rem] p-6">
              <AuthForm />
            </div>
          </div>
        )}

        <div className="grid grid-cols-2 gap-4 pt-4">
          <div className="p-5 glass-card rounded-2xl text-center flex flex-col items-center gap-2 hover:border-primary/50 transition-colors">
            <Lock className="w-5 h-5 text-primary" />
            <div className="space-y-1">
              <h3 className="text-xs font-bold text-foreground uppercase tracking-widest">Encrypted</h3>
              <p className="text-[10px] text-muted-foreground">E2E Delivery</p>
            </div>
          </div>
          <div className="p-5 glass-card rounded-2xl text-center flex flex-col items-center gap-2 hover:border-accent/50 transition-colors">
            <Fingerprint className="w-5 h-5 text-accent" />
            <div className="space-y-1">
              <h3 className="text-xs font-bold text-foreground uppercase tracking-widest">Proctored</h3>
              <p className="text-[10px] text-muted-foreground">Focus Enforced</p>
            </div>
          </div>
        </div>
      </div>
    </main>
  )
}
