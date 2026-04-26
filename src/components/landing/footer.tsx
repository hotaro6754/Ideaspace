import { Zap, Mail, Globe, Shield } from "lucide-react"
import Link from "next/link"

const footerLinks = {
  Protocol: [
    { label: "Features", href: "#features" },
    { label: "Challenges", href: "#challenges" },
    { label: "Talent Board", href: "#talent" },
    { label: "Integrations", href: "#integrations" },
  ],
  Resources: [
    { label: "Documentation", href: "/roadmap" },
    { label: "Roadmap", href: "/roadmap" },
    { label: "Changelog", href: "/roadmap" },
    { label: "Status", href: "/roadmap" },
  ],
  Institution: [
    { label: "About LIET", href: "#" },
    { label: "Faculty Portal", href: "#" },
    { label: "Contact", href: "#" },
    { label: "Careers", href: "#" },
  ],
}

export default function Footer() {
  return (
    <footer className="relative border-t border-border/50 bg-background overflow-hidden font-inter">
      <div className="absolute inset-0 blueprint-grid opacity-20" />

      <div className="relative z-10 max-w-7xl mx-auto px-6 py-16">
        <div className="grid grid-cols-2 lg:grid-cols-5 gap-10 mb-14">
          {/* Brand */}
          <div className="col-span-2">
            <Link href="/" className="flex items-center gap-2.5 mb-4">
              <div className="w-8 h-8 rounded-lg bg-lendi flex items-center justify-center">
                <Zap size={16} className="text-white fill-white" />
              </div>
              <span className="font-bold text-lg text-foreground tracking-tight font-sans">
                Idea<span className="text-synk">Sync</span>
              </span>
            </Link>
            <p className="text-sm text-muted-foreground leading-relaxed max-w-xs text-pretty font-medium">
              The campus collaboration platform built for builders at Lendi Institute
              of Engineering & Technology.
            </p>
            <div className="flex items-center gap-3 mt-5">
              {[
                { icon: Globe, label: "Network" },
                { icon: Mail, label: "Email" },
                { icon: Shield, label: "Security" },
              ].map(({ icon: Icon, label }) => (
                <Link
                  key={label}
                  href="#"
                  aria-label={label}
                  className="w-8 h-8 rounded-lg bg-secondary/60 border border-border/50 flex items-center justify-center text-muted-foreground hover:text-foreground hover:border-lendi/40 transition-all duration-200"
                >
                  <Icon size={14} />
                </Link>
              ))}
            </div>
          </div>

          {/* Links */}
          {Object.entries(footerLinks).map(([category, links]) => (
            <div key={category}>
              <div className="text-[10px] font-black uppercase tracking-[0.4em] text-white/20 mb-6">
                {category}
              </div>
              <ul className="space-y-3">
                {links.map((link) => (
                  <li key={link.label}>
                    <Link
                      href={link.href}
                      className="text-xs font-bold text-muted-foreground hover:text-foreground transition-colors duration-200"
                    >
                      {link.label}
                    </Link>
                  </li>
                ))}
              </ul>
            </div>
          ))}
        </div>

        {/* Bottom bar */}
        <div className="flex flex-col sm:flex-row items-center justify-between gap-4 pt-8 border-t border-border/40">
          <div className="text-[10px] font-bold text-muted-foreground/40 uppercase tracking-widest">
            &copy; 2026 IdeaSync — Built for{" "}
            <span className="text-muted-foreground/60">
              Lendi Institute of Engineering & Technology
            </span>
          </div>
          <div className="flex items-center gap-8 text-[10px] font-bold text-muted-foreground/40 uppercase tracking-widest">
            <Link href="#" className="hover:text-white transition-colors">Privacy</Link>
            <Link href="#" className="hover:text-white transition-colors">Terms</Link>
            <div className="flex items-center gap-2">
               <div className="w-1 h-1 rounded-full bg-green-500 animate-pulse" />
               <span>Sector 7 Active</span>
            </div>
          </div>
        </div>
      </div>
    </footer>
  )
}
