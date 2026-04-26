# Project Patterns & Guidelines

## Authentication
- Allowed Domains: `lendi.org`, `liethub.org`, `lendi.edu.in`.
- Always use `validateLendiEmail` from `src/lib/auth.ts` for domain verification.
- OAuth (Google/GitHub) must be followed by a domain check in the callback route.

## UI/UX Design System
- Aesthetic: Professional, high-contrast, institutional SaaS.
- Colors: Lendi Blue (#004a99), Synk Cyan (#06b6d4).
- Animations: Use GSAP for scroll-triggered reveals and Framer Motion for micro-interactions.
- Philosophy: ZeroSlop - use high-quality institutional terminology, avoid "hacker/terminal" generic styles.

## Component Organization
- Landing Page components reside in `src/components/landing/`.
- Use the `GSAPProvider` for scroll-driven animations on the main page.
