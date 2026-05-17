import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Idea } from "@/models/Idea";
import { ProofEntry } from "@/models/ProofEntry";
import { auth } from "@/lib/auth";
import { logger } from "@/lib/logger";

export async function GET(
  _req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    await connectDB();
    const { id } = await params;
    const idea = await Idea.findById(id)
      .populate("owner", "name username rankTier points avatarUrl bio skills")
      .populate("collaborators", "name username rankTier avatarUrl")
      .lean();

    if (!idea) {
      return NextResponse.json({ error: "Idea not found" }, { status: 404 });
    }

    // Fetch proof entries for the ProofRail
    const proofs = await ProofEntry.find({ idea: id })
      .populate("submittedBy", "name username avatarUrl rankTier")
      .sort({ createdAt: -1 })
      .lean();

    // Increment views
    await Idea.updateOne({ _id: id }, { $inc: { views: 1 } });

    return NextResponse.json({ data: { ...idea, proofs } });
  } catch (error) {
    logger.error("Failed to fetch idea", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch idea" }, { status: 500 });
  }
}

export async function PATCH(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    await connectDB();
    const { id } = await params;
    const idea = await Idea.findById(id);

    if (!idea) {
      return NextResponse.json({ error: "Idea not found" }, { status: 404 });
    }

    if (idea.owner.toString() !== session.user.id) {
      return NextResponse.json({ error: "Forbidden" }, { status: 403 });
    }

    const body = await req.json();
    const updated = await Idea.findByIdAndUpdate(id, body, { new: true });

    return NextResponse.json({ data: updated });
  } catch (error) {
    logger.error("Failed to update idea", { error: String(error) });
    return NextResponse.json({ error: "Failed to update idea" }, { status: 500 });
  }
}

export async function DELETE(
  _req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    await connectDB();
    const { id } = await params;
    const idea = await Idea.findById(id);

    if (!idea) {
      return NextResponse.json({ error: "Idea not found" }, { status: 404 });
    }

    const user = session.user as any;
    if (idea.owner.toString() !== session.user.id && user.role !== "admin") {
      return NextResponse.json({ error: "Forbidden" }, { status: 403 });
    }

    await Idea.findByIdAndDelete(id);

    return NextResponse.json({ data: { deleted: true } });
  } catch (error) {
    logger.error("Failed to delete idea", { error: String(error) });
    return NextResponse.json({ error: "Failed to delete idea" }, { status: 500 });
  }
}
