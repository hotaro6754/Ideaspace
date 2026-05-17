import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Idea } from "@/models/Idea";
import { User } from "@/models/User";
import { ProofEntry } from "@/models/ProofEntry";
import { auth } from "@/lib/auth";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    await connectDB();

    const [myIdeas, totalIdeas, totalProofs] = await Promise.all([
      Idea.find({ owner: session.user.id }).sort({ createdAt: -1 }).lean(),
      Idea.countDocuments({ status: { $nin: ["draft", "rejected"] } }),
      ProofEntry.countDocuments({ submittedBy: session.user.id, isVerified: true }),
    ]);

    const user = await User.findById(session.user.id).select("points rankTier").lean();

    return NextResponse.json({
      data: {
        myIdeas,
        stats: {
          ideasOwned: myIdeas.length,
          totalIdeas,
          proofsVerified: totalProofs,
          points: user?.points ?? 0,
          rankTier: user?.rankTier ?? "Bronze",
        },
      },
    });
  } catch (error) {
    logger.error("Failed to fetch dashboard", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch dashboard" }, { status: 500 });
  }
}
