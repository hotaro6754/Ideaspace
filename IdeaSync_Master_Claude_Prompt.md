# IdeaSync — Master Build Prompt for Claude
## Use this prompt in a fresh Claude conversation. Attach nothing else. This prompt is fully self-contained.

---

# CONTEXT — READ THIS FIRST

You are building **IdeaSync + Forge** — Lendi Institute of Engineering and Technology's campus innovation platform. This is NOT a prototype. NOT a mockup. NOT a demo. This is a fully functional, production-grade web application where every single feature works end to end.

The previous version was built in GitHub Codespaces with plain PHP and it was visually broken, half-functional, and amateur. You are rebuilding this from scratch with zero shortcuts. Every line of code must be production quality.

## Design Reference

Your visual quality benchmark is the following class of products:
- **modal.com** — Razor-sharp developer platform. Obsessive typography, generous whitespace, monospaced code elements, surgical precision
- **composio.dev** — Dark modern developer tool. Strong grid, sharp cards, clear hierarchy
- **antimetal.com** — Clean SaaS. Confident layout, no clutter, bold value props
- **langbase.com** — AI platform. Sophisticated dark mode, readable, strong brand
- **meetjamie.ai** — Product-first design. Clean cards, soft shadows, confident typography
- **dualite.dev** — Minimalist dev tool. Premium spacing, subtle interactions
- **swan.so** — Elegant minimal SaaS

Your UI must look like it belongs in that company. Not a student project. Not Bootstrap defaults. Not cookie-cutter Tailwind.

---

# TECH STACK — NON-NEGOTIABLE

```
Frontend:    Next.js 14 (App Router) + TypeScript
Styling:     Tailwind CSS v3 + custom CSS variables
Database:    PostgreSQL via Prisma ORM
Auth:        NextAuth.js v5 (credentials provider)
State:       Zustand for client state
API:         Next.js API routes (REST)
External:    GitHub REST API v3 (user repos, languages)
Deployment:  Vercel-compatible (no XAMPP, no localhost-only hacks)
Font:        Geist (Vercel's font — sharp, technical, professional)
Icons:       Lucide React
Animations:  Framer Motion for page transitions + micro-interactions
```

---

# DESIGN SYSTEM — IMPLEMENT EXACTLY

```css
:root {
  /* Core palette */
  --bg-primary: #09090B;        /* Near-black base */
  --bg-secondary: #111113;      /* Card backgrounds */
  --bg-tertiary: #18181B;       /* Elevated surfaces */
  --border: #27272A;            /* Subtle borders */
  --border-bright: #3F3F46;     /* Hover borders */

  /* Text */
  --text-primary: #FAFAFA;      /* Headlines */
  --text-secondary: #A1A1AA;    /* Body, descriptions */
  --text-muted: #52525B;        /* Placeholders, metadata */

  /* Brand accent */
  --accent: #6366F1;            /* Indigo — primary CTA */
  --accent-hover: #4F46E5;      /* Darker indigo on hover */
  --accent-glow: rgba(99,102,241,0.15); /* Glow for cards */

  /* Status colors */
  --green: #22C55E;             /* Open/success */
  --yellow: #EAB308;            /* In Progress */
  --red: #EF4444;               /* SOS/danger */
  --gold: #F59E0B;              /* IIC Featured */
  --cyan: #06B6D4;              /* GitHub/tech accent */

  /* Tier colors */
  --tier-1: #52525B;
  --tier-2: #22C55E;
  --tier-3: #06B6D4;
  --tier-4: #6366F1;
  --tier-5: #F59E0B;

  /* Spacing scale */
  --radius-sm: 6px;
  --radius-md: 10px;
  --radius-lg: 16px;
  --radius-xl: 24px;
}
```

**Typography rules:**
- Headlines: `font-semibold` or `font-bold`, tight tracking (`-0.02em`)
- Body: `text-sm` with `leading-6`, `text-secondary`
- Metadata: `text-xs`, `text-muted`
- Code/IDs: `font-mono`, `text-cyan`

**Card anatomy:**
```
bg-[#111113] border border-[#27272A] rounded-[10px] p-5
hover:border-[#3F3F46] hover:shadow-[0_0_20px_rgba(99,102,241,0.08)]
transition-all duration-200
```

**Button system:**
- Primary: `bg-[#6366F1] hover:bg-[#4F46E5] text-white px-4 py-2 rounded-[6px] text-sm font-medium`
- Secondary: `bg-transparent border border-[#27272A] hover:border-[#3F3F46] text-[#A1A1AA] hover:text-[#FAFAFA]`
- Danger: `bg-transparent border border-red-500/30 text-red-400 hover:bg-red-500/10`

---

# DATABASE SCHEMA — IMPLEMENT IN PRISMA

