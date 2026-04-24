export class AnalyticsService {
  static async logEvent(name: string, payload: any) {
    const event = {
      timestamp: new Date().toISOString(),
      event: name,
      ...payload
    };

    // In production, this would stream to Tinybird via High-level Ingestion API
    console.log('[Analytics]:', event);

    // For the sandbox, we'll append to a local log file for verification
    // (This would be handled by a server-side route/action in Next.js)
  }

  static async getLeaderboard() {
    // This would query a Tinybird Pipe endpoint
    return [];
  }
}
