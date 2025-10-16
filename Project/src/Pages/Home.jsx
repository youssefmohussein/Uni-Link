import React from 'react';
import Header from '../components/Header';
import HeroSection from '../components/HeroSection';
import TrendingProjectsSection from '../components/TrendingProjectsSection';
import Footer from '../components/Footer';

export default function Home() {
  return (
    <div className="min-h-screen bg-[#0d1117] font-main">
      <Header />

      <main className="pt-16">
        <HeroSection />
        <TrendingProjectsSection />
      </main>

      <Footer />
    </div>
  );
}
