import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { ReviewQueue } from "@/models/ReviewQueue";
import { ProofEntry } from "@/models/ProofEntry";
import { Idea } from "@/models/Idea";
import { auth } from "@/lib/auth";
import { logger } from "@/lib/logger";

export async function PATCH(
  req: Request,
  { params }: { params: Promise<{ id: string }> }
) {
  try {
    const session = await auth();
    const user = session?.user as any;
    
    if (!session || !["admin", "faculty"].includes(user?.role)) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    const { id } = await params;
    const { status, reviewNotes } = await req.json();

    if (!["approved", "rejected", "in_review"].includes(status)) {
      return NextResponse.json({ error: "Invalid status" }, { status: 400 });
    }

    await connectDB();

    const reviewItem = await ReviewQueue.findById(id);
    if (!reviewItem) {
      return NextResponse.json({ error: "Review item not found" }, { status: 404 });
    }

    reviewItem.status = status;
    reviewItem.reviewNotes = reviewNotes;
    reviewItem.reviewedBy = session.user.id as any;
    await reviewItem.save();

    // Side effects based on targetType
    if (status === "approved") {
      if (reviewItem.targetType === "proof") {
        await ProofEntry.findByIdAndUpdate(reviewItem.targetId, {
          isVerified: true,
          verifiedBy: session.user.id,
          verifiedAt: new Date(),
        });
        // Points awarding logic would normally be triggered here or via a Service
      } else if (reviewItem.targetType === "idea") {
        await Idea.findByIdAndUpdate(reviewItem.targetId, {
          isVerified: true,
        });
      }
    }

    return NextResponse.json({ data: reviewItem });
  } catch (error) {
    logger.error("Failed to update review item", { error: String(error) });
    return NextResponse.json({ error: "Internal Server Error" }, { status: 500 });
  }
}
