import mongoose, { Schema, Document, Types } from "mongoose";

export type ProofType = "link" | "image" | "github" | "document";
export type ProofStatus = "pending" | "approved" | "rejected";

export interface IProof extends Document {
  ideaId: Types.ObjectId;
  submitter: Types.ObjectId;
  type: ProofType;
  title: string;
  url: string;
  description?: string;
  status: ProofStatus;
  pointsAwarded: number;
  reviewedBy?: Types.ObjectId;
  reviewedAt?: Date;
  createdAt: Date;
  updatedAt: Date;
}

const ProofSchema = new Schema<IProof>(
  {
    ideaId: { type: Schema.Types.ObjectId, ref: "Idea", required: true },
    submitter: { type: Schema.Types.ObjectId, ref: "User", required: true },
    type: { type: String, enum: ["link", "image", "github", "document"], required: true },
    title: { type: String, required: true },
    url: { type: String, required: true },
    description: { type: String },
    status: { type: String, enum: ["pending", "approved", "rejected"], default: "pending" },
    pointsAwarded: { type: Number, default: 0 },
    reviewedBy: { type: Schema.Types.ObjectId, ref: "User" },
    reviewedAt: { type: Date },
  },
  { timestamps: true }
);

ProofSchema.index({ ideaId: 1 });
ProofSchema.index({ submitter: 1 });
ProofSchema.index({ status: 1 });

export const Proof = mongoose.models.Proof ?? mongoose.model<IProof>("Proof", ProofSchema);
