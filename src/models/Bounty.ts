import mongoose, { Schema, Document, Types } from "mongoose";

export interface IBounty extends Document {
  title: string;
  description: string;
  postedBy: Types.ObjectId;
  rewardPoints: number;
  kind: "build" | "research" | "design" | "mentor" | "judge";
  participationMode: "solo" | "team" | "either";
  deadlineAt: Date;
  status: "open" | "in_review" | "awarded" | "closed";
  submissions: Types.ObjectId[];
  winnerId?: Types.ObjectId;
  track: string;
  createdAt: Date;
  updatedAt: Date;
}

const BountySchema = new Schema<IBounty>(
  {
    title: { type: String, required: true, maxlength: 120 },
    description: { type: String, required: true, maxlength: 1000 },
    postedBy: { type: Schema.Types.ObjectId, ref: "User", required: true },
    rewardPoints: { type: Number, required: true },
    kind: {
      type: String,
      enum: ["build", "research", "design", "mentor", "judge"],
      required: true,
    },
    participationMode: { type: String, enum: ["solo", "team", "either"], default: "either" },
    deadlineAt: { type: Date, required: true },
    status: {
      type: String,
      enum: ["open", "in_review", "awarded", "closed"],
      default: "open",
    },
    submissions: [{ type: Schema.Types.ObjectId, ref: "User" }],
    winnerId: { type: Schema.Types.ObjectId, ref: "User" },
    track: { type: String, required: true },
  },
  { timestamps: true }
);

BountySchema.index({ status: 1, deadlineAt: 1 });

export const Bounty =
  mongoose.models.Bounty ?? mongoose.model<IBounty>("Bounty", BountySchema);
