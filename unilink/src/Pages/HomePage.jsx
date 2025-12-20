import React, { Suspense, lazy, useState } from 'react';
import Scene3D from '../Components/Scene3D';
import GlassCard from '../Components/GlassCard';
import Navbar from '../Components/Navbar';
import { FaBookOpen, FaLayerGroup, FaMagic } from 'react-icons/fa';
import LogoLoop from '../Components/LogoLoop/LogoLoop';
import MagicBento from '../Components/MagicBento';
import HeroSection from '../Components/HeroSection';
import { SiReact, SiNextdotjs, SiTypescript, SiTailwindcss } from 'react-icons/si';

// Lazy load only Galaxy (non-critical)
const Galaxy = lazy(() => import('../Animations/Galaxy/Galaxy'));

const techLogos = [
    { src: "/assets/footer-logos/logo1.png", title: "Partner 1", href: "#", alt: "Partner Logo 1" },
    { src: "/assets/footer-logos/logo2.png", title: "Partner 2", href: "#", alt: "Partner Logo 2" },
    { src: "/assets/footer-logos/logo3.png", title: "Partner 3", href: "#", alt: "Partner Logo 3" },
    { src: "/assets/footer-logos/logo4.png", title: "Partner 4", href: "#", alt: "Partner Logo 4" },
    { src: "/assets/footer-logos/logo5.png", title: "Partner 5", href: "#", alt: "Partner Logo 5" },
    { src: "/assets/footer-logos/logo6.png", title: "Partner 6", href: "#", alt: "Partner Logo 6" },
    { src: "/assets/footer-logos/logo7.png", title: "Partner 7", href: "#", alt: "Partner Logo 7" },
];

