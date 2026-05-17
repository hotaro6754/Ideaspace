import mongoose, { Schema, Document, Types } from "mongoose";

export interface ILeaderboard extends Document {
  user: Types.ObjectId;
  points: number;
  rank: number;
  rankTier: string;
  period: "weekly" | "monthly" | "alltime";
  ideasShipped: number;
  proofsSubmitted: number;
  workshopsAttended: number;
  updatedAt: Date;
}

const LeaderboardSchema = new Schema<ILeaderboard>(
  {
    user: { type: Schema.Types.ObjectId, ref: "User", required: true },
    points: { type: Number, default: 0 },
    rank: { type: Number },
    rankTier: { type: String },
    period: { type: String, enum: ["weekly", "monthly", "alltime"], required: true },
    ideasShipped: { type: Number, default: 0 },
    proofsSubmitted: { type: Number, default: 0 },
    workshopsAttended: { type: Number, default: 0 },
  },
  { timestamps: true }
);

LeaderboardSchema.index({ period: 1, points: -1 });
LeaderboardSchema.index({ period: 1, user: 1 }, { unique: true });

export const Leaderboard =
  mongoose.models.Leaderboard ??
  mongoose.model<ILeaderboard>("Leaderboard", LeaderboardSchema);
