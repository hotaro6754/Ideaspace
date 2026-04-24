"use client";

import { motion } from "framer-motion";
import { Header } from "@/components/layout/Header";
import { Button } from "@/components/ui/Button";
import { BackgroundGradient } from "@/components/ui/BackgroundGradient";
import { SkillTagging } from "@/components/profile/SkillTagging";
import { Globe, Zap, Rocket } from "lucide-react";
import { useParams } from "next/navigation";

export default function ProfilePage() {
  return (
    <div className="relative min-h-screen text-white font-inter bg-black overflow-x-hidden">
      <Header title="Personnel Dossier" />
      <main className="p-6 md:p-12 max-w-7xl mx-auto relative z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12">
          <div className="lg:col-span-4 space-y-8">
            <div className="glass rounded-[3rem] p-10 border border-white/10 relative overflow-hidden group">
              <div className="absolute top-0 right-0 w-32 h-32 bg-lendi-blue/10 blur-[80px] rounded-full group-hover:bg-lendi-blue/20 transition-all duration-700" />
              <div className="relative z-10">
                <div className="w-32 h-32 rounded-[2.5rem] bg-gradient-to-br from-lendi-blue to-purple-600 p-1 mb-8 shadow-2xl shadow-lendi-blue/20 mx-auto">
                  <div className="w-full h-full rounded-[2.2rem] bg-black flex items-center justify-center text-4xl font-black">JD</div>
                </div>
                <div className="text-center">
                  <h1 className="text-3xl font-black font-plus-jakarta tracking-tight mb-2">John Doe</h1>
                  <div className="flex items-center justify-center gap-2 mb-6">
                    <div className="px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">Innovator</div>
                    <span className="text-[10px] font-bold text-white/30 uppercase tracking-widest">CSE • 3rd Year</span>
                  </div>
                  <div className="grid grid-cols-3 gap-4 py-6 border-y border-white/5 mb-8">
                    <div><p className="text-xl font-black font-plus-jakarta">1.2k</p><p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter">Points</p></div>
                    <div><p className="text-xl font-black font-plus-jakarta">12</p><p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter">Missions</p></div>
                    <div><p className="text-xl font-black font-plus-jakarta">84</p><p className="text-[8px] font-bold text-white/20 uppercase tracking-tighter">Rep</p></div>
                  </div>
                  <div className="flex gap-3">
                    <Button className="flex-1 rounded-2xl h-12 font-black shadow-xl shadow-lendi-blue/20">Follow</Button>
                    <Button variant="glass" className="h-12 w-12 rounded-2xl p-0"><Globe className="w-4 h-4 mx-auto" /></Button>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="lg:col-span-8 space-y-12">
            <section>
              <h3 className="text-xs font-black uppercase tracking-[0.3em] text-white/20 mb-8 ml-1">Identity & Expertise</h3>
              <SkillTagging initialSkills={["React", "PostgreSQL", "Machine Learning", "System Design"]} />
            </section>
            <section>
              <h3 className="text-xs font-black uppercase tracking-[0.3em] text-white/20 mb-8 ml-1">Active Missions</h3>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {[
                  { name: "Lendi Mesh", role: "Lead", tech: "Rust" },
                  { name: "EcoTrack AI", role: "Contributor", tech: "Python" }
                ].map((p) => (
                  <div key={p.name} className="p-6 rounded-3xl bg-white/5 border border-white/5 hover:border-lendi-blue/30 transition-all group cursor-pointer" >
                    <div className="flex justify-between items-start mb-4">
                      <div className="p-2 rounded-xl bg-white/5 text-white/40 group-hover:text-lendi-blue group-hover:bg-lendi-blue/10 transition-all"><Rocket className="w-5 h-5" /></div>
                      <span className="text-[10px] font-black uppercase tracking-widest text-white/20">{p.role}</span>
                    </div>
                    <h4 className="text-lg font-black mb-1">{p.name}</h4>
                    <p className="text-[10px] font-bold text-lendi-blue uppercase tracking-widest">{p.tech}</p>
                  </div>
                ))}
              </div>
            </section>
          </div>
        </div>
      </main>
    </div>
  );
}
