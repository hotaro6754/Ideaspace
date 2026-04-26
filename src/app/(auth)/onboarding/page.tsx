"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Button } from "@/components/ui/Button";
import { User, Rocket, GraduationCap, ShieldCheck, Check, Building2, Target, Loader2 } from "lucide-react";
import { supabase } from "@/lib/supabase";
import { toast } from "sonner";

const ROLES = [
  { id: "student", name: "Student", icon: User, desc: "Collaborate on research and development" },
  { id: "alumni", name: "Alumni", icon: GraduationCap, desc: "Mentor students and share industry insights" },
  { id: "faculty", name: "Faculty", icon: ShieldCheck, desc: "Manage bounties and academic tracks" },
];

const DEPARTMENTS = ["CSE", "CSM", "CSIT", "CSSE", "ECE", "EEE", "MECH"];

const INTERESTS = [
  "Artificial Intelligence", "Full-Stack Dev", "Mobile Systems", "Cybersecurity",
  "Blockchain", "Cloud Infrastructure", "UI/UX Architecture", "Data Science",
  "Robotics", "Embedded Systems", "IoT Solutions", "Game Engine Dev"
];

export default function OnboardingPage() {
  const [step, setStep] = useState(1);
  const [selectedRole, setSelectedRole] = useState<string | null>(null);
  const [selectedDept, setSelectedDept] = useState<string | null>(null);
  const [selectedInterests, setSelectedInterests] = useState<string[]>([]);
  const [loading, setLoading] = useState(false);

  const toggleInterest = (interest: string) => {
    setSelectedInterests((prev) =>
      prev.includes(interest) ? prev.filter((i) => i !== interest) : [...prev, interest]
    );
  };

  const handleComplete = async () => {
    if (!selectedRole || !selectedDept || selectedInterests.length === 0) return;
    setLoading(true);
    try {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) throw new Error("Authentication node not found");

      const { error } = await supabase
        .from('profiles')
        .update({
          role: selectedRole,
          department: selectedDept,
          interests: selectedInterests,
          onboarded: true
        })
        .eq('id', user.id);

      if (error) throw error;

      toast.success("Institutional profile synchronized");
      window.location.href = "/dashboard";
    } catch (e: any) {
      toast.error(e.message);
      setLoading(false);
    }
  };

  return (
    <div className="relative min-h-screen flex items-center justify-center bg-background soft-grid p-6">
      <div className="absolute top-[10%] left-[10%] w-[30vw] h-[30vw] bg-lendi-blue/5 rounded-full blur-[100px] pointer-events-none" />

      <motion.div
        layout
        className="w-full max-w-2xl inst-card p-12 bg-card shadow-premium relative z-10"
      >
        <AnimatePresence mode="wait">
          {step === 1 && (
            <motion.div key="step1" initial={{ opacity: 0, x: 20 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: -20 }} className="space-y-10">
              <div className="text-center">
                <div className="w-12 h-12 rounded-2xl bg-secondary mx-auto mb-6 flex items-center justify-center text-lendi-blue border border-border shadow-sm">
                  <Target size={24} />
                </div>
                <h2 className="text-3xl font-black tracking-tight-inst mb-2 uppercase">Define Your Identity</h2>
                <p className="text-muted-foreground font-medium">Select your primary role within the Lendi network.</p>
              </div>

              <div className="grid grid-cols-1 gap-4">
                {ROLES.map((role) => (
                  <button
                    key={role.id}
                    onClick={() => { setSelectedRole(role.id); setStep(2); }}
                    className={`group relative flex items-center gap-6 p-6 rounded-2xl border transition-all text-left ${
                      selectedRole === role.id ? "bg-lendi-blue/5 border-lendi-blue shadow-sm" : "bg-secondary/50 border-border hover:border-lendi-blue/30"
                    }`}
                  >
                    <div className={`w-14 h-14 rounded-xl flex items-center justify-center transition-colors ${
                      selectedRole === role.id ? "bg-lendi-blue text-white" : "bg-white text-muted-foreground group-hover:bg-lendi-blue/10 group-hover:text-lendi-blue"
                    }`}>
                      <role.icon className="w-6 h-6" />
                    </div>
                    <div>
                      <h3 className="font-black uppercase tracking-widest text-sm text-foreground">{role.name}</h3>
                      <p className="text-muted-foreground text-xs font-medium">{role.desc}</p>
                    </div>
                  </button>
                ))}
              </div>
            </motion.div>
          )}

          {step === 2 && (
            <motion.div key="step2" initial={{ opacity: 0, x: 20 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: -20 }} className="space-y-10">
              <div className="text-center">
                <div className="w-12 h-12 rounded-2xl bg-secondary mx-auto mb-6 flex items-center justify-center text-lendi-blue border border-border shadow-sm">
                  <Building2 size={24} />
                </div>
                <h2 className="text-3xl font-black tracking-tight-inst mb-2 uppercase">Institutional Sector</h2>
                <p className="text-muted-foreground font-medium">Which department do you represent at LIET?</p>
              </div>

              <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                {DEPARTMENTS.map((dept) => (
                  <button
                    key={dept}
                    onClick={() => { setSelectedDept(dept); setStep(3); }}
                    className={`p-6 rounded-2xl border font-black uppercase tracking-widest text-xs transition-all ${
                      selectedDept === dept ? "bg-lendi-blue border-lendi-blue text-white shadow-lendi" : "bg-secondary/50 border-border hover:border-lendi-blue/30 text-muted-foreground"
                    }`}
                  >
                    {dept}
                  </button>
                ))}
              </div>
              <Button variant="ghost" onClick={() => setStep(1)} className="w-full text-[10px] uppercase font-black tracking-widest">&larr; Change Role</Button>
            </motion.div>
          )}

          {step === 3 && (
            <motion.div key="step3" initial={{ opacity: 0, x: 20 }} animate={{ opacity: 1, x: 0 }} exit={{ opacity: 0, x: -20 }} className="space-y-10">
              <div className="text-center">
                <div className="w-12 h-12 rounded-2xl bg-secondary mx-auto mb-6 flex items-center justify-center text-lendi-blue border border-border shadow-sm">
                  <Rocket size={24} />
                </div>
                <h2 className="text-3xl font-black tracking-tight-inst mb-2 uppercase">Innovation Tracks</h2>
                <p className="text-muted-foreground font-medium">Select areas that align with your technical expertise.</p>
              </div>

              <div className="flex flex-wrap justify-center gap-3">
                {INTERESTS.map((interest) => {
                  const isSelected = selectedInterests.includes(interest);
                  return (
                    <button
                      key={interest}
                      onClick={() => toggleInterest(interest)}
                      className={`px-5 py-2.5 rounded-xl border text-[11px] font-black uppercase tracking-wider transition-all ${
                        isSelected ? "bg-lendi-blue border-lendi-blue text-white shadow-sm" : "bg-secondary border-border text-muted-foreground/60 hover:border-lendi-blue/30"
                      }`}
                    >
                      {interest}
                    </button>
                  );
                })}
              </div>

              <div className="flex gap-4 pt-6">
                <Button variant="secondary" onClick={() => setStep(2)} className="flex-1 rounded-2xl h-14 font-black uppercase tracking-widest text-[10px]">Back</Button>
                <Button
                  onClick={handleComplete}
                  disabled={loading || selectedInterests.length === 0}
                  className="flex-[2] rounded-2xl h-14 font-black uppercase tracking-widest text-[10px] shadow-lendi"
                >
                  {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : "Initialize Hub Access"}
                </Button>
              </div>
            </motion.div>
          )}
        </AnimatePresence>

        <div className="mt-12 flex justify-between items-center text-[8px] font-black uppercase tracking-[0.3em] text-muted-foreground/30">
          <div className="flex gap-1.5">
            {[1, 2, 3].map((i) => (
              <div key={i} className={`h-1 rounded-full transition-all duration-500 ${step === i ? "w-10 bg-lendi-blue" : "w-2 bg-border"}`} />
            ))}
          </div>
          <span>Secure Institutional Protocol</span>
        </div>
      </motion.div>
    </div>
  );
}
