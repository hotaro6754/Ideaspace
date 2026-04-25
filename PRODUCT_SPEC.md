# IdeaSync: Master Product Specification

## 1. Vision & Core Philosophy
IdeaSync is the "Lendi College Internet" — a premium, unified digital ecosystem for students, alumni, and faculty. It merges social networking (Reddit/Instagram), professional identity (LinkedIn), and collaborative building (GitHub/Linear) into a single, gamified campus experience.

### Core Principles
- **ZeroSlop Methodology:** Every feature is built with high "taste" and passes strict quality gates (Completeness, Security, Design, Regression).
- **GSD (Get Shit Done):** Focused on turning raw student ideas into shippable projects using structured Idea Charters and Project Briefs.
- **Lendi Exclusive:** Access is strictly gated to college email domains (`@lendi.org`, `@liethub.org`).

---

## 2. Design System (The "Premium" Aesthetic)
Inspired by **Linear, Antimetal, and Fey**.
- **Theme:** High-end Dark Mode with Glassmorphic overlays and subtle Lendi Blue (#004a99) / Red (#ed1c24) accents.
- **Layout:** Bento Grid dashboards for high-density information display.
- **Typography:** Plus Jakarta Sans (Headers), Inter (Body).
- **Motion:**
  - **GSAP ScrollTrigger:** Smooth entry reveals for all landing and feed sections.
  - **Lottie:** Loop animations for login backgrounds and success states.
  - **Framer Motion:** Bouncy micro-interactions for buttons, card hovers, and state transitions.

---

## 3. Core Modules & Features

### A. The Feed (The Campus Pulse)
- **Algorithm:** Personalized mix of Following + Trending + Skill-matched projects.
- **Post Types:** Text, Image/Video, Polls, Resource Shares, and Milestone Celebrations.
- **Interactions:** Reddit-style threaded comments, real-time upvotes, and campus-wide "hot takes".

### B. Projects (The GitHub Layer)
- **Posting:** Rich project cards with tech stack chips, status (Planning/Dev/Ship), and role requirements.
- **Collaboration:** "I want to join" request flow with profile-based screening.
- **Forking:** Ability to fork project ideas, linking back to original inspirations.
- **Roadmap:** Visual "Discuss -> Ship" timeline based on GSD Quality Gates.

### C. Bounties (The College Problem Solver)
- **Board:** Dedicated board for real-world campus problems posted by Faculty.
- **Flow:** Submission gateway -> Admin Judging Panel -> Public Winner Announcement (with confetti).
- **Rewards:** High point values and unique badges (Bounty Hunter, Campus Hero).

### D. Profiles (The LinkedIn Layer)
- **Identity:** PUBLIC portfolio doubling as a campus resume.
- **Heatmap:** GitHub-style activity graph showing contribution frequency.
- **Ranks:** Visual progression: 🌱 Seedling -> ⚡ Spark -> 🔧 Builder -> 🚀 Launcher -> 🏅 Innovator -> 👑 Campus Legend.
- **Endorsements:** Peer-to-peer skill verification.

### E. Messaging & Teams (The Discord Layer)
- **Direct Messaging:** Real-time 1-on-1 collaboration.
- **Team Channels:** Auto-created private rooms for approved project teams.
- **Interest Channels:** Public rooms like #tech-talk, #lendi-memes, and #startup-ideas.
- **Features:** File sharing, @mentions, and reaction triggers.

---

## 4. Technical Architecture
- **Framework:** Next.js 15 (App Router), TypeScript.
- **Styling:** Tailwind CSS + Shadcn/UI + Magic UI.
- **Backend:** Supabase (Auth, Postgres, Realtime, Storage).
- **Analytics:** Tinybird (Real-time leaderboard and activity metrics).
- **Tracking:** Linear (Internal task mirroring).
- **Deployment:** Render (Production-ready CI/CD).

---

## 5. Innovation Frameworks
- **AI Project Agents:** Persona-driven assistants (Researcher, Advisor, Lead) providing context-aware suggestions.
- **AntiPattern Detection:** Real-time monitoring for project risks like 'Silent Partner' or 'Deadline Drift'.
- **Quality Gates:** Mandatory self-scoring and gate validation before any project is marked "Shipped".
