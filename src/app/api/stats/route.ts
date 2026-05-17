import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Idea } from "@/models/Idea";
import { User } from "@/models/User";
import { ProofEntry } from "@/models/ProofEntry";
import { Workshop } from "@/models/Workshop";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    await connectDB();

    const [totalIdeas, activeBuilds, shipped, totalUsers, totalProofs, totalWorkshops] =
      await Promise.all([
        Idea.countDocuments({ status: { $nin: ["draft", "rejected"] } }),
        Idea.countDocuments({ status: "building" }),
        Idea.countDocuments({ status: "shipped" }),
        User.countDocuments(),
        ProofEntry.countDocuments({ isVerified: true }),
        Workshop.countDocuments(),
      ]);

    return NextResponse.json({
      data: {
        totalIdeas,
        activeBuilds,
        shipped,
        totalUsers,
        totalProofs,
        totalWorkshops,
      },
    });
  } catch (error) {
    logger.error("Failed to fetch stats", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch stats" }, { status: 500 });
  }
}