```prisma
// prisma/schema.prisma

generator client {
  provider = "prisma-client-js"
}

datasource db {
  provider = "postgresql"
  url      = env("DATABASE_URL")
}

model User {
  id               String    @id @default(cuid())
  rollNumber       String    @unique
  name             String
  email            String    @unique
  passwordHash     String
  branch           String
  year             Int
  githubUsername   String?
  githubData       Json?
  domainInterests  String[]
  totalPoints      Int       @default(0)
  tier             Int       @default(1)
  isMentorOpen     Boolean   @default(false)
  mentorDomains    String[]
  isIicAdmin       Boolean   @default(false)
  avatarColor      String    @default("#6366F1")
  createdAt        DateTime  @default(now())
  lastLogin        DateTime?

  ideas            Idea[]    @relation("IdeaOwner")
  collaborations   Collaboration[]
  pointTransactions PointTransaction[]
  notifications    Notification[]
  eventRegistrations EventRegistration[]
  postmortems      Postmortem[]
  conductedEvents  Event[]
}

model Idea {
  id            String   @id @default(cuid())
  ownerId       String
  parentIdeaId  String?
  title         String
  description   String
  domain        String
  skillsNeeded  String[]
  status        IdeaStatus @default(OPEN)
  upvotes       Int      @default(0)
  applicantCount Int     @default(0)
  healthScore   Int      @default(0)
  githubRepoUrl String?
  isIicFeatured Boolean  @default(false)
  isSosActive   Boolean  @default(false)
  isActive      Boolean  @default(true)
  createdAt     DateTime @default(now())
  updatedAt     DateTime @updatedAt

  owner         User     @relation("IdeaOwner", fields: [ownerId], references: [id])
  parentIdea    Idea?    @relation("IdeaFork", fields: [parentIdeaId], references: [id])
  forks         Idea[]   @relation("IdeaFork")
  collaborations Collaboration[]
  postmortems   Postmortem[]
}

enum IdeaStatus {
  OPEN
  IN_PROGRESS
  COMPLETED
  ABANDONED
  INHERITED
}

model Collaboration {
  id          String   @id @default(cuid())
  ideaId      String
  applicantId String
  role        CollabRole @default(BUILDER)
  status      CollabStatus @default(PENDING)
  message     String?
  appliedAt   DateTime @default(now())
  respondedAt DateTime?

  idea        Idea     @relation(fields: [ideaId], references: [id])
  applicant   User     @relation(fields: [applicantId], references: [id])
}

enum CollabRole {
  BUILDER
  RESCUER
  INHERITOR
}

enum CollabStatus {
  PENDING
  ACCEPTED
  REJECTED
}

model PointTransaction {
  id            String   @id @default(cuid())
  userId        String
  action        String
  points        Int
  referenceId   String?
  referenceType String?
  createdAt     DateTime @default(now())

  user          User     @relation(fields: [userId], references: [id])
}

model Event {
  id            String   @id @default(cuid())
  conductorId   String
  title         String
  description   String
  domain        String
  format        EventFormat
  eventDate     DateTime
  seatLimit     Int
  seatsTaken    Int      @default(0)
  prerequisites String?
  resourcesUrl  String?
  status        EventStatus @default(UPCOMING)
  createdAt     DateTime @default(now())

  conductor     User     @relation(fields: [conductorId], references: [id])
  registrations EventRegistration[]
}

enum EventFormat {
  WORKSHOP
  SEMINAR
  BOOTCAMP
}

enum EventStatus {
  UPCOMING
  ONGOING
  COMPLETED
  CANCELLED
}

model EventRegistration {
  id           String   @id @default(cuid())
  eventId      String
  userId       String
  ticketCode   String   @unique @default(cuid())
  attended     Boolean  @default(false)
  registeredAt DateTime @default(now())

  event        Event    @relation(fields: [eventId], references: [id])
  user         User     @relation(fields: [userId], references: [id])
}

model Postmortem {
  id           String   @id @default(cuid())
  ideaId       String
  authorId     String
  whatWasIdea  String
  whyStopped   String
  lessonsLearned String
  nextSteps    String?
  isPublic     Boolean  @default(true)
  createdAt    DateTime @default(now())

  idea         Idea     @relation(fields: [ideaId], references: [id])
  author       User     @relation(fields: [authorId], references: [id])
}

model Notification {
  id          String   @id @default(cuid())
  userId      String
  type        String
  message     String
  referenceId String?
  isRead      Boolean  @default(false)
  createdAt   DateTime @default(now())

  user        User     @relation(fields: [userId], references: [id])
}
```

---

# FILE STRUCTURE — BUILD EXACTLY THIS

