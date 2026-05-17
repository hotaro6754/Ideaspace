import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Idea } from "@/models/Idea";
import { Proof } from "@/models/Proof";
import { auth } from "@/lib/auth";
import { z } from "zod";
import { logger } from "@/lib/logger";

const proofSchema = z.object({
  title: z.string().min(5).max(100),
  url: z.string().url(),
  type: z.enum(["link", "image", "github", "document"]),
  description: z.string().max(300).optional(),
});

export async function GET(req: Request, { params }: { params: Promise<{ id: string }> }) {
  try {
    await connectDB();
    const { id: ideaId } = await params;

    const proofs = await Proof.find({ ideaId })
      .sort({ createdAt: -1 })
      .populate("submitter", "name username avatarUrl")
      .lean();

    return NextResponse.json({ data: proofs });
  } catch (error) {
    logger.error("Failed to fetch proofs", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch proofs" }, { status: 500 });
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

    if (!idea) {
      return NextResponse.json({ error: "Idea not found" }, { status: 404 });
    }

    const isCollaborator = idea.collaborators.some((c: any) => c.toString() === session.user.id) || idea.owner.toString() === session.user.id;
    
    if (!isCollaborator && (session.user as any).role !== "admin") {
      return NextResponse.json({ error: "Only team members can submit proof" }, { status: 403 });
    }

    const body = await req.json();
    const data = proofSchema.parse(body);

    const proof = await Proof.create({
      ...data,
      ideaId,
      submitter: session.user.id,
      status: "pending",
    });

    const populatedProof = await Proof.findById(proof._id).populate("submitter", "name username avatarUrl").lean();

    logger.info("Proof submitted", { proofId: proof._id.toString(), ideaId });

    return NextResponse.json({ data: populatedProof }, { status: 201 });
  } catch (error) {
    if (error instanceof z.ZodError) {
      return NextResponse.json({ error: "Invalid input", details: error.issues }, { status: 400 });
    }
    logger.error("Failed to submit proof", { error: String(error) });
    return NextResponse.json({ error: "Failed to submit proof" }, { status: 500 });
  }
}
