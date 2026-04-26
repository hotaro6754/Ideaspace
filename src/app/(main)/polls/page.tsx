"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { motion, AnimatePresence } from "framer-motion";
import { Vote, MessageCircle, TrendingUp, Clock, Plus, Loader2, CheckCircle2 } from "lucide-react";
import { Button } from "@/components/ui/Button";

export default function PollsPage() {
  const [polls, setPolls] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Simulated polls data
    setPolls([
      {
        id: 1,
        question: "Which tech stack should the next campus-wide hackathon prioritize?",
        options: [
          { id: "a", label: "Next.js & AI APIs", votes: 142 },
          { id: "b", label: "Rust & Systems Dev", votes: 56 },
          { id: "c", label: "Solidity & Web3", votes: 89 },
        ],
        totalVotes: 287,
        expires: "2d left",
        voted: "a"
      },
      {
        id: 2,
        question: "Should we implement a mandatory peer-review system for project XP?",
        options: [
          { id: "y", label: "Yes, for transparency", votes: 210 },
          { id: "n", label: "No, too much overhead", votes: 45 },
        ],
        totalVotes: 255,
        expires: "5h left",
        voted: null
      }
    ]);
    setLoading(false);
  }, []);

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Consensus Engine" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-4xl mx-auto">
          <div className="flex justify-between items-end mb-12">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2 flex items-center gap-4">
                Campus Debates
                <div className="px-3 py-1 rounded-full bg-purple-500/10 border border-purple-500/20 text-[10px] font-black text-purple-500 uppercase tracking-widest">
                  Active Votes
                </div>
              </h1>
              <p className="text-white/40 font-medium">Cast your vote on the future of the LIET innovation network.</p>
            </div>
            <Button variant="glass" className="rounded-2xl h-14 px-8 border-white/5 hover:border-purple-500/30 flex gap-2 font-black text-[10px] uppercase tracking-widest">
              <Plus className="w-4 h-4" />
              Propose Debate
            </Button>
          </div>

          <div className="space-y-8 pb-20">
            {polls.map((poll, i) => (
              <motion.div key={poll.id} initial={{ opacity: 0, y: 20 }} animate={{ opacity: 1, y: 0 }} transition={{ delay: i * 0.1 }} className="glass rounded-[3rem] p-10 border border-white/5 relative overflow-hidden" >
                <div className="flex justify-between items-start mb-10">
                   <div className="flex items-center gap-4">
                     <div className="p-3 rounded-2xl bg-purple-500/10 text-purple-500"><Vote className="w-6 h-6" /></div>
                     <h3 className="text-2xl font-black font-plus-jakarta tracking-tight max-w-xl leading-tight">{poll.question}</h3>
                   </div>
                   <div className="text-right">
                     <p className="text-xs font-black uppercase tracking-widest text-white/20 flex items-center gap-2 justify-end"><Clock className="w-3 h-3" />{poll.expires}</p>
                   </div>
                </div>

                <div className="space-y-4 mb-10">
                  {poll.options.map((opt: any) => {
                    const percentage = Math.round((opt.votes / poll.totalVotes) * 100);
                    const isSelected = poll.voted === opt.id;
                    return (
                      <div key={opt.id} className="relative h-16 w-full rounded-2xl bg-white/5 border border-white/5 overflow-hidden group cursor-pointer" >
                        <motion.div initial={{ width: 0 }} animate={{ width: `${percentage}%` }} transition={{ duration: 1, delay: 0.5 }} className={`absolute inset-y-0 left-0 ${isSelected ? 'bg-purple-500/20' : 'bg-white/5'}`} />
                        <div className="absolute inset-0 px-6 flex items-center justify-between">
                          <div className="flex items-center gap-3">
                            {isSelected && <CheckCircle2 className="w-4 h-4 text-purple-500" />}
                            <span className={`text-sm font-bold ${isSelected ? 'text-white' : 'text-white/40 group-hover:text-white transition-colors'}`}>{opt.label}</span>
                          </div>
                          <span className="text-xs font-black font-plus-jakarta opacity-40">{percentage}%</span>
                        </div>
                      </div>
                    );
                  })}
                </div>

                <div className="flex items-center justify-between pt-8 border-t border-white/5">
                  <div className="flex items-center gap-6">
                    <p className="text-[10px] font-black uppercase tracking-widest text-white/20">{poll.totalVotes} Verified Votes</p>
                    <div className="flex -space-x-2">
                      {[1,2,3,4].map(j => <div key={j} className="w-6 h-6 rounded-full bg-white/10 border-2 border-black flex items-center justify-center text-[8px] font-black">U</div>)}
                    </div>
                  </div>
                  <Button variant="glass" className="rounded-xl h-10 px-6 flex gap-2 text-[10px] font-black uppercase tracking-widest text-white/40 hover:text-white">
                    <MessageCircle className="w-4 h-4" /> 12 Comments
                  </Button>
                </div>
              </motion.div>
            ))}
          </div>
        </div>
      </main>
    </div>
  );
}
