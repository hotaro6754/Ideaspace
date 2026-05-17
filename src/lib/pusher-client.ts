"use client";

import PusherClient from "pusher-js";

export const pusherClient = new PusherClient(
  process.env.NEXT_PUBLIC_PUSHER_KEY || "temp-key",
  {
    cluster: process.env.NEXT_PUBLIC_PUSHER_CLUSTER || "mt1",
  }
);
