import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import Header from "../../Components/Posts/Header";
import * as professorHandler from "../../../api/professorHandler";
import * as postHandler from "../../../api/postHandler";
import * as gradingHandler from "../../../api/gradingHandler";
import PostCard from "../../Components/Posts/PostCard";
import GradeModal from "../../Components/Professor/GradeModal";
import authHandler from "../../handlers/authHandler";

const ProfessorPage = () => {
    const navigate = useNavigate();
    const [professor, setProfessor] = useState(null);
    const [stats, setStats] = useState(null);
    const [posts, setPosts] = useState([]);
    const [projects, setProjects] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [activeTab, setActiveTab] = useState("overview"); // overview, grading, qna
    const [gradeFilter, setGradeFilter] = useState('all'); // all, graded, not_graded
    const [selectedProject, setSelectedProject] = useState(null);
    const [showGradeModal, setShowGradeModal] = useState(false);
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

    // Fetch projects for grading
    const fetchProjects = async () => {
        try {
            if (!currentUser?.faculty_id) return;
            const projectsData = await gradingHandler.getProjects(currentUser.faculty_id, gradeFilter);
            setProjects(projectsData);
        } catch (err) {
            console.error("Failed to fetch projects:", err);
        }
    };

    // Fetch projects when tab changes to grading or filter changes
    useEffect(() => {
        if (activeTab === 'grading' && currentUser) {
            fetchProjects();
        }
    }, [activeTab, gradeFilter, currentUser]);

    // Handle grading submission
    const handleGradeSubmit = async (projectId, grade, comments, status) => {
        await gradingHandler.gradeProject(projectId, grade, comments, status);
        // Refresh projects list
        await fetchProjects();
        // Refresh stats to update grade distribution
        const statsData = await professorHandler.getDashboardStats();
        setStats(statsData);
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
                    {[
                        { id: 'overview', label: 'Overview', icon: 'fa-chart-line' },
                        { id: 'grading', label: 'Grading', icon: 'fa-graduation-cap' },
                        { id: 'qna', label: 'Q&A', icon: 'fa-comments' }
                    ].map((tab) => (
                        <button
                            key={tab.id}
                            onClick={() => setActiveTab(tab.id)}
                            className={`px-6 py-3 rounded-full transition-all flex items-center gap-2 ${activeTab === tab.id
                                ? 'bg-accent text-white shadow-lg shadow-accent/20'
                                : 'bg-white/5 hover:bg-white/10 text-muted'
                                }`}
                        >
                            <i className={`fa-solid ${tab.icon}`}></i>
                            <span className="font-medium capitalize">{tab.label}</span>
                        </button>
                    ))}
                </div>

                {/* Content Area */}
                <div className="space-y-8">
                    {activeTab === 'overview' && (
                        <div className="grid md:grid-cols-2 gap-8">
                            {/* Student Analytics Card */}
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

                            {/* Project Grade Distribution */}
                            <div className="bg-white/5 p-6 rounded-custom border border-white/10">
                                <h3 className="text-xl font-bold mb-4">Project Grade Distribution</h3>
                                <div className="space-y-4">
                                    {stats.projectGradeDistribution && stats.projectGradeDistribution.length > 0 ? (
                                        stats.projectGradeDistribution.map((grade, idx) => {
                                            const maxCount = Math.max(...stats.projectGradeDistribution.map(g => g.project_count));
                                            const gradeColors = {
                                                'A (90-100)': 'bg-green-500',
                                                'B (80-89)': 'bg-blue-500',
                                                'C (70-79)': 'bg-yellow-500',
                                                'D (60-69)': 'bg-orange-500',
                                                'F (Below 60)': 'bg-red-500',
                                                'Not Graded': 'bg-gray-500'
                                            };
                                            const color = gradeColors[grade.grade_range] || 'bg-accent';

                                            return (
                                                <div key={idx} className="space-y-1">
                                                    <div className="flex justify-between text-sm">
                                                        <span className="font-medium">{grade.grade_range}</span>
                                                        <span className="text-muted">{grade.project_count} {grade.project_count === 1 ? 'Project' : 'Projects'}</span>
                                                    </div>
                                                    <div className="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                                                        <div
                                                            className={`h-full ${color} transition-all duration-300`}
                                                            style={{ width: `${(grade.project_count / maxCount) * 100}%` }}
                                                        />
                                                    </div>
                                                </div>
                                            );
                                        })
                                    ) : (
                                        <div className="text-center text-muted py-8">
                                            <div className="text-4xl mb-2">📊</div>
                                            <p className="text-sm">No graded projects yet</p>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    )}

                    {activeTab === 'grading' && (
                        <div className="space-y-6">
                            {/* Header with Filters */}
                            <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                <h2 className="text-2xl font-bold">Project Grading</h2>
                                <div className="flex gap-2">
                                    {['all', 'graded', 'not_graded'].map((filter) => (
                                        <button
                                            key={filter}
                                            onClick={() => setGradeFilter(filter)}
                                            className={`px-4 py-2 rounded-lg text-sm font-medium transition ${gradeFilter === filter
                                                ? 'bg-accent text-white'
                                                : 'bg-white/5 text-gray-400 hover:bg-white/10'
                                                }`}
                                        >
                                            {filter === 'all' ? 'All' : filter === 'graded' ? 'Graded' : 'Not Graded'}
                                        </button>
                                    ))}
                                </div>
                            </div>

                            {/* Projects List */}
                            {projects.length === 0 ? (
                                <div className="text-center py-16 bg-white/5 rounded-2xl border border-white/10">
                                    <div className="text-6xl mb-4">📁</div>
                                    <p className="text-xl font-medium text-gray-400">No projects found</p>
                                    <p className="text-sm text-gray-500 mt-2">
                                        {gradeFilter === 'graded' ? 'No graded projects yet' :
                                            gradeFilter === 'not_graded' ? 'All projects have been graded' :
                                                'No projects submitted yet'}
                                    </p>
                                </div>
                            ) : (
                                <div className="grid gap-4">
                                    {projects.map((project) => (
                                        <div
                                            key={project.project_id}
                                            className="bg-white/5 p-6 rounded-2xl border border-white/10 hover:border-white/20 transition"
                                        >
                                            <div className="flex flex-col md:flex-row justify-between gap-4">
                                                {/* Project Info */}
                                                <div className="flex-grow">
                                                    <div className="flex items-start gap-3 mb-3">
                                                        <div className="w-12 h-12 rounded-lg bg-accent/20 flex items-center justify-center flex-shrink-0">
                                                            <i className="fa-solid fa-file-code text-accent text-xl"></i>
                                                        </div>
                                                        <div>
                                                            <h3 className="text-lg font-bold text-white mb-1">{project.title}</h3>
                                                            <p className="text-sm text-gray-400">{project.description || 'No description provided'}</p>
                                                        </div>
                                                    </div>

                                                    <div className="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                                        <div>
                                                            <span className="text-gray-500">Student:</span>
                                                            <p className="text-white font-medium">{project.student_name}</p>
                                                        </div>
                                                        <div>
                                                            <span className="text-gray-500">Faculty:</span>
                                                            <p className="text-white font-medium">{project.faculty_name}</p>
                                                        </div>
                                                        <div>
                                                            <span className="text-gray-500">Major:</span>
                                                            <p className="text-white font-medium">{project.major_name}</p>
                                                        </div>
                                                        <div>
                                                            <span className="text-gray-500">Submitted:</span>
                                                            <p className="text-white font-medium">
                                                                {new Date(project.submitted_at).toLocaleDateString()}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                {/* Grade Status & Action */}
                                                <div className="flex flex-col items-end justify-between gap-3">
                                                    <div className="flex flex-col items-end gap-2">
                                                        {/* Grade Display */}
                                                        {project.grade ? (
                                                            <div className="text-center">
                                                                <div className="text-3xl font-bold text-accent">{project.grade}</div>
                                                                <div className="text-xs text-gray-500">/ 100</div>
                                                                <span className="inline-block mt-2 px-3 py-1 bg-green-500/20 text-green-500 rounded-full text-xs font-medium">
                                                                    Graded
                                                                </span>
                                                            </div>
                                                        ) : (
                                                            <span className="px-3 py-1 bg-yellow-500/20 text-yellow-500 rounded-full text-xs font-medium">
                                                                Not Graded
                                                            </span>
                                                        )}

                                                        {/* Status Badge */}
                                                        {project.status && (
                                                            <span className={`px-3 py-1 rounded-full text-xs font-medium ${project.status === 'APPROVED' ? 'bg-green-500/20 text-green-500' :
                                                                    project.status === 'REJECTED' ? 'bg-red-500/20 text-red-500' :
                                                                        'bg-gray-500/20 text-gray-400'
                                                                }`}>
                                                                {project.status === 'APPROVED' ? '✅ Approved' :
                                                                    project.status === 'REJECTED' ? '❌ Rejected' :
                                                                        '⏳ Pending'}
                                                            </span>
                                                        )}
                                                    </div>

                                                    <button
                                                        onClick={() => {
                                                            setSelectedProject(project);
                                                            setShowGradeModal(true);
                                                        }}
                                                        className="px-6 py-2 bg-accent text-white rounded-lg hover:bg-accent/80 transition font-medium text-sm flex items-center gap-2"
                                                    >
                                                        <i className="fa-solid fa-graduation-cap"></i>
                                                        {project.grade ? 'Edit Grade' : 'Grade Project'}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    )}

                    {activeTab === 'qna' && (
                        <div className="space-y-6">
                            <h2 className="text-2xl font-bold mb-4">Student Questions</h2>
                            {questionPosts.length === 0 ? (
                                <div className="text-center py-16 bg-white/5 rounded-2xl border border-white/10">
                                    <div className="text-6xl mb-4">❓</div>
                                    <p className="text-xl font-medium text-gray-400">No questions yet</p>
                                    <p className="text-sm text-gray-500 mt-2">Students haven't posted any questions</p>
                                </div>
                            ) : (
                                <div className="grid gap-4">
                                    {questionPosts.map((post) => (
                                        <div
                                            key={post.post_id}
                                            onClick={() => navigate(`/post/${post.post_id}`)}
                                            className="bg-white/5 p-6 rounded-2xl border border-white/10 hover:border-accent/50 transition cursor-pointer group"
                                        >
                                            <div className="flex items-start gap-4">
                                                <div className="w-12 h-12 rounded-full bg-accent/20 flex items-center justify-center flex-shrink-0 group-hover:bg-accent/30 transition">
                                                    <i className="fa-solid fa-question text-accent text-xl"></i>
                                                </div>
                                                <div className="flex-grow">
                                                    <h3 className="text-lg font-bold text-white mb-2 group-hover:text-accent transition">
                                                        {post.content.substring(0, 100)}
                                                        {post.content.length > 100 ? '...' : ''}
                                                    </h3>
                                                    <div className="flex items-center gap-4 text-sm text-gray-400">
                                                        <span className="flex items-center gap-1">
                                                            <i className="fa-solid fa-user"></i>
                                                            {post.author_name || 'Anonymous'}
                                                        </span>
                                                        <span className="flex items-center gap-1">
                                                            <i className="fa-solid fa-clock"></i>
                                                            {new Date(post.created_at).toLocaleDateString()}
                                                        </span>
                                                        {post.comment_count > 0 && (
                                                            <span className="flex items-center gap-1">
                                                                <i className="fa-solid fa-comments"></i>
                                                                {post.comment_count} {post.comment_count === 1 ? 'Answer' : 'Answers'}
                                                            </span>
                                                        )}
                                                    </div>
                                                </div>
                                                <div className="flex-shrink-0">
                                                    <i className="fa-solid fa-arrow-right text-gray-400 group-hover:text-accent group-hover:translate-x-1 transition"></i>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>

            {/* Grade Modal */}
            {showGradeModal && selectedProject && (
                <GradeModal
                    project={selectedProject}
                    onClose={() => {
                        setShowGradeModal(false);
                        setSelectedProject(null);
                    }}
                    onSubmit={handleGradeSubmit}
                />
            )}
        </div>
    );
};

export default ProfessorPage;

