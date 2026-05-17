import mongoose, { Schema, Document, Types } from "mongoose";

export type NotificationType =
  | "collaborator_request"
  | "idea_upvoted"
  | "proof_verified"
  | "bounty_awarded"
  | "workshop_reminder"
  | "system_announcement";

export interface INotification extends Document {
  recipient: Types.ObjectId;
  type: NotificationType;
  title: string;
  body: string;
  linkUrl?: string;
  isRead: boolean;
  createdAt: Date;
}

const NotificationSchema = new Schema<INotification>(
  {
    recipient: { type: Schema.Types.ObjectId, ref: "User", required: true },
    type: {
      type: String,
      enum: [
        "collaborator_request",
        "idea_upvoted",
        "proof_verified",
        "bounty_awarded",
        "workshop_reminder",
        "system_announcement",
      ],
      required: true,
    },
    title: { type: String, required: true },
    body: { type: String, required: true },
    linkUrl: { type: String },
    isRead: { type: Boolean, default: false },
  },
  { timestamps: true }
);

NotificationSchema.index({ recipient: 1, isRead: 1, createdAt: -1 });

export const Notification =
  mongoose.models.Notification ??
  mongoose.model<INotification>("Notification", NotificationSchema);
