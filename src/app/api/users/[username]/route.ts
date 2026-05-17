import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { User } from "@/models/User";
import { Idea } from "@/models/Idea";
import { logger } from "@/lib/logger";

export async function GET(
  _req: Request,
  { params }: { params: Promise<{ username: string }> }
) {
  try {
    await connectDB();
    const { username } = await params;
    const user = await User.findOne({ username: username.toLowerCase() })
      .select("-passwordHash")
      .lean();

    if (!user) {
      return NextResponse.json({ error: "User not found" }, { status: 404 });
    }

    // Fetch Ideas forged by the user
    const forgedIdeas = await Idea.find({ owner: user._id })
      .sort({ createdAt: -1 })
      .populate("owner", "name username avatarUrl rankTier")
      .lean();

    // Fetch Ideas where user is a collaborator
    const collaboratedIdeas = await Idea.find({ collaborators: user._id })
      .sort({ createdAt: -1 })
      .populate("owner", "name username avatarUrl rankTier")
      .lean();

    return NextResponse.json({ 
      data: {
        ...user,
        forgedIdeas,
        collaboratedIdeas
      } 
    });
  } catch (error) {
    logger.error("Failed to fetch profile", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch profile" }, { status: 500 });
  }
}
