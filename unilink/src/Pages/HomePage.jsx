import React, { Suspense, lazy } from 'react';
import Scene3D from '../Components/Scene3D';
import GlassCard from '../Components/GlassCard';
import Navbar from '../Components/Navbar';
import { FaBookOpen, FaLayerGroup, FaMagic } from 'react-icons/fa';

// Lazy load only Galaxy (non-critical)
const Galaxy = lazy(() => import('../Animations/Galaxy/Galaxy'));

const HomePage = () => {
    return (
        <div className="relative w-full min-h-screen bg-black text-white overflow-x-hidden">
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
            <Scene3D>
                <main className="w-full relative z-10">
                    {/* Section 1: Hero */}
                    <section className="h-screen flex flex-col justify-center items-start px-10 md:px-20 max-w-7xl mx-auto pt-20">
                        <div className="max-w-2xl">
                            <h1
                                className="text-6xl md:text-8xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-white to-white/50"
                                style={{ transform: 'translateZ(0)' }}
                            >
                                Uni-Link
                            </h1>
                            <p className="text-xl md:text-2xl text-gray-300 mb-8 leading-relaxed">
                                Experience the future of knowledge management.
                                <br></br>
                                <span className="text-[#008080]"> Liquid Glass aesthetics meet
                                    immersive 3D interactions.</span>
                            </p>
                            <GlassCard className="inline-block px-8 py-4 cursor-pointer">
                                <span className="text-lg font-semibold tracking-wide">Get Started</span>
                            </GlassCard>
                        </div>
                    </section>

                    {/* Section 2: Features */}
                    <section className="h-screen flex items-center px-10 md:px-20 max-w-7xl mx-auto">
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 w-full">
                            <GlassCard className="p-8 flex flex-col items-center text-center h-80 justify-center">
                                <FaBookOpen className="text-5xl text-[#008080] mb-6" />
                                <h3 className="text-2xl font-bold mb-4">Smart Library</h3>
                                <p className="text-gray-400">Access thousands of resources with our intelligent indexing system.</p>
                            </GlassCard>

                            <GlassCard className="p-8 flex flex-col items-center text-center h-80 justify-center md:mt-20">
                                <FaLayerGroup className="text-5xl text-[#ffb547] mb-6" />
                                <h3 className="text-2xl font-bold mb-4">Liquid Design</h3>
                                <p className="text-gray-400">Immersive glassmorphism interface that feels premium and modern.</p>
                            </GlassCard>

                            <GlassCard className="p-8 flex flex-col items-center text-center h-80 justify-center">
                                <FaMagic className="text-5xl text-purple-400 mb-6" />
                                <h3 className="text-2xl font-bold mb-4">AI Powered</h3>
                                <p className="text-gray-400">Advanced algorithms to help you find exactly what you need.</p>
                            </GlassCard>
                        </div>
                    </section>

                    {/* Section 3: CTA */}
                    <section className="h-screen flex flex-col justify-center items-center px-10 text-center">
                        <GlassCard className="p-12 max-w-3xl w-full">
                            <h2 className="text-4xl md:text-5xl font-bold mb-8">Ready to Dive In?</h2>
                            <p className="text-xl text-gray-300 mb-10">
                                Join thousands of students and professors using Uni-Link today.
                            </p>
                            <button className="bg-[#008080] hover:bg-[#006666] text-white font-bold py-4 px-10 rounded-full text-xl transition-all shadow-[0_0_20px_rgba(0,128,128,0.5)] hover:shadow-[0_0_40px_rgba(0,128,128,0.7)]">
                                Join Now
                            </button>
                        </GlassCard>
                    </section>

                    {/* Spacer for 3D Book Reveal */}
                    <section className="h-screen w-full pointer-events-none" />
                </main>
            </Scene3D>
        </div>
    );
};

export default HomePage;