```
ideasync/
├── prisma/
│   ├── schema.prisma
│   └── seed.ts
├── src/
│   ├── app/
│   │   ├── layout.tsx                    # Root layout, Geist font, dark theme
│   │   ├── page.tsx                      # Landing page
│   │   ├── auth/
│   │   │   ├── login/page.tsx
│   │   │   ├── register/page.tsx
│   │   │   └── onboarding/page.tsx       # Domain interest selector
│   │   ├── feed/page.tsx                 # Ideas feed (main app shell)
│   │   ├── ideas/
│   │   │   ├── post/page.tsx             # Post new idea
│   │   │   └── [id]/page.tsx             # Idea detail page
│   │   ├── profile/
│   │   │   ├── [id]/page.tsx             # Public builder profile
│   │   │   └── edit/page.tsx             # Edit own profile
│   │   ├── dashboard/page.tsx            # Personal dashboard
│   │   ├── leaderboard/page.tsx          # Three leaderboards
│   │   ├── forge/
│   │   │   ├── page.tsx                  # Events feed
│   │   │   └── [id]/page.tsx             # Event detail + register
│   │   ├── archive/page.tsx              # Postmortems + dead ideas
│   │   ├── admin/
│   │   │   ├── page.tsx                  # Skill gap map + IIC dashboard
│   │   │   └── ideas/page.tsx            # Manage featured ideas
│   │   ├── wall/page.tsx                 # Proof Wall — all completed projects
│   │   └── api/
│   │       ├── auth/[...nextauth]/route.ts
│   │       ├── ideas/
│   │       │   ├── route.ts              # GET list, POST create
│   │       │   └── [id]/
│   │       │       ├── route.ts          # GET detail, PATCH update
│   │       │       ├── apply/route.ts    # POST apply
│   │       │       ├── upvote/route.ts   # POST upvote
│   │       │       ├── fork/route.ts     # POST fork
│   │       │       └── sos/route.ts      # POST trigger SOS
│   │       ├── collaborations/
│   │       │   └── [id]/
│   │       │       └── respond/route.ts  # PATCH accept/reject
│   │       ├── users/
│   │       │   ├── route.ts              # POST register
│   │       │   └── [id]/
│   │       │       ├── route.ts          # GET profile
│   │       │       └── github/route.ts   # POST refresh GitHub data
│   │       ├── events/
│   │       │   ├── route.ts              # GET list, POST create
│   │       │   └── [id]/
│   │       │       ├── route.ts          # GET detail
│   │       │       └── register/route.ts # POST register
│   │       ├── leaderboard/route.ts      # GET all three boards
│   │       ├── admin/
│   │       │   ├── skill-gap/route.ts    # GET skill demand vs supply
│   │       │   └── feature/[id]/route.ts # PATCH feature idea
│   │       ├── postmortems/route.ts      # GET list, POST create
│   │       ├── notifications/route.ts    # GET + PATCH mark read
│   │       └── dashboard/route.ts        # GET user's full dashboard data
│   ├── components/
│   │   ├── layout/
│   │   │   ├── AppShell.tsx              # Sidebar + topbar wrapper
│   │   │   ├── Sidebar.tsx               # Left nav
│   │   │   ├── Topbar.tsx                # Search + notifications + avatar
│   │   │   └── MobileNav.tsx
│   │   ├── ideas/
│   │   │   ├── IdeaCard.tsx
│   │   │   ├── IdeaFeed.tsx
│   │   │   ├── IdeaFilters.tsx
│   │   │   ├── IdeaHealthBar.tsx
│   │   │   ├── IdeaStatusBadge.tsx
│   │   │   └── DomainBadge.tsx
│   │   ├── profile/
│   │   │   ├── BuilderCard.tsx           # GitHub live card
│   │   │   ├── TierBadge.tsx
│   │   │   ├── PointsDisplay.tsx
│   │   │   └── ActivityGraph.tsx
│   │   ├── leaderboard/
│   │   │   ├── LeaderboardTable.tsx
│   │   │   └── RankRow.tsx
│   │   ├── forge/
│   │   │   ├── EventCard.tsx
│   │   │   └── TicketModal.tsx
│   │   ├── admin/
│   │   │   ├── SkillGapChart.tsx
│   │   │   └── StatsGrid.tsx
│   │   └── ui/
│   │       ├── Button.tsx
│   │       ├── Input.tsx
│   │       ├── Modal.tsx
│   │       ├── Badge.tsx
│   │       ├── Skeleton.tsx
│   │       ├── EmptyState.tsx
│   │       ├── Toast.tsx
│   │       └── ProgressBar.tsx
│   ├── lib/
│   │   ├── prisma.ts                     # Prisma client singleton
│   │   ├── auth.ts                       # NextAuth config
│   │   ├── points.ts                     # Points engine
│   │   ├── notify.ts                     # Notification creator
│   │   ├── github.ts                     # GitHub API calls
│   │   ├── health.ts                     # IdeaHealth score calculator
│   │   └── validators.ts                 # Zod schemas
│   ├── store/
│   │   └── useAppStore.ts                # Zustand store
│   └── types/
│       └── index.ts                      # All TypeScript types
├── .env.local.example
├── tailwind.config.ts
├── next.config.ts
└── package.json
```

