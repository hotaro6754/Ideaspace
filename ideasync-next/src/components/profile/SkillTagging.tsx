"use client";

import { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { Plus, X, ThumbsUp } from "lucide-react";
import { Button } from "@/components/ui/Button";
import { Input } from "@/components/ui/Input";

interface Skill {
  name: string;
  endorsements: number;
  hasEndorsed: boolean;
}

export const SkillTagging = ({ initialSkills }: { initialSkills: string[] }) => {
  const [skills, setSkills] = useState<Skill[]>(
    initialSkills.map(s => ({ name: s, endorsements: Math.floor(Math.random() * 20), hasEndorsed: false }))
  );
  const [newSkill, setNewSkill] = useState("");
  const [isAdding, setIsAdding] = useState(false);

  const addSkill = () => {
    if (newSkill && !skills.find(s => s.name.toLowerCase() === newSkill.toLowerCase())) {
      setSkills([...skills, { name: newSkill, endorsements: 0, hasEndorsed: false }]);
      setNewSkill("");
      setIsAdding(false);
    }
  };

  const endorseSkill = (name: string) => {
    setSkills(prev => prev.map(s => {
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

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center text-left">
        <h3 className="text-xs font-bold text-white/30 uppercase tracking-widest">Expertise & Endorsements</h3>
        <button
          onClick={() => setIsAdding(!isAdding)}
          className="p-1 hover:bg-white/5 rounded-full transition-colors"
        >
          <Plus className="w-4 h-4 text-lendi-blue" />
        </button>
      </div>

      <AnimatePresence>
        {isAdding && (
          <motion.div
            initial={{ height: 0, opacity: 0 }}
            animate={{ height: "auto", opacity: 1 }}
            exit={{ height: 0, opacity: 0 }}
            className="flex gap-2 overflow-hidden"
          >
            <Input
              placeholder="e.g. Next.js"
              value={newSkill}
              onChange={(e) => setNewSkill(e.target.value)}
              className="h-10 text-xs"
              onKeyDown={(e) => e.key === 'Enter' && addSkill()}
            />
            <Button onClick={addSkill} className="h-10 text-xs px-4">Add</Button>
          </motion.div>
        )}
      </AnimatePresence>

      <div className="flex flex-wrap gap-3">
        {skills.map((skill) => (
          <motion.div
            layout
            key={skill.name}
            className="group flex items-center gap-2 px-3 py-2 rounded-xl bg-white/5 border border-white/5 hover:border-lendi-blue/30 transition-all"
          >
            <span className="text-sm text-white/80 font-medium">{skill.name}</span>
            <button
              onClick={() => endorseSkill(skill.name)}
              className={`flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-[10px] font-bold transition-all ${
                skill.hasEndorsed
                ? "bg-lendi-blue/20 text-lendi-blue"
                : "bg-white/5 text-white/20 hover:text-white/40"
              }`}
            >
              <ThumbsUp className={`w-3 h-3 ${skill.hasEndorsed ? "fill-current" : ""}`} />
              {skill.endorsements}
            </button>
          </motion.div>
        ))}
      </div>
    </div>
  );
};
