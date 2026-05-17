import mongoose, { Schema, Document, Types } from "mongoose";

export type ProofType =
  | "github_commit"
  | "demo_link"
  | "presentation"
  | "build_log"
  | "external_validation"
  | "media_upload";

export interface IProofEntry extends Document {
  idea: Types.ObjectId;
  submittedBy: Types.ObjectId;
  type: ProofType;
  title: string;
  description?: string;
  evidenceUrl: string;
  thumbnailUrl?: string;
  isVerified: boolean;
  verifiedBy?: Types.ObjectId;
  verifiedAt?: Date;
  pointsAwarded: number;
  createdAt: Date;
}

const ProofEntrySchema = new Schema<IProofEntry>(
  {
    idea: { type: Schema.Types.ObjectId, ref: "Idea", required: true },
    submittedBy: { type: Schema.Types.ObjectId, ref: "User", required: true },
    type: {
      type: String,
      enum: ["github_commit", "demo_link", "presentation", "build_log", "external_validation", "media_upload"],
      required: true,
    },
    title: { type: String, required: true, maxlength: 120 },
    description: { type: String, maxlength: 400 },
    evidenceUrl: { type: String, required: true },
    thumbnailUrl: { type: String },
    isVerified: { type: Boolean, default: false },
    verifiedBy: { type: Schema.Types.ObjectId, ref: "User" },
    verifiedAt: { type: Date },
    pointsAwarded: { type: Number, default: 0 },
  },
  { timestamps: true }
);

ProofEntrySchema.index({ idea: 1 });
ProofEntrySchema.index({ submittedBy: 1 });
ProofEntrySchema.index({ isVerified: 1, createdAt: -1 });

export const ProofEntry =
  mongoose.models.ProofEntry ??
  mongoose.model<IProofEntry>("ProofEntry", ProofEntrySchema);
