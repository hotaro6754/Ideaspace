# IdeaSync | Lendi Innovation Network (Sentinel V1)

A premium, high-motion collaboration platform for the Lendi Institute of Engineering and Technology (LIET). IdeaSync bridges the gap between students, faculty, and industry alumni through a gamified innovation ecosystem.

## 🚀 Key Technologies
- **Frontend**: Next.js 16 (Turbopack), Tailwind CSS v4, Framer Motion, Spline 3D.
- **Backend**: Neon Postgres (Serverless), Supabase (Auth/Realtime/Storage).
- **Quality**: ZeroSlop Foundation (100% Verified Build).

## 🛠 Project Structure
- \`ideasync-next/\`: Primary platform codebase.
- \`Dockerfile\`: Optimized multi-stage Node.js build for standalone production.
- \`vercel.json\`, \`railway.json\`: Multi-platform deployment manifests.

## 🔑 Core Modules
1. **Mission Hub**: End-to-end project lifecycle with GSD Quality Gates.
2. **Bounty System**: Faculty-sanctioned challenges with XP rewards.
3. **Talent Network**: Reputation-based directory with verified skills.
4. **Mentorship Hub**: Secure student-alumni guidance uplink.
5. **Consensus Engine**: Campus-wide polls and tech track debates.
6. **AI Project Agents**: Persona-driven mission intelligence (Researcher, Advisor, Lead).

## ⚙️ Deployment
The platform is ready for production deployment on Render, Railway, or Vercel.

### Render / Railway (Docker)
Built-in Dockerfile automatically handles the Next.js standalone build. Ensure \`DATABASE_URL\` and Supabase keys are provided in environment variables.

### Vercel
Configured to build from the \`ideasync-next/\` directory via \`vercel.json\`.

---
*Built for the future of LIET innovation.*
