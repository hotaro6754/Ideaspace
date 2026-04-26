"use client"

import { useEffect } from "react"
import gsap from "gsap"
import { ScrollTrigger } from "gsap/ScrollTrigger"

export default function GSAPProvider({ children }: { children: React.ReactNode }) {
  useEffect(() => {
    gsap.registerPlugin(ScrollTrigger)

    // Cinematic section reveals inspired by GSAP.com
    const sections = document.querySelectorAll("section")
    sections.forEach((section) => {
      // Skip hero for manual animation
      if (section.querySelector(".hero-title-line")) return;

      gsap.from(section, {
        opacity: 0,
        y: 40,
        duration: 1.2,
        ease: "power4.out",
        scrollTrigger: {
          trigger: section,
          start: "top 80%",
          toggleActions: "play none none none",
        },
      })
    })

    // Specialized staggered reveal for institutional cards
    const cardGrids = document.querySelectorAll(".grid")
    cardGrids.forEach((grid) => {
      const cards = grid.querySelectorAll(".inst-card")
      if (cards.length > 0) {
        gsap.from(cards, {
          opacity: 0,
          y: 30,
          scale: 0.98,
          duration: 0.8,
          stagger: 0.1,
          ease: "power2.out",
          scrollTrigger: {
            trigger: grid,
            start: "top 85%",
          },
        })
      }
    })

    return () => {
      ScrollTrigger.getAll().forEach((t) => t.kill())
    }
  }, [])

  return <>{children}</>
}
