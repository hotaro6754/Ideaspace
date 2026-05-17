import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Idea } from "@/models/Idea";
import { auth } from "@/lib/auth";
import { z } from "zod";
import { logger } from "@/lib/logger";
import { slugify } from "@/lib/utils";
import { ideaSchema } from "@/lib/validations/idea";
import { calculateHealthScore } from "@/lib/healthScore";

export async function GET(req: Request) {
  try {
    await connectDB();
    const { searchParams } = new URL(req.url);

    const track = searchParams.get("track");
    const status = searchParams.get("status");
    const sort = searchParams.get("sort") ?? "newest";
    const limit = parseInt(searchParams.get("limit") ?? "20");
    const page = parseInt(searchParams.get("page") ?? "1");

    const filter: Record<string, unknown> = {};
    if (track) filter.track = track;
    if (status) filter.status = status;
    else filter.status = { $nin: ["draft", "rejected"] };

    const sortMap: Record<string, Record<string, 1 | -1>> = {
      newest: { createdAt: -1 },
      trending: { upvotes: -1 },
      health: { healthScore: -1 },
    };

    const ideas = await Idea.find(filter)
      .sort(sortMap[sort] ?? sortMap.newest)
      .skip((page - 1) * limit)
      .limit(limit)
      .populate("owner", "name username rankTier points avatarUrl")
      .populate("collaborators", "name username rankTier avatarUrl")
      .lean();

    const total = await Idea.countDocuments(filter);

    return NextResponse.json({
      data: ideas,
      meta: { total, page, limit, pages: Math.ceil(total / limit) },
    });
  } catch (error) {
    logger.error("Failed to fetch ideas", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch ideas" }, { status: 500 });
  }
}

export async function POST(req: Request) {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    const body = await req.json();
    
    // We expect a status field in the body that indicates intent
    const statusIntent = body.status === "discovery" ? "discovery" : "draft";
    
    // Validate core fields
    const data = ideaSchema.parse(body);

    await connectDB();

    const healthScore = calculateHealthScore({
      title: data.title,
      tagline: data.tagline,
      problem: data.problem,
      solution: data.solution,
      tags: data.tags,
      track: data.track,
      githubUrl: data.githubUrl || undefined,
      demoUrl: data.demoUrl || undefined,
      coverImage: data.coverImage,
      collaboratorsCount: 1, // owner counts as 1 for now
    });

    if (statusIntent === "discovery" && healthScore < 60) {
      return NextResponse.json(
        { error: `Health score must be at least 60 to publish. Current score: ${healthScore}` },
        { status: 422 }
      );
    }

    const baseSlug = slugify(data.title);
    let slug = baseSlug;
    let counter = 1;
    while (await Idea.findOne({ slug })) {
      slug = `${baseSlug}-${counter}`;
      counter++;
    }

    const idea = await Idea.create({
      title: data.title,
      slug,
      tagline: data.tagline,
      problem: data.problem,
      solution: data.solution,
      track: data.track,
      tags: data.tags,
      skillsNeeded: data.skillsNeeded,
      githubUrl: data.githubUrl || undefined,
      demoUrl: data.demoUrl || undefined,
      coverImage: data.coverImage,
      status: statusIntent,
      healthScore,
      owner: session.user.id,
      collaborators: [], // Init empty, users can join later
    });

    logger.info("Idea created", { ideaId: idea._id.toString(), title: data.title, healthScore });

    return NextResponse.json({ data: idea }, { status: 201 });
  } catch (error) {
    if (error instanceof z.ZodError) {
      return NextResponse.json({ error: "Invalid input", details: error.issues }, { status: 400 });
    }
    logger.error("Failed to create idea", { error: String(error) });
    return NextResponse.json({ error: "Failed to create idea" }, { status: 500 });
  }
}
