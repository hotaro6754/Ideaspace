import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Collaborator } from "@/models/Collaborator";
import { auth } from "@/lib/auth";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    await connectDB();

    const collaborations = await Collaborator.find({ user: session.user.id })
      .populate("idea", "title slug status")
      .lean();

    return NextResponse.json({ data: collaborations });
  } catch (error) {
    logger.error("Failed to fetch collaborations", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch collaborations" }, { status: 500 });
  }
}

export async function POST(req: Request) {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    const { ideaId, role } = await req.json();
    await connectDB();

    const existing = await Collaborator.findOne({ idea: ideaId, user: session.user.id });
    if (existing) {
      return NextResponse.json({ error: "Already a collaborator" }, { status: 409 });
    }

    const collab = await Collaborator.create({
      idea: ideaId,
      user: session.user.id,
      role: role ?? "member",
      status: "pending",
    });

    return NextResponse.json({ data: collab }, { status: 201 });
  } catch (error) {
    logger.error("Failed to create collaboration", { error: String(error) });
    return NextResponse.json({ error: "Failed to create collaboration" }, { status: 500 });
  }
}
