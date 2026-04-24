"use client";
import dynamic from "next/dynamic";

const Lottie = dynamic(() => import("lottie-react"), { ssr: false });

export const LottieAnimation = ({ url, className }: { url: string; className?: string }) => {
  return (
    <div className={className}>
      <Lottie
        animationData={null} // We would fetch this or import a local JSON
        loop={true}
        autoPlay={true}
        // Placeholder for real Lottie integration
      />
    </div>
  );
};
