export interface IdeaHealthInput {
  title: string;
  problem: string;
  solution: string;
  tags: string[];
  skillsNeeded: string[];
  githubUrl?: string;
  demoUrl?: string;
  coverImage?: string;
}

export function computeHealthScore(idea: IdeaHealthInput): number {
  let score = 0;

  if (idea.title.length >= 10) score += 15;
  if (idea.problem.length >= 80) score += 20;
  if (idea.solution.length >= 80) score += 20;
  if (idea.tags.length >= 2) score += 10;
  if (idea.skillsNeeded.length >= 1) score += 10;
  if (idea.githubUrl) score += 10;
  if (idea.demoUrl) score += 10;
  if (idea.coverImage) score += 5;

  return Math.min(score, 100);
}

export function getHealthColor(score: number): string {
  if (score < 40) return "danger";
  if (score < 70) return "warning";
  return "success";
}

export function getHealthLabel(score: number): string {
  if (score < 40) return "Needs Work";
  if (score < 70) return "Getting There";
  return "Healthy";
}
