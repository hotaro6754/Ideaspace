import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { ProofEntry } from "@/models/ProofEntry";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    await connectDB();
    const proofs = await ProofEntry.find({ isVerified: true })
      .sort({ createdAt: -1 })
      .limit(20)
      .populate("submittedBy", "name username rankTier avatarUrl")
      .populate("idea", "title slug track")
      .lean();

    return NextResponse.json({ data: proofs });
  } catch (error) {
    logger.error("Failed to fetch proofs", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch proofs" }, { status: 500 });
  }
}
