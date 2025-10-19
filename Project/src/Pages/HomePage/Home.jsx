import React from 'react';
import HomeHeader from '../../components/HomePage/HomeHeader';
import HeroSection from '../../components/HomePage/HeroSection';
import TrendingProjectsSection from '../../components/HomePage/TrendingProjectsSection';
import Footer from '../../components/HomePage/Footer';

export default function Home() {
  return (
    <div className="min-h-screen bg-[#0d1117] font-main">
      <HomeHeader />

      <main className="pt-16">
        <HeroSection />
        <TrendingProjectsSection />
      </main>

      <Footer />
    </div>
  );
}
