"use client";

import { useState, useEffect } from "react";
import { Plus, Check, ShieldCheck, Zap } from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { Button } from "@/components/ui/Button";

interface Skill {
  name: string;
  endorsements: number;
  hasEndorsed: boolean;
}

export const SkillTagging = ({ initialSkills }: { initialSkills: string[] }) => {
  const [skills, setSkills] = useState<Skill[]>([]);
  const [newSkill, setNewSkill] = useState("");
  const [isAdding, setIsAdding] = useState(false);

  useEffect(() => {
    setSkills(initialSkills.map(s => ({
      name: s,
      endorsements: Math.floor(Math.random() * 20),
      hasEndorsed: false
    })));
  }, [initialSkills]);

  const handleEndorse = (name: string) => {
    setSkills(skills.map(s => {
      if (s.name === name) {
        return {
          ...s,
          endorsements: s.hasEndorsed ? s.endorsements - 1 : s.endorsements + 1,
          hasEndorsed: !s.hasEndorsed
        };
      }
      return s;
    }));
  };

  const addSkill = () => {
    if (newSkill && !skills.find(s => s.name === newSkill)) {
      setSkills([...skills, { name: newSkill, endorsements: 0, hasEndorsed: true }]);
      setNewSkill("");
      setIsAdding(false);
    }
  };

  return (
    <div className="space-y-6">
      <div className="flex flex-wrap gap-3">
        <AnimatePresence>
          {skills.map((skill) => (
            <motion.button
              key={skill.name}
              initial={{ opacity: 0, scale: 0.9 }}
              animate={{ opacity: 1, scale: 1 }}
              exit={{ opacity: 0, scale: 0.9 }}
              onClick={() => handleEndorse(skill.name)}
              className={`px-4 py-2 rounded-xl border flex items-center gap-3 transition-all ${
                skill.hasEndorsed
                ? "bg-lendi-blue/10 border-lendi-blue/30 text-lendi-blue shadow-[0_0_15px_rgba(0,74,153,0.2)]"
                : "bg-white/5 border-white/5 text-white/40 hover:border-white/20"
              }`}
            >
              <span className="text-xs font-bold">{skill.name}</span>
              <div className="h-3 w-px bg-current opacity-20" />
              <span className="text-[10px] font-black">{skill.endorsements}</span>
              {skill.endorsements > 10 && <ShieldCheck className="w-3 h-3" />}
            </motion.button>
          ))}
        </AnimatePresence>

        <button
          onClick={() => setIsAdding(true)}
          className="px-4 py-2 rounded-xl border border-dashed border-white/10 text-white/20 hover:text-white hover:border-white/30 transition-all text-xs font-bold"
        >
          + Add Expertise
        </button>
      </div>

      <AnimatePresence>
        {isAdding && (
          <motion.div
            initial={{ opacity: 0, y: 10 }}
            animate={{ opacity: 1, y: 0 }}
            exit={{ opacity: 0, y: 10 }}
            className="p-6 rounded-3xl bg-white/[0.02] border border-white/5 flex gap-4"
          >
            <input
              autoFocus
              type="text"
              placeholder="Enter skill name..."
              value={newSkill}
              onChange={(e) => setNewSkill(e.target.value)}
              className="flex-1 bg-white/5 border border-white/5 rounded-xl px-4 text-xs font-bold focus:outline-none focus:border-lendi-blue/50"
              onKeyDown={(e) => e.key === 'Enter' && addSkill()}
            />
            <Button onClick={addSkill} className="rounded-xl px-6 h-12">Verify</Button>
          </motion.div>
        )}
      </AnimatePresence>
    </div>
  );
};
