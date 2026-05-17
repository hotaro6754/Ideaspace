import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Archive } from "@/models/Archive";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    await connectDB();
    const archives = await Archive.find({ isPublic: true })
      .sort({ createdAt: -1 })
      .populate("author", "name username rankTier avatarUrl")
      .populate("idea", "title slug track")
      .lean();

    return NextResponse.json({ data: archives });
  } catch (error) {
    logger.error("Failed to fetch archives", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch archives" }, { status: 500 });
  }
}
