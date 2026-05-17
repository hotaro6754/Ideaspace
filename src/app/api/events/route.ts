import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Workshop } from "@/models/Workshop";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    await connectDB();
    const events = await Workshop.find()
      .sort({ scheduledAt: 1 })
      .populate("host", "name username")
      .lean();
    return NextResponse.json({ data: events });
  } catch (error) {
    logger.error("Failed to fetch events", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch events" }, { status: 500 });
  }
}
