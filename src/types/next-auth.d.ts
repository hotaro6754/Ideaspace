import type { DefaultSession } from "next-auth";

declare module "next-auth" {
  interface Session {
    user: {
      id: string;
      role: string;
      username: string;
      isOnboarded: boolean;
      rankTier: string;
      points: number;
    } & DefaultSession["user"];
  }
}
