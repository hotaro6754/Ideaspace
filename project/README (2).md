# IdeaSpace 🚀

**Build in public. Prove your work. Earn your rank.**

A campus-native innovation platform for **Lendi Institute of Engineering and Technology** — where student ideas find builders, projects get completed, workshops run on demand, and nothing valuable is lost to a WhatsApp group.

---

## Why IdeaSpace Exists

Every college has students with ideas. Lendi has plenty.

But right now, those ideas live in WhatsApp messages that get buried in 24 hours. They live in Google Forms nobody checks twice. They live in hallway conversations that end with *"yeah let's do something"* and go nowhere. Talented students share knowledge with no acknowledgment. Builders start projects with no way to prove they finished them. Events happen with no record that they ever existed.

IdeaSpace was built to fix exactly this — not with another productivity tool, not with a glorified Notion board, but with a platform built specifically for the rhythm and culture of a campus like Lendi.

The goal is simple: **make the best student ideas discoverable, buildable, and finishable — with visible proof that the work was real.**

---

## Who It's For

IdeaSpace is built for every person in the Lendi ecosystem.

### 🎓 Students
You have ideas. You have skills. But you have no visible record of either. IdeaSpace gives you a structured place to post your ideas with a real brief, find teammates who actually want to build, submit verifiable proof of what you shipped, and earn a reputation score that reflects real effort — not just attendance. Your profile becomes your portfolio. Your rank becomes your credential.

### 👨‍🏫 Faculty
Running a workshop through a WhatsApp broadcast and hoping students show up is not a system. IdeaSpace gives faculty a proper event management layer — post workshops, track RSVPs, see real demand signals from students before you even schedule a session. Verify student milestones and proof submissions. Moderate the platform. See what your students are actually building.

### 🏢 Alumni and Mentors
Your experience is valuable to the students still figuring things out. IdeaSpace gives alumni and mentors a formal presence — guide active projects, verify proof submissions, post bounties for students to solve, and stay connected to the college's innovation activity long after graduation.

---

## What IdeaSpace Does

### 🧠 IdeaBoard — The Feed
Post a full idea brief: the problem, your proposed solution, skills you need, and the track it belongs to. Every idea gets an automated **Health Score** (0–100) that measures completeness and clarity. Ideas with higher health scores rank higher, attract more collaborators, and are more likely to actually get built.

**Status pipeline:** `DRAFT → DISCOVERY → BUILDING → SHIPPED → ARCHIVED`

### 🔨 The Forge — Demand-Driven Workshops
Students can signal demand for topics they want to learn. When enough students request a topic, the IIC gets notified and a workshop gets scheduled. Workshops are hosted by Tier 4+ builders (Architect rank and above). Every attendee earns points. Every host builds reputation.

### 🏆 Proof Wall — Verified Work Only
No dummy projects. No screenshots of a landing page and calling it shipped. The Proof Wall shows verified builds with GitHub evidence, demo links, and timestamp-anchored proof entries. Submissions go through a faculty or mentor verification step. Verified proof earns points. Unverified claims earn nothing.

### 📦 The Archive — Failures Worth Learning From
Not every project ships. That's fine. The Archive turns abandoned projects into institutional knowledge. Every archived idea requires a postmortem — what was tried, what broke, what was learned. Anyone can **fork and adopt** an archived idea and pick up where the last team left off. Honest postmortems earn reputation points. Nothing is wasted.

### 📊 Leaderboard — Transparent Rankings
Five tiers based on cumulative points earned through real actions: posting ideas, submitting proof, attending workshops, mentoring, hosting events. Rankings are weekly, monthly, and all-time. Every point event is logged with a reason — no black boxes, no fake scores.

| Tier | Name | Points Required |
|------|------|----------------|
| 1 | Initiate | 0 – 99 |
| 2 | Builder | 100 – 499 |
| 3 | Architect | 500 – 1,499 |
| 4 | Visionary | 1,500 – 3,999 |
| 5 | Legend | 4,000+ |

### 🎯 Bounties — Task-Based Challenges
Faculty, mentors, or admins post bounties — specific build, research, or design challenges with point rewards and deadlines. Students submit solutions and earn points upon verification. Bounties create structured challenges that go beyond open-ended idea posting.

### 👤 Profile — Your Work History
No vanity metrics. Your profile shows what you posted, what you built, what events you attended, what proof you submitted. Skills you claim are visible. Proof entries link your claims to evidence. Your rank badge is earned, not assigned.

