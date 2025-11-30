import React, { Suspense, lazy } from 'react';
import GlassCard from '../../Components/GlassCard';
import Navbar from '../../Components/Navbar';
import { FaLightbulb, FaUsers, FaRocket, FaQuoteLeft } from 'react-icons/fa';
import CardSwap, { Card } from '../../Components/CardSwap/CardSwap';

// Reuse the Galaxy animation
const Galaxy = lazy(() => import('../../Animations/Galaxy/Galaxy'));

const AboutUsPage = () => {
    return (
        <div className="relative w-full min-h-screen bg-black text-white overflow-x-hidden font-main">
            {/* Background - Galaxy */}
            <Suspense fallback={<div className="fixed inset-0 bg-black" />}>
                <div className="fixed inset-0 w-full h-full z-0">
                    <Galaxy
                        transparent={true}
                        hueShift={260} // Purple/Blue shift for "Visionary" vibe
                        density={1.5}
                        glowIntensity={0.1}
                        saturation={0.6}
                        speed={0.15}
                        mouseRepulsion={false}
                    />
                </div>
            </Suspense>

            <Navbar />

            <main className="relative z-10 pt-32 px-6 md:px-20 max-w-7xl mx-auto flex flex-col gap-24 pb-32">

                {/* Split Layout: Journey & CardSwap */}
                <section className="flex flex-col lg:flex-row items-center gap-12 lg:gap-20">

                    {/* Left Column: Text Content */}
                    <div className="flex-1 space-y-8 text-left">
                        <div>
                            <h1 className="text-5xl md:text-6xl font-bold tracking-tight mb-4">
                                <span className="block text-white">We Dream of</span>
                                <span className="block text-[#3b82f6]">
                                    Connection.
                                </span>
                            </h1>
                            <p className="text-xl text-gray-300 leading-relaxed">
                                From a dorm room idea to a movement empowering students everywhere. We are redefining how collaboration happens in the academic world.
                            </p>
                        </div>

                        <div className="space-y-6">
                            <div className="flex items-start gap-4">
                                <div className="p-3 bg-teal-500/20 rounded-lg text-teal-400">
                                    <FaLightbulb className="text-2xl" />
                                </div>
                                <div>
                                    <h3 className="text-xl font-bold text-white">The Spark</h3>
                                    <p className="text-gray-400">Breaking Down Academic Barriers.</p>
                                </div>
                            </div>
                            <div className="flex items-start gap-4">
                                <div className="p-3 bg-purple-500/20 rounded-lg text-purple-400">
                                    <FaUsers className="text-2xl" />
                                </div>
                                <div>
                                    <h3 className="text-xl font-bold text-white">The Mission</h3>
                                    <p className="text-gray-400">Connecting Majors. Empowering Collaboration.</p>
                                </div>
                            </div>
                            <div className="flex items-start gap-4">
                                <div className="p-3 bg-orange-500/20 rounded-lg text-orange-400">
                                    <FaRocket className="text-2xl" />
                                </div>
                                <div>
                                    <h3 className="text-xl font-bold text-white">The Future</h3>
                                    <p className="text-gray-400">A Smarter, More Connected Academic Community.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Right Column: CardSwap */}
                    <div className="flex-1 w-full h-[600px] relative flex items-center justify-center -mt-64">
                        <CardSwap
                            width={550}
                            height={400}
                            cardDistance={50}
                            verticalDistance={60}
                            delay={4000}
                            pauseOnHover={true}
                        >
                            {/* Card 1: The Spark */}
                            <Card className="bg-black/80 backdrop-blur-xl border border-white/10 p-8 flex flex-col justify-center items-start text-left shadow-2xl">
                                <div className="mb-6 p-4 bg-teal-500/20 rounded-full inline-block">
                                    <FaLightbulb className="text-4xl text-teal-400" />
                                </div>
                                <span className="text-teal-400 font-bold tracking-widest uppercase text-xs mb-2 block">The Spark</span>
                                <h3 className="text-3xl font-bold mb-4 text-white">It Started as an idea.</h3>
                                <p className="text-gray-400 leading-relaxed text-sm">
                                    UniLink began as a response to the isolation between university departments, where valuable work often stayed hidden. We wanted a platform that connects majors and encourages cross-disciplinary engagement.
                                </p>
                            </Card>

                            {/* Card 2: The Mission */}
                            <Card className="bg-black/80 backdrop-blur-xl border border-white/10 p-8 flex flex-col justify-center items-start text-left shadow-2xl">
                                <div className="mb-6 p-4 bg-purple-500/10 rounded-full inline-block">
                                    <FaUsers className="text-4xl text-purple-400" />
                                </div>
                                <span className="text-purple-400 font-bold tracking-widest uppercase text-xs mb-2 block">The Mission</span>
                                <h3 className="text-3xl font-bold mb-4 text-white">Empowering the Collective</h3>
                                <p className="text-gray-400 leading-relaxed text-sm">
                                    To provide a unified digital space where students, professors, and admins can communicate, share projects, collaborate, and support academic growth across all fields..
                                </p>
                            </Card>

                            {/* Card 3: The Future */}
                            <Card className="bg-black/80 backdrop-blur-xl border border-white/10 p-8 flex flex-col justify-center items-start text-left shadow-2xl">
                                <div className="mb-6 p-4 bg-orange-500/20 rounded-full inline-block">
                                    <FaRocket className="text-4xl text-orange-400" />
                                </div>
                                <span className="text-orange-400 font-bold tracking-widest uppercase text-xs mb-2 block">The Future</span>
                                <h3 className="text-3xl font-bold mb-4 text-white">Growing With Every Student.</h3>
                                <p className="text-gray-400 leading-relaxed text-sm">
                                    UniLink aims to introduce smarter features and become the university’s main academic ecosystem—supporting research, mentorship, and innovation across the entire campus.
                                </p>
                            </Card>
                        </CardSwap>
                    </div>
                </section>

                {/* Quote Section */}
                <section className="py-10">
                    <div className="text-center max-w-3xl mx-auto">
                        <FaQuoteLeft className="text-4xl text-white/20 mx-auto mb-6" />
                        <blockquote className="text-3xl md:text-4xl font-serif italic text-gray-200 leading-normal mb-6">
                            "Education is not the filling of a pail, but the lighting of a fire."
                        </blockquote>
                        <cite className="text-accent font-semibold not-italic tracking-wide">— William Butler Yeats</cite>
                    </div>
                </section>

                {/* Simple CTA */}
                <section className="text-center">
                    <h2 className="text-2xl font-bold mb-6">Ready to write your chapter?</h2>
                    <button className="bg-accent hover:bg-accent-hover text-white font-bold py-4 px-12 rounded-full text-lg transition-all shadow-[0_0_20px_rgba(0,128,128,0.4)] hover:shadow-[0_0_40px_rgba(0,128,128,0.6)]">
                        <a href="/login">Join Our Story</a>
                    </button>
                </section>

            </main>
        </div>
    );
};

export default AboutUsPage;