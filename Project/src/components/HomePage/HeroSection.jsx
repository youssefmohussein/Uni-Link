import React from 'react';
import { Link } from 'react-router-dom';
import BlobBackground from './BlobBackground';

export default function HeroSection() {
  return (
    <section className="relative min-h-[80vh] flex items-center justify-center overflow-hidden">
      <BlobBackground />

      <div className="relative z-10 container mx-auto px-4 text-center max-w-5xl">
        <h1 className="text-5xl md:text-7xl font-bold text-white mb-6 animate-fade-in">
          Empowering Collaboration <br />
          <span className="bg-gradient-to-r from-[#58a6ff] to-[#79b8ff] text-transparent bg-clip-text">
            Across Every Major
          </span>
        </h1>

        <p className="text-lg md:text-xl text-gray-300 mb-10 max-w-3xl mx-auto leading-relaxed">
          Connect with peers and professors, share your projects, and explore academic innovation at your university.
        </p>

        <div className="flex flex-col sm:flex-row gap-4 justify-center items-center">
          <Link
            to="/majors"
            className="px-8 py-4 rounded-xl font-semibold text-white bg-gradient-to-r from-[#58a6ff] to-[#79b8ff] hover:from-[#388bfd] hover:to-[#58a6ff] shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300"
          >
            Explore Majors
          </Link>

          <Link
            to="/home"
            className="px-8 py-4 rounded-xl font-semibold text-white bg-white/10 backdrop-blur-sm border border-white/20 hover:bg-white/20 hover:border-white/30 shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300"
          >
            View Projects
          </Link>
        </div>
      </div>
    </section>
  );
}