---

# ALL PAGES — IMPLEMENT FULLY

## PAGE 1: Landing Page (`/`)

**DO NOT make this a standard marketing page. Make it feel like the real product leaking through.**

Layout:
- Full-screen dark hero (`#09090B` background)
- Subtle dot grid pattern in background (CSS, no image)
- Top navigation: `IdeaSync` wordmark left, `Log in` + `Join Lendi` buttons right
- Hero section centered:
  - Small badge pill: `LENDI CAMPUS PLATFORM • SINCE 2025` in muted text
  - Headline (3-4rem, bold, tight tracking): `"Your idea deserves a builder. Your skills deserve a vision."`
  - Subheadline (text-secondary, max-w-xl): `"IdeaSync is Lendi's campus operating system — where ideas find builders, projects get completed, and nothing is lost to WhatsApp."`
  - CTA buttons: `I have an idea` (primary indigo) + `I have skills` (secondary ghost)
  - Live stats bar below buttons: animated counters — ideas posted | collaborations active | projects completed | builders registered
- Below hero: 3 feature blocks in a horizontal strip (dark cards with subtle indigo glow on hover):
  - **IdeaBoard** — Ideas meet verified builders
  - **Forge** — Workshops run by top builders
  - **The Archive** — Every project, permanent record
- Section: "How it works" — 4-step horizontal flow with connecting line
- Section: Builder Ranks preview — show all 5 tiers (Initiate → Legend) with badges
- Footer: minimal. Wordmark + tagline + GitHub link

**Critical:** Every element must feel intentional. No Lorem Ipsum. Use real copy from this PRD.

---

## PAGE 2: Register (`/auth/register`)

Split-panel layout:
- Left (40%): Dark panel with IdeaSync brand, a single powerful quote, and 3 bullet points about what they're joining
- Right (60%): Registration form

Form fields (all validated):
- Full Name
- Roll Number (regex: must match Lendi format like `21B21A0501`)
- Email (must end in `@lendi.org` — client + server validated)
- Branch (dropdown: CSE, ECE, MECH, CIVIL, EEE, IT)
- Year (1, 2, 3, 4)
- GitHub Username (optional, labeled "adds your BuilderCard")
- Password (show/hide toggle, strength meter)
- Confirm Password

On submit:
- bcrypt hash password (use `bcryptjs`)
- Validate roll number uniqueness
- Validate email uniqueness
- Create user in DB
- Redirect to `/auth/onboarding`

Error states: inline, red, never alert boxes.

---

## PAGE 3: Domain Onboarding (`/auth/onboarding`)

Full screen. Centered. Animated entrance (Framer Motion stagger).

Header: `"What are you into?"` with progress indicator `Step 2 of 2`

8 domain tiles in a 4×2 grid. Each tile:
- Icon (Lucide) + label
- Unselected: `bg-[#18181B] border border-[#27272A]`
- Selected: `bg-indigo-500/10 border border-indigo-500 text-indigo-300`
- Click to toggle. Scales up on hover (Framer Motion whileHover)

Domains:
1. Brain → AI & Machine Learning
2. Shield → Cybersecurity
3. Code → Web Development
4. Smartphone → App Development
5. Cpu → IoT & Hardware
6. TrendingUp → Finance & FinTech
7. Globe → Social Impact
8. Palette → Design & UI/UX

`Continue to IdeaSync` button: disabled (opacity-50) until at least 1 selected. On click: save to DB, redirect to `/feed`.

---

## PAGE 4: Login (`/auth/login`)

Same split-panel as register but reversed. Minimal form: email + password. Forgot password (placeholder link). `New to Lendi? Join here` link.

On success: redirect to `/feed`.

---

## PAGE 5: Ideas Feed (`/feed`) — THE CORE SCREEN

**App Shell wraps this and all authenticated pages.**

**App Shell — Sidebar:**
Left sidebar (240px, fixed, dark `#0D0D0F`, border-right `#1C1C1E`):
- Logo at top
- Nav items with icons:
  - Feed (Layers)
  - Post Idea (Plus)
  - Leaderboard (BarChart2)
  - Forge Events (Calendar)
  - The Archive (Archive)
  - Proof Wall (Award)
  - [Admin only] IIC Dashboard (BarChart)
- Bottom section: user avatar + name + tier badge + points

**Top bar (full width, 60px):**
- Search input (command+K shortcut): searches ideas by title/domain/skill
- Notification bell with unread count badge
- Quick post button: `+ Post Idea`

