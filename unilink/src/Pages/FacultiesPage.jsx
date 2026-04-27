import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import GlassCard from '../Components/GlassCard';
import Navbar from '../Components/Navbar';
import { getAllFaculties } from '../../api/facultyandmajorHandler';

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
            <Navbar />

            <main className="w-full pt-24 px-6 md:px-20 max-w-7xl mx-auto pb-20">
                <h1 className="text-4xl md:text-5xl font-bold mb-12 text-center">Our Faculties</h1>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {faculties.map((faculty, index) => (
                        <GlassCard
                            key={faculty.faculty_id || index}
                            className="p-8 border border-white/10"
                        >
                            <h2 className="text-3xl md:text-4xl font-bold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-white via-accent to-white/70 leading-tight">
                                {faculty.name || faculty.faculty_name || `Faculty ${index + 1}`}
                            </h2>

                            <div className="flex items-center justify-between gap-8 border-t border-white/10 pt-6 mb-6">
                                <div className="group relative cursor-default flex-1 text-center">
                                    <div className="text-3xl font-bold text-accent">
                                        {faculty.major_count || 0}
                                    </div>
                                    <div className="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wider">Majors</div>
                                </div>

                                <div className="flex-1 text-center">
                                    <div className="text-3xl font-bold text-[#ffb547]">{faculty.professor_count || 0}</div>
                                    <div className="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wider">Professors</div>
                                </div>

                                <div className="flex-1 text-center">
                                    <div className="text-3xl font-bold text-purple-400">{faculty.student_count || 0}</div>
                                    <div className="text-xs text-gray-400 mt-1 font-medium uppercase tracking-wider">Students</div>
                                </div>
                            </div>

                            <button
                                onClick={() => {
                                    let name = faculty.name || '';
                                    let slug = name.replace(/\s+/g, '-');
                                    if (!slug.startsWith('Faculty-of-')) {
                                        slug = `Faculty-of-${slug}`;
                                    }
                                    navigate(`/faculty/${encodeURIComponent(slug)}`);
                                }}
                                className="w-full bg-accent hover:bg-accent-hover text-white font-bold py-3 px-8 rounded-full text-base transition-colors text-center"
                            >
                                Learn More
                            </button>
                        </GlassCard>
                    ))}
                </div>

                {/* CTA Section */}
                <section className="flex flex-col justify-center items-center pt-24 text-center">
                    <GlassCard className="p-12 max-w-3xl w-full">
                        <h2 className="text-4xl md:text-5xl font-bold mb-8">Find Your Path</h2>
                        <p className="text-xl text-gray-300 mb-10">
                            Explore all our faculties and discover where your passion leads you.
                        </p>
                        <button className="bg-accent hover:bg-accent-hover text-white font-bold py-4 px-10 rounded-full text-xl transition-colors">
                            Apply Now
                        </button>
                    </GlassCard>
                </section>
            </main>
        </div>
    );
};

export default FacultiesPage;
