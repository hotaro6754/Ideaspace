"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import { Briefcase, Search, Filter, ArrowUpRight, Loader2, Sparkles, MapPin, DollarSign } from "lucide-react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";

export default function RecruitmentPortal() {
  const [jobs, setJobs] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const fetchJobs = async () => {
      // In a real app, we'd have a 'jobs' table.
      // For now, let's simulate with some high-quality mocks.
      setJobs([
        { id: 1, title: "Full Stack Engineer (Intern)", company: "Google Cloud", location: "Hyderabad", type: "Remote", salary: "₹50k/mo", track: "Web Development" },
        { id: 2, title: "AI Researcher", company: "Microsoft Research", location: "Bangalore", type: "On-site", salary: "₹80k/mo", track: "AI & ML" },
        { id: 3, title: "Smart Contract Developer", company: "Polygon Labs", location: "Dubai", type: "Remote", salary: "$3k/mo", track: "Web3" }
      ]);
      setLoading(false);
    };
    fetchJobs();
  }, []);

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Recruitment Portal" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">
          <div className="flex flex-col md:flex-row md:items-center justify-between mb-12 gap-8">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2 flex items-center gap-4">
                Alumni Network Jobs
                <div className="px-3 py-1 rounded-full bg-orange-500/10 border border-orange-500/20 text-[10px] font-black text-orange-500 uppercase tracking-widest">
                  Verified Leads
                </div>
              </h1>
              <p className="text-white/40 font-medium tracking-tight">Direct recruitment opportunities from LIET alumni at top-tier global companies.</p>
            </div>
            <div className="flex gap-4">
              <div className="relative group">
                <Search className="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20 group-focus-within:text-lendi-blue transition-colors" />
                <input type="text" placeholder="Search opportunities..." className="bg-white/5 border border-white/5 rounded-2xl pl-14 pr-6 h-14 w-80 text-sm focus:outline-none focus:border-lendi-blue/50 transition-all" />
              </div>
            </div>
          </div>

          {loading ? (
            <div className="flex justify-center py-20"><Loader2 className="w-10 h-10 animate-spin text-orange-500" /></div>
          ) : (
            <div className="grid grid-cols-1 gap-6 pb-20">
              {jobs.map((job, i) => (
                <motion.div key={job.id} initial={{ opacity: 0, x: -20 }} animate={{ opacity: 1, x: 0 }} transition={{ delay: i * 0.1 }} className="glass rounded-[2.5rem] p-10 border border-white/5 hover:border-orange-500/30 transition-all group flex flex-col md:flex-row md:items-center justify-between gap-8" >
                  <div className="flex gap-8 items-start">
                    <div className="w-20 h-20 rounded-3xl bg-white/5 border border-white/10 flex items-center justify-center relative overflow-hidden group-hover:border-orange-500/30 transition-all">
                      <div className="absolute inset-0 bg-gradient-to-br from-orange-500/10 to-transparent" />
                      <Briefcase className="w-8 h-8 text-white/20 group-hover:text-orange-500 transition-colors relative z-10" />
                    </div>
                    <div>
                      <h3 className="text-2xl font-black font-plus-jakarta mb-2 group-hover:text-orange-500 transition-colors">{job.title}</h3>
                      <div className="flex flex-wrap items-center gap-6">
                        <p className="text-xs font-bold text-white/80">{job.company}</p>
                        <div className="h-4 w-px bg-white/10" />
                        <p className="text-[10px] font-black uppercase tracking-widest text-white/40 flex items-center gap-2"><MapPin className="w-3.5 h-3.5" />{job.location} • {job.type}</p>
                        <div className="h-4 w-px bg-white/10" />
                        <p className="text-[10px] font-black uppercase tracking-widest text-orange-500 flex items-center gap-2"><DollarSign className="w-3.5 h-3.5" />{job.salary}</p>
                      </div>
                      <div className="mt-6 flex flex-wrap gap-2">
                        <span className="px-3 py-1 rounded-lg bg-orange-500/5 border border-orange-500/10 text-[9px] font-black text-orange-500 uppercase tracking-widest">{job.track}</span>
                      </div>
                    </div>
                  </div>
                  <div className="flex gap-4 min-w-[200px]">
                    <Button className="flex-1 rounded-xl h-14 font-black bg-orange-500 hover:bg-orange-600 text-black text-[10px] uppercase tracking-widest">Apply Now</Button>
                    <Button variant="glass" className="w-14 h-14 rounded-xl p-0 border-white/5 hover:border-orange-500/30 transition-all"><ArrowUpRight className="w-5 h-5" /></Button>
                  </div>
                </motion.div>
              ))}
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
