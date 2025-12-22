import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import LeftSidebar from '../../Components/Posts/LeftSidebar';
import RightSidebar from '../../Components/Posts/RightSidebar';
import Header from '../../Components/Posts/Header';
import GlassSurface from '../../Components/Login_Components/LiquidGlass/GlassSurface';

const LeaderboardPage = () => {
    const [leaderboardData, setLeaderboardData] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [currentFilter, setCurrentFilter] = useState('Leaderboard');
    const [searchQuery, setSearchQuery] = useState('');
    const navigate = useNavigate();

    useEffect(() => {
        fetchLeaderboard();
    }, []);

    const fetchLeaderboard = async () => {
        try {
            const response = await fetch('http://localhost/backend/api/leaderboard?limit=20', {
                credentials: 'include'
            });
            const result = await response.json();

            if (result.status === 'success') {
                // Ensure data is an array before setting
                if (Array.isArray(result.data)) {
                    setLeaderboardData(result.data);
                } else {
                    console.error("Leaderboard data is not an array:", result.data);
                    setLeaderboardData([]);
                }
            } else {
                setError('Failed to load leaderboard data');
            }
        } catch (err) {
            console.error('Error fetching leaderboard:', err);
            setError('Error connecting to server');
        } finally {
            setLoading(false);
        }
    };

    const handleFilterChange = (filter) => {
        if (filter === 'Leaderboard') return;

        switch (filter) {
            case 'all': navigate('/home'); break;
            case 'project-rooms': navigate('/project-rooms'); break;
            case 'Questions': navigate('/questions'); break;
            default: navigate('/home'); // Fallback
        }
    };

    const handleSearch = (query) => {
        setSearchQuery(query);
    };

    const getMedalColor = (index) => {
        switch (index) {
            case 0: return 'text-yellow-400'; // Gold
            case 1: return 'text-gray-300';   // Silver
            case 2: return 'text-amber-600';  // Bronze
            default: return 'text-blue-400';
        }
    };

    const getPositionStyle = (index) => {
        if (index < 3) return 'font-bold scale-110';
        return 'font-medium';
    };

    return (
        <div className="min-h-screen bg-[#0d1117] font-main text-[#e6edf3]">
            <Header onSearch={handleSearch} searchQuery={searchQuery} />

            <main className="max-w-7xl mx-auto pt-24 pb-10 px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between items-start gap-6">
                    {/* Left Sidebar */}
                    <div className="hidden lg:block w-64 flex-shrink-0">
                        <LeftSidebar currentFilter={currentFilter} onFilterChange={handleFilterChange} />
                    </div>

                    {/* Main Content */}
                    <div className="flex-grow max-w-2xl mx-auto w-full">
                        <GlassSurface
                            width="100%"
                            height="auto"
                            borderRadius={24}
                            opacity={0.6}
                            blur={12}
                            borderWidth={0.05}
                            className="bg-gradient-to-br from-[#161b22]/90 to-[#0d1117]/90 p-6 md:p-8"
                        >
                            <div className="w-full relative z-10">
                                <header className="text-center mb-10">
                                    <h1 className="text-3xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 inline-block mb-3">
                                        Community Leaderboard
                                    </h1>
                                    <p className="text-gray-400 text-sm">Top contributors and active students</p>
                                </header>

                                {loading ? (
                                    <div className="flex justify-center items-center py-20">
                                        <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                                    </div>
                                ) : error ? (
                                    <div className="text-center py-10 bg-red-500/10 rounded-xl border border-red-500/20">
                                        <p className="text-red-400"><i className="fas fa-exclamation-circle mr-2"></i>{error}</p>
                                        <button
                                            onClick={fetchLeaderboard}
                                            className="mt-4 px-4 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-300 rounded-lg transition-colors text-sm"
                                        >
                                            Try Again
                                        </button>
                                    </div>
                                ) : (
                                    <div className="space-y-4">
                                        {/* Table Header */}
                                        <div className="grid grid-cols-12 gap-4 px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-700/50">
                                            <div className="col-span-1 text-center">#</div>
                                            <div className="col-span-7">User</div>
                                            <div className="col-span-4 text-right">Points</div>
                                        </div>

                                        {leaderboardData.length === 0 ? (
                                            <div className="text-center py-12 text-gray-500">
                                                <i className="fas fa-users text-4xl mb-3 opacity-50"></i>
                                                <p>No data available yet.</p>
                                            </div>
                                        ) : (
                                            leaderboardData.map((user, index) => (
                                                <div
                                                    key={user.student_id}
                                                    className={`
                                                        grid grid-cols-12 gap-4 px-6 py-4 items-center rounded-xl transition-all duration-300
                                                        ${index < 3 ? 'bg-gradient-to-r from-white/5 to-transparent border border-white/5' : 'hover:bg-white/5'}
                                                    `}
                                                >
                                                    {/* Rank */}
                                                    <div className="col-span-1 flex justify-center">
                                                        <span className={`text-lg ${getMedalColor(index)} ${getPositionStyle(index)}`}>
                                                            {index < 3 ? <i className="fas fa-crown"></i> : index + 1}
                                                        </span>
                                                    </div>

                                                    {/* User Info */}
                                                    <div className="col-span-7 flex items-center space-x-4">
                                                        <div className="relative group cursor-pointer" onClick={() => navigate(`/profile/${user.user_id}`)}>
                                                            <div className={`absolute -inset-0.5 rounded-full opacity-75 blur-[2px] transition duration-200 group-hover:opacity-100 ${index === 0 ? 'bg-gradient-to-r from-yellow-400 to-amber-500' :
                                                                index === 1 ? 'bg-gradient-to-r from-gray-300 to-gray-100' :
                                                                    index === 2 ? 'bg-gradient-to-r from-amber-700 to-amber-500' :
                                                                        'bg-transparent'
                                                                }`}></div>
                                                            <img
                                                                src={user.profile_image || `https://placehold.co/100x100/1f2937/9ca3af?text=${user.username?.[0]?.toUpperCase()}`}
                                                                alt={user.username}
                                                                className="relative w-10 h-10 rounded-full object-cover border-2 border-[#161b22]"
                                                            />
                                                            {index < 3 && (
                                                                <div className="absolute -top-1 -right-1 bg-[#161b22] rounded-full p-[2px]">
                                                                    <div className={`w-3 h-3 rounded-full flex items-center justify-center text-[8px] font-bold text-[#161b22] ${index === 0 ? 'bg-yellow-400' : index === 1 ? 'bg-gray-300' : 'bg-amber-600'
                                                                        }`}>
                                                                        {index + 1}
                                                                    </div>
                                                                </div>
                                                            )}
                                                        </div>
                                                        <div className="min-w-0">
                                                            <h3 className="text-sm font-bold text-white truncate hover:text-blue-400 cursor-pointer transition-colors" onClick={() => navigate(`/profile/${user.user_id}`)}>
                                                                {user.username}
                                                            </h3>
                                                            <p className="text-xs text-gray-500 truncate">
                                                                {user.major_name || 'Student'} • {user.faculty_name || 'Faculty'}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    {/* Points */}
                                                    <div className="col-span-4 text-right">
                                                        <div className="inline-flex items-center space-x-2 bg-blue-500/10 px-3 py-1 rounded-full border border-blue-500/20">
                                                            <i className="fas fa-star text-yellow-400 text-xs shadow-glow"></i>
                                                            <span className="font-bold text-blue-300 text-sm">{user.points || 0} XP</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            ))
                                        )}
                                    </div>
                                )}
                            </div>
                        </GlassSurface>
                    </div>

                    {/* Right Sidebar */}
                    <div className="hidden xl:block w-72 flex-shrink-0 ml-6">
                        <RightSidebar currentFilter={currentFilter} onFilterChange={handleFilterChange} />
                    </div>
                </div>
            </main>
        </div>
    );
};

export default LeaderboardPage;
