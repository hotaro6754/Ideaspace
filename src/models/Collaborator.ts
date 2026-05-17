import mongoose, { Schema, Document, Types } from "mongoose";

export interface ICollaborator extends Document {
  idea: Types.ObjectId;
  user: Types.ObjectId;
  role: "owner" | "member" | "observer";
  status: "pending" | "active" | "removed";
  joinedAt: Date;
}

const CollaboratorSchema = new Schema<ICollaborator>(
  {
    idea: { type: Schema.Types.ObjectId, ref: "Idea", required: true },
    user: { type: Schema.Types.ObjectId, ref: "User", required: true },
    role: { type: String, enum: ["owner", "member", "observer"], default: "observer" },
    status: { type: String, enum: ["pending", "active", "removed"], default: "pending" },
    joinedAt: { type: Date, default: Date.now },
  },
  { timestamps: true }
);

CollaboratorSchema.index({ idea: 1, user: 1 }, { unique: true });

export const Collaborator =
  mongoose.models.Collaborator ??
  mongoose.model<ICollaborator>("Collaborator", CollaboratorSchema);
