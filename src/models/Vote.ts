import mongoose, { Schema, Document, Types } from "mongoose";

export interface IVote extends Document {
  targetId: Types.ObjectId;
  targetType: "idea" | "proof" | "archive";
  voter: Types.ObjectId;
  value: 1 | -1;
  createdAt: Date;
}

const VoteSchema = new Schema<IVote>(
  {
    targetId: { type: Schema.Types.ObjectId, required: true },
    targetType: { type: String, enum: ["idea", "proof", "archive"], required: true },
    voter: { type: Schema.Types.ObjectId, ref: "User", required: true },
    value: { type: Number, enum: [1, -1], required: true },
  },
  { timestamps: true }
);

VoteSchema.index({ targetId: 1, targetType: 1, voter: 1 }, { unique: true });

export const Vote = mongoose.models.Vote ?? mongoose.model<IVote>("Vote", VoteSchema);
