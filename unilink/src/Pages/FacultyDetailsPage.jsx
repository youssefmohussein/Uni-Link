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
        let data = raw.data && !raw.name ? raw.data : raw;

        const normalized = {};
        Object.keys(data).forEach(key => {
            normalized[key.toLowerCase()] = data[key];
        });

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
                <div className="text-2xl">Loading details...</div>
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
                    <Link to="/faculties" className="bg-accent px-6 py-2 rounded-full hover:bg-accent-hover transition-colors">
                        Back to Faculties
                    </Link>
                </div>
            </div>
        );
    }

    return (
        <div className="relative w-full min-h-screen bg-black text-white overflow-hidden pt-24 px-6 md:px-20">
            <Navbar />

            <div className="max-w-5xl mx-auto pb-20">
                <Link to="/faculties" className="inline-flex items-center text-accent mb-8 hover:text-accent-alt transition-colors gap-2 group">
                    <FiArrowLeft className="group-hover:-translate-x-1 transition-transform" />
                    <span className="font-medium">Back to Faculties</span>
                </Link>

                <GlassCard className="p-12 w-full shadow-2xl border border-white/10">
                    {/* Header Section */}
                    <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 pb-8 border-b border-white/10">
                        <div>
                            <h1 className="text-6xl md:text-7xl font-extrabold bg-clip-text text-transparent bg-gradient-to-r from-white via-accent to-white/70 mb-4 leading-tight">
                                {faculty.name || faculty.faculty_name}
                            </h1>
                            <div className="flex items-center gap-3">
                                <span className="bg-accent px-5 py-2 rounded-full text-sm font-semibold">
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
                        <div className="mb-12">
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
                                        className="bg-white/5 border border-white/10 px-6 py-3 rounded-2xl hover:bg-accent/10 hover:border-accent/30 transition-colors cursor-default flex items-center gap-3"
                                    >
                                        <div className="w-2.5 h-2.5 rounded-full bg-accent"></div>
                                        <span className="text-gray-300 font-medium">{major}</span>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Stats Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 pt-10 border-t border-white/10">
                        {/* Majors Card */}
                        <div className="bg-white/5 border border-[#58a6ff]/20 p-8 rounded-3xl text-center hover:bg-white/10 transition-colors cursor-default">
                            <div className="text-5xl font-extrabold text-accent mb-3">
                                {faculty.major_count || 0}
                            </div>
                            <div className="text-gray-400 uppercase tracking-widest text-sm font-semibold">Majors</div>
                        </div>

                        {/* Professors Card */}
                        <div className="bg-white/5 border border-[#ffb547]/20 p-8 rounded-3xl text-center hover:bg-white/10 transition-colors cursor-default">
                            <div className="text-5xl font-extrabold text-[#ffb547] mb-3">
                                {faculty.professor_count || 0}
                            </div>
                            <div className="text-gray-400 uppercase tracking-widest text-sm font-semibold">Professors</div>
                        </div>

                        {/* Students Card */}
                        <div className="bg-white/5 border border-purple-500/20 p-8 rounded-3xl text-center hover:bg-white/10 transition-colors cursor-default">
                            <div className="text-5xl font-extrabold text-purple-400 mb-3">
                                {faculty.student_count || 0}
                            </div>
                            <div className="text-gray-400 uppercase tracking-widest text-sm font-semibold">Enrolled Students</div>
                        </div>
                    </div>
                </GlassCard>
            </div>
        </div>
    );
};

export default FacultyDetailsPage;
