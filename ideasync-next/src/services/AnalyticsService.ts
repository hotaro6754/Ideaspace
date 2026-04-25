import { logger } from "@/lib/logger";

export const AnalyticsService = {
  async trackEvent(eventName: string, data: Record<string, unknown> = {}) {
    logger.info("Analytics", `Tracking event: ${eventName}`, data);

    // Simulate high-performance uplink to Tinybird
    try {
      // In real implementation:
      // await fetch('https://api.tinybird.co/v0/events', { ... })
    } catch (e) {
      logger.error("Analytics", "Uplink failure", e);
    }
  }
};