const HomePage = () => {
    const [isLoaded, setIsLoaded] = useState(false);

    return (
        <div className="relative w-full min-h-screen bg-black text-white overflow-x-hidden">
            {/* Static Hero for LCP - Fades out when 3D loads */}
            <div
                className={`fixed inset-0 z-20 pointer-events-none transition-opacity duration-1000 ease-in-out ${isLoaded ? 'opacity-0' : 'opacity-100'}`}
                style={{ zIndex: 20 }}
            >
                <HeroSection />
            </div>

            {/* Galaxy Background - Lazy loaded */}
            <Suspense fallback={<div className="fixed inset-0 bg-black" />}>
                <div className="fixed inset-0 w-full h-full z-0">
                    <Galaxy
                        transparent={true}
                        hueShift={180}
                        density={0.3}
                        glowIntensity={0.2}
                        saturation={0.4}
                        speed={0.3}
                        mouseRepulsion={false}
                        repulsionStrength={2}
                        twinkleIntensity={0.3}
                        disableAnimation={false}
                    />
                </div>
            </Suspense>

            {/* Navbar - Fixed Top */}
            <Navbar />

            {/* 3D Scene - Not lazy loaded for better LCP */}
            <Scene3D pages={5.5} onCreated={() => setIsLoaded(true)}>
                <main className="w-full relative z-30">
                    {/* Section 1: Hero (Scrollable version) */}
                    <HeroSection />

                    {/* Section 2: MagicBento Features */}
                    <section className="min-h-screen flex items-center justify-center px-4 sm:px-6 md:px-10 lg:px-20 max-w-7xl mx-auto py-10 md:py-0 relative z-40">
                        <MagicBento
                            textAutoHide={true}
                            enableStars={true}
                            enableSpotlight={true}
                            enableBorderGlow={true}
                            enableTilt={true}
                            enableMagnetism={true}
                            clickEffect={true}
                            spotlightRadius={300}
                            particleCount={12}
                            glowColor="88, 166, 255"
                        />
                    </section>

                    {/* Section 3: CTA */}
                    <section className="min-h-screen flex flex-col justify-center items-center px-4 sm:px-6 md:px-10 text-center py-10 md:py-0 relative z-40">
                        <GlassCard className="p-6 sm:p-8 md:p-12 max-w-3xl w-full">
                            <h2 className="text-3xl sm:text-4xl md:text-5xl font-bold mb-6 md:mb-8">Ready to Dive In?</h2>
                            <p className="text-lg sm:text-xl text-gray-300 mb-8 md:mb-10">
                                Join thousands of students and professors using Uni-Link today.
                            </p>
                            <button className="cursor-pointer bg-accent hover:bg-accent-alt text-white font-bold py-3 sm:py-4 px-8 sm:px-10 rounded-full text-lg sm:text-xl transition-all shadow-[0_0_20px_rgba(88,166,255,0.5)] hover:shadow-[0_0_40px_rgba(88,166,255,0.7)]">
                                <a href="/login">Join Now</a>
                            </button>
                        </GlassCard>
                    </section>

                    {/* Spacer for 3D Book Reveal - Hidden on mobile */}
                    <section className="hidden md:block h-[150vh] w-full pointer-events-none" />

                    {/* Full Footer */}
                    <footer className="w-full bg-black border-t border-white/10 pt-12 md:pt-20 pb-20 md:pb-10 relative z-50">
                        {/* LogoLoop Section */}
                        <div className="mb-12 md:mb-20">
                            <h3 className="text-center text-gray-500 mb-6 md:mb-8 text-xs sm:text-sm uppercase tracking-widest px-4">Powered By Modern Tech</h3>
                            <LogoLoop
                                logos={techLogos}
                                speed={100}
                                direction="left"
                                logoHeight={40}
                                gap={60}
                                hoverSpeed={0}
                                scaleOnHover
                                fadeOut
                                fadeOutColor="#000000"
                                ariaLabel="Technology partners"
                            />
                        </div>

                        {/* Main Footer Content */}
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 md:px-20 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 md:gap-12 mb-12 md:mb-16">
                            {/* Brand */}
                            <div className="space-y-3 md:space-y-4 sm:col-span-2 md:col-span-1">
                                <h4 className="text-xl md:text-2xl font-bold text-white">Uni-Link</h4>
                                <p className="text-gray-400 text-sm leading-relaxed">
                                    Redefining academic collaboration. Connect, share, and grow with students worldwide.
                                </p>
                            </div>

                            {/* Quick Links */}
                            <div>
                                <h5 className="text-white font-bold mb-4 md:mb-6">Explore</h5>
                                <ul className="space-y-3 md:space-y-4 text-gray-400 text-sm">
                                    <li><a href="#" className="hover:text-accent transition-colors">Home</a></li>
                                    <li><a href="#" className="hover:text-accent transition-colors">About Us</a></li>
                                    <li><a href="#" className="hover:text-accent transition-colors">Features</a></li>
                                    <li><a href="#" className="hover:text-accent transition-colors">Community</a></li>
                                </ul>
                            </div>

                            {/* Resources */}
                            <div>
                                <h5 className="text-white font-bold mb-4 md:mb-6">Resources</h5>
                                <ul className="space-y-3 md:space-y-4 text-gray-400 text-sm">
                                    <li><a href="#" className="hover:text-accent transition-colors">Documentation</a></li>
                                    <li><a href="#" className="hover:text-accent transition-colors">Help Center</a></li>
                                    <li><a href="#" className="hover:text-accent transition-colors">Guidelines</a></li>
                                    <li><a href="#" className="hover:text-accent transition-colors">Privacy Policy</a></li>
                                </ul>
                            </div>

                            {/* Connect */}
                            <div>
                                <h5 className="text-white font-bold mb-4 md:mb-6">Connect</h5>
                                <ul className="space-y-3 md:space-y-4 text-gray-400 text-sm">
                                    <li><a href="#" className="hover:text-accent transition-colors">Twitter</a></li>
                                    <li><a href="#" className="hover:text-accent transition-colors">LinkedIn</a></li>
                                    <li><a href="#" className="hover:text-accent transition-colors">Instagram</a></li>
                                    <li><a href="#" className="hover:text-accent transition-colors">GitHub</a></li>
                                </ul>
                            </div>
                        </div>

                        {/* Copyright */}
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 md:px-20 pt-6 md:pt-8 border-t border-white/5 text-center md:text-left flex flex-col md:flex-row justify-between items-center text-gray-500 text-xs">
                            <p>&copy; {new Date().getFullYear()} Uni-Link. All rights reserved.</p>
                            <p className="mt-2 md:mt-0">Designed for the Future.</p>
                        </div>
                    </footer>
                </main>
            </Scene3D>
        </div>
    );
};

export default HomePage;
