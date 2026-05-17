import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Leaderboard } from "@/models/Leaderboard";
import { logger } from "@/lib/logger";

export async function GET(req: Request) {
  try {
    await connectDB();
    const { searchParams } = new URL(req.url);
    const period = searchParams.get("period") ?? "alltime";

    const entries = await Leaderboard.find({ period })
      .sort({ points: -1 })
      .limit(50)
      .populate("user", "name username rankTier points avatarUrl bio")
      .lean();

    return NextResponse.json({ data: entries });
  } catch (error) {
    logger.error("Failed to fetch leaderboard", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch leaderboard" }, { status: 500 });
  }
}
