import React from "react";

export default function BlobBackground() {
  return (
    <div
      className="absolute inset-0 overflow-hidden pointer-events-none select-none"
      aria-hidden="true"
    >
      {/* 💠 Layered Gradient Blobs */}
      <div className="absolute top-[-10%] left-[-15%] w-[45vw] h-[45vw] bg-[#58a6ff]/30 rounded-full mix-blend-screen blur-[100px] animate-blob" />
      <div className="absolute bottom-[-10%] right-[-15%] w-[50vw] h-[50vw] bg-[#79b8ff]/25 rounded-full mix-blend-screen blur-[100px] animate-blob animation-delay-2000" />
      <div className="absolute top-[30%] right-[25%] w-[40vw] h-[40vw] bg-[#1f6feb]/20 rounded-full mix-blend-screen blur-[100px] animate-blob animation-delay-4000" />
      <div className="absolute top-[40%] left-[30%] w-[25vw] h-[25vw] bg-[#3b82f6]/20 rounded-full mix-blend-screen blur-[120px] animate-blob animation-delay-6000" />

      {/* 🌫 Subtle Noise Overlay */}
      <div className="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/noise.png')] opacity-[0.05] mix-blend-overlay" />
    </div>
  );
}
