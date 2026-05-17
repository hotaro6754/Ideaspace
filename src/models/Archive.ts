import mongoose, { Schema, Document, Types } from "mongoose";

export interface IArchive extends Document {
  idea: Types.ObjectId;
  title: string;
  outcome: "shipped" | "paused" | "failed" | "pivoted";
  whatWorked: string;
  whatFailed: string;
  lessons: string;
  forkedFrom?: Types.ObjectId;
  forkCount: number;
  author: Types.ObjectId;
  isPublic: boolean;
  createdAt: Date;
}

const ArchiveSchema = new Schema<IArchive>(
  {
    idea: { type: Schema.Types.ObjectId, ref: "Idea", required: true },
    title: { type: String, required: true, maxlength: 120 },
    outcome: {
      type: String,
      enum: ["shipped", "paused", "failed", "pivoted"],
      required: true,
    },
    whatWorked: { type: String, required: true, maxlength: 1000 },
    whatFailed: { type: String, required: true, maxlength: 1000 },
    lessons: { type: String, required: true, maxlength: 800 },
    forkedFrom: { type: Schema.Types.ObjectId, ref: "Archive" },
    forkCount: { type: Number, default: 0 },
    author: { type: Schema.Types.ObjectId, ref: "User", required: true },
    isPublic: { type: Boolean, default: true },
  },
  { timestamps: true }
);

ArchiveSchema.index({ idea: 1 });
ArchiveSchema.index({ author: 1 });

export const Archive =
  mongoose.models.Archive ?? mongoose.model<IArchive>("Archive", ArchiveSchema);
