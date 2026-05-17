import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { User } from "@/models/User";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    await connectDB();
    const users = await User.find()
      .select("-passwordHash")
      .sort({ points: -1 })
      .limit(50)
      .lean();
    return NextResponse.json({ data: users });
  } catch (error) {
    logger.error("Failed to fetch users", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch users" }, { status: 500 });
  }
}
