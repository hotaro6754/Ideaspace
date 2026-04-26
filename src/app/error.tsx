'use client';

import { useEffect } from 'react';
import { Button } from '@/components/ui/Button';
import { ShieldAlert, RotateCcw } from 'lucide-react';
import { logger } from '@/lib/logger';

export default function Error({
  error,
  reset,
}: {
  error: Error & { digest?: string };
  reset: () => void;
}) {
  useEffect(() => {
    logger.error('GlobalError', 'An unexpected application error occurred', error);
  }, [error]);

  return (
    <div className="min-h-screen flex items-center justify-center bg-background p-6">
      <div className="max-w-md w-full text-center">
        <div className="w-16 h-16 rounded-2xl bg-lendi-red/10 flex items-center justify-center mx-auto mb-8 text-lendi-red">
          <ShieldAlert size={40} />
        </div>
        <h1 className="text-3xl font-black text-foreground mb-4 tracking-tight">System Exception</h1>
        <p className="text-muted-foreground font-medium mb-10 leading-relaxed">
          The IdeaSync protocol encountered an unexpected error. Our system integrity checks are underway.
        </p>
        <div className="flex gap-4 justify-center">
          <Button
            onClick={() => reset()}
            className="flex items-center gap-2 px-8 py-4 bg-lendi text-white rounded-2xl font-bold"
          >
            <RotateCcw size={18} />
            Reset Protocol
          </Button>
          <Button
            variant="glass"
            onClick={() => window.location.href = '/'}
            className="px-8 py-4 rounded-2xl font-bold"
          >
            Return Home
          </Button>
        </div>
      </div>
    </div>
  );
}
