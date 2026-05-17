import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { User } from "@/models/User";
import { auth } from "@/lib/auth";
import { z } from "zod";
import { logger } from "@/lib/logger";

const onboardSchema = z.object({
  name: z.string().min(2).max(100),
  username: z.string().min(3).max(30).regex(/^[a-z0-9_-]+$/),
  bio: z.string().max(300).optional(),
  skills: z.array(z.string()).min(1),
  interests: z.array(z.string()).min(1),
  primaryTrack: z.string().min(1),
  secondaryTracks: z.array(z.string()).max(2).optional(),
  branch: z.string().optional(),
  year: z.number().min(1).max(6).optional(),
});

export async function POST(req: Request) {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    const body = await req.json();
    const data = onboardSchema.parse(body);

    await connectDB();

    if (data.username) {
      const existing = await User.findOne({
        username: data.username.toLowerCase(),
        _id: { $ne: session.user.id },
      });
      if (existing) {
        return NextResponse.json({ error: "Username already taken" }, { status: 409 });
      }
    }

    await User.findByIdAndUpdate(session.user.id, {
      ...data,
      username: data.username.toLowerCase(),
      isOnboarded: true,
    });

    logger.info("User onboarded", { userId: session.user.id });

    return NextResponse.json({ data: { success: true } });
  } catch (error) {
    if (error instanceof z.ZodError) {
      return NextResponse.json({ error: "Invalid input", details: error.issues }, { status: 400 });
    }
    logger.error("Onboarding failed", { error: String(error) });
    return NextResponse.json({ error: "Onboarding failed" }, { status: 500 });
  }
}