### 🔔 Notifications — Actionable, Not Noisy
Every notification links to the exact action it refers to. Collaboration requests, proof verification updates, workshop RSVPs, upvotes on your ideas — all in one compact, uncluttered list.

### 🛡️ Admin Console — Faculty and Admins Only
A full moderation and management layer. Review queue for pending proof submissions, role management for users, content reports, and analytics. Nothing goes live on the platform without the appropriate review layer.

---

## The Proof Pipeline

Every idea on IdeaSpace follows a three-step proof pipeline:

```
POST  ──→  BUILD  ──→  VERIFY
  ↓            ↓            ↓
Idea brief   Progress    Proof submission
+ Health     updates     + faculty review
  Score      on feed     + points awarded
```

This is not a portfolio generator. It is a verification system. The difference matters.

---

## Why Not Just Use... 

| Tool | The Problem |
|------|-------------|
| WhatsApp / Telegram | Ideas get buried in 24 hours. No structure. No proof. No accountability. |
| Google Forms | One-way. No collaboration. No tracking. No follow-up. |
| LinkedIn | Not campus-specific. No real-time project layer. Résumé theater, not proof of work. |
| Discord | Good for chat, not for structured ideation, verification, or reputation. |
| College ERP | Attendance and grades. Not innovation. Not collaboration. Not proof. |

IdeaSpace is none of these. It is purpose-built for the specific way students and faculty at a campus like Lendi actually work — in semester rhythms, with project cycles, under academic constraints, in pursuit of real skills that go beyond marks.

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Framework | Next.js 15 (App Router) |
| Language | TypeScript 5.x (strict mode) |
| Database | MongoDB Atlas |
| ODM | Mongoose 8.x |
| Auth | NextAuth.js v5 — Email magic link + GitHub OAuth |
| Styling | Tailwind CSS v4 with custom design tokens |
| Animation | Framer Motion (UI) + GSAP (hero/landing) |
| Forms | React Hook Form + Zod |
| Data Fetching | TanStack Query v5 |
| File Uploads | Cloudinary |
| Icons | Lucide React |
| Fonts | Sora (display) · IBM Plex Sans (body) · JetBrains Mono |
| Testing | Vitest (unit) + Playwright (e2e) |
| Deployment | Vercel + MongoDB Atlas |

---

## Getting Started (Developers)

### Prerequisites
- Node.js 20+
- MongoDB Atlas account (free M0 tier works)
- GitHub OAuth App
- Resend account (email magic links)
- Cloudinary account (file uploads)

### Environment variables
Copy `.env.example` to `.env.local` and fill in the values:

```bash
cp .env.example .env.local
```

Required variables:
```
MONGODB_URI=
AUTH_SECRET=
AUTH_GITHUB_ID=
AUTH_GITHUB_SECRET=
AUTH_RESEND_KEY=
AUTH_EMAIL_FROM=
CLOUDINARY_CLOUD_NAME=
CLOUDINARY_API_KEY=
CLOUDINARY_API_SECRET=
NEXT_PUBLIC_APP_URL=
```

### Install and run

```bash
# Install dependencies
npm install

# Run development server
npm run dev
```

