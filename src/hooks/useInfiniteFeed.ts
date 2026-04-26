import { useInfiniteQuery } from "@tanstack/react-query";
import { FeedService, FeedItem } from "@/services/FeedService";
import { supabase } from "@/lib/supabase";

export function useInfiniteFeed() {
  return useInfiniteQuery<FeedItem[], Error>({
    queryKey: ["campus-feed"],
    queryFn: async ({ pageParam = 0 }) => {
      const { data: { user } } = await supabase.auth.getUser();
      if (!user) return [];
      return await FeedService.getPersonalizedFeed(user.id, 10, (pageParam as number) * 10);
    },
    initialPageParam: 0,
    getNextPageParam: (lastPage, allPages) => {
      return lastPage.length === 10 ? allPages.length : undefined;
    },
  });
}