**Feed layout:**
- Filter sidebar (220px): 
  - Domain filter (checkboxes, all 8 domains)
  - Skills Needed (tag pills, click to toggle)
  - Status (Open | In Progress | Completed)
  - Sort: Newest | Most Applied | IdeaHealth | IIC Featured
  - `Clear filters` link

- Feed area (remaining width):
  - Pinned section: `IIC Featured` with gold header bar — shows IIC-featured ideas with gold border glow
  - Main feed: responsive grid (2 columns desktop, 1 mobile)

**Idea Card — implement pixel-perfectly:**
```
┌─────────────────────────────────────────┐
│ [Domain Badge] [Status Badge]  [⭐ IIC] │
│                                          │
│ [Idea Title — bold, 16px]                │
│ [Short description — 2 lines, muted]     │
│                                          │
│ [Avatar] Ravi K. · ECE 3rd Year         │
│                                          │
│ Skills: [Python] [React] [Flutter]       │
│                                          │
│ ─────────────────────────────────────── │
│ 12 applicants  [IdeaHealth ████░░ 68%]   │
│                              [Apply →]   │
└─────────────────────────────────────────┘
```

IdeaHealth bar color: green (>70%) → yellow (40-70%) → red (<40%)

IdeaHealth score is computed as:
```
score = 0
+ description.length > 200 ? 20 : 0
+ skillsNeeded.length >= 2 ? 20 : 0
+ applicantCount > 0 ? 20 : 0
+ updatedAt within 7 days ? 20 : 0
+ isIicFeatured ? 20 : 0
```

Domain badge colors:
- AI/ML: indigo
- Cybersecurity: red/rose
- Web Dev: cyan
- App Dev: green
- IoT: orange
- FinTech: yellow
- Social Impact: purple
- Design: pink

On `Apply` click: opens application modal (message textarea + confirm button). Requires login.

**Infinite scroll or pagination (choose pagination — simpler, more reliable)**

---

## PAGE 6: Post Idea (`/ideas/post`)

Full-page form. Left column form, right column live preview card.

Fields:
- **Title** — text input, 5-200 chars
- **Description** — rich textarea (just a styled textarea, no complex editor), min 100 chars. Character count shown.
- **Domain** — radio group (styled pills, not a dropdown)
- **Skills Needed** — tag input: type a skill, press enter, shows as pill. Delete with ×. Min 1, max 8.
- **Status** — Open (default) or In Progress
- **GitHub Repo URL** — optional
- **Attach Document** — optional file upload (stores reference only)

Right panel shows live preview of how the idea card will look.

On submit: award +10 points, create notification to user, redirect to `/ideas/[id]`.

---

## PAGE 7: Idea Detail (`/ideas/[id]`)

Full page for a single idea.

Left column (60%):
- Idea title (large, bold)
- Owner row: avatar + name + branch + year + tier badge
- Domain badge + status badge
- IdeaHealth score with progress bar
- Full description (rendered, proper line breaks)
- Skills Needed tags
- GitHub Repo link (if set)
- `Fork this Idea` button (any logged-in user)
- `SOS — This project is stuck` button (owner only, when In Progress)
- If forked: `Forked from [original title]` attribution link
- Actions (owner only): Change Status dropdown, Mark IIC Featured (admin only), Delete

Right column (40%):
- Apply panel (if Open):
  - Message textarea: "What can you bring to this?"
  - Submit button
  - Applicant count
- Collaborators panel: shows accepted collaborators with their GitHub card preview
- Pending applicants (owner only): shows list with Accept/Reject buttons, each applicant has GitHub link
- Fork tree: shows if this idea has been forked and by whom

Below: Comments section (basic — textarea + list of comments with timestamps)

If status = ABANDONED: banner linking to postmortem if it exists

---

## PAGE 8: Builder Profile (`/profile/[id]`)

**This is the most important single page. Make it feel like a premium developer card.**

Header section (dark card, full width):
- Large avatar circle (initials-based, uses `avatarColor`)
- Name (2rem, bold)
- Branch + Year + Roll Number (muted)
- Tier badge: `BUILDER · TIER 3` with appropriate tier color
- Points: `340 pts ⚡` 
- Edit Profile button (own profile only)
- Open to Mentor toggle badge (if active): `🟢 Open to Mentor · AI/ML, Cybersecurity`
- 3 stat boxes in a row:
  - Ideas Posted
  - Collaborations
  - Projects Completed

**GitHub Live Card section (title: "Verified Skills"):**
Background: `#111113`, border with subtle cyan glow
- GitHub username: `@hotaro6754` with octicons-style icon
- Last synced: `2 hours ago` + `Refresh` button (calls `/api/users/[id]/github`)
- Top 3 repos as mini-cards:
  ```
  ┌─────────────────────────────────────┐
  │ campus-token-system          ⭐ 12  │
  │ Python · 14 commits                 │
  └─────────────────────────────────────┘
  ```
