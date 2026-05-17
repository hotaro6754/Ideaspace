import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Idea } from "@/models/Idea";
import { JoinRequest } from "@/models/JoinRequest";
import { auth } from "@/lib/auth";
import { z } from "zod";
import { logger } from "@/lib/logger";

const joinSchema = z.object({
  role: z.string().min(2).max(50),
  message: z.string().max(300).optional(),
});

export async function GET(req: Request, { params }: { params: Promise<{ id: string }> }) {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    await connectDB();
    const { id: ideaId } = await params;
    const idea = await Idea.findById(ideaId);

    if (!idea) return NextResponse.json({ error: "Idea not found" }, { status: 404 });

    if (idea.owner.toString() !== session.user.id && (session.user as any).role !== "admin") {
      return NextResponse.json({ error: "Forbidden" }, { status: 403 });
    }

    const requests = await JoinRequest.find({ ideaId, status: "pending" })
      .sort({ createdAt: -1 })
      .populate("userId", "name username avatarUrl rankTier")
      .lean();

    return NextResponse.json({ data: requests });
  } catch (error) {
    logger.error("Failed to fetch join requests", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch join requests" }, { status: 500 });
  }
}

export async function POST(req: Request, { params }: { params: Promise<{ id: string }> }) {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    await connectDB();
    const { id: ideaId } = await params;
    const idea = await Idea.findById(ideaId);

    if (!idea) return NextResponse.json({ error: "Idea not found" }, { status: 404 });

    if (idea.owner.toString() === session.user.id) {
      return NextResponse.json({ error: "Cannot join your own idea" }, { status: 400 });
    }
    if (idea.collaborators.some(c => c.toString() === session.user.id)) {
      return NextResponse.json({ error: "Already a collaborator" }, { status: 400 });
    }

    const existing = await JoinRequest.findOne({ ideaId, userId: session.user.id, status: "pending" });
    if (existing) {
      return NextResponse.json({ error: "You already have a pending request" }, { status: 400 });
    }

    const body = await req.json();
    const data = joinSchema.parse(body);

    const request = await JoinRequest.create({
      ideaId,
      userId: session.user.id,
      role: data.role,
      message: data.message,
      status: "pending",
    });

    return NextResponse.json({ data: request }, { status: 201 });
  } catch (error) {
    if (error instanceof z.ZodError) {
      return NextResponse.json({ error: "Invalid input", details: error.issues }, { status: 400 });
    }
    logger.error("Failed to create join request", { error: String(error) });
    return NextResponse.json({ error: "Failed to submit request" }, { status: 500 });
  }
}

export async function PATCH(req: Request, { params }: { params: Promise<{ id: string }> }) {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    await connectDB();
    const { id: ideaId } = await params;
    const idea = await Idea.findById(ideaId);

    if (!idea) return NextResponse.json({ error: "Idea not found" }, { status: 404 });

    if (idea.owner.toString() !== session.user.id && (session.user as any).role !== "admin") {
      return NextResponse.json({ error: "Only the idea owner can manage requests" }, { status: 403 });
    }

    const { requestId, status } = await req.json();
    if (!["approved", "rejected"].includes(status)) {
      return NextResponse.json({ error: "Invalid status" }, { status: 400 });
    }

    const joinRequest = await JoinRequest.findById(requestId);
    if (!joinRequest || joinRequest.ideaId.toString() !== ideaId) {
      return NextResponse.json({ error: "Request not found" }, { status: 404 });
    }

    if (joinRequest.status !== "pending") {
      return NextResponse.json({ error: "Request already processed" }, { status: 400 });
    }

    joinRequest.status = status;
    await joinRequest.save();

    if (status === "approved") {
      await Idea.findByIdAndUpdate(ideaId, {
        $addToSet: { collaborators: joinRequest.userId }
      });
    }

    return NextResponse.json({ data: joinRequest });
  } catch (error) {
    logger.error("Failed to process join request", { error: String(error) });
    return NextResponse.json({ error: "Failed to process request" }, { status: 500 });
  }
}
