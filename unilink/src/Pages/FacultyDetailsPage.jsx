import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import Navbar from '../Components/Navbar';
import GlassCard from '../Components/GlassCard';
import { getFacultyById } from '../../api/facultyandmajorHandler';
import { FiArrowLeft } from 'react-icons/fi';

const FacultyDetailsPage = () => {
    const { idOrName } = useParams();
    const [faculty, setFaculty] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const normalizeFaculty = (raw) => {
        if (!raw) return null;
        // Handle double wrapping { status: 'success', data: { ... } }
        let data = raw.data && !raw.name ? raw.data : raw;

        // Normalize keys to lowercase for internal use
        const normalized = {};
        Object.keys(data).forEach(key => {
            normalized[key.toLowerCase()] = data[key];
        });

        // Ensure critical fields have fallbacks
        normalized.name = normalized.name || normalized.faculty_name || 'Faculty';
        normalized.description = normalized.description || normalized.desc || '';
        normalized.major_count = normalized.major_count || 0;
        normalized.student_count = normalized.student_count || 0;
        normalized.professor_count = normalized.professor_count || 0;

        return normalized;
    };

    useEffect(() => {
        const fetchFaculty = async () => {
            try {
                setLoading(true);
                const rawData = await getFacultyById(idOrName);
                const normalized = normalizeFaculty(rawData);
                setFaculty(normalized);
            } catch (err) {
                console.error('Error fetching faculty details:', err);
                setError(err.message || 'Failed to load faculty details');
            } finally {
                setLoading(false);
            }
        };

        if (idOrName) {
            fetchFaculty();
        }
    }, [idOrName]);

    if (loading) {
        return (
            <div className="relative w-full min-h-screen bg-black text-white overflow-x-hidden flex items-center justify-center">
                <Navbar />
                <div className="text-2xl animate-pulse">Loading details...</div>
            </div>
        );
    }

    if (error || !faculty) {
        return (
            <div className="relative w-full min-h-screen bg-black text-white overflow-x-hidden flex items-center justify-center">
                <Navbar />
                <div className="text-center">
                    <h2 className="text-3xl font-bold mb-4">Error</h2>
                    <p className="text-red-400 mb-6">{error || 'Faculty not found'}</p>
                    <Link to="/faculties" className="bg-accent px-6 py-2 rounded-full hover:bg-accent-hover transition-all">
                        Back to Faculties
                    </Link>
                </div>
            </div>
        );
    }

    return (
        <div className="relative w-full min-h-screen bg-gradient-to-br from-black via-gray-900 to-black text-white overflow-hidden pt-24 px-6 md:px-20">
            {/* Animated Background Elements */}
            <div className="fixed inset-0 overflow-hidden pointer-events-none">
                <div className="absolute top-1/4 left-1/4 w-96 h-96 bg-accent/10 rounded-full blur-[100px] animate-pulse"></div>
                <div className="absolute bottom-1/4 right-1/4 w-96 h-96 bg-purple-500/10 rounded-full blur-[100px] animate-pulse" style={{ animationDelay: '1s' }}></div>
            </div>

            <Navbar />

            <div className="max-w-5xl mx-auto relative z-10">
                <Link to="/faculties" className="inline-flex items-center text-accent mb-8 hover:text-accent-alt transition-colors gap-2 group">
                    <FiArrowLeft className="group-hover:-translate-x-1 transition-transform" />
                    <span className="font-medium">Back to Faculties</span>
                </Link>

                <GlassCard className="p-12 w-full animate-fade-in-up shadow-2xl border border-white/10 hover:border-white/20 transition-all duration-500">
                    {/* Header Section */}
                    <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 pb-8 border-b border-white/10">
                        <div>
                            <h1 className="text-6xl md:text-7xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-white via-accent to-white/70 mb-4 leading-tight">
                                {faculty.name || faculty.faculty_name}
                            </h1>
                            <div className="flex items-center gap-3">
                                <span className="bg-accent px-5 py-2 rounded-full text-sm font-semibold shadow-lg shadow-accent/30">
                                    Faculty
                                </span>
                                <span className="text-gray-400 text-sm">•</span>
                                <span className="text-gray-400 text-sm font-medium">{faculty.major_count || 0} Programs</span>
                            </div>
                        </div>
                    </div>

                    {/* Description Section */}
                    <div className="mb-12 text-lg text-gray-300 leading-relaxed min-h-[100px] whitespace-pre-line bg-white/5 p-6 rounded-2xl border border-white/10">
                        <p className="text-xl leading-relaxed">
                            {faculty.description || 'No description available for this faculty.'}
                        </p>
                    </div>

                    {/* Majors List Section */}
                    {faculty.major_names && (
                        <div className="mb-12 animate-fade-in-up" style={{ animationDelay: '200ms' }}>
                            <h3 className="text-3xl font-bold mb-6 flex items-center gap-3">
                                <span className="w-12 h-1.5 bg-accent rounded-full"></span>
                                <span className="bg-clip-text text-transparent bg-gradient-to-r from-white to-gray-400">
                                    Featured Majors
                                </span>
                            </h3>
                            <div className="flex flex-wrap gap-4">
                                {faculty.major_names && String(faculty.major_names || '').split(',').map(m => m.trim()).filter(m => m).map((major, i) => (
                                    <div
                                        key={i}
                                        className="bg-gradient-to-br from-white/10 to-white/5 border border-white/20 px-6 py-3 rounded-2xl hover:bg-gradient-to-br hover:from-accent/20 hover:to-accent/10 hover:border-accent/50 hover:shadow-lg hover:shadow-accent/20 transition-all duration-300 cursor-default group flex items-center gap-3"
                                    >
                                        <div className="w-2.5 h-2.5 rounded-full bg-accent group-hover:shadow-lg group-hover:shadow-accent/50 transition-all"></div>
                                        <span className="text-gray-300 group-hover:text-white transition-colors font-medium">{major}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Stats Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 pt-10 border-t border-white/10">
                        {/* Majors Card with Hover Tooltip */}
                        <div className="group relative bg-gradient-to-br from-[#58a6ff]/20 to-transparent border border-[#58a6ff]/30 p-8 rounded-3xl text-center hover:from-[#58a6ff]/30 hover:border-[#58a6ff]/50 hover:shadow-2xl hover:shadow-[#58a6ff]/20 transition-all duration-500 cursor-default transform hover:-translate-y-1">
                            <div className="text-5xl font-extrabold text-accent mb-3 group-hover:scale-110 transition-transform duration-300">
                                {faculty.major_count || 0}
                            </div>
                            <div className="text-gray-400 uppercase tracking-widest text-sm font-semibold">Majors</div>

                            {/* Enhanced Majors Hover Tooltip */}
                            {faculty.major_names && (
                                <div className="absolute bottom-full left-1/2 -translate-x-1/2 mb-6 w-72 p-6 bg-gradient-to-br from-gray-900 to-black border-2 border-[#58a6ff]/40 rounded-3xl backdrop-blur-2xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-500 z-[100] shadow-[0_25px_60px_rgba(88,166,255,0.4)] pointer-events-none">
                                    <h4 className="text-accent font-bold mb-4 text-base uppercase tracking-widest flex items-center gap-2">
                                        <span className="w-2 h-2 rounded-full bg-accent animate-pulse"></span>
                                        Available Majors
                                    </h4>
                                    <div className="text-sm text-gray-300 space-y-2 text-left">
                                        {String(faculty.major_names || '').split(',').map(m => m.trim()).filter(m => m).slice(0, 6).map((major, i) => (
                                            <div key={i} className="flex items-center gap-3 p-1">
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
                                    <div className="absolute -bottom-3 left-1/2 -translate-x-1/2 w-6 h-6 bg-gradient-to-br from-gray-900 to-black border-r-2 border-b-2 border-[#58a6ff]/40 rotate-45"></div>
                                </div>
                            )}
                        </div>

                        {/* Professors Card */}
                        <div className="bg-gradient-to-br from-[#ffb547]/10 to-transparent border border-[#ffb547]/30 p-8 rounded-3xl text-center hover:from-[#ffb547]/20 hover:border-[#ffb547]/50 hover:shadow-2xl hover:shadow-[#ffb547]/20 transition-all duration-500 cursor-default transform hover:-translate-y-1">
                            <div className="text-5xl font-extrabold text-[#ffb547] mb-3 hover:scale-110 transition-transform duration-300">
                                {faculty.professor_count || 0}
                            </div>
                            <div className="text-gray-400 uppercase tracking-widest text-sm font-semibold">Professors</div>
                        </div>

                        {/* Students Card */}
                        <div className="bg-gradient-to-br from-purple-500/10 to-transparent border border-purple-500/30 p-8 rounded-3xl text-center hover:from-purple-500/20 hover:border-purple-500/50 hover:shadow-2xl hover:shadow-purple-500/20 transition-all duration-500 cursor-default transform hover:-translate-y-1">
                            <div className="text-5xl font-extrabold text-purple-400 mb-3 hover:scale-110 transition-transform duration-300">
                                {faculty.student_count || 0}
                            </div>
                            <div className="text-gray-400 uppercase tracking-widest text-sm font-semibold">Enrolled Students</div>
                        </div>
                    </div>
                </GlassCard>
            </div>

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

export default FacultyDetailsPage;
