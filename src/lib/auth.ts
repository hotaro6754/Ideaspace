import NextAuth from "next-auth";
import GitHub from "next-auth/providers/github";
import Google from "next-auth/providers/google";
import CredentialsProvider from "next-auth/providers/credentials";
import { MongoDBAdapter } from "@auth/mongodb-adapter";
import clientPromise from "./mongodb-client";
import { connectDB } from "./db";
import { User } from "@/models/User";
import bcrypt from "bcryptjs";

export const { handlers, auth, signIn, signOut } = NextAuth({
  adapter: MongoDBAdapter(clientPromise),
  providers: [
    GitHub({
      clientId: process.env.AUTH_GITHUB_ID!,
      clientSecret: process.env.AUTH_GITHUB_SECRET!,
    }),
    Google({
      clientId: process.env.AUTH_GOOGLE_ID!,
      clientSecret: process.env.AUTH_GOOGLE_SECRET!,
    }),
    CredentialsProvider({
      name: "ideaspace",
      credentials: {
        email: { label: "Email", type: "email" },
        password: { label: "Password", type: "password" },
      },
      async authorize(credentials) {
        if (!credentials?.email || !credentials?.password) return null;

        const email = (credentials.email as string).toLowerCase();
        const password = credentials.password as string;

        await connectDB();
        const user = await User.findOne({ email });

        if (!user || !user.passwordHash) {
          throw new Error("No user found with this email.");
        }

        const isPasswordValid = await bcrypt.compare(password, user.passwordHash);
        if (!isPasswordValid) throw new Error("Invalid password.");

        await User.updateOne({ _id: user._id }, { lastLogin: new Date() });

        return {
          id: user._id.toString(),
          name: user.name,
          email: user.email,
          role: user.role,
          username: user.username,
          isOnboarded: user.isOnboarded,
          rankTier: user.rankTier,
          points: user.points,
        };
      },
    }),
  ],
  session: {
    strategy: "jwt",
  },
  pages: {
    signIn: "/auth/login",
    error: "/auth/error",
  },
  callbacks: {
    async jwt({ token, user, trigger, session }) {
      if (user) {
        token.id = user.id;
        token.role = (user as any).role || "student";
        token.username = (user as any).username || user.email?.split("@")[0];
        token.isOnboarded = (user as any).isOnboarded || false;
        token.rankTier = (user as any).rankTier || "Bronze";
        token.points = (user as any).points || 0;
      }

      if (trigger === "update" && session) {
        return { ...token, ...session };
      }

      if (!token.username) {
        await connectDB();
        const dbUser = await User.findOne({ email: token.email });
        if (dbUser) {
          token.role = dbUser.role;
          token.username = dbUser.username;
          token.isOnboarded = dbUser.isOnboarded;
          token.rankTier = dbUser.rankTier;
          token.points = dbUser.points;
        }
      }

      return token;
    },
    async session({ session, token }) {
      if (token && session.user) {
        session.user.id = token.id as string;
        (session.user as any).role = token.role;
        (session.user as any).username = token.username;
        (session.user as any).isOnboarded = token.isOnboarded;
        (session.user as any).rankTier = token.rankTier;
        (session.user as any).points = token.points;
      }
      return session;
    },
    async signIn({ user, account, profile }) {
      const email = user?.email?.toLowerCase();
      if (!email) return false;

      // Institutional domain check
      if (!email.endsWith("@lendi.org") && !email.endsWith("@lendi.edu.in")) {
        return "/auth/error?error=AccessDenied&reason=institutional_email_required";
      }

      if (account?.provider === "github" || account?.provider === "google") {
        await connectDB();
        const existingUser = await User.findOne({ email });
        
        if (!existingUser) {
          await User.create({
            email,
            name: user.name || "Student",
            username: (profile as any)?.login || email.split("@")[0],
            avatarUrl: user.image,
            role: "student",
            primaryTrack: "ai", 
            isOnboarded: false,
          });
        }
      }
      return true;
    },
  },
});
