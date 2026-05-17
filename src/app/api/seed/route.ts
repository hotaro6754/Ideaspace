import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { User } from "@/models/User";
import { Idea } from "@/models/Idea";
import { Workshop } from "@/models/Workshop";
import { Bounty } from "@/models/Bounty";
import { ProofEntry } from "@/models/ProofEntry";
import { Archive } from "@/models/Archive";
import { Notification } from "@/models/Notification";
import { Leaderboard } from "@/models/Leaderboard";
import bcrypt from "bcryptjs";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    await connectDB();

    // Check if seed data already exists
    const userCount = await User.countDocuments();
    if (userCount > 0) {
      return NextResponse.json({ data: { message: "Database already seeded", users: userCount } });
    }

    const password = await bcrypt.hash("password123", 12);

    // Create users
    const users = await User.insertMany([
      {
        name: "Harshith Kumar",
        email: "harshith@lendi.org",
        username: "harshith",
        passwordHash: password,
        role: "admin",
        bio: "Building the future of campus innovation. Full-stack developer and AI enthusiast.",
        skills: ["TypeScript", "React", "Node.js", "Python", "MongoDB"],
        interests: ["AI", "Web3", "Systems Design"],
        primaryTrack: "ai-intelligent-systems",
        secondaryTracks: ["software-saas-platform"],
        points: 2850,
        rankTier: "Platinum",
        isOnboarded: true,
        isVerified: true,
        branch: "CSE",
        year: 3,
      },
      {
        name: "Priya Reddy",
        email: "priya@lendi.org",
        username: "priya",
        passwordHash: password,
        role: "student",
        bio: "UI/UX designer turned developer. Love creating beautiful, functional interfaces.",
        skills: ["Figma", "React", "CSS", "Framer Motion"],
        interests: ["Design Systems", "Motion Design"],
        primaryTrack: "ux-product-design",
        points: 1200,
        rankTier: "Gold",
        isOnboarded: true,
        isVerified: true,
        branch: "CSE",
        year: 2,
      },
      {
        name: "Arjun Patel",
        email: "arjun@lendi.org",
        username: "arjun",
        passwordHash: password,
        role: "student",
        bio: "ML researcher and data scientist. Kaggle top 5% globally.",
        skills: ["Python", "TensorFlow", "PyTorch", "SQL", "R"],
        interests: ["NLP", "Computer Vision", "MLOps"],
        primaryTrack: "data-analytics",
        points: 680,
        rankTier: "Gold",
        isOnboarded: true,
        isVerified: true,
        branch: "AI&ML",
        year: 3,
      },
      {
        name: "Dr. Suresh Babu",
        email: "suresh@lendi.org",
        username: "drsuresh",
        passwordHash: password,
        role: "faculty",
        bio: "Associate Professor, Dept of CSE. Research in distributed systems and cloud computing.",
        skills: ["Cloud Architecture", "Distributed Systems", "Research Methodology"],
        interests: ["Cloud Computing", "Edge Computing"],
        primaryTrack: "cybersecurity-cloud",
        points: 420,
        rankTier: "Silver",
        isOnboarded: true,
        isVerified: true,
      },
      {
        name: "Meera Sharma",
        email: "meera@lendi.org",
        username: "meera",
        passwordHash: password,
        role: "student",
        bio: "Mobile app developer specializing in Flutter and React Native.",
        skills: ["Flutter", "Dart", "React Native", "Firebase"],
        interests: ["Mobile UX", "Cross-platform", "App Performance"],
        primaryTrack: "mobile-consumer-apps",
        points: 350,
        rankTier: "Silver",
        isOnboarded: true,
        isVerified: true,
        branch: "CSE",
        year: 4,
      },
      {
        name: "Rahul Verma",
        email: "rahul@lendi.org",
        username: "rahul",
        passwordHash: password,
        role: "student",
        bio: "IoT enthusiast and embedded systems developer. Building smart campus solutions.",
        skills: ["Arduino", "Raspberry Pi", "C++", "MQTT", "Node-RED"],
        interests: ["Smart Cities", "Sensor Networks"],
        primaryTrack: "embedded-iot-robotics",
        points: 150,
        rankTier: "Bronze",
        isOnboarded: true,
        isVerified: true,
        branch: "ECE",
        year: 3,
      },
    ]);

    // Create ideas
    const ideas = await Idea.insertMany([
      {
        title: "Smart Campus Navigation with AR Wayfinding",
        slug: "smart-campus-navigation-ar",
        problem: "Students and visitors struggle to find classrooms, labs, and facilities across our large campus. Current signage is insufficient and campus maps are outdated.",
        solution: "Build an AR-powered mobile app that provides real-time indoor navigation using BLE beacons and smartphone camera. The app overlays directional arrows on the camera view.",
        track: "mobile-consumer-apps",
        tags: ["AR", "Mobile", "Campus", "Navigation"],
        status: "building",
        healthScore: 85,
        owner: users[0]._id,
        collaborators: [users[1]._id, users[5]._id],
        skillsNeeded: ["Flutter", "ARCore", "BLE", "UI/UX"],
        upvotes: 47,
        views: 234,
        isFeatured: true,
        proofCount: 3,
        githubUrl: "https://github.com/harshith/campus-nav",
      },
      {
        title: "AI-Powered Code Review Bot for Student Projects",
        slug: "ai-code-review-bot",
        problem: "Faculty lack time to thoroughly review student code submissions. Students don't get enough feedback on code quality, patterns, and best practices.",
        solution: "Create an AI assistant that automatically reviews pull requests using LLM analysis, providing inline suggestions for code quality, security vulnerabilities, and design patterns.",
        track: "ai-intelligent-systems",
        tags: ["AI", "Code Review", "LLM", "DevOps"],
        status: "discovery",
        healthScore: 72,
        owner: users[2]._id,
        collaborators: [users[0]._id],
        skillsNeeded: ["Python", "OpenAI API", "GitHub API", "NLP"],
        upvotes: 38,
        views: 189,
        isFeatured: true,
        proofCount: 1,
      },
      {
        title: "Campus Food Court Digital Ordering System",
        slug: "campus-food-ordering",
        problem: "Long queues at the campus food court waste students' break time. There's no way to pre-order or see real-time menu availability.",
        solution: "A web app where students can browse menus, place orders, and pay digitally. Kitchen staff see orders in real-time with estimated prep times. Students get notified when food is ready.",
        track: "software-saas-platform",
        tags: ["SaaS", "Food Tech", "Real-time", "Payments"],
        status: "shipped",
        healthScore: 95,
        owner: users[1]._id,
        collaborators: [users[4]._id],
        skillsNeeded: ["React", "Node.js", "Stripe", "WebSockets"],
        upvotes: 62,
        views: 445,
        isFeatured: true,
        proofCount: 5,
        githubUrl: "https://github.com/priya/food-court",
        demoUrl: "https://lendi-food.vercel.app",
      },
      {
        title: "Smart Attendance System using Face Recognition",
        slug: "smart-attendance-face-recognition",
        problem: "Manual attendance takes 5-10 minutes per class and is prone to proxy attendance. Faculty spend significant time on attendance management.",
        solution: "Deploy cameras in classrooms that use face recognition to automatically mark attendance. Faculty get a dashboard to review and override. Students can see their attendance history.",
        track: "ai-intelligent-systems",
        tags: ["Computer Vision", "Face Recognition", "IoT", "Automation"],
        status: "building",
        healthScore: 78,
        owner: users[5]._id,
        collaborators: [users[2]._id],
        skillsNeeded: ["Python", "OpenCV", "TensorFlow", "React"],
        upvotes: 29,
        views: 156,
        proofCount: 2,
      },
      {
        title: "Decentralized Academic Credential Verification",
        slug: "decentralized-credential-verification",
        problem: "Employers and grad schools spend weeks verifying student credentials. The current process involves manual phone calls and emails to the registrar's office.",
        solution: "Issue academic credentials as verifiable blockchain certificates. Employers can instantly verify degrees, grades, and project completions through a simple web portal.",
        track: "cybersecurity-cloud",
        tags: ["Blockchain", "Credentials", "Security", "Web3"],
        status: "discovery",
        healthScore: 60,
        owner: users[4]._id,
        skillsNeeded: ["Solidity", "React", "IPFS", "Cryptography"],
        upvotes: 18,
        views: 98,
        proofCount: 0,
      },
    ]);

    // Create proof entries
    await ProofEntry.insertMany([
      {
        idea: ideas[0]._id,
        submittedBy: users[0]._id,
        type: "github_commit",
        title: "Initial AR navigation prototype",
        description: "First working version with BLE beacon detection and basic AR overlay.",
        evidenceUrl: "https://github.com/harshith/campus-nav/commit/abc123",
        isVerified: true,
        verifiedBy: users[3]._id,
        verifiedAt: new Date(),
        pointsAwarded: 30,
      },
      {
        idea: ideas[0]._id,
        submittedBy: users[1]._id,
        type: "demo_link",
        title: "UI/UX prototype walkthrough",
        description: "Figma to code: all navigation screens with smooth transitions.",
        evidenceUrl: "https://www.loom.com/share/demo123",
        isVerified: true,
        verifiedBy: users[3]._id,
        verifiedAt: new Date(),
        pointsAwarded: 30,
      },
      {
        idea: ideas[2]._id,
        submittedBy: users[1]._id,
        type: "demo_link",
        title: "Live demo of food ordering flow",
        description: "Full ordering flow from menu browse to payment to pickup notification.",
        evidenceUrl: "https://lendi-food.vercel.app",
        isVerified: true,
        verifiedBy: users[3]._id,
        verifiedAt: new Date(),
        pointsAwarded: 30,
      },
    ]);

    // Create workshops
    await Workshop.insertMany([
      {
        title: "Building Production-Ready APIs with Node.js",
        description: "Learn how to build scalable, secure REST APIs using Node.js, Express, and MongoDB. We'll cover authentication, rate limiting, error handling, and deployment.",
        host: users[3]._id,
        scheduledAt: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000),
        durationMins: 120,
        location: "Seminar Hall A",
        isOnline: false,
        maxAttendees: 60,
        rsvpList: [users[0]._id, users[1]._id, users[2]._id],
        demandSignals: 12,
        track: "software-saas-platform",
        status: "upcoming",
      },
      {
        title: "Introduction to Computer Vision with OpenCV",
        description: "Hands-on workshop covering image processing, object detection, and face recognition using OpenCV and Python. Bring your laptop!",
        host: users[2]._id,
        scheduledAt: new Date(Date.now() + 14 * 24 * 60 * 60 * 1000),
        durationMins: 90,
        location: "Online",
        isOnline: true,
        meetLink: "https://meet.google.com/xyz",
        maxAttendees: 100,
        rsvpList: [users[5]._id, users[4]._id],
        demandSignals: 8,
        track: "ai-intelligent-systems",
        status: "upcoming",
      },
    ]);

    // Create bounties
    await Bounty.insertMany([
      {
        title: "Design a campus event poster generator",
        description: "Build a web tool that lets student clubs create professional event posters using customizable templates. Should support export to PNG/PDF.",
        postedBy: users[3]._id,
        rewardPoints: 100,
        kind: "design",
        participationMode: "solo",
        deadlineAt: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000),
        status: "open",
        track: "ux-product-design",
      },
      {
        title: "Write a technical blog about ML model deployment",
        description: "Write a comprehensive guide on deploying ML models to production. Cover Docker, FastAPI, model serving, monitoring, and A/B testing.",
        postedBy: users[3]._id,
        rewardPoints: 75,
        kind: "research",
        participationMode: "solo",
        deadlineAt: new Date(Date.now() + 21 * 24 * 60 * 60 * 1000),
        status: "open",
        track: "ai-intelligent-systems",
      },
    ]);

    // Create archive entries
    await Archive.insertMany([
      {
        idea: ideas[2]._id,
        title: "Campus Food Court System - Shipped!",
        outcome: "shipped",
        whatWorked: "Starting with a minimal MVP (just menu + ordering) and getting real feedback from canteen staff before adding payments. The WebSocket-based order tracking was a huge hit.",
        whatFailed: "Initially tried to support multiple payment gateways simultaneously. Should have started with just one (UPI) and expanded later.",
        lessons: "Ship the core loop first. Get real users ASAP. The hardest part isn't the code — it's getting buy-in from non-technical stakeholders (canteen management).",
        author: users[1]._id,
        isPublic: true,
      },
    ]);

    // Create notifications
    await Notification.insertMany([
      {
        recipient: users[0]._id,
        type: "idea_upvoted",
        title: "Your idea got upvoted!",
        body: "Smart Campus Navigation received 5 new upvotes today.",
        linkUrl: "/ideas/" + ideas[0]._id,
        isRead: false,
      },
      {
        recipient: users[0]._id,
        type: "collaborator_request",
        title: "New collaboration request",
        body: "Meera Sharma wants to join Smart Campus Navigation.",
        linkUrl: "/ideas/" + ideas[0]._id,
        isRead: false,
      },
      {
        recipient: users[1]._id,
        type: "proof_verified",
        title: "Proof verified!",
        body: "Your demo link proof was verified by Dr. Suresh. +30 points!",
        linkUrl: "/wall",
        isRead: true,
      },
    ]);

    // Create leaderboard entries
    await Leaderboard.insertMany([
      { user: users[0]._id, points: 2850, rank: 1, rankTier: "Platinum", period: "alltime", ideasShipped: 2, proofsSubmitted: 8, workshopsAttended: 5 },
      { user: users[1]._id, points: 1200, rank: 2, rankTier: "Gold", period: "alltime", ideasShipped: 1, proofsSubmitted: 5, workshopsAttended: 3 },
      { user: users[2]._id, points: 680, rank: 3, rankTier: "Gold", period: "alltime", ideasShipped: 0, proofsSubmitted: 3, workshopsAttended: 4 },
      { user: users[3]._id, points: 420, rank: 4, rankTier: "Silver", period: "alltime", ideasShipped: 0, proofsSubmitted: 0, workshopsAttended: 2 },
      { user: users[4]._id, points: 350, rank: 5, rankTier: "Silver", period: "alltime", ideasShipped: 0, proofsSubmitted: 2, workshopsAttended: 1 },
      { user: users[5]._id, points: 150, rank: 6, rankTier: "Bronze", period: "alltime", ideasShipped: 0, proofsSubmitted: 1, workshopsAttended: 2 },
    ]);

    logger.info("Database seeded successfully");

    return NextResponse.json({
      data: {
        message: "Database seeded successfully",
        users: users.length,
        ideas: ideas.length,
      },
    });
  } catch (error) {
    logger.error("Seed failed", { error: String(error) });
    return NextResponse.json({ error: "Seed failed: " + String(error) }, { status: 500 });
  }
}
