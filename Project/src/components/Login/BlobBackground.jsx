import React from "react";

export default function BlobBackground() {
  return (
    <div
      className="absolute inset-0 overflow-hidden pointer-events-none"
      aria-hidden="true"
    >
      {/* Layered blurred gradient blobs */}
      <div className="absolute top-[-10%] left-[-10%] w-[40vw] h-[40vw] bg-[#58a6ff]/30 rounded-full mix-blend-screen blur-[100px] animate-blob" />
      <div className="absolute bottom-[-10%] right-[-10%] w-[45vw] h-[45vw] bg-[#79b8ff]/25 rounded-full mix-blend-screen blur-[100px] animate-blob animation-delay-2000" />
      <div className="absolute top-[30%] right-[25%] w-[35vw] h-[35vw] bg-[#1f6feb]/20 rounded-full mix-blend-screen blur-[100px] animate-blob animation-delay-4000" />

      {/* Subtle noise overlay for texture */}
      <div className="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/noise.png')] opacity-[0.04]" />
    </div>
  );
}
