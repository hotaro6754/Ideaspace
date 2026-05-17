import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { User } from "@/models/User";
import bcrypt from "bcryptjs";
import { z } from "zod";
import { logger } from "@/lib/logger";

const registerSchema = z.object({
  name: z.string().min(2).max(100),
  email: z.string().email(),
  password: z.string().min(6),
  username: z.string().min(3).max(30).regex(/^[a-z0-9_-]+$/),
});

export async function POST(req: Request) {
  try {
    const body = await req.json();
    const data = registerSchema.parse(body);

    await connectDB();

    const isInstitutionalEmail = data.email.toLowerCase().endsWith("@lendi.org") || data.email.toLowerCase().endsWith("@lendi.edu.in");
    if (!isInstitutionalEmail) {
      return NextResponse.json({ error: "Only @lendi.org or @lendi.edu.in emails are allowed to register" }, { status: 403 });
    }

    const existingEmail = await User.findOne({ email: data.email.toLowerCase() });
    if (existingEmail) {
      return NextResponse.json({ error: "Email already registered" }, { status: 409 });
    }

    const existingUsername = await User.findOne({ username: data.username.toLowerCase() });
    if (existingUsername) {
      return NextResponse.json({ error: "Username already taken" }, { status: 409 });
    }

    const passwordHash = await bcrypt.hash(data.password, 12);

    const user = await User.create({
      name: data.name,
      email: data.email.toLowerCase(),
      username: data.username.toLowerCase(),
      passwordHash,
      role: "student",
      isOnboarded: false,
    });

    logger.info("User registered", { userId: user._id.toString(), email: data.email });

    return NextResponse.json(
      { data: { id: user._id.toString(), email: user.email } },
      { status: 201 }
    );
  } catch (error) {
    if (error instanceof z.ZodError) {
      return NextResponse.json({ error: "Invalid input", details: error.issues }, { status: 400 });
    }
    logger.error("Registration failed", { error: String(error) });
    return NextResponse.json({ error: "Registration failed" }, { status: 500 });
  }
}
