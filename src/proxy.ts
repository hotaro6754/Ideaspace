import { auth } from "@/lib/auth";
import { NextResponse } from "next/server";

const PUBLIC_PATHS = ["/", "/auth/login", "/auth/register", "/auth/error", "/auth/verify"];
const ADMIN_PATHS = ["/admin"];
const REVIEWER_ROLES = ["faculty", "admin"];

export default auth((req) => {
  const { pathname } = req.nextUrl;
  const session = req.auth;

  // Allow API auth routes
  if (pathname.startsWith("/api/auth")) {
    return NextResponse.next();
  }

  // Allow public paths
  if (PUBLIC_PATHS.some((p) => pathname === p || pathname.startsWith(p + "/"))) {
    // If logged in and visiting auth pages, redirect to dashboard
    if (session && (pathname.startsWith("/auth/login") || pathname.startsWith("/auth/register"))) {
      return NextResponse.redirect(new URL("/dashboard", req.url));
    }
    return NextResponse.next();
  }

  // Allow public API routes
  if (pathname.startsWith("/api")) {
    return NextResponse.next();
  }

  // Not authenticated — redirect to login
  if (!session) {
    return NextResponse.redirect(new URL("/auth/login", req.url));
  }

  // Not onboarded — redirect to onboarding
  const user = session.user as any;
  if (!user.isOnboarded && pathname !== "/auth/onboarding") {
    return NextResponse.redirect(new URL("/auth/onboarding", req.url));
  }

  // Admin paths require admin/faculty role
  if (ADMIN_PATHS.some((p) => pathname.startsWith(p))) {
    if (!REVIEWER_ROLES.includes(user.role as string)) {
      return NextResponse.redirect(new URL("/dashboard", req.url));
    }
  }

  return NextResponse.next();
});

export const config = {
  matcher: ["/((?!_next/static|_next/image|favicon.ico|public).*)"],
};
