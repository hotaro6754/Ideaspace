# IdeaSpace 🚀

> **Build in public. Prove your work. Earn your rank.**

IdeaSpace is a campus-native innovation platform built for Lendi Institute of Engineering and Technology. Students post ideas, recruit collaborators, run workshops, build a verifiable proof-of-work trail, and earn reputation on a transparent leaderboard. The product is benchmarked against professional SaaS tools — not a student project, not a Notion clone.

---

## Table of Contents

1. [Quick Start](#quick-start)
2. [Product Vision](#product-vision)
3. [User Roles](#user-roles)
4. [Core Features](#core-features)
   - [Feed](#feed)
   - [Idea Board](#idea-board)
   - [Idea Health Score](#idea-health-score)
   - [The Forge (Workshops)](#the-forge-workshops)
   - [Proof Wall](#proof-wall)
   - [The Archive (Postmortems)](#the-archive-postmortems)
   - [Bounties](#bounties)
   - [Leaderboard & Reputation](#leaderboard--reputation)
   - [Profile](#profile)
   - [Notifications](#notifications)
   - [Admin Console](#admin-console)
   - [Landing Page](#landing-page)
5. [Gamification & Points](#gamification--points)
6. [Tech Stack](#tech-stack)
7. [Project Structure](#project-structure)
8. [Database Schema](#database-schema)
9. [Auth & Access Control](#auth--access-control)
10. [API Reference](#api-reference)
11. [Design System](#design-system)
12. [Environment Variables](#environment-variables)
13. [Development Setup](#development-setup)
14. [Testing](#testing)
15. [Deployment](#deployment)
16. [Milestones](#milestones)
17. [Design Principles](#design-principles)

---

## Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/hotaro6754/ZeroSlop.git
cd ZeroSlop

# 2. Install all dependencies
npm install

# 3. Copy environment variables and fill in your secrets
cp .env.example .env.local

# 4. Start the development server
npm run dev
```

Open [http://localhost:3000](http://localhost:3000)

### Demo Credentials

| Email | Password |
|---|---|
| `harshith@lendi.org` | `password123` |

---

## Product Vision

IdeaSpace exists to make the best student ideas **discoverable**, **buildable**, and **finishable** — with visible proof of impact at every stage.

Campus innovation usually dies in WhatsApp groups, random Google Forms, or Discord servers where nothing is tracked and nothing is finished. IdeaSpace replaces that chaos with a structured system: post an idea, recruit builders, prove your work, and earn recognition that follows you beyond graduation.

**The core loop:**
```
POST IDEA → RECRUIT TEAM → BUILD IN PUBLIC → SUBMIT PROOF → GET VERIFIED → EARN RANK
```

---

## User Roles

| Role | Description | Key Permissions |
|---|---|---|
| `student` | Default role for all campus members | Post ideas, join builds, submit proof, attend workshops, earn points |
| `faculty` | Staff, instructors, IIC coordinators | Host workshops, verify proof entries, review queue access |
| `admin` | Platform administrators | Full moderation, role management, analytics, feature flags |
| `mentor` | Senior builders or external mentors | Guide projects, verify milestone completions |
| `judge` | Bounty and competition evaluators | Score bounty submissions, review entries |
| `alumni` | Graduated students | Read-only access to archive and feed, can mentor |

Access is strictly enforced via Next.js middleware. All protected routes check role before rendering.

---

## Core Features

### Feed

**Route:** `/feed`

The Feed is the primary discovery surface. It is a filterable, searchable grid of every idea on the platform.

- **Filter rail** — filter by Track (8 engineering tracks), Status (discovery / building / shipped), and Sort (latest / trending / top upvoted)
- **KPI strip** — live stats: total ideas posted, active builds this week, shipped this month
- **Idea cards** — each card shows cover image, status pill, title, problem preview, tags, owner avatar, collaborator stack, upvote count, and a mini health score bar
- Cards link directly to the full idea detail page
- Mobile: single column. Desktop: two column grid.

### Idea Board

**Routes:** `/ideas/new` (create), `/ideas/[id]` (view), `/ideas/[id]/edit` (edit)

The Idea Board is where ideas are written, shared, and built. Every idea is a structured brief, not a casual post.

**Creating an idea** requires:
- **Title** — clear, descriptive, 10–120 characters
- **Problem** — what is broken or missing, minimum 80 characters
- **Solution** — what you propose to build, minimum 80 characters
- **Track** — one of 8 engineering tracks (required)
- **Tags** — at least 2 topic tags for discoverability
- **Skills needed** — what kind of collaborators you want
- **GitHub URL** — optional but earns health score points
- **Demo URL** — optional but earns health score points
- **Cover image** — uploaded via Cloudinary

**Ideas go through the following status lifecycle:**

| Status | Meaning |
|---|---|
| `draft` | Saved but not visible on feed |
| `discovery` | Published, accepting collaborators and upvotes |
| `building` | Active team, in-progress |
| `shipped` | Completed and has verified proof |
| `archived` | Abandoned — must write a postmortem |
| `rejected` | Removed by admin/faculty |

**Idea detail page layout:**
- Left panel (8 columns): status + track badge, title, problem, solution, skills needed, collaborator stack with "Request to join" button, activity feed
- Right sidebar (4 columns): owner card, circular health score widget, Proof Rail timeline, GitHub/demo links, upvote button

**Collaboration flow:**
Any student can request to join an idea. The owner receives a notification and can approve or reject. Approved collaborators appear in the collaborator stack on the idea card and detail page.

### Idea Health Score

Every idea has a **Health Score** — a real-time completeness gauge from 0 to 100 that updates live as the creation form is filled in.

**Scoring breakdown:**

| Criterion | Points |
|---|---|
| Title ≥ 10 characters | +15 |
| Problem ≥ 80 characters | +20 |
| Solution ≥ 80 characters | +20 |
| At least 2 tags | +10 |
| At least 1 skill needed | +10 |
| GitHub URL provided | +10 |
| Demo URL provided | +10 |
| Cover image uploaded | +5 |

- **Score < 40:** Cannot publish. The form shows a checklist of what's missing.
- **Score 40–69:** Published with a warning color on the health widget (orange).
- **Score ≥ 70:** Healthy idea, green health widget. Eligible to be featured.

The Health Score widget is a circular SVG gauge with animated fill (Framer Motion). It appears both on the creation form (live feedback) and on the idea detail sidebar.

### The Forge (Workshops