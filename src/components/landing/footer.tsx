import { Mail, Globe, ShieldCheck } from "lucide-react"
import Link from "next/link"

const footerLinks = {
  Platform: [
    { label: "Research Tracks", href: "#features" },
    { label: "Challenge Board", href: "#challenges" },
    { label: "Talent Directory", href: "#talent" },
    { label: "Industry Partners", href: "#integrations" },
  ],
  Governance: [
    { label: "Ethics Policy", href: "/roadmap" },
    { label: "Innovation Roadmap", href: "/roadmap" },
    { label: "Institutional Rights", href: "/roadmap" },
    { label: "Security Status", href: "/roadmap" },
  ],
  Institution: [
    { label: "About LIET", href: "https://lendi.org" },
    { label: "Faculty Portal", href: "#faculty" },
    { label: "Alumni Relations", href: "#alumni" },
    { label: "Career Services", href: "#" },
  ],
}

export default function Footer() {
  return (
    <footer className="relative border-t border-border bg-card overflow-hidden py-16">
      <div className="absolute inset-0 soft-grid opacity-10 pointer-events-none" />

      <div className="relative z-10 max-w-7xl mx-auto px-6">
        <div className="grid grid-cols-2 lg:grid-cols-5 gap-12 mb-16">
          {/* Brand */}
          <div className="col-span-2">
            <Link href="/" className="flex items-center gap-3 mb-6 group">
              <div className="w-10 h-10 rounded-xl bg-lendi flex items-center justify-center shadow-lg shadow-lendi/10 group-hover:scale-105 transition-transform">
                <ShieldCheck size={20} className="text-white" />
              </div>
              <span className="font-black text-xl text-foreground tracking-tight">
                Idea<span className="text-lendi-light">Sync</span>
              </span>
            </Link>
            <p className="text-sm text-muted-foreground font-medium leading-relaxed max-w-xs">
              The institutional ecosystem for collaborative innovation and
              academic excellence at Lendi Institute of Engineering & Technology.
            </p>
            <div className="flex items-center gap-3 mt-8">
              {[
                { icon: Globe, label: "Website" },
                { icon: Mail, label: "Support" },
              ].map(({ icon: Icon, label }) => (
                <Link
                  key={label}
                  href="#"
                  className="w-10 h-10 rounded-xl bg-secondary border border-border flex items-center justify-center text-muted-foreground hover:text-lendi hover:border-lendi transition-all"
                >
                  <Icon size={18} />
                </Link>
              ))}
            </div>
          </div>

          {/* Links */}
          {Object.entries(footerLinks).map(([category, links]) => (
            <div key={category}>
              <div className="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground mb-8">
                {category}
              </div>
              <ul className="space-y-4">
                {links.map((link) => (
                  <li key={link.label}>
                    <Link
                      href={link.href}
                      className="text-xs font-bold text-muted-foreground hover:text-foreground transition-colors"
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
        <div className="flex flex-col sm:flex-row items-center justify-between gap-6 pt-10 border-t border-border">
          <div className="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">
            &copy; {new Date().getFullYear()} IdeaSync &bull; Lendi Institute of Engineering & Technology
          </div>
          <div className="flex items-center gap-8 text-[10px] font-black text-muted-foreground uppercase tracking-[0.2em]">
            <Link href="#" className="hover:text-lendi transition-colors">Privacy</Link>
            <Link href="#" className="hover:text-lendi transition-colors">Terms</Link>
            <div className="flex items-center gap-2">
               <div className="w-1.5 h-1.5 rounded-full bg-green-500" />
               <span className="text-foreground">Network Active</span>
            </div>
          </div>
        </div>
      </div>
    </footer>
  )
}
