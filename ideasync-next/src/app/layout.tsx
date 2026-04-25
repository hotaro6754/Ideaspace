import type { Metadata } from "next";
import { Plus_Jakarta_Sans, Inter } from "next/font/google";
import "./globals.css";
import QueryProvider from "@/lib/QueryProvider";
import { ThemeProvider } from "@/lib/ThemeProvider";
import { Toaster } from "sonner";

const plusJakarta = Plus_Jakarta_Sans({
  subsets: ["latin"],
  variable: "--font-plus-jakarta",
});

const inter = Inter({
  subsets: ["latin"],
  variable: "--font-inter",
});

export const metadata: Metadata = {
  title: "IdeaSync | Lendi College Internet",
  description: "A premium collaboration platform for students, alumni, and faculty of Lendi College.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" suppressHydrationWarning>
      <body className={`${plusJakarta.variable} ${inter.variable} antialiased`}>
        <ThemeProvider attribute="class" defaultTheme="dark" enableSystem={false}>
          <QueryProvider>
            <Toaster position="bottom-right" />
            {children}
          </QueryProvider>
        </ThemeProvider>
      </body>
    </html>
  );
}
