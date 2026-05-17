import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { ReviewQueue } from "@/models/ReviewQueue";
import { auth } from "@/lib/auth";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    const session = await auth();
    const user = session?.user as any;
    
    if (!session || !["admin", "faculty"].includes(user?.role)) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    await connectDB();

    const queue = await ReviewQueue.find({ status: { $in: ["pending", "in_review"] } })
      .populate("submittedBy", "name username avatarUrl")
      .sort({ createdAt: 1 }) // Oldest first
      .lean();

    return NextResponse.json({ data: queue });
  } catch (error) {
    logger.error("Failed to fetch review queue", { error: String(error) });
    return NextResponse.json({ error: "Internal Server Error" }, { status: 500 });
  }
}
