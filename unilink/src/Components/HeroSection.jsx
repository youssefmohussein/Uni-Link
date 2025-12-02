import React from 'react';
import GlassCard from './GlassCard';

const HeroSection = ({ className = '', style = {} }) => {
    return (
        <section className={`h-screen flex flex-col justify-center items-start px-10 md:px-20 max-w-7xl mx-auto pt-20 ${className}`} style={style}>
            <div className="max-w-2xl">
                <h1
                    className="text-6xl md:text-8xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-white to-white/50"
                    style={{ transform: 'translateZ(0)' }}
                >
                    Uni-Link
                </h1>
                <p className="text-xl md:text-2xl text-gray-300 mb-8 leading-relaxed">
                    Your College Journey, Simplified.
                    <br />
                    <span className="text-[#008080]"> The all-in-one platform where students connect, collaborate, and conquer projects.</span>
                </p>
                <GlassCard className="inline-block px-8 py-4 cursor-pointer">
                    <span className="text-lg font-semibold tracking-wide">Get Started</span>
                </GlassCard>
            </div>
        </section>
    );
};

export default HeroSection;
