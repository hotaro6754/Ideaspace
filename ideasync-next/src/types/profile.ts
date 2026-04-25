export type UserRole = 'student' | 'senior' | 'alumni' | 'faculty' | 'admin';

export type UserRank = 'Seedling' | 'Spark' | 'Builder' | 'Launcher' | 'Innovator' | 'Campus Legend';

export interface Profile {
  id: string;
  roll_number: string | null;
  full_name: string | null;
  avatar_url: string | null;
  role: UserRole;
  department: string | null;
  year: number | null;
  bio: string | null;
  interests: string[];
  skills: string[];
  points: number;
  rank: UserRank;
  github_username: string | null;
  linkedin_url: string | null;
  portfolio_url: string | null;
  created_at: string;
  updated_at: string;
}
