import mongoose, { Schema, Document, Types } from "mongoose";

export interface IReviewQueue extends Document {
  targetId: Types.ObjectId;
  targetType: "proof" | "idea" | "bounty_submission";
  reviewType: "milestone_verification" | "external_proof" | "content_report";
  status: "pending" | "in_review" | "approved" | "rejected";
  submittedBy: Types.ObjectId;
  reviewedBy?: Types.ObjectId;
  reviewNotes?: string;
  createdAt: Date;
  updatedAt: Date;
}

const ReviewQueueSchema = new Schema<IReviewQueue>(
  {
    targetId: { type: Schema.Types.ObjectId, required: true },
    targetType: {
      type: String,
      enum: ["proof", "idea", "bounty_submission"],
      required: true,
    },
    reviewType: {
      type: String,
      enum: ["milestone_verification", "external_proof", "content_report"],
      required: true,
    },
    status: {
      type: String,
      enum: ["pending", "in_review", "approved", "rejected"],
      default: "pending",
    },
    submittedBy: { type: Schema.Types.ObjectId, ref: "User", required: true },
    reviewedBy: { type: Schema.Types.ObjectId, ref: "User" },
    reviewNotes: { type: String, maxlength: 500 },
  },
  { timestamps: true }
);

ReviewQueueSchema.index({ status: 1, createdAt: 1 });

export const ReviewQueue =
  mongoose.models.ReviewQueue ??
  mongoose.model<IReviewQueue>("ReviewQueue", ReviewQueueSchema);
