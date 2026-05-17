import { NextResponse } from "next/server";
import { connectDB } from "@/lib/db";
import { Notification } from "@/models/Notification";
import { auth } from "@/lib/auth";
import { logger } from "@/lib/logger";

export async function GET() {
  try {
    const session = await auth();
    if (!session?.user?.id) {
      return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
    }

    await connectDB();
    const notifications = await Notification.find({ recipient: session.user.id })
      .sort({ createdAt: -1 })
      .limit(50)
      .lean();

    const unreadCount = await Notification.countDocuments({
      recipient: session.user.id,
      isRead: false,
    });

    return NextResponse.json({ data: notifications, meta: { unreadCount } });
  } catch (error) {
    logger.error("Failed to fetch notifications", { error: String(error) });
    return NextResponse.json({ error: "Failed to fetch notifications" }, { status: 500 });
  }
}
