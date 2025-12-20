import React, { Suspense, lazy, useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import PlanetScene3D from '../Components/PlanetScene3D';
import GlassCard from '../Components/GlassCard';
import Navbar from '../Components/Navbar';
import { getAllFaculties } from '../../api/facultyandmajorHandler';

// Lazy load Galaxy background
const Galaxy = lazy(() => import('../Animations/Galaxy/Galaxy'));

const FacultiesPage = () => {
    const navigate = useNavigate();
    const [faculties, setFaculties] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const normalizeFaculties = (data) => {
        if (!Array.isArray(data)) return [];
        return data.map(item => {
            const normalized = {};
            Object.keys(item).forEach(key => {
                normalized[key.toLowerCase()] = item[key];
            });
            // Fallbacks
            normalized.name = normalized.name || normalized.faculty_name || 'Faculty';
            normalized.major_count = normalized.major_count || 0;
            return normalized;
        });
    };

    useEffect(() => {
        const fetchFaculties = async () => {
            try {
                setLoading(true);
                const rawData = await getAllFaculties();
                const normalized = normalizeFaculties(rawData);
                setFaculties(normalized);
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
        <div className="relative w-full min-h-screen bg-black text-white overflow-hidden">
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
                                    <GlassCard className={`p-8 max-w-2xl shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500 ${isEven ? 'text-left' : 'text-left'}`}>
                                        <h2 className="text-4xl md:text-5xl font-bold mb-6 bg-clip-text text-transparent bg-gradient-to-r from-white via-accent to-white/70 leading-tight">
                                            {faculty.name || faculty.faculty_name || `Faculty ${index + 1}`}
                                        </h2>

                                        <div className="flex flex-col sm:flex-row gap-4 mb-8">
                                            <button
                                                onClick={() => {
                                                    let name = faculty.name || '';
                                                    let slug = name.replace(/\s+/g, '-');
                                                    if (!slug.startsWith('Faculty-of-')) {
                                                        slug = `Faculty-of-${slug}`;
                                                    }
                                                    navigate(`/faculty/${encodeURIComponent(slug)}`);
                                                }}
                                                className="bg-accent hover:bg-accent-hover text-white font-bold py-3 px-8 rounded-full text-base transition-all shadow-[0_0_30px_rgba(88,166,255,0.5)] hover:shadow-[0_0_50px_rgba(88,166,255,0.8)] text-center transform hover:scale-105 duration-300"
                                            >
                                                Learn More
                                            </button>
                                        </div>

                                        <div className="flex items-center justify-between gap-8 border-t border-white/10 pt-6">
                                            <div className="group relative cursor-default flex-1 text-center">
                                                <div className="text-3xl font-bold text-accent group-hover:scale-110 transition-transform duration-300">
                                                    {faculty.major_count || 0}
                                                </div>
                                                <div className="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wider">Majors</div>

                                                {/* Enhanced Majors Hover Tooltip */}
                                                {faculty.major_names && (
                                                    <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-6 w-64 p-6 bg-gradient-to-br from-gray-900 to-black border-2 border-accent/40 rounded-3xl backdrop-blur-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-500 z-50 shadow-[0_25px_60px_rgba(88,166,255,0.4)] pointer-events-none">
                                                        <div className="text-accent font-bold mb-3 text-sm uppercase tracking-wider border-b border-accent/20 pb-2 flex items-center gap-2">
                                                            <span className="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                                                            Specializations
                                                        </div>
                                                        <div className="space-y-2">
                                                            {String(faculty.major_names || '').split(',').map(m => m.trim()).filter(m => m).slice(0, 6).map((major, idx) => (
                                                                <div key={idx} className="text-gray-300 text-sm flex items-center gap-3 p-1">
                                                                    <div className="w-2 h-2 rounded-full bg-accent shadow-lg shadow-accent/50"></div>
                                                                    <span className="font-medium">{major}</span>
                                                                </div>
                                                            ))}
                                                            {String(faculty.major_names || '').split(',').map(m => m.trim()).filter(m => m).length > 6 && (
                                                                <div className="text-gray-400 text-xs text-center pt-2 border-t border-white/10">
                                                                    +{String(faculty.major_names || '').split(',').map(m => m.trim()).filter(m => m).length - 6} more
                                                                </div>
                                                            )}
                                                        </div>
                                                        {/* Enhanced Arrow */}
                                                        <div className="absolute -bottom-2.5 left-1/2 -translate-x-1/2 w-5 h-5 bg-gradient-to-br from-gray-900 to-black border-r-2 border-b-2 border-accent/40 rotate-45"></div>
                                                    </div>
                                                )}
                                            </div>

                                            <div className="flex-1 text-center">
                                                <div className="text-3xl font-bold text-[#ffb547] hover:scale-110 transition-transform duration-300">{faculty.professor_count || 0}</div>
                                                <div className="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wider">Professors</div>
                                            </div>

                                            <div className="flex-1 text-center">
                                                <div className="text-3xl font-bold text-purple-400 hover:scale-110 transition-transform duration-300">{faculty.student_count || 0}</div>
                                                <div className="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wider">Students</div>
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
                            <button className="bg-accent hover:bg-accent-hover text-white font-bold py-4 px-10 rounded-full text-xl transition-all shadow-[0_0_20px_rgba(88,166,255,0.5)] hover:shadow-[0_0_40px_rgba(88,166,255,0.7)]">
                                Apply Now
                            </button>
                        </GlassCard>
                    </section>
                </main>
            </PlanetScene3D>

            <style jsx>{`
                .custom-scrollbar::-webkit-scrollbar {
                    width: 6px;
                }
                .custom-scrollbar::-webkit-scrollbar-track {
                    background: rgba(255, 255, 255, 0.05);
                    border-radius: 10px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: #58a6ff;
                    border-radius: 10px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: #3b82f6;
                }
            `}</style>
        </div>
    );
};

export default FacultiesPage;
