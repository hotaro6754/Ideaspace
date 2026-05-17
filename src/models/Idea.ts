import mongoose, { Schema, Document, Types } from "mongoose";

export type IdeaStatus =
  | "draft"
  | "discovery"
  | "building"
  | "shipped"
  | "archived"
  | "rejected";

export interface IIdea extends Document {
  title: string;
  slug: string;
  tagline: string;
  problem: string;
  solution: string;
  track: string;
  tags: string[];
  status: IdeaStatus;
  healthScore: number;
  owner: Types.ObjectId;
  collaborators: Types.ObjectId[];
  skillsNeeded: string[];
  upvotes: number;
  views: number;
  coverImage?: string;
  githubUrl?: string;
  demoUrl?: string;
  isFeatured: boolean;
  proofCount: number;
  createdAt: Date;
  updatedAt: Date;
}

const IdeaSchema = new Schema<IIdea>(
  {
    title: { type: String, required: true, maxlength: 120 },
    slug: { type: String, required: true, unique: true, lowercase: true },
    tagline: { type: String, required: true, maxlength: 120 },
    problem: { type: String, required: true, maxlength: 600 },
    solution: { type: String, required: true, maxlength: 600 },
    track: { type: String, required: true },
    tags: [{ type: String }],
    status: {
      type: String,
      enum: ["draft", "discovery", "building", "shipped", "archived", "rejected"],
      default: "draft",
    },
    healthScore: { type: Number, default: 0, min: 0, max: 100 },
    owner: { type: Schema.Types.ObjectId, ref: "User", required: true },
    collaborators: [{ type: Schema.Types.ObjectId, ref: "User" }],
    skillsNeeded: [{ type: String }],
    upvotes: { type: Number, default: 0 },
    views: { type: Number, default: 0 },
    coverImage: { type: String },
    githubUrl: { type: String },
    demoUrl: { type: String },
    isFeatured: { type: Boolean, default: false },
    proofCount: { type: Number, default: 0 },
  },
  { timestamps: true }
);


IdeaSchema.index({ owner: 1 });
IdeaSchema.index({ track: 1, status: 1 });
IdeaSchema.index({ upvotes: -1 });
IdeaSchema.index({ createdAt: -1 });

export const Idea = mongoose.models.Idea ?? mongoose.model<IIdea>("Idea", IdeaSchema);
