/**
 * ZeroSlop Quality Score Engine
 * Based on hotaro6754/ZeroSlop metrics
 */

export interface QualityReport {
  feature: string;
  backend: number; // /25
  frontend: number; // /20
  data: number; // /15
  connect: number; // /15
  quality: number; // /15
  evidence: number; // /10
  total: number;
  notes: string[];
}

export const scoreFeature = (
  feature: string,
  scores: Omit<QualityReport, 'feature' | 'total' | 'notes'>,
  notes: string[] = []
): QualityReport => {
  const total =
    scores.backend +
    scores.frontend +
    scores.data +
    scores.connect +
    scores.quality +
    scores.evidence;

  return {
    feature,
    ...scores,
    total,
    notes
  };
};

export const validateGate = (report: QualityReport) => {
  if (report.total < 70) {
    throw new Error(`ZeroSlop Gate Failed: ${report.feature} scored ${report.total}/100. Rebuild required.`);
  }
  return true;
};