- Language bars:
  ```
  Python     ████████░░  80%
  JavaScript █████░░░░░  50%
  C          ███░░░░░░░  30%
  ```

If no GitHub username set: call-to-action card with field to add username.

**Completed Projects (Proof Wall cards for this user):**
Grid of completed project cards.

**Current Projects:**
Ideas they're actively collaborating on.

**Activity section:**
Contribution-style graph (7×52 grid squares showing point activity per day — build this with computed data from `point_transactions`)

---

## PAGE 9: Personal Dashboard (`/dashboard`)

Only accessible to logged-in user for their own data.

**Layout: 3-column grid at top, then full-width sections below.**

Top stats row:
- Total Points (large number + tier progress bar to next tier)
- Rank position on leaderboard
- Ideas posted
- Projects completed

Middle: Two cards side-by-side:
- "My Ideas" — list of ideas I posted with status badges
- "My Collaborations" — ideas I'm collaborating on with status

Bottom:
- Point history: table of recent `point_transactions` with action, points, timestamp
- Notifications panel: list of all notifications, mark all read button
- If mentor open: "Mentorship Requests" section

---

## PAGE 10: Leaderboard (`/leaderboard`)

Three tabs:
1. **Builder Board** — ranked by GitHub commits linked to projects + points
2. **Visionary Board** — ranked by ideas posted + upvotes received
3. **Community Board** — ranked by rescues accepted + postmortems written

Filter: All Branches | CSE | ECE | MECH | CIVIL | EEE | IT
Toggle: Monthly | All-Time

Each board: numbered ranking table.
Top 3: gold/silver/bronze styling with crown/medal icon.
Remaining: standard row.

Row anatomy:
```
#1  [Avatar] Harshith G.  CSE 1st  ⚡ 840 pts  LEGEND 🏆
```

Your own row is highlighted with indigo background if you're in view.

---

## PAGE 11: Forge Events (`/forge`)

**Header:** "Forge" wordmark + `"Built by builders. For builders."`

**Student Demand system at top:**
- Row of skill tags: `+ I want to learn this` for each
- Banner showing demands close to 15: `"12/15 students want Flutter — IIC will be notified soon!"`

**Events grid:** cards similar to idea cards but with date + seats prominently shown.

**Event Card:**
```
┌──────────────────────────────────────┐
│ [WORKSHOP] [Domain Badge]            │
│                                      │
│ Building REST APIs with PHP          │
│ Conducted by Ravi K. · ARCHITECT     │
│                                      │
│ Apr 22, 2025 · 3:00 PM              │
│ ██████░░░░ 14/20 seats              │
│                                      │
│ Prerequisites: Basic HTML, PHP       │
│                            [Register]│
└──────────────────────────────────────┘
```

If registered: shows `Registered ✓ · View Ticket`

**Tier 4/5 users:** see `+ Create Event` button.

---

## PAGE 12: Event Detail (`/forge/[id]`)

Full event page:
- Title, conductor profile card, date/time, format, domain
- Seat status with visual: `██████████░░░░░░░░░░ 14/20 seats taken`
- Prerequisites section
- Description (what will be taught)
- Register button → opens modal with confirmation
- On register: generates unique `ticketCode` (CUID), shows QR-code-style ticket modal
- Post-event (status=COMPLETED): Resources URL link + archive note

---

## PAGE 13: The Archive (`/archive`)

**Header:** "The Archive" + `"Every abandoned idea. Every lesson. Permanent."`

Postmortem cards in masonry-style grid:
```
┌──────────────────────────────────────┐
│ ABANDONED · AI & ML                  │
│                                      │
│ Campus Attendance AI                 │
│ by Sai K. · CSE 2nd Year            │
│                                      │
│ "We ran out of time before exams.    │
│  The dataset collection alone took   │
│  three weeks..."                     │
│                                      │
│ Lessons: [Data collection] [Scope]   │
│ Jan 2025 · Read Postmortem →         │
└──────────────────────────────────────┘
```

Postmortem detail modal (on click):
- What was the idea
- Why it stopped
- Lessons learned
- What the next person needs to know
- `Apply to Inherit` button → creates INHERITOR collaboration request

---

## PAGE 14: Proof Wall (`/wall`)

**Header:** "Proof Wall" + `"Every project that made it."`

Gallery of completed project cards. Each card:
```
┌──────────────────────────────────────┐
│ ✅ COMPLETED · IIC VERIFIED ⭐        │
│                                      │
│ Smart Attendance System              │
│                                      │
│ Ravi K. (Visionary) + Priya M. (Builder) │
│                                      │
│ Duration: 6 weeks                    │
│ Domain: AI & ML                      │
│ GitHub: [view repo →]                │
│                                      │
│ Completed March 2025                 │
└──────────────────────────────────────┘
```

