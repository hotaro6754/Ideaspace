import { connectDB } from "./db";
import { Notification } from "@/models/Notification";
import type { NotificationType } from "@/models/Notification";
import { logger } from "./logger";

export async function createNotification(
  recipientId: string,
  type: NotificationType,
  title: string,
  body: string,
  linkUrl?: string
) {
  await connectDB();

  return Notification.create({
    recipient: recipientId,
    type,
    title,
    body,
    linkUrl,
  });
}

export async function markAsRead(notificationId: string) {
  await connectDB();
  return Notification.updateOne({ _id: notificationId }, { isRead: true });
}

export async function markAllAsRead(userId: string) {
  await connectDB();
  return Notification.updateMany(
    { recipient: userId, isRead: false },
    { isRead: true }
  );
}
