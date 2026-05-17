import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Idea } from "@/models/Idea";
import { Vote } from "@/models/Vote";
import { auth } from "@/lib/auth";
import { pusherServer } from "@/lib/pusher";
import { logger } from "@/lib/logger";

export async function POST(
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

    const existingVote = await Vote.findOne({
      targetId: id,
      targetType: "idea",
      voter: session.user.id,
    });

    let newCount = 0;

    if (existingVote) {
      await Vote.deleteOne({ _id: existingVote._id });
      const updatedIdea = await Idea.findByIdAndUpdate(id, { $inc: { upvotes: -1 } }, { new: true });
      newCount = updatedIdea?.upvotes || 0;
      
      // Trigger Real-Time update
      await pusherServer.trigger("ideas-channel", "upvote-update", {
        ideaId: id,
        upvotes: newCount,
      });

      return NextResponse.json({ data: { voted: false, upvotes: newCount } });
    }

    await Vote.create({
      targetId: id,
      targetType: "idea",
      voter: session.user.id,
      value: 1,
    });

    const updatedIdea = await Idea.findByIdAndUpdate(id, { $inc: { upvotes: 1 } }, { new: true });
    newCount = updatedIdea?.upvotes || 0;

    // Trigger Real-Time update
    await pusherServer.trigger("ideas-channel", "upvote-update", {
      ideaId: id,
      upvotes: newCount,
    });

    return NextResponse.json({ data: { voted: true, upvotes: newCount } });
  } catch (error) {
    logger.error("Failed to toggle upvote", { error: String(error) });
    return NextResponse.json({ error: "Failed to upvote" }, { status: 500 });
  }
}
