import React, { Suspense, lazy } from 'react';
import GlassCard from '../../Components/GlassCard';
import Navbar from '../../Components/Navbar';
import { FaLightbulb, FaUsers, FaRocket, FaQuoteLeft } from 'react-icons/fa';

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
                        density={0.15}
                        glowIntensity={0.1}
                        saturation={0.6}
                        speed={0.15}
                        mouseRepulsion={false}
                    />
                </div>
            </Suspense>

            <Navbar />

            <main className="relative z-10 pt-32 px-6 md:px-20 max-w-5xl mx-auto flex flex-col gap-24 pb-32">

                {/* Hero: The Vision */}
                <section className="text-center space-y-8 animate-fade-in-up">
                    <h1 className="text-6xl md:text-8xl font-bold tracking-tight">
                        <span className="block text-white">We Dream of</span>
                        <span className="block bg-clip-text text-transparent bg-gradient-to-r from-accent to-purple-500">
                            Connection.
                        </span>
                    </h1>
                    <p className="text-2xl text-gray-300 max-w-2xl mx-auto font-light leading-relaxed">
                        Uni-Link isn't just a platform. It's a movement to dismantle the walls between disciplines, campuses, and ideas.
                    </p>
                </section>

                {/* The Story: Vertical Timeline */}
                <section className="relative border-l-2 border-white/10 ml-4 md:ml-0 md:pl-10 space-y-16">

                    {/* Timeline Item 1 */}
                    <div className="relative group">
                        <div className="absolute -left-[45px] top-2 w-6 h-6 rounded-full bg-black border-4 border-accent z-10 group-hover:scale-125 transition-transform duration-300 hidden md:block" />
                        <GlassCard className="p-8 md:p-10 relative overflow-hidden">
                            <div className="absolute top-0 right-0 p-6 opacity-5">
                                <FaLightbulb className="text-8xl" />
                            </div>
                            <span className="text-accent font-bold tracking-widest uppercase text-sm mb-2 block">The Spark</span>
                            <h2 className="text-3xl font-bold mb-4">It Started in a Dorm Room</h2>
                            <p className="text-gray-400 leading-relaxed">
                                Like all great ideas, Uni-Link was born from frustration. We saw brilliant students struggling to find collaborators for their passion projects. We realized that the university experience was fragmented—silos of knowledge that never touched. We decided to light a match.
                            </p>
                        </GlassCard>
                    </div>

                    {/* Timeline Item 2 */}
                    <div className="relative group">
                        <div className="absolute -left-[45px] top-2 w-6 h-6 rounded-full bg-black border-4 border-purple-500 z-10 group-hover:scale-125 transition-transform duration-300 hidden md:block" />
                        <GlassCard className="p-8 md:p-10 relative overflow-hidden">
                            <div className="absolute top-0 right-0 p-6 opacity-5">
                                <FaUsers className="text-8xl" />
                            </div>
                            <span className="text-purple-400 font-bold tracking-widest uppercase text-sm mb-2 block">The Mission</span>
                            <h2 className="text-3xl font-bold mb-4">Empowering the Collective</h2>
                            <p className="text-gray-400 leading-relaxed">
                                We believe that collaboration is the engine of innovation. Our mission is simple: to provide every student with the tools, network, and resources they need to turn "what if" into "what is." We are building the digital infrastructure for the next generation of leaders.
                            </p>
                        </GlassCard>
                    </div>

                    {/* Timeline Item 3 */}
                    <div className="relative group">
                        <div className="absolute -left-[45px] top-2 w-6 h-6 rounded-full bg-black border-4 border-highlight z-10 group-hover:scale-125 transition-transform duration-300 hidden md:block" />
                        <GlassCard className="p-8 md:p-10 relative overflow-hidden">
                            <div className="absolute top-0 right-0 p-6 opacity-5">
                                <FaRocket className="text-8xl" />
                            </div>
                            <span className="text-highlight font-bold tracking-widest uppercase text-sm mb-2 block">The Future</span>
                            <h2 className="text-3xl font-bold mb-4">Beyond Boundaries</h2>
                            <p className="text-gray-400 leading-relaxed">
                                We are just getting started. From AI-driven mentorship to cross-university research initiatives, we are constantly pushing the envelope. We envision a world where your potential is defined by your ambition, not your zip code.
                            </p>
                        </GlassCard>
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
                        Join Our Story
                    </button>
                </section>

            </main>
        </div>
    );
};

export default AboutUsPage;