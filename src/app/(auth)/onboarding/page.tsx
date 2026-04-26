"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Button } from "@/components/ui/Button";
import { BackgroundGradient } from "@/components/ui/BackgroundGradient";
import { User, Rocket, GraduationCap, ShieldCheck, Check } from "lucide-react";
import { logger } from "@/lib/logger";

const ROLES = [
  { id: "student", name: "Student", icon: User, desc: "Browse ideas and join teams" },
  { id: "senior", name: "Senior", icon: Rocket, desc: "Post projects and recruit juniors" },
  { id: "alumni", name: "Alumni", icon: GraduationCap, desc: "Hire talent and mentor students" },
  { id: "faculty", name: "Faculty", icon: ShieldCheck, desc: "Post bounties and judge solutions" },
];

const INTERESTS = [
  "Artificial Intelligence", "Web Development", "Mobile Apps", "Cybersecurity",
  "Internet of Things", "Blockchain", "Cloud Computing", "UI/UX Design",
  "Game Development", "Data Science", "Embedded Systems", "Robotics"
];

export default function OnboardingPage() {
  const [step, setStep] = useState(1);
  const [selectedRole, setSelectedRole] = useState<string | null>(null);
  const [selectedInterests, setSelectedInterests] = useState<string[]>([]);

  const nextStep = () => setStep((s) => s + 1);
  const prevStep = () => setStep((s) => s - 1);

  const handleRoleSelect = (roleId: string) => {
    setSelectedRole(roleId);
    logger.info("Onboarding", "Role selected", { roleId });
    nextStep();
  };

  const toggleInterest = (interest: string) => {
    setSelectedInterests((prev) =>
      prev.includes(interest)
        ? prev.filter((i) => i !== interest)
        : [...prev, interest]
    );
  };

  const handleComplete = () => {
    logger.info("Onboarding", "Onboarding completed", {
      role: selectedRole,
      interests: selectedInterests
    });
    window.location.href = "/dashboard";
  };

  return (
    <div className="relative min-h-screen flex items-center justify-center overflow-hidden text-white font-inter p-6">
      <BackgroundGradient />

      <motion.div
        layout
        className="w-full max-w-2xl glass p-10 rounded-[2rem] relative z-10 shadow-2xl border border-white/10"
      >
        <AnimatePresence mode="wait">
          {step === 1 && (
            <motion.div
              key="step1"
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: -20 }}
              className="space-y-8"
            >
              <div className="text-center">
                <h2 className="text-3xl font-bold font-plus-jakarta mb-2 tracking-tight">Identify Your Mission</h2>
                <p className="text-white/50">Choose your role in the Lendi innovation ecosystem</p>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {ROLES.map((role) => (
                  <button
                    key={role.id}
                    onClick={() => handleRoleSelect(role.id)}
                    className="group relative flex items-start gap-4 p-5 rounded-2xl bg-white/5 border border-white/5 hover:border-lendi-blue/50 hover:bg-white/10 transition-all text-left"
                  >
                    <div className="p-3 rounded-xl bg-white/5 group-hover:bg-lendi-blue/20 group-hover:text-lendi-blue transition-colors">
                      <role.icon className="w-6 h-6" />
                    </div>
                    <div>
                      <h3 className="font-semibold text-lg">{role.name}</h3>
                      <p className="text-white/40 text-sm">{role.desc}</p>
                    </div>
                  </button>
                ))}
              </div>
            </motion.div>
          )}

          {step === 2 && (
            <motion.div
              key="step2"
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: -20 }}
              className="space-y-8"
            >
              <div className="text-center">
                <h2 className="text-3xl font-bold font-plus-jakarta mb-2 tracking-tight">Personalize Your Feed</h2>
                <p className="text-white/50">Select tracks that align with your technical interests</p>
              </div>

              <div className="flex flex-wrap justify-center gap-3 py-4">
                {INTERESTS.map((interest) => {
                  const isSelected = selectedInterests.includes(interest);
                  return (
                    <button
                      key={interest}
                      onClick={() => toggleInterest(interest)}
                      className={`px-4 py-2 rounded-full border text-sm transition-all duration-300 ${
                        isSelected
                        ? "bg-lendi-blue border-lendi-blue text-white shadow-[0_0_20px_rgba(0,74,153,0.3)]"
                        : "bg-white/5 border-white/10 text-white/60 hover:border-white/30 hover:bg-white/10"
                      }`}
                    >
                      <span className="flex items-center gap-2">
                        {interest}
                        {isSelected && <Check className="w-3 h-3" />}
                      </span>
                    </button>
                  );
                })}
              </div>

              <div className="flex gap-4 pt-4">
                <Button variant="glass" onClick={prevStep} className="flex-1">
                  Back
                </Button>
                <Button onClick={handleComplete} className="flex-[2] h-12 font-bold shadow-lg shadow-lendi-blue/20">
                  Initialize Dashboard
                </Button>
              </div>
            </motion.div>
          )}
        </AnimatePresence>

        <div className="mt-10 flex justify-between items-center text-xs text-white/20">
          <div className="flex gap-1">
            {[1, 2].map((i) => (
              <div
                key={i}
                className={`h-1 rounded-full transition-all duration-500 ${
                  step === i ? "w-8 bg-lendi-blue" : "w-2 bg-white/10"
                }`}
              />
            ))}
          </div>
          <span className="uppercase tracking-[0.2em] font-medium">Secure Protocol Active</span>
        </div>
      </motion.div>
    </div>
  );
}
