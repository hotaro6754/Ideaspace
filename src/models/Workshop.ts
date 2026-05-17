import mongoose, { Schema, Document, Types } from "mongoose";

export interface IWorkshop extends Document {
  title: string;
  description: string;
  host: Types.ObjectId;
  coHosts: Types.ObjectId[];
  scheduledAt: Date;
  durationMins: number;
  location: string;
  isOnline: boolean;
  meetLink?: string;
  maxAttendees: number;
  rsvpList: Types.ObjectId[];
  demandSignals: number;
  track: string;
  status: "upcoming" | "live" | "completed" | "cancelled";
  coverImage?: string;
  recordingUrl?: string;
  createdAt: Date;
  updatedAt: Date;
}

const WorkshopSchema = new Schema<IWorkshop>(
  {
    title: { type: String, required: true, maxlength: 120 },
    description: { type: String, required: true, maxlength: 800 },
    host: { type: Schema.Types.ObjectId, ref: "User", required: true },
    coHosts: [{ type: Schema.Types.ObjectId, ref: "User" }],
    scheduledAt: { type: Date, required: true },
    durationMins: { type: Number, default: 60 },
    location: { type: String, default: "Online" },
    isOnline: { type: Boolean, default: true },
    meetLink: { type: String },
    maxAttendees: { type: Number, default: 50 },
    rsvpList: [{ type: Schema.Types.ObjectId, ref: "User" }],
    demandSignals: { type: Number, default: 0 },
    track: { type: String, required: true },
    status: {
      type: String,
      enum: ["upcoming", "live", "completed", "cancelled"],
      default: "upcoming",
    },
    coverImage: { type: String },
    recordingUrl: { type: String },
  },
  { timestamps: true }
);

WorkshopSchema.index({ scheduledAt: 1, status: 1 });
WorkshopSchema.index({ host: 1 });

export const Workshop =
  mongoose.models.Workshop ?? mongoose.model<IWorkshop>("Workshop", WorkshopSchema);
