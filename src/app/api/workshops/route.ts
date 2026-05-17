import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Workshop } from "@/models/Workshop";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    await connectDB();
    const workshops = await Workshop.find()
      .sort({ scheduledAt: 1 })
      .populate("host", "name username rankTier avatarUrl")
      .lean();

    return NextResponse.json({ data: workshops });
  } catch (error) {
    logger.error("Failed to fetch workshops", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch workshops" }, { status: 500 });
  }
}
