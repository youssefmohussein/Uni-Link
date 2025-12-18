import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import Navbar from '../Components/Navbar';
import GlassCard from '../Components/GlassCard';
import { getFacultyById } from '../../api/facultyandmajorHandler';
import { FiArrowLeft } from 'react-icons/fi';

const FacultyDetailsPage = () => {
    const { id } = useParams();
    const [faculty, setFaculty] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const fetchFaculty = async () => {
            try {
                setLoading(true);
                const data = await getFacultyById(id);
                setFaculty(data);
            } catch (err) {
                console.error('Error fetching faculty details:', err);
                setError(err.message || 'Failed to load faculty details');
            } finally {
                setLoading(false);
            }
        };

        if (id) {
            fetchFaculty();
        }
    }, [id]);

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
                    <Link to="/faculties" className="bg-[#008080] px-6 py-2 rounded-full hover:bg-[#006666] transition-all">
                        Back to Faculties
                    </Link>
                </div>
            </div>
        );
    }

    return (
        <div className="relative w-full min-h-screen bg-black text-white overflow-x-hidden pt-24 px-6 md:px-20">
            <Navbar />

            <div className="max-w-4xl mx-auto">
                <Link to="/faculties" className="flex items-center text-[#008080] mb-8 hover:underline gap-2">
                    <FiArrowLeft /> Back to Faculties
                </Link>

                <GlassCard className="p-10 w-full animate-fade-in-up">
                    <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                        <h1 className="text-5xl md:text-6xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-white to-white/50">
                            {faculty.name || faculty.faculty_name}
                        </h1>
                        <div className="mt-4 md:mt-0 flex gap-4">
                            {/* Badges or extra info could go here */}
                            <span className="bg-[#008080]/20 border border-[#008080] px-4 py-1 rounded-full text-sm">
                                Faculty
                            </span>
                        </div>
                    </div>

                    <div className="mb-10 text-xl text-gray-300 leading-relaxed min-h-[100px]">
                        {faculty.description || 'No description available for this faculty.'}
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 pt-8 border-t border-white/10">
                        <div className="bg-white/5 p-6 rounded-2xl text-center hover:bg-white/10 transition-all cursor-default">
                            <div className="text-4xl font-bold text-[#008080] mb-2">{faculty.major_count || 0}</div>
                            <div className="text-gray-400 uppercase tracking-widest text-sm">Active Programs</div>
                        </div>

                        <div className="bg-white/5 p-6 rounded-2xl text-center hover:bg-white/10 transition-all cursor-default">
                            <div className="text-4xl font-bold text-[#ffb547] mb-2">{faculty.professor_count || 0}</div>
                            <div className="text-gray-400 uppercase tracking-widest text-sm">Professors</div>
                        </div>

                        <div className="bg-white/5 p-6 rounded-2xl text-center hover:bg-white/10 transition-all cursor-default">
                            <div className="text-4xl font-bold text-purple-400 mb-2">{faculty.student_count || 0}</div>
                            <div className="text-gray-400 uppercase tracking-widest text-sm">Enrolled Students</div>
                        </div>
                    </div>

                    {/* Future: List of Majors or Professors */}
                </GlassCard>
            </div>
        </div>
    );
};

export default FacultyDetailsPage;
