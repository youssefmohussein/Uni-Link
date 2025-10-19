import React from "react";
import { Link } from "react-router-dom";
import BlobBackground from "../Login/BlobBackground";

export default function HeroSection() {
  return (
    <section
      className="relative h-screen flex items-center justify-center overflow-hidden bg-main -mt-16 text-main"
      aria-label="University Collaboration Portal"
    >
      {/* Animated Blob Background */}
      <BlobBackground />

      {/* Hero Content */}
      <div className="relative z-10 max-w-5xl mx-auto px-6 text-center font-main">
        {/* Title */}
        <h1
          className="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-tight tracking-tight mb-6 animate-fade-in"
        >
          Empowering{" "}
          <span className="bg-gradient-to-r from-[#58a6ff] to-[#79b8ff] text-transparent bg-clip-text">
            Collaboration
          </span>
          <br />
          Across Every Major
        </h1>

        {/* Subtitle */}
        <p
          className="text-gray-400 text-lg sm:text-xl lg:text-2xl leading-relaxed max-w-3xl mx-auto mb-10 animate-fade-in"
          style={{ animationDelay: "0.3s" }}
        >
          A unified platform for students and professors to connect, innovate,
          and showcase impactful academic projects.
        </p>

        {/* CTA Buttons */}
        <div
          className="flex flex-col sm:flex-row gap-5 justify-center items-center animate-fade-in"
          style={{ animationDelay: "0.6s" }}
        >
          <Link
            to="/majors"
            className="px-8 py-4 rounded-2xl font-semibold bg-gradient-to-r from-[#58a6ff] to-[#79b8ff] text-white hover:from-[#388bfd] hover:to-[#58a6ff] shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[#79b8ff]"
          >
            Explore Majors
          </Link>

          <Link
            to="/home"
            className="px-8 py-4 rounded-2xl font-semibold text-white bg-white/10 border border-white/20 backdrop-blur-md hover:bg-white/20 hover:border-white/30 shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-[#79b8ff]/50"
          >
            View Projects
          </Link>
        </div>
      </div>

      {/* Decorative Gradient Overlay (for depth) */}
      <div className="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-black/10 pointer-events-none" />
    </section>
  );
}
