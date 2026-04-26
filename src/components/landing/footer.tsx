"use client"

import { ArrowRight, GraduationCap, ShieldCheck, Mail, Globe, MapPin } from "lucide-react"
import Link from "next/link"

export default function Footer() {
  return (
    <footer className="bg-card border-t border-border pt-24 pb-12 px-6 overflow-hidden relative">
      <div className="absolute top-0 right-0 p-32 opacity-5 pointer-events-none">
        <GraduationCap size={400} />
      </div>

      <div className="max-w-7xl mx-auto">
        <div className="grid grid-cols-1 md:grid-cols-12 gap-12 mb-20">
          <div className="md:col-span-5 space-y-8">
            <Link href="/" className="flex items-center gap-3">
              <div className="w-12 h-12 rounded-2xl bg-lendi-blue flex items-center justify-center shadow-lendi">
                <ShieldCheck size={24} className="text-white" />
              </div>
              <div>
                <span className="font-black text-foreground text-2xl tracking-tighter uppercase">
                  Idea<span className="text-lendi-blue">Sync</span>
                </span>
                <p className="text-[10px] text-muted-foreground uppercase tracking-[0.4em] font-black mt-1">
                  Lendi Institutional Hub
                </p>
              </div>
            </Link>

            <p className="text-muted-foreground font-medium leading-relaxed max-w-sm text-balance">
              Lendi Institute of Engineering and Technology's premier collaboration platform. Bridging academic research and professional excellence.
            </p>

            <div className="space-y-4 pt-4">
              <div className="flex items-center gap-3 text-sm font-bold text-foreground">
                <MapPin size={18} className="text-lendi-blue" />
                Jonnada, Vizianagaram, AP - 535005
              </div>
              <div className="flex items-center gap-3 text-sm font-bold text-foreground">
                <Mail size={18} className="text-lendi-blue" />
                innovate@lendi.org
              </div>
              <div className="flex items-center gap-3 text-sm font-bold text-foreground">
                <Globe size={18} className="text-lendi-blue" />
                www.lendi.org
              </div>
            </div>
          </div>

          <div className="md:col-span-2 space-y-6">
            <h4 className="text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground">Ecosystem</h4>
            <ul className="space-y-4">
              {["Mission Hub", "Bounty Board", "Talent Pool", "Mentorship", "Events"].map((link) => (
                <li key={link}>
                  <Link href="#" className="text-sm font-bold text-foreground hover:text-lendi-blue transition-colors uppercase tracking-wider text-[11px]">
                    {link}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div className="md:col-span-2 space-y-6">
            <h4 className="text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground">Institutional</h4>
            <ul className="space-y-4">
              {["Faculty Desk", "Alumni Connect", "IIC Lendi", "Research Cell", "IQAC Portal"].map((link) => (
                <li key={link}>
                  <Link href="#" className="text-sm font-bold text-foreground hover:text-lendi-blue transition-colors uppercase tracking-wider text-[11px]">
                    {link}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          <div className="md:col-span-3 space-y-6">
            <h4 className="text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground">Platform</h4>
            <div className="p-6 rounded-[2rem] bg-secondary border border-border relative overflow-hidden group">
              <p className="text-xs font-bold text-foreground mb-4 relative z-10">Sign up for institutional innovation alerts.</p>
              <div className="flex gap-2 relative z-10">
                <input
                  type="email"
                  placeholder="name@lendi.org"
                  className="bg-white border border-border rounded-xl px-4 py-2 text-xs w-full focus:outline-none focus:border-lendi-blue transition-all"
                />
                <button className="bg-lendi-blue text-white p-2 rounded-xl shadow-lendi hover:scale-105 transition-transform">
                  <ArrowRight size={16} />
                </button>
              </div>
              <div className="absolute -bottom-6 -right-6 w-24 h-24 bg-lendi-blue/5 rounded-full group-hover:bg-lendi-blue/10 transition-colors" />
            </div>
          </div>
        </div>

        <div className="pt-12 border-t border-border flex flex-col md:flex-row justify-between items-center gap-6">
          <p className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground">
            © 2026 Lendi Institute of Engineering & Technology. All rights reserved.
          </p>
          <div className="flex gap-8">
            <Link href="#" className="text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:text-foreground">Privacy Protocol</Link>
            <Link href="#" className="text-[10px] font-black uppercase tracking-widest text-muted-foreground hover:text-foreground">Terms of Service</Link>
          </div>
        </div>
      </div>
    </footer>
  )
}
