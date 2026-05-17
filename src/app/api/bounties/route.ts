import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Bounty } from "@/models/Bounty";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    await connectDB();
    const bounties = await Bounty.find()
      .sort({ createdAt: -1 })
      .populate("postedBy", "name username rankTier avatarUrl")
      .lean();

    return NextResponse.json({ data: bounties });
  } catch (error) {
    logger.error("Failed to fetch bounties", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch bounties" }, { status: 500 });
  }
}
