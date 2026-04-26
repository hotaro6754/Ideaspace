const tags = [
  "Smart Campus",
  "Artificial Intelligence",
  "Blockchain",
  "Internet of Things",
  "Machine Learning",
  "Sustainability",
  "Cybersecurity",
  "Clean Energy",
  "EdTech",
  "FinTech",
  "Computer Vision",
  "Embedded Systems",
  "Data Science",
  "Full-Stack Dev",
  "Robotics",
  "AR / VR",
]

const TagItem = ({ label }: { label: string }) => (
  <div className="flex items-center gap-3 mx-5 shrink-0">
    <div className="w-1 h-1 rounded-full bg-synk/60" />
    <span className="text-sm font-medium text-muted-foreground whitespace-nowrap hover:text-foreground transition-colors font-sans">
      {label}
    </span>
  </div>
)

export default function MarqueeStrip() {
  const doubled = [...tags, ...tags]

  return (
    <div className="relative py-5 border-y border-border/50 overflow-hidden bg-secondary/20">
      {/* Fade masks */}
      <div className="absolute left-0 top-0 bottom-0 w-24 bg-gradient-to-r from-background to-transparent z-10 pointer-events-none" />
      <div className="absolute right-0 top-0 bottom-0 w-24 bg-gradient-to-l from-background to-transparent z-10 pointer-events-none" />

      <div className="flex animate-marquee" style={{ width: "max-content" }}>
        {doubled.map((tag, i) => (
          <TagItem key={`${tag}-${i}`} label={tag} />
        ))}
      </div>
    </div>
  )
}
