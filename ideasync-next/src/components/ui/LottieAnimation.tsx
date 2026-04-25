"use client";

import dynamic from "next/dynamic";

const Lottie = dynamic(() => import("lottie-react"), { ssr: false });

export const LottieAnimation = ({ animationData, className }: { animationData: any; className?: string }) => {
  return (
    <div className={className}>
      <Lottie animationData={animationData} loop={true} />
    </div>
  );
};
