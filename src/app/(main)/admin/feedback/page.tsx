"use client";

import { Header } from "@/components/layout/Header";
import { MessageSquare, Star, Clock, User, Filter, MoreHorizontal, CheckCircle2 } from "lucide-react";
import { motion } from "framer-motion";
import { Button } from "@/components/ui/Button";

export default function FeedbackTerminal() {
  const feedbacks = [
    { id: 1, user: "K. Ravi Teja", dept: "CSE", rating: 5, content: "The new Mission Layer protocol is significantly better for tracking research credits.", date: "2 hours ago" },
    { id: 2, user: "S. Priya", dept: "ECE", rating: 4, content: "Would love to see more hardware-focused bounties from the Robotics department.", date: "5 hours ago" },
    { id: 3, user: "Admin Sentinel", dept: "IQAC", rating: 5, content: "ZeroSlop validation is now fully operational for all active tracks.", date: "1 day ago" },
  ];

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Institutional Feedback" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-5xl mx-auto">
          <div className="mb-16">
            <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst mb-4">Innovation Feedback</h1>
            <p className="text-muted-foreground font-medium max-w-xl">Reviewing student and faculty input to iterate on the institutional innovación protocol.</p>
          </div>

          <div className="space-y-6 pb-24">
            {feedbacks.map((f, i) => (
              <motion.div
                key={f.id}
                initial={{ opacity: 0, x: -10 }}
                animate={{ opacity: 1, x: 0 }}
                transition={{ delay: i * 0.1 }}
                className="inst-card p-8 flex flex-col md:flex-row gap-8 hover:border-lendi-blue transition-all"
              >
                <div className="flex-1">
                  <div className="flex items-center gap-4 mb-4">
                    <div className="w-10 h-10 rounded-xl bg-secondary border border-border flex items-center justify-center font-black text-muted-foreground/30">
                      {f.user[0]}
                    </div>
                    <div>
                      <h4 className="font-bold text-foreground text-sm">{f.user}</h4>
                      <p className="text-[10px] font-black uppercase text-muted-foreground/40">{f.dept} • {f.date}</p>
                    </div>
                    <div className="ml-auto flex gap-0.5">
                      {Array.from({ length: 5 }).map((_, i) => (
                        <Star key={i} size={12} className={i < f.rating ? "text-amber-500 fill-amber-500" : "text-border"} />
                      ))}
                    </div>
                  </div>
                  <p className="text-muted-foreground text-sm font-medium leading-relaxed italic">&quot;{f.content}&quot;</p>
                </div>
                <div className="flex md:flex-col gap-2 min-w-[140px]">
                  <Button variant="outline" size="sm" className="w-full text-[10px] font-black uppercase tracking-widest gap-2">
                    <CheckCircle2 size={14} /> Resolve
                  </Button>
                  <Button variant="ghost" size="sm" className="w-full text-[10px] font-black uppercase tracking-widest text-muted-foreground">
                    Archive
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