Open [http://localhost:3000](http://localhost:3000)

### Demo credentials
```
Email: harshith@lendi.org
```
*(Magic link auth — use a valid @lendi.org or @lendi.edu.in address)*

---

## Project Structure

```
ideaspace/
├── src/
│   ├── app/
│   │   ├── page.tsx               # Landing page (public)
│   │   ├── auth/                  # Login, verify, error
│   │   ├── onboarding/            # Post-auth profile setup
│   │   └── (app)/                 # Protected app shell
│   │       ├── feed/              # Idea discovery
│   │       ├── ideas/             # Create, detail, edit
│   │       ├── forge/             # Workshops + RSVPs
│   │       ├── wall/              # Proof wall
│   │       ├── archive/           # Postmortems + forks
│   │       ├── leaderboard/       # Rankings
│   │       ├── bounties/          # Challenges
│   │       ├── profile/           # User profiles
│   │       ├── notifications/     # Notification center
│   │       └── admin/             # Moderation console
│   │
│   ├── components/
│   │   ├── layout/                # Sidebar, Header, AppShell
│   │   ├── landing/               # Hero, ProofPipeline, EvidenceWall
│   │   ├── ideas/                 # IdeaCard, HealthScoreWidget, ProofTimeline
│   │   ├── forge/                 # WorkshopCard, RSVPModule
│   │   ├── leaderboard/           # LeaderboardRow, RankBadge
│   │   └── ui/                    # Button, Input, Badge, Modal, Toast, etc.
│   │
│   ├── lib/
│   │   ├── db.ts                  # MongoDB connection singleton
│   │   ├── auth.ts                # NextAuth config
│   │   ├── health-score.ts        # Idea health algorithm
│   │   ├── points.ts              # Gamification engine
│   │   ├── validators.ts          # Zod schemas
│   │   └── product-config.ts     # Tracks, ranks, point values
│   │
│   └── models/                    # Mongoose models (User, Idea, Proof, etc.)
│
├── tests/
│   ├── unit/                      # Vitest unit tests
│   └── e2e/                       # Playwright end-to-end tests
```

---

## Quality Gates

Every contribution must pass all four gates before it is considered complete:

```bash
npm run lint          # ESLint + Prettier
npm run test:unit     # Vitest unit tests
npm run test:e2e      # Playwright e2e tests
npm run build         # Next.js production build (zero errors)
```

No exceptions. No shortcuts. Every API route has error handling. Every async UI has a skeleton loader. Every interactive element has accessibility attributes.

---

## Design System

IdeaSpace has a custom design system — not a Tailwind starter, not a UI kit. The system is built for speed, legibility, and trust.

**Palette:** Warm neutrals with a sharp teal accent. No purple gradients. No generic blues.

| Token | Value | Use |
|-------|-------|-----|
| `ink-900` | #0B0F14 | Primary text |
| `canvas-0` | #F7F3EC | Background base |
| `teal-500` | #1FB7A6 | Primary actions |
| `ember-500` | #FF6B4A | Destructive / alerts |
| `lime-400` | #C7E85B | Achievement badges |

**Typography:** Sora for display headings. IBM Plex Sans for all body text. JetBrains Mono for code and technical labels. Not Inter. Never Inter.

---

## Deployment

### Vercel (Recommended)
1. Push to GitHub
2. Import project at [vercel.com](https://vercel.com)
3. Set all environment variables from `.env.example`
4. Deploy

### MongoDB Atlas
1. Create a free M0 cluster at [mongodb.com/atlas](https://www.mongodb.com/cloud/atlas)
2. Whitelist Vercel IPs or set `0.0.0.0/0` for free tier
3. Copy the connection string to `MONGODB_URI`
4. Indexes are created automatically on first Mongoose model import

---

## Roadmap

### Phase 0 — Foundation *(current)*
MongoDB Atlas setup, all Mongoose models, NextAuth v5, route protection, institutional email validation, Vitest + Playwright configuration.

### Phase 1 — Core Screens
Design system tokens, all base UI components, app shell, landing page, auth flow, onboarding wizard, feed, create idea form, idea detail page.

### Phase 2 — Proof and Reputation
Proof wall, archive + postmortem form, leaderboard, vote API, score event tracking, rank auto-update, notification system.

### Phase 3 — Institutional Layer
Forge (workshops), Bounties, Admin dashboard, review queue, role management, moderation, full e2e test suite.

### Phase 4 — Polish and Launch
Framer Motion page transitions, GSAP hero animation, skeleton loaders, empty states, mobile audit, WCAG AA contrast check, Lighthouse LCP < 2.5s, production deploy.

---

## Contributing

IdeaSpace is an open build. If you're a Lendi student and you want to contribute, here's how:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Make your changes with full TypeScript coverage (no `any`)
4. Ensure all quality gates pass: lint, unit tests, e2e tests, build
5. Open a pull request with a clear description of what changed and why

**Before writing any code, read the master build document in `/docs/MASTER_PROMPT.md`.** It is the single source of truth for every decision in this codebase.

---

## Campus Access

IdeaSpace is restricted to verified Lendi students, faculty, and staff. Login requires a valid institutional email address:

- `@lendi.org`
- `@lendi.edu.in`

No exceptions. No workarounds. Campus-only is a feature, not a limitation.

---

## License

Built for Lendi Institute of Engineering and Technology.

IdeaSpace is an internal campus platform. All rights reserved.

---

*Ideas die in WhatsApp groups. Build something that lasts.*