Filter by domain, branch, year.

---

## PAGE 15: IIC Admin Dashboard (`/admin`)

**Access-gated: only users with `isIicAdmin: true`**

**Skill Gap Map (top section):**
- Bar chart (use Recharts): 
  - X-axis: skill tags
  - Two bars per skill: `Demand` (how many open ideas need it) vs `Supply` (how many builders have it on GitHub)
  - Gap score shown on bar
  - Color: red bar if gap > 5, yellow if gap 1-5, green if supply > demand
- Below chart: top 5 skills with biggest shortages as cards

**Platform Health stats:**
- Total ideas (this month vs last month)
- Active collaborations
- Completed projects
- Registered builders
- Ideas by domain (pie chart or donut — Recharts)

**Idea management table:**
- All ideas with toggle: `Feature / Unfeature` → updates `isIicFeatured`
- Status filter
- Can mark any idea as `IIC Featured`

**Pending SOS Alerts:**
- All ideas with `isSosActive: true` — for IIC awareness

---

# POINTS ENGINE — IMPLEMENT IN `lib/points.ts`

```typescript
export const POINTS_MAP = {
  post_idea: 10,
  apply_to_idea: 5,
  get_accepted: 25,
  complete_project: 100,
  sos_rescue_accepted: 30,
  write_postmortem: 20,
  commit_linked: 5,
  host_event: 50,
  attend_event: 10,
  quality_post: 15,
} as const

export const TIER_THRESHOLDS = {
  1: 0,    // INITIATE
  2: 50,   // CONTRIBUTOR
  3: 200,  // BUILDER
  4: 500,  // ARCHITECT
  5: 1000, // LEGEND
}

export const TIER_NAMES = {
  1: 'INITIATE',
  2: 'CONTRIBUTOR', 
  3: 'BUILDER',
  4: 'ARCHITECT',
  5: 'LEGEND',
}

export async function awardPoints(
  userId: string,
  action: keyof typeof POINTS_MAP,
  referenceId?: string,
  referenceType?: string
) {
  const points = POINTS_MAP[action]
  
  await prisma.$transaction([
    // Log transaction
    prisma.pointTransaction.create({
      data: { userId, action, points, referenceId, referenceType }
    }),
    // Update user total
    prisma.user.update({
      where: { id: userId },
      data: { totalPoints: { increment: points } }
    })
  ])
  
  // Recalculate tier
  const user = await prisma.user.findUnique({ where: { id: userId } })
  const newTier = calculateTier(user!.totalPoints)
  
  if (newTier !== user!.tier) {
    await prisma.user.update({
      where: { id: userId },
      data: { tier: newTier }
    })
    // Send tier upgrade notification
    await createNotification(userId, 'tier_upgrade', 
      `🎉 You've reached ${TIER_NAMES[newTier]}! Keep building.`)
  }
}
```

---

# GITHUB API INTEGRATION — IMPLEMENT IN `lib/github.ts`

```typescript
export async function fetchGithubData(username: string) {
  const headers = { 
    'Accept': 'application/vnd.github.v3+json',
    'User-Agent': 'IdeaSync-Lendi'
  }
  
  const [userRes, reposRes] = await Promise.all([
    fetch(`https://api.github.com/users/${username}`, { headers }),
    fetch(`https://api.github.com/users/${username}/repos?sort=stars&per_page=5`, { headers })
  ])
  
  if (!userRes.ok) throw new Error('GitHub user not found')
  
  const user = await userRes.json()
  const repos = await reposRes.json()
  
  // Aggregate languages
  const languageCounts: Record<string, number> = {}
  for (const repo of repos) {
    if (repo.language) {
      languageCounts[repo.language] = (languageCounts[repo.language] || 0) + (repo.size || 1)
    }
  }
  
  const totalSize = Object.values(languageCounts).reduce((a, b) => a + b, 0)
  const languages = Object.entries(languageCounts)
    .map(([lang, size]) => ({ lang, percentage: Math.round((size / totalSize) * 100) }))
    .sort((a, b) => b.percentage - a.percentage)
    .slice(0, 5)
  
  return {
    username,
    publicRepos: user.public_repos,
    followers: user.followers,
    topRepos: repos.slice(0, 3).map((r: any) => ({
      name: r.name,
      stars: r.stargazers_count,
      language: r.language,
      description: r.description,
      url: r.html_url,
    })),
    languages,
    syncedAt: new Date().toISOString()
  }
}
```

---

# NOTIFICATION SYSTEM — IMPLEMENT IN `lib/notify.ts`

Create notifications for these triggers (all implemented):
- Someone applies to your idea
- Your application was accepted
- Your application was rejected
- An idea you upvoted triggers SOS
- A new event in your domain interest
- Your idea was featured by IIC
- Tier upgrade
- Someone forked your idea
- A project you're part of was completed

Notifications show in topbar bell icon with unread count. Dropdown panel with list. Mark all read.

---

# SEED DATA — `prisma/seed.ts`

Create realistic seed data:
- 15 users across all branches and years, with varied points and tiers
- 20 ideas across all domains (some open, some in progress, 3 completed, 2 abandoned)
- 8 collaborations (mix of pending/accepted)
- 2 completed project entries on Proof Wall
- 2 postmortems in The Archive
- 4 events (2 upcoming, 1 completed, 1 bootcamp)
- Point transactions for all users
- 1 IIC admin user (email: admin@lendi.org, password: admin123)
- 1 regular user you can test with (email: harshith@lendi.org, password: test123, roll: 24B21A0501)

---

# AUTHENTICATION — `lib/auth.ts`

NextAuth credentials provider. Session contains: `id`, `name`, `email`, `tier`, `totalPoints`, `isIicAdmin`.

Email must end in `@lendi.org`. Hash passwords with `bcryptjs`.

Protect all `/feed`, `/dashboard`, `/ideas/post`, `/profile/edit`, `/admin` routes with middleware.

---

# CRITICAL QUALITY REQUIREMENTS

1. **Zero placeholder content** — no "lorem ipsum", no "coming soon", no empty states without real empty state components
2. **All API routes fully implemented** — no `// TODO` comments, no returning empty arrays
3. **All forms have validation** — client side (Zod + react-hook-form) AND server side
4. **All error states handled** — network errors, 404s, empty results, loading states
5. **Loading states** — every data-fetching component has a `Skeleton` component while loading
6. **Mobile responsive** — every page works on 375px screens
7. **Accessibility** — all interactive elements have proper ARIA labels, keyboard navigable
8. **TypeScript strict** — no `any` types, all props properly typed
9. **Toast notifications** — all user actions (apply, post, accept, etc.) show a toast confirmation
10. **Real-time-feeling** — after any mutation, the UI updates immediately (optimistic updates)

