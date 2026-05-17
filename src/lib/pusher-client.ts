"use client";

import PusherClient from "pusher-js";

let clientInstance: PusherClient | null = null;

export function getPusherClient(): PusherClient {
  if (typeof window === "undefined") {
    // Return a safe mock for server-side SSR / prerendering
    return {
      subscribe: () => ({ bind: () => {}, unbind: () => {} }),
      unsubscribe: () => {},
    } as any;
  }

  if (!clientInstance) {
    clientInstance = new PusherClient(
      process.env.NEXT_PUBLIC_PUSHER_KEY || "temp-key",
      {
        cluster: process.env.NEXT_PUBLIC_PUSHER_CLUSTER || "mt1",
      }
    );
  }

  return clientInstance;
}
