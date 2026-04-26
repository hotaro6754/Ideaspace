"use client";

import { useEffect, useState } from "react";
import { Header } from "@/components/layout/Header";
import { supabase } from "@/lib/supabase";
import {
  Users,
  Search,
  ShieldAlert,
  BadgeCheck,
  Loader2,
  MoreVertical,
  Mail,
  UserCog,
  Ban,
  CheckCircle2,
  Filter
} from "lucide-react";
import { motion, AnimatePresence } from "framer-motion";
import { Button } from "@/components/ui/Button";
import { toast } from "sonner";

export default function UserManagement() {
  const [users, setUsers] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");

  const fetchUsers = async () => {
    setLoading(true);
    try {
      const { data, error } = await supabase
        .from('profiles')
        .select('*')
        .order('created_at', { ascending: false });
      if (error) throw error;
      setUsers(data || []);
    } catch (e) {
      toast.error("Failed to access personnel records");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => { fetchUsers(); }, []);

  const toggleVerification = async (userId: string, isVerified: boolean) => {
    // In a real app, this might update a 'verified' column
    toast.info("Verification protocol initiated");
  };

  const filteredUsers = users.filter(u =>
    u.full_name?.toLowerCase().includes(search.toLowerCase()) ||
    u.roll_number?.toLowerCase().includes(search.toLowerCase())
  );

  return (
    <div className="flex flex-col h-full overflow-hidden bg-background">
      <Header title="Personnel Command" />
      <main className="flex-1 overflow-y-auto p-6 md:p-12 custom-scrollbar">
        <div className="max-w-7xl mx-auto">
          <div className="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-8">
            <div className="space-y-4">
              <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-lendi-blue/10 border border-lendi-blue/20 text-[10px] font-black text-lendi-blue uppercase tracking-widest">
                <UserCog size={12} />
                Admin Terminal
              </div>
              <h1 className="text-4xl md:text-5xl font-black tracking-tight-inst">User Registry</h1>
              <p className="text-muted-foreground font-medium max-w-xl text-balance">
                Manage institutional identities, verify student credentials, and moderate the Lendi innovation community.
              </p>
            </div>

            <div className="flex items-center gap-4">
              <div className="relative group">
                <Search className="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground/40 group-focus-within:text-lendi-blue transition-colors" />
                <input
                  type="text"
                  placeholder="Search by name or roll number..."
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                  className="bg-card border border-border rounded-2xl pl-12 pr-6 h-14 w-full md:w-80 text-sm font-bold focus:outline-none focus:border-lendi-blue transition-all shadow-sm"
                />
              </div>
              <Button variant="secondary" className="h-14 w-14 p-0 rounded-2xl border border-border">
                <Filter size={18} className="text-muted-foreground" />
              </Button>
            </div>
          </div>

          {loading ? (
            <div className="h-[400px] flex flex-col items-center justify-center gap-6">
              <div className="w-12 h-12 border-4 border-lendi-blue border-t-transparent rounded-full animate-spin" />
              <p className="text-muted-foreground font-black uppercase tracking-[0.3em] text-[10px]">Retrieving Registry Data...</p>
            </div>
          ) : (
            <div className="inst-card overflow-hidden bg-card shadow-sm border-border">
              <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                  <thead>
                    <tr className="border-b border-border bg-muted/30">
                      <th className="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-muted-foreground">Personnel</th>
                      <th className="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-muted-foreground">Identity</th>
                      <th className="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-muted-foreground">Department</th>
                      <th className="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-muted-foreground">Rank</th>
                      <th className="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-muted-foreground">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    {filteredUsers.map((user, i) => (
                      <motion.tr
                        key={user.id}
                        initial={{ opacity: 0, y: 10 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ delay: i * 0.03 }}
                        className="border-b border-border hover:bg-secondary/30 transition-colors"
                      >
                        <td className="px-8 py-6">
                          <div className="flex items-center gap-4">
                            <div className="w-10 h-10 rounded-xl bg-secondary border border-border flex items-center justify-center text-sm font-black text-muted-foreground/30">
                              {user.full_name?.[0]}
                            </div>
                            <div>
                              <p className="font-bold text-foreground text-sm">{user.full_name}</p>
                              <p className="text-[10px] font-medium text-muted-foreground">{user.role}</p>
                            </div>
                          </div>
                        </td>
                        <td className="px-8 py-6">
                          <p className="text-xs font-black text-foreground uppercase tracking-wider">{user.roll_number || 'N/A'}</p>
                        </td>
                        <td className="px-8 py-6">
                          <p className="text-xs font-bold text-muted-foreground uppercase">{user.department}</p>
                        </td>
                        <td className="px-8 py-6">
                          <span className="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-lendi-blue/5 text-lendi-blue border border-lendi-blue/10 text-[10px] font-black uppercase tracking-widest">
                            <BadgeCheck size={12} />
                            {user.rank}
                          </span>
                        </td>
                        <td className="px-8 py-6">
                          <div className="flex items-center gap-2">
                            <Button variant="outline" size="sm" className="h-9 w-9 p-0 rounded-lg">
                              <Mail size={14} className="text-muted-foreground" />
                            </Button>
                            <Button variant="outline" size="sm" className="h-9 w-9 p-0 rounded-lg hover:border-lendi-red hover:bg-lendi-red/5">
                              <Ban size={14} className="text-lendi-red" />
                            </Button>
                          </div>
                        </td>
                      </motion.tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}
        </div>
      </main>
    </div>
  );
}
