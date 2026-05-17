import mongoose, { Schema, Document } from "mongoose";

export interface IUser extends Document {
  email: string;
  username: string;
  name: string;
  passwordHash?: string;
  avatarUrl?: string;
  role: "student" | "faculty" | "admin" | "mentor" | "judge" | "alumni";
  bio?: string;
  skills: string[];
  interests: string[];
  primaryTrack: string;
  secondaryTracks: string[];
  points: number;
  rankTier: "Bronze" | "Silver" | "Gold" | "Platinum" | "Elite";
  githubUsername?: string;
  branch?: string;
  year?: number;
  isOnboarded: boolean;
  isVerified: boolean;
  lastLogin?: Date;
  createdAt: Date;
  updatedAt: Date;
}

const UserSchema = new Schema<IUser>(
  {
    email: { type: String, required: true, unique: true, lowercase: true },
    username: { type: String, required: true, unique: true, lowercase: true },
    name: { type: String, required: true },
    passwordHash: { type: String },
    avatarUrl: { type: String },
    role: {
      type: String,
      enum: ["student", "faculty", "admin", "mentor", "judge", "alumni"],
      default: "student",
    },
    bio: { type: String, maxlength: 300 },
    skills: [{ type: String }],
    interests: [{ type: String }],
    primaryTrack: { type: String, default: "ai-intelligent-systems" },
    secondaryTracks: [{ type: String }],
    points: { type: Number, default: 0 },
    rankTier: {
      type: String,
      enum: ["Bronze", "Silver", "Gold", "Platinum", "Elite"],
      default: "Bronze",
    },
    githubUsername: { type: String },
    branch: { type: String },
    year: { type: Number },
    isOnboarded: { type: Boolean, default: false },
    isVerified: { type: Boolean, default: false },
    lastLogin: { type: Date },
  },
  { timestamps: true }
);

UserSchema.index({ email: 1 });
UserSchema.index({ username: 1 });
UserSchema.index({ points: -1 });

export const User = mongoose.models.User ?? mongoose.model<IUser>("User", UserSchema);
