import mongoose, { Schema, Document, Types } from "mongoose";

export type JoinRequestStatus = "pending" | "approved" | "rejected";

export interface IJoinRequest extends Document {
  ideaId: Types.ObjectId;
  userId: Types.ObjectId;
  role: string;
  message?: string;
  status: JoinRequestStatus;
  createdAt: Date;
  updatedAt: Date;
}

const JoinRequestSchema = new Schema<IJoinRequest>(
  {
    ideaId: { type: Schema.Types.ObjectId, ref: "Idea", required: true },
    userId: { type: Schema.Types.ObjectId, ref: "User", required: true },
    role: { type: String, required: true },
    message: { type: String },
    status: { type: String, enum: ["pending", "approved", "rejected"], default: "pending" },
  },
  { timestamps: true }
);

// A user can only have one pending request per idea
JoinRequestSchema.index({ ideaId: 1, userId: 1 }, { unique: true, partialFilterExpression: { status: "pending" } });

export const JoinRequest = mongoose.models.JoinRequest ?? mongoose.model<IJoinRequest>("JoinRequest", JoinRequestSchema);
