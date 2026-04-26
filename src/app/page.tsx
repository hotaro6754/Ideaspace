import Navbar from "@/components/landing/navbar"
import Hero from "@/components/landing/hero"
import MarqueeStrip from "@/components/landing/marquee-strip"
import FeaturesBento from "@/components/landing/features-bento"
import ProblemStatements from "@/components/landing/problem-statements"
import TalentBoard from "@/components/landing/talent-board"
import IntegrationsSection from "@/components/landing/integrations-section"
import CtaSection from "@/components/landing/cta-section"
import Footer from "@/components/landing/footer"

export default function Home() {
  return (
    <main className="min-h-screen bg-background">
      <Navbar />
      <Hero />
      <MarqueeStrip />
      <FeaturesBento />
      <ProblemStatements />
      <TalentBoard />
      <IntegrationsSection />
      <CtaSection />
      <Footer />
    </main>
  )
}