---

# ENV VARIABLES REQUIRED

```
DATABASE_URL=postgresql://...
NEXTAUTH_SECRET=your-secret-here
NEXTAUTH_URL=http://localhost:3000
GITHUB_PAT=optional-for-higher-rate-limits
```

---

# PACKAGE.JSON DEPENDENCIES

```json
{
  "dependencies": {
    "next": "^14.2.0",
    "react": "^18.3.0",
    "react-dom": "^18.3.0",
    "@prisma/client": "^5.14.0",
    "next-auth": "^5.0.0-beta",
    "bcryptjs": "^2.4.3",
    "zustand": "^4.5.0",
    "framer-motion": "^11.2.0",
    "lucide-react": "^0.378.0",
    "recharts": "^2.12.0",
    "zod": "^3.23.0",
    "react-hook-form": "^7.51.0",
    "@hookform/resolvers": "^3.3.0",
    "react-hot-toast": "^2.4.1",
    "clsx": "^2.1.0",
    "tailwind-merge": "^2.3.0",
    "geist": "^1.3.0"
  },
  "devDependencies": {
    "prisma": "^5.14.0",
    "typescript": "^5.4.0",
    "@types/react": "^18.3.0",
    "@types/node": "^20.12.0",
    "@types/bcryptjs": "^2.4.6",
    "tailwindcss": "^3.4.0",
    "autoprefixer": "^10.4.0",
    "postcss": "^8.4.0"
  }
}
```

---

# HOW TO BUILD — YOUR SEQUENCE

Build in this exact order:

1. `prisma/schema.prisma` + `prisma/seed.ts`
2. `lib/auth.ts` + `lib/prisma.ts` + `lib/points.ts` + `lib/notify.ts` + `lib/github.ts`
3. All API routes (start with `/api/users`, then `/api/ideas`, then rest)
4. Global design system: `tailwind.config.ts` + root `layout.tsx`
5. UI primitives: `Button`, `Input`, `Modal`, `Badge`, `Skeleton`, `Toast`
6. App Shell: `Sidebar`, `Topbar`, `AppShell`
7. Pages in priority order: Landing → Register → Onboarding → Login → Feed → Idea Detail → Profile → Dashboard → Leaderboard → Forge → Archive → Proof Wall → Admin
8. Seed data + test everything

---

# FINAL INSTRUCTION

Build this as a professional engineering team would. Every file complete. Every feature working. No "this is left as an exercise." No half-built pages. No commented-out sections. 

This will be presented as a live demo at Lendi Institute on April 22. It must run without crashing, look better than anything else shown that day, and demonstrate every feature in the PRD working end-to-end.

Harshith is a first-year student who built this. The platform must make that look impossible to believe.

**Start by generating the complete `prisma/schema.prisma`, then `prisma/seed.ts`, then the full API layer, then the frontend. Do not skip any file.**
