# IdeaSync

**IdeaSync** is a premium, enterprise-grade collaboration platform specifically designed for the **Lendi Institute of Engineering and Technology (LIET)**. It serves as a unified digital ecosystem where students, alumni, and faculty collaborate on innovation tracks, manage IIC events, and drive technological growth.

## 🚀 Mission & Philosophy

### ZeroSlop Philosophy
IdeaSync adheres to the **ZeroSlop** standard: every feature is demo-ready, every UI interaction is purposeful, and the codebase is maintained with strict quality gates (Completeness, Security, Design, and Regression). We prioritize high-quality, bug-free delivery over rushed, 'sloppy' implementations.

### Sentinel Design System
The platform features the **Sentinel** design system—a sophisticated aesthetic characterized by:
- **Monochrome Midnight** backgrounds with glassmorphic depth.
- **Electric Cyan** accents for technical precision.
- **Lendi Blue & Red** custom theme tokens for institutional branding.
- **High-Motion Interactivity** powered by Framer Motion and GSAP ScrollTrigger.
- **3D Visuals** integrated via Spline for an immersive experience.

---

## ✨ Core Features

- **Project Dossiers & Missions:** A GitHub-inspired collaboration workflow with tech-stack tagging and multi-stage quality gates (Discuss, Charter, Build, Ship).
- **Mentorship Hub:** Facilitating alumni-to-student guidance with structured request flows and dedicated Mentor Terminals.
- **Bounty Management:** Faculty-led challenges and competitive bidding for high-impact student projects.
- **AI Project Agents:** Persona-driven consultations (Researcher, Advisor, Lead) integrated directly into project dossiers.
- **Smart News Feed:** A personalized activity stream using weighted scoring for maximum relevance.
- **Health Monitoring:** Real-time tracking of project anti-patterns like 'Scope Creep' and 'Deadline Drift.'
- **Institutional Integration:** Restricted access to @lendi.org or @liethub.org domains with role-based onboarding.

---

## 🛠 Tech Stack

- **Frontend:** Next.js 16 (App Router), TypeScript, Tailwind CSS v4.
- **Interactivity:** Framer Motion, GSAP, Spline 3D, Lottie.
- **Backend/Data:**
  - **Neon Postgres:** Primary relational data store.
  - **Supabase:** Authentication, Realtime features, and Storage.
- **Quality:** Playwright (E2E Testing), Sonner (Toasts), React Query (Data Fetching).
- **Deployment:** Optimized for Docker (Standalone), Railway, and Nixpacks.

---

## 🏃 Getting Started

### Prerequisites
- Node.js 22+
- npm 11+
- Supabase Project & Neon Postgres instance

### Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/hotaro6754/Ideaspace.git
   cd Ideaspace
   ```

2. Install dependencies:
   ```bash
   npm install
   ```

3. Configure Environment:
   Copy .env.example to .env.local and fill in your credentials:
   ```bash
   cp .env.example .env.local
   ```

4. Run the development server:
   ```bash
   npm run dev
   ```

### Deployment
The project is configured for a multi-stage **standalone** build.
```bash
docker build -t ideasync .
docker run -p 3000:3000 ideasync
```

---

## 🛡 Security & Quality Gates
IdeaSync implements the **GSD (Get Shit Done) Framework**:
- **Discuss:** Initial concept and alignment.
- **Charter:** Formalizing requirements and roles.
- **Build:** Development with continuous regression testing.
- **Ship:** Final audit and deployment to production.

---
© 2025 IdeaSync. Built for Lendi Institute of Engineering and Technology.
