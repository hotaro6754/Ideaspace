"use client";

import dynamic from "next/dynamic";

const Spline = dynamic(() => import("@splinetool/react-spline"), {
  ssr: false,
  loading: () => <div className="w-full h-full bg-white/5 animate-pulse rounded-[3rem]" />
});

export const SplineScene = () => {
  return (
    <div className="w-full h-full relative">
      <Spline
        scene="https://prod.spline.design/6Wq1Q7YAnThZpIBJ/scene.splinecode"
      />
      <div className="absolute inset-0 pointer-events-none bg-gradient-to-t from-black via-transparent to-transparent" />
    </div>
  );
};
