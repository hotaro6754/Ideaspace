import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Archive } from "@/models/Archive";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    await connectDB();
    const postmortems = await Archive.find({ isPublic: true })
      .sort({ createdAt: -1 })
      .populate("author", "name username")
      .populate("idea", "title slug")
      .lean();
    return NextResponse.json({ data: postmortems });
  } catch (error) {
    logger.error("Failed to fetch postmortems", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch postmortems" }, { status: 500 });
  }
}
