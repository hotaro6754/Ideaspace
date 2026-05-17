export const DAILY_POST_CAP = 5;

export const BASE_POINTS = {
  idea_published: 10,
  comment_created: 1,
  idea_upvoted_received: 2,
  collaborator_joined: 5,
  proof_submitted: 15,
  proof_verified: 30,
  bounty_completed: 50,
  milestone_confirmed: 40,
  workshop_attended: 8,
  workshop_hosted: 20,
  postmortem_published: 12,
} as const;

export type ScoreEvent = keyof typeof BASE_POINTS;

export function getBasePointsForEvent(event: ScoreEvent): number {
  return BASE_POINTS[event];
}
