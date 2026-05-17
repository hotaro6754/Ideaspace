import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { ProofEntry } from "@/models/ProofEntry";
import { ReviewQueue } from "@/models/ReviewQueue";
import { Idea } from "@/models/Idea";
import { auth } from "@/lib/auth";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    await connectDB();

    const recentProofs = await ProofEntry.find({ isVerified: true })
      .sort({ createdAt: -1 })
      .limit(8)
      .populate("submittedBy", "name username avatarUrl")
      .populate("idea", "title slug")
      .lean();

    const rail = recentProofs.map((proof: any) => ({
      kind: "idea" as const,
      title: proof.idea?.title ?? "Proof",
      type: proof.type === "github_commit" ? "Code Shipped" : proof.type === "demo_link" ? "Demo Live" : "Evidence Added",
      detail: proof.title,
      owner: proof.submittedBy?.name ?? "",
      time: formatTimeAgo(new Date(proof.createdAt)),
    }));

    return NextResponse.json({ rail, evidence: rail });
  } catch (error) {
    logger.error("Failed to fetch proof rail", { error: String(error) });
    return NextResponse.json({ rail: [], evidence: [] });
  }
}

export async function POST(req: Request) {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    await connectDB();
    const body = await req.json();
    const { ideaId, type, title, description, evidenceUrl } = body;

    if (!ideaId || !type || !title || !evidenceUrl) {
      return NextResponse.json({ error: "Missing required fields" }, { status: 400 });
    }

    // Create Proof Entry
    const proof = await ProofEntry.create({
      idea: ideaId,
      submittedBy: session.user.id,
      type,
      title,
      description,
      evidenceUrl,
      isVerified: false,
    });

    // Add to Review Queue
    await ReviewQueue.create({
      targetId: proof._id,
      targetType: "proof",
      reviewType: "milestone_verification",
      status: "pending",
      submittedBy: session.user.id,
    });

    // Update proof count on idea
    await Idea.findByIdAndUpdate(ideaId, { $inc: { proofCount: 1 } });

    return NextResponse.json({ data: proof });
  } catch (error) {
    logger.error("Failed to submit proof", { error: String(error) });
    return NextResponse.json({ error: "Internal Server Error" }, { status: 500 });
  }
}

function formatTimeAgo(date: Date): string {
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();
  const diffMins = Math.floor(diffMs / 60000);
  const diffHours = Math.floor(diffMs / 3600000);
  const diffDays = Math.floor(diffMs / 86400000);

  if (diffMins < 60) return `${diffMins}m ago`;
  if (diffHours < 24) return `${diffHours}h ago`;
  return `${diffDays}d ago`;
}