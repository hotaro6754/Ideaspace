import Link from 'next/link';
import { ShieldCheck, ArrowLeft } from 'lucide-react';

export default function NotFound() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-background p-6">
      <div className="max-w-md w-full text-center">
        <div className="w-16 h-16 rounded-2xl bg-lendi/10 flex items-center justify-center mx-auto mb-8 text-lendi">
          <ShieldCheck size={40} />
        </div>
        <h1 className="text-4xl font-black text-foreground mb-4 tracking-tight">404</h1>
        <p className="text-xl font-bold text-foreground mb-2">Resource Not Found</p>
        <p className="text-muted-foreground font-medium mb-10 leading-relaxed">
          The page you are looking for doesn't exist or has been moved within the Lendi institutional hub.
        </p>
        <Link
          href="/"
          className="inline-flex items-center gap-2 px-8 py-4 bg-lendi text-white rounded-2xl font-bold transition-all hover:scale-105 active:scale-95"
        >
          <ArrowLeft size={18} />
          Return to Hub
        </Link>
      </div>
    </div>
  );
}
