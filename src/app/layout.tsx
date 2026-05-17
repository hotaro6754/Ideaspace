import type { Metadata } from "next";
import { Sora, IBM_Plex_Sans, JetBrains_Mono } from "next/font/google";
import "./globals.css";
import { Toaster } from "react-hot-toast";
import { Providers } from "@/components/providers";

const sora = Sora({
  variable: "--font-display",
  subsets: ["latin"],
  display: "swap",
  weight: ["400", "500", "600", "700", "800"],
});

const ibmPlexSans = IBM_Plex_Sans({
  variable: "--font-body",
  subsets: ["latin"],
  display: "swap",
  weight: ["400", "500", "600", "700"],
});

const jetbrainsMono = JetBrains_Mono({
  variable: "--font-mono",
  subsets: ["latin"],
  display: "swap",
  weight: ["400", "500", "600"],
});

export const metadata: Metadata = {
  title: "IdeaSpace | Build in public. Prove your work. Earn your rank.",
  description:
    "IdeaSpace is a campus-native innovation platform for Lendi Institute. Post ideas, recruit collaborators, run workshops, build proof-of-work trails, and earn reputation.",
  keywords: ["innovation", "campus", "ideas", "collaboration", "Lendi"],
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className="dark" suppressHydrationWarning>
      <body
        className={`${sora.variable} ${ibmPlexSans.variable} ${jetbrainsMono.variable} antialiased selection:bg-accent/30 selection:text-white min-h-screen flex flex-col`}
      >
        <Providers>
          {children}
          <Toaster
            position="bottom-right"
            toastOptions={{
              style: {
                background: "#111114",
                color: "#f5f5f7",
                border: "1px solid #1e1e22",
                borderRadius: "12px",
                fontSize: "14px",
                fontFamily: "var(--font-body)",
              },
            }}
          />
        </Providers>
      </body>
    </html>
  );
}
