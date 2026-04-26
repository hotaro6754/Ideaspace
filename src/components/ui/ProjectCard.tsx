"use client";

import { motion, useMotionValue, useSpring, useTransform } from "framer-motion";
import { Users, Star, ArrowUpRight, Terminal } from "lucide-react";
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
  const x = useMotionValue(0);
  const y = useMotionValue(0);
  const mouseXSpring = useSpring(x);
  const mouseYSpring = useSpring(y);
  const rotateX = useTransform(mouseYSpring, [-0.5, 0.5], ["10deg", "-10deg"]);
  const rotateY = useTransform(mouseXSpring, [-0.5, 0.5], ["-10deg", "10deg"]);
  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    const rect = e.currentTarget.getBoundingClientRect();
    x.set((e.clientX - rect.left) / rect.width - 0.5);
    y.set((e.clientY - rect.top) / rect.height - 0.5);
  };
  const handleMouseLeave = () => { x.set(0); y.set(0); };

  return (
    <motion.div style={{ rotateX, rotateY, transformStyle: "preserve-3d" }} onMouseMove={handleMouseMove} onMouseLeave={handleMouseLeave} className="relative group h-[420px] w-full" >
      <div style={{ transform: "translateZ(50px)" }} className="absolute inset-0 rounded-[2.5rem] glass border border-white/5 bg-gradient-to-br from-white/[0.02] to-transparent p-8 flex flex-col shadow-2xl transition-all duration-500 group-hover:border-lendi-blue/30 group-hover:bg-white/[0.05]" >
        <div className="flex justify-between items-start mb-6">
          <div className="p-3 rounded-2xl bg-white/5 border border-white/5 group-hover:scale-110 transition-transform duration-500"><Terminal className="w-6 h-6 text-lendi-blue" /></div>
          <div className="flex gap-2">
            <div className="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/5 border border-white/5 text-[10px] font-black uppercase tracking-widest text-white/40"><Star className="w-3 h-3 text-yellow-500 fill-yellow-500" />{stars}</div>
            <div className="flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white/5 border border-white/5 text-[10px] font-black uppercase tracking-widest text-white/40"><Users className="w-3 h-3 text-lendi-blue" />{members}</div>
          </div>
        </div>
        <div className="flex-1">
          <h3 className="text-2xl font-black font-plus-jakarta mb-3 tracking-tight group-hover:translate-x-1 transition-transform duration-500">{title}</h3>
          <p className="text-white/40 text-sm leading-relaxed mb-6 line-clamp-3 font-medium">{description}</p>
          <div className="flex flex-wrap gap-2 mb-6">
            {tags.map((tag) => (<span key={tag} className="px-3 py-1 rounded-lg bg-white/5 border border-white/5 text-[10px] font-bold text-white/60 hover:border-lendi-blue/50 hover:text-white transition-colors cursor-default" >{tag}</span>))}
          </div>
        </div>
        <div className="mb-8">
          <div className="flex justify-between items-center mb-2">
            <span className="text-[10px] font-black uppercase tracking-widest opacity-20">Progress</span>
            <span className="text-[10px] font-black text-lendi-blue">{progress}%</span>
          </div>
          <div className="h-1 w-full bg-white/5 rounded-full overflow-hidden">
            <motion.div initial={{ width: 0 }} whileInView={{ width: `${progress}%` }} transition={{ duration: 1.5, ease: "circOut" }} className="h-full bg-lendi-blue shadow-[0_0_15px_rgba(0,74,153,0.6)]" />
          </div>
        </div>
        <div className="flex items-center justify-end">
          <Link href={`/projects/${id}`}><Button variant="glass" className="rounded-2xl px-6 h-10 text-xs font-black uppercase tracking-widest flex gap-2 group/btn">View<ArrowUpRight className="w-4 h-4 group-hover/btn:translate-x-1 group-hover/btn:-translate-y-1 transition-transform" /></Button></Link>
        </div>
      </div>
    </motion.div>
  );
};
