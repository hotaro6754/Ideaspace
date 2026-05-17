import { connectDB } from "./db";
import { User } from "@/models/User";
import { Notification } from "@/models/Notification";
import { getRankTier } from "./product-config";
import { BASE_POINTS } from "./score-config";
import type { ScoreEvent } from "./score-config";
import { logger } from "./logger";

export async function awardPoints(
  userId: string,
  event: ScoreEvent,
  meta?: { title?: string; linkUrl?: string }
) {
  const points = BASE_POINTS[event];

  await connectDB();

  const user = await User.findByIdAndUpdate(
    userId,
    { $inc: { points } },
    { new: true }
  );

  if (!user) {
    logger.error("User not found for point award", { userId, event });
    return;
  }

  const newTier = getRankTier(user.points);

  if (newTier !== user.rankTier) {
    await User.updateOne({ _id: userId }, { rankTier: newTier });

    await Notification.create({
      recipient: userId,
      type: "system_announcement",
      title: `🎉 Rank Up: ${newTier}`,
      body: `Congratulations! You've reached ${newTier} tier with ${user.points} points.`,
      linkUrl: meta?.linkUrl ?? "/leaderboard",
    });
  }

  logger.info("Points awarded", { userId, event, points, total: user.points });
}
