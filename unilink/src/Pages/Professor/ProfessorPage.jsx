import React, { useState, useEffect } from "react";
import Header from "../../Components/Posts/Header";
import * as professorHandler from "../../../api/professorHandler";
import * as postHandler from "../../../api/postHandler";
import PostCard from "../../Components/Posts/PostCard";
import authHandler from "../../handlers/authHandler";

const ProfessorPage = () => {
    const [professor, setProfessor] = useState(null);
    const [stats, setStats] = useState(null);
    const [posts, setPosts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [activeTab, setActiveTab] = useState("overview"); // overview, reviews, qna
    const [currentUser, setCurrentUser] = useState(null);

    // Check authentication and get current user
    useEffect(() => {
        const checkAuth = async () => {
            const user = await authHandler.getCurrentUser();
            if (!user) {
                setError("Please log in to view your profile.");
                setLoading(false);
            } else {
                setCurrentUser(user);
            }
        };
        checkAuth();
    }, []);

    useEffect(() => {
        if (currentUser) {
            fetchData();
        }
    }, [currentUser]);

    const fetchData = async () => {
        try {
            setLoading(true);
            if (!currentUser?.id) return;

            const [profData, statsData, postsData] = await Promise.all([
                professorHandler.getProfessorById(currentUser.id),
                professorHandler.getDashboardStats(),
                postHandler.getAllPosts()
            ]);
            setProfessor(profData);
            setStats(statsData);
            setPosts(postsData);
        } catch (err) {
            console.error("Failed to fetch professor data:", err);
            setError("Failed to load professor profile.");
        } finally {
            setLoading(false);
        }
    };

    if (loading) return <div className="flex justify-center items-center h-screen bg-main text-main">Loading...</div>;
    if (error) return <div className="flex justify-center items-center h-screen bg-main text-red-500">{error}</div>;

    const projectPosts = posts.filter(p => p.category === 'Projects');
    const questionPosts = posts.filter(p => p.category === 'Questions');

    return (
        <div className="min-h-screen bg-main text-main font-main">
            <Header logoSize="large" />

            <div className="container mx-auto pt-24 px-4 md:px-8 max-w-7xl">
                {/* Profile Header */}
                <div className="bg-white/5 backdrop-blur-xl rounded-custom p-8 mb-8 border border-white/10 flex flex-col md:flex-row items-center gap-8">
                    <img
                        src={professor.profile_image || `https://ui-avatars.com/api/?name=${professor.username}`}
                        alt={professor.username}
                        className="w-32 h-32 rounded-full border-4 border-accent object-cover"
                    />
                    <div className="text-center md:text-left flex-grow">
                        <h1 className="text-3xl font-bold mb-2">{professor.username}</h1>
                        <p className="text-xl text-accent mb-1">{professor.academic_rank} • {professor.faculty_name}</p>
                        <p className="text-muted mb-4">{professor.major_name} Department</p>
                        <div className="flex flex-wrap gap-4 justify-center md:justify-start">
                            <span className="px-4 py-2 bg-white/10 rounded-full text-sm">📍 {professor.office_location}</span>
                            <span className="px-4 py-2 bg-white/10 rounded-full text-sm">📧 {professor.email}</span>
                        </div>
                    </div>

                    {/* Quick Stats */}
                    <div className="grid grid-cols-2 gap-4 text-center">
                        <div className="p-4 bg-white/5 rounded-lg">
                            <div className="text-2xl font-bold text-accent">{stats.stats.students}</div>
                            <div className="text-xs text-muted">Students</div>
                        </div>
                        <div className="p-4 bg-white/5 rounded-lg">
                            <div className="text-2xl font-bold text-accent">{stats.stats.totalUsers}</div>
                            <div className="text-xs text-muted">Total Users</div>
                        </div>
                    </div>
                </div>

                {/* Tabs */}
                <div className="flex gap-4 mb-8 border-b border-white/10 pb-4">
                    {['overview', 'reviews', 'qna'].map((tab) => (
                        <button
                            key={tab}
                            onClick={() => setActiveTab(tab)}
                            className={`px-6 py-2 rounded-full transition-all ${activeTab === tab
                                ? 'bg-accent text-white'
                                : 'bg-white/5 hover:bg-white/10 text-muted'
                                }`}
                        >
                            {tab.charAt(0).toUpperCase() + tab.slice(1)}
                        </button>
                    ))}
                </div>

                {/* Content Area */}
                <div className="space-y-8">
                    {activeTab === 'overview' && (
                        <div className="grid md:grid-cols-2 gap-8">
                            {/* Analytics Card */}
                            <div className="bg-white/5 p-6 rounded-custom border border-white/10">
                                <h3 className="text-xl font-bold mb-4">Student Analytics</h3>
                                <div className="space-y-4">
                                    {stats.facultyDistribution.map((fac, idx) => (
                                        <div key={idx} className="space-y-2">
                                            <div className="flex justify-between items-center font-medium">
                                                <span>{fac.faculty_name}</span>
                                                <span className="text-sm text-muted">{fac.student_count} Students</span>
                                            </div>

                                            {/* Major Breakdown */}
                                            <div className="pl-4 border-l-2 border-white/10 space-y-2">
                                                {stats.majorDistribution
                                                    .filter(m => m.faculty_name === fac.faculty_name)
                                                    .map((major, mIdx) => (
                                                        <div key={mIdx} className="flex justify-between items-center text-sm">
                                                            <span className="text-muted-foreground">{major.major_name}</span>
                                                            <div className="flex items-center gap-2">
                                                                <div className="w-24 h-1.5 bg-white/10 rounded-full overflow-hidden">
                                                                    <div
                                                                        className="h-full bg-accent/70"
                                                                        style={{ width: `${(major.student_count / fac.student_count) * 100}%` }}
                                                                    />
                                                                </div>
                                                                <span className="text-xs text-muted">{major.student_count}</span>
                                                            </div>
                                                        </div>
                                                    ))}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Recent Activity Placeholder */}
                            <div className="bg-white/5 p-6 rounded-custom border border-white/10">
                                <h3 className="text-xl font-bold mb-4">Weekly Activity</h3>
                                <div className="flex items-end justify-between h-48 gap-2">
                                    {stats.weeklyActivity.map((day, idx) => (
                                        <div key={idx} className="flex flex-col items-center gap-2 w-full">
                                            <div
                                                className="w-full bg-accent/20 hover:bg-accent/40 transition-colors rounded-t-lg"
                                                style={{ height: `${(day.count / stats.stats.totalUsers) * 100}%` }}
                                            />
                                            <span className="text-xs text-muted">{day.day}</span>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}

                    {activeTab === 'reviews' && (
                        <div className="space-y-6">
                            <h2 className="text-2xl font-bold mb-4">Student Projects to Review</h2>
                            {projectPosts.length === 0 ? (
                                <p className="text-muted">No projects found.</p>
                            ) : (
                                projectPosts.map(post => (
                                    <PostCard
                                        key={post.post_id}
                                        initialPost={post}
                                        onRefresh={fetchData}
                                        currentUserId={currentUser?.id}
                                    />
                                ))
                            )}
                        </div>
                    )}

                    {activeTab === 'qna' && (
                        <div className="space-y-6">
                            <h2 className="text-2xl font-bold mb-4">Student Questions</h2>
                            {questionPosts.length === 0 ? (
                                <p className="text-muted">No questions found.</p>
                            ) : (
                                questionPosts.map(post => (
                                    <PostCard
                                        key={post.post_id}
                                        initialPost={post}
                                        onRefresh={fetchData}
                                        currentUserId={currentUser?.id}
                                    />
                                ))
                            )}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ProfessorPage;
