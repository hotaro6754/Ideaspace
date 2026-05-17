// Idea Health Score System - Returns 0 to 100

export function calculateIdeaHealth(params: {
  hasClearDescription: boolean,
  skillsSpecifiedCount: number,
  upvotes: number,
  applicantsCount: number,
  githubLinked: boolean,
  daysActive: number
}): number {
  let score = 0;

  // Base setup (up to 40)
  if (params.hasClearDescription) score += 20;
  score += Math.min(params.skillsSpecifiedCount * 5, 20); // 4 skills max out

  // Traction (up to 40)
  score += Math.min(params.upvotes * 2, 20); // 10 upvotes max out
  score += Math.min(params.applicantsCount * 5, 20); // 4 applicants max out

  // Validation/Execution (up to 20)
  if (params.githubLinked) score += 20;

  // Penalty for age without traction
  if (params.daysActive > 14 && params.applicantsCount === 0) {
    score -= 15;
  }

  // Bound it between 10 and 100
  return Math.max(10, Math.min(100, score));
}
