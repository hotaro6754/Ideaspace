"use client";

import { motion } from "framer-motion";
import { Users, Star, ArrowUpRight, GraduationCap } from "lucide-react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";

interface ProjectCardProps {
  id: string;
  title: string;
  description: string;
  tags: string[];
  stars: number;
  members: number;
  progress: number;
}

export const ProjectCard = ({ id, title, description, tags, stars, members, progress }: ProjectCardProps) => {
  return (
    <motion.div
      initial={{ opacity: 0, y: 20 }}
      whileInView={{ opacity: 1, y: 0 }}
      viewport={{ once: true }}
      className="inst-card p-8 flex flex-col h-[440px] bg-card shadow-sm group border-border hover:border-lendi-blue transition-all duration-300"
    >
      <div className="flex justify-between items-start mb-8">
        <div className="w-12 h-12 rounded-2xl bg-secondary flex items-center justify-center text-lendi-blue shadow-sm transition-transform group-hover:scale-110 duration-500">
          <GraduationCap size={24} />
        </div>
        <div className="flex gap-2">
          <div className="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-secondary border border-border text-[10px] font-black uppercase tracking-widest text-muted-foreground">
            <Star size={12} className="text-amber-500 fill-amber-500" />
            {stars}
          </div>
          <div className="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-secondary border border-border text-[10px] font-black uppercase tracking-widest text-muted-foreground">
            <Users size={12} className="text-lendi-blue" />
            {members}
          </div>
        </div>
      </div>

      <div className="flex-1">
        <h3 className="text-2xl font-black tracking-tight mb-3 text-foreground group-hover:text-lendi-blue transition-colors">
          {title}
        </h3>
        <p className="text-muted-foreground text-sm leading-relaxed mb-6 line-clamp-3 font-medium text-balance">
          {description}
        </p>
        <div className="flex flex-wrap gap-2 mb-6">
          {tags.slice(0, 3).map((tag) => (
            <span key={tag} className="px-3 py-1 rounded-lg bg-secondary border border-border text-[10px] font-bold text-muted-foreground uppercase tracking-wider">
              {tag}
            </span>
          ))}
          {tags.length > 3 && (
            <span className="px-3 py-1 text-[10px] font-bold text-muted-foreground/50 uppercase tracking-wider">
              +{tags.length - 3} More
            </span>
          )}
        </div>
      </div>

      <div className="mt-auto space-y-6">
        <div>
          <div className="flex justify-between items-center mb-2.5">
            <span className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground/40">Mission Progress</span>
            <span className="text-[10px] font-black text-lendi-blue">{progress}%</span>
          </div>
          <div className="h-1.5 w-full bg-secondary rounded-full overflow-hidden">
            <motion.div
              initial={{ width: 0 }}
              whileInView={{ width: `${progress}%` }}
              transition={{ duration: 1.5, ease: "circOut" }}
              className="h-full bg-lendi-blue shadow-lendi"
            />
          </div>
        </div>

        <div className="flex items-center justify-end border-t border-border pt-6">
          <Link href={`/projects/${id}`} className="w-full">
            <Button variant="outline" className="w-full h-11 rounded-xl text-xs font-black uppercase tracking-widest flex gap-2 group/btn">
              Access Dossier
              <ArrowUpRight className="w-4 h-4 group-hover/btn:translate-x-1 group-hover/btn:-translate-y-1 transition-transform" />
            </Button>
          </Link>
        </div>
      </div>
    </motion.div>
  );
};
