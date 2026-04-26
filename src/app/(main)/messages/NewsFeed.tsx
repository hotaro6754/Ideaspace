"use client";

import { useEffect, useState } from "react";
import { Globe, Clock, ArrowUpRight, Loader2, Zap } from "lucide-react";
import { motion } from "framer-motion";

export const NewsFeed = () => {
  const [news, setNews] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Simulated news fetch
    const fetchNews = async () => {
      setNews([
        { id: 1, title: "Lendi Hackathon 2026: Registration Open", source: "Campus Times", time: "2h ago", summary: "The annual innovation marathon is back with over ₹2L in total bounties. Secure your mission sector now." },
        { id: 2, title: "OpenAI Announces GPT-5 Sentinel Integration", source: "TechCrunch", time: "5h ago", summary: "New enterprise APIs allow deeper integration into collaboration platforms like IdeaSync for LIET." },
        { id: 3, title: "Web3 Adoption Soars in Higher Education", source: "CoinDesk", time: "1d ago", summary: "Colleges are leveraging blockchain for verified credentialing and skill-based reputation systems." }
      ]);
      setLoading(false);
    };
    fetchNews();
  }, []);

  return (
    <div className="space-y-6">
      {loading ? (
        <div className="h-48 flex items-center justify-center"><Loader2 className="w-6 h-6 animate-spin text-lendi-blue" /></div>
      ) : (
        news.map((item, i) => (
          <motion.div key={item.id} initial={{ opacity: 0, x: 20 }} animate={{ opacity: 1, x: 0 }} transition={{ delay: i * 0.1 }} className="p-6 rounded-3xl bg-white/5 border border-white/5 hover:border-lendi-blue/20 transition-all group cursor-pointer" >
            <div className="flex justify-between items-start mb-4">
              <div className="px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[8px] font-black text-lendi-blue uppercase tracking-widest flex items-center gap-2">
                <Globe className="w-3 h-3" />
                {item.source}
              </div>
              <span className="text-[8px] font-black uppercase text-white/20">{item.time}</span>
            </div>
            <h3 className="text-sm font-black mb-2 group-hover:text-lendi-blue transition-colors leading-snug">{item.title}</h3>
            <p className="text-xs text-white/40 leading-relaxed line-clamp-2">{item.summary}</p>
          </motion.div>
        ))
      )}
    </div>
  );
};
