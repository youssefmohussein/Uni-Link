import React, { Suspense, lazy, useState, useEffect } from 'react';
import PlanetScene3D from '../Components/PlanetScene3D';
import GlassCard from '../Components/GlassCard';
import Navbar from '../Components/Navbar';
import { getAllFaculties } from '../../api/facultyandmajorHandler';

// Lazy load Galaxy background
const Galaxy = lazy(() => import('../Animations/Galaxy/Galaxy'));

const FacultiesPage = () => {
    const [faculties, setFaculties] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchFaculties = async () => {
            try {
                setLoading(true);
                const data = await getAllFaculties();
                setFaculties(data);
                setError(null);
            } catch (err) {
                console.error('Error fetching faculties:', err);
                setError(err.message || 'Failed to load faculties');
            } finally {
                setLoading(false);
            }
        };

        fetchFaculties();
    }, []);

    if (loading) {
        return (
            <div className="relative w-full min-h-screen bg-black text-white overflow-x-hidden flex items-center justify-center">
                <Navbar />
                <div className="text-2xl">Loading faculties...</div>
            </div>
        );
    }

    if (error) {
        return (
            <div className="relative w-full min-h-screen bg-black text-white overflow-x-hidden flex items-center justify-center">
                <Navbar />
                <div className="text-center">
                    <h2 className="text-3xl font-bold mb-4">Error Loading Faculties</h2>
                    <p className="text-red-400">{error}</p>
                </div>
            </div>
        );
    }

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

            {/* 3D Planet Scene */}
            <PlanetScene3D facultiesCount={faculties.length}>
                <main className="w-full relative z-10">
                    {faculties.map((faculty, index) => {
                        const isEven = index % 2 === 0;

                        return (
                            <section
                                key={faculty.faculty_id || index}
                                className="h-screen flex items-center px-10 md:px-20 max-w-7xl mx-auto"
                            >
                                <div className={`w-full flex items-center ${isEven ? 'justify-end' : 'justify-start'}`}>
                                    <GlassCard className={`p-10 max-w-2xl ${isEven ? 'text-left' : 'text-left'}`}>
                                        <h2 className="text-5xl md:text-6xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-white to-white/50">
                                            {faculty.faculty_name || `Faculty ${index + 1}`}
                                        </h2>

                                        <p className="text-xl text-gray-300 mb-8 leading-relaxed">
                                            {faculty.description || 'Discover excellence in education and research. Join a community of innovators, thinkers, and leaders shaping the future.'}
                                        </p>

                                        <div className="flex flex-col sm:flex-row gap-4">
                                            <button className="bg-[#008080] hover:bg-[#006666] text-white font-bold py-3 px-8 rounded-full text-lg transition-all shadow-[0_0_20px_rgba(0,128,128,0.5)] hover:shadow-[0_0_40px_rgba(0,128,128,0.7)]">
                                                Explore Faculty
                                            </button>
                                            <button className="border-2 border-[#008080] hover:bg-[#008080]/20 text-white font-bold py-3 px-8 rounded-full text-lg transition-all">
                                                Learn More
                                            </button>
                                        </div>

                                        {/* Faculty Stats */}
                                        <div className="mt-8 pt-8 border-t border-white/20 grid grid-cols-3 gap-4 text-center">
                                            <div>
                                                <div className="text-3xl font-bold text-[#008080]">50+</div>
                                                <div className="text-sm text-gray-400 mt-1">Programs</div>
                                            </div>
                                            <div>
                                                <div className="text-3xl font-bold text-[#ffb547]">100+</div>
                                                <div className="text-sm text-gray-400 mt-1">Faculty</div>
                                            </div>
                                            <div>
                                                <div className="text-3xl font-bold text-purple-400">5000+</div>
                                                <div className="text-sm text-gray-400 mt-1">Students</div>
                                            </div>
                                        </div>
                                    </GlassCard>
                                </div>
                            </section>
                        );
                    })}

                    {/* Final CTA Section */}
                    <section className="h-screen flex flex-col justify-center items-center px-10 text-center">
                        <GlassCard className="p-12 max-w-3xl w-full">
                            <h2 className="text-4xl md:text-5xl font-bold mb-8">Find Your Path</h2>
                            <p className="text-xl text-gray-300 mb-10">
                                Explore all our faculties and discover where your passion leads you.
                            </p>
                            <button className="bg-[#008080] hover:bg-[#006666] text-white font-bold py-4 px-10 rounded-full text-xl transition-all shadow-[0_0_20px_rgba(0,128,128,0.5)] hover:shadow-[0_0_40px_rgba(0,128,128,0.7)]">
                                Apply Now
                            </button>
                        </GlassCard>
                    </section>
                </main>
            </PlanetScene3D>
        </div>
    );
};

export default FacultiesPage;
