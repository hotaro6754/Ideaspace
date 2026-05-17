export const CORE_TRACKS = [
  { slug: "ai-intelligent-systems", label: "AI & Intelligent Systems" },
  { slug: "software-saas-platform", label: "Software, SaaS & Platform Engineering" },
  { slug: "mobile-consumer-apps", label: "Mobile & Consumer Applications" },
  { slug: "data-analytics", label: "Data, Analytics & Decision Systems" },
  { slug: "cybersecurity-cloud", label: "Cybersecurity, Cloud & Infrastructure" },
  { slug: "embedded-iot-robotics", label: "Embedded Systems, IoT & Robotics" },
  { slug: "ux-product-design", label: "UI/UX, Product & Experience Design" },
  { slug: "applied-campus-innovation", label: "Applied Engineering & Campus Innovation" },
] as const;

export const RANK_TIERS = ["Bronze", "Silver", "Gold", "Platinum", "Elite"] as const;

export const RANK_THRESHOLDS: Record<string, number> = {
  Bronze: 0,
  Silver: 200,
  Gold: 600,
  Platinum: 1500,
  Elite: 4000,
};

export type TrackSlug = (typeof CORE_TRACKS)[number]["slug"];
export type RankTier = (typeof RANK_TIERS)[number];

export function getTrackLabel(slug: string): string {
  const track = CORE_TRACKS.find(t => t.slug === slug);
  return track?.label ?? slug;
}

export function getRankTier(points: number): RankTier {
  if (points >= RANK_THRESHOLDS.Elite) return "Elite";
  if (points >= RANK_THRESHOLDS.Platinum) return "Platinum";
  if (points >= RANK_THRESHOLDS.Gold) return "Gold";
  if (points >= RANK_THRESHOLDS.Silver) return "Silver";
  return "Bronze";
}
