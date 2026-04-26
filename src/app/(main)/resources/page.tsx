"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import { motion, AnimatePresence } from "framer-motion";
import {
  FileText,
  Search,
  Filter,
  Download,
  Upload,
  Loader2,
  BookOpen,
  Globe,
  ShieldCheck,
  Plus
} from "lucide-react";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

export default function ResourceLibrary() {
  const [resources, setResources] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [searchQuery, setSearchQuery] = useState("");
  const [activeTrack, setActiveTrack] = useState("All");

  const tracks = ["All", "AI & ML", "IoT", "Web3", "Cybersecurity", "Cloud Architecture"];

  useEffect(() => {
    const fetchResources = async () => {
      setLoading(true);
      const { data } = await supabase
        .from('resources')
        .select(`*, profiles:uploader_id (full_name)`)
        .order('created_at', { ascending: false });
      if (data) setResources(data);
      setLoading(false);
    };
    fetchResources();
  }, []);

  const filteredResources = resources.filter(r => {
    const matchesSearch = r.title.toLowerCase().includes(searchQuery.toLowerCase()) ||
                         r.description?.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesTrack = activeTrack === "All" || r.innovation_track === activeTrack;
    return matchesSearch && matchesTrack;
  });

  return (
    <div className="flex flex-col h-full overflow-hidden bg-black text-white">
      <Header title="Knowledge Base" />
      <main className="flex-1 overflow-y-auto p-10 custom-scrollbar">
        <div className="max-w-[1400px] mx-auto">
          <div className="flex flex-col lg:flex-row lg:items-center justify-between mb-12 gap-8">
            <div>
              <h1 className="text-4xl font-black font-plus-jakarta tracking-tight mb-2 flex items-center gap-4">
                Research Archives
                <div className="px-3 py-1 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-[10px] font-black text-cyan-500 uppercase tracking-widest">
                  v1.0 Assets
                </div>
              </h1>
              <p className="text-white/40 font-medium tracking-tight">Access whitepapers, project blueprints, and academic research from LIET innovation sectors.</p>
            </div>
            <div className="flex flex-col sm:flex-row gap-4">
              <div className="relative group">
                <Search className="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20 group-focus-within:text-lendi-blue transition-colors" />
                <input
                  type="text"
                  placeholder="Search archives..."
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  className="bg-white/5 border border-white/5 rounded-2xl pl-14 pr-6 h-14 w-full sm:w-64 text-sm focus:outline-none focus:border-lendi-blue/50 transition-all"
                />
              </div>
              <Button className="h-14 rounded-2xl px-8 bg-lendi-blue hover:bg-lendi-blue/80 flex gap-2 font-black text-[10px] uppercase tracking-widest">
                <Upload className="w-4 h-4" />
                Publish Resource
              </Button>
            </div>
          </div>

          <div className="flex gap-2 overflow-x-auto pb-8 no-scrollbar">
            {tracks.map(track => (
              <button
                key={track}
                onClick={() => setActiveTrack(track)}
                className={`px-6 py-2.5 rounded-full text-[10px] font-black uppercase tracking-widest border transition-all whitespace-nowrap ${
                  activeTrack === track
                  ? "bg-white text-black border-white"
                  : "bg-white/5 border-white/5 text-white/40 hover:text-white"
                }`}
              >
                {track}
              </button>
            ))}
          </div>

          {loading ? (
            <div className="flex justify-center py-20"><Loader2 className="w-10 h-10 animate-spin text-lendi-blue" /></div>
          ) : (
            <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
              {filteredResources.length === 0 ? (
                <div className="col-span-full py-20 text-center glass rounded-[3rem] border border-white/5">
                  <BookOpen className="w-12 h-12 text-white/10 mx-auto mb-4" />
                  <p className="text-white/20 font-bold uppercase tracking-widest text-xs">No research assets found in this sector</p>
                </div>
              ) : (
                filteredResources.map((res, i) => (
                  <motion.div
                    key={res.id}
                    initial={{ opacity: 0, scale: 0.95 }}
                    animate={{ opacity: 1, scale: 1 }}
                    transition={{ delay: i * 0.05 }}
                    className="glass rounded-[2.5rem] p-8 border border-white/5 hover:border-lendi-blue/30 transition-all group flex flex-col"
                  >
                    <div className="flex items-start justify-between mb-6">
                      <div className="w-12 h-12 rounded-xl bg-white/5 flex items-center justify-center border border-white/10 group-hover:border-lendi-blue/30 transition-all">
                        <FileText className="w-6 h-6 text-white/40 group-hover:text-lendi-blue" />
                      </div>
                      <div className="px-2 py-1 rounded-md bg-white/5 text-[8px] font-black uppercase tracking-widest text-white/20">
                        {res.file_type}
                      </div>
                    </div>

                    <div className="flex-1 mb-8">
                      <h3 className="text-lg font-black font-plus-jakarta mb-2 group-hover:text-lendi-blue transition-colors">{res.title}</h3>
                      <p className="text-xs text-white/40 line-clamp-2 mb-4 leading-relaxed font-medium">{res.description}</p>

                      <div className="flex items-center gap-2 text-[8px] font-black uppercase tracking-widest text-white/20">
                        <Globe className="w-3 h-3" />
                        {res.innovation_track} • Published by {res.profiles?.full_name}
                      </div>
                    </div>

                    <Button variant="glass" className="w-full rounded-xl h-12 text-[10px] font-black uppercase tracking-widest flex gap-2 border-white/5 hover:border-lendi-blue/30">
                      <Download className="w-4 h-4" />
                      Download Asset
                    </Button>
                  </motion.div>
                ))
              )}
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
