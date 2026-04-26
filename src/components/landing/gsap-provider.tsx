"use client"

import { useEffect } from "react"
import gsap from "gsap"
import { ScrollTrigger } from "gsap/ScrollTrigger"

export default function GSAPProvider({ children }: { children: React.ReactNode }) {
  useEffect(() => {
    gsap.registerPlugin(ScrollTrigger)

    // Global section entry logic
    const sections = document.querySelectorAll("section")
    sections.forEach((section) => {
      gsap.fromTo(
        section,
        { opacity: 0, y: 100 },
        {
          opacity: 1,
          y: 0,
          duration: 1.5,
          ease: "expo.out",
          scrollTrigger: {
            trigger: section,
            start: "top 90%",
            end: "top 20%",
            toggleActions: "play none none reverse",
          },
        }
      )
    })

    // Parallax logic for floating elements
    const floaters = document.querySelectorAll(".hero-float")
    floaters.forEach((floater, i) => {
      gsap.to(floater, {
        y: i % 2 === 0 ? -40 : 40,
        ease: "none",
        scrollTrigger: {
          trigger: "body",
          start: "top top",
          end: "bottom bottom",
          scrub: 1,
        },
      })
    })

    return () => {
      ScrollTrigger.getAll().forEach((t) => t.kill())
    }
  }, [])

  return <>{children}</>
}
