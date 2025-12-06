import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import PostCard from "../../Components/Posts/PostCard";
import Header from "../../Components/Posts/Header";
import Galaxy from "../../Animations/Galaxy/Galaxy";
import starryNightBg from "../../assets/starry_night_user.jpg";
import * as postHandler from "../../../api/postHandler";
import authHandler from "../../handlers/authHandler";

const CollectionsPage = () => {
    const navigate = useNavigate();
    const [savedPosts, setSavedPosts] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentUser, setCurrentUser] = useState(null);
    const [error, setError] = useState(null);

    useEffect(() => {
        initializePage();
    }, []);

    const initializePage = async () => {
        try {
            // Get current user
            const user = await authHandler.getCurrentUser();
            if (!user) {
                navigate("/login");
                return;
            }
            setCurrentUser(user);

            // Fetch saved posts
            await fetchSavedPosts(user.id);
        } catch (err) {
            console.error("Failed to initialize collections page:", err);
            setError("Failed to load collections. Please try again.");
        } finally {
            setLoading(false);
        }
    };

    const fetchSavedPosts = async (userId) => {
        try {
            setLoading(true);
            const data = await postHandler.getSavedPosts(userId);

            // Transform data to match PostCard format
            const transformedPosts = data.map((post) => ({
                post_id: post.post_id,
                content: post.content,
                category: post.category,
                timeAgo: formatTimeAgo(post.created_at),
                savedAt: formatTimeAgo(post.saved_at),
                user: {
                    name: post.username,
                    major: `${post.major_name || "Unknown"} - ${post.faculty_name || "Unknown"}`,
                    profilePic: `https://placehold.co/50x50/8B5CF6/FFFFFF?text=${post.username[0]}`,
                },
                reactions: post.reactions,
                comments: Array(post.comment_count).fill({}), // Placeholder for count
                media: post.media || [],
                isReacted: post.is_reacted,
                isSaved: true,
            }));

            setSavedPosts(transformedPosts);
        } catch (err) {
            console.error("Failed to fetch saved posts:", err);
            setError("Failed to load saved posts.");
        } finally {
            setLoading(false);
        }
    };

    const formatTimeAgo = (timestamp) => {
        if (!timestamp) return "Just now";

        const now = new Date();
        const postDate = new Date(timestamp);
        const diffMs = now - postDate;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMins / 60);
        const diffDays = Math.floor(diffHours / 24);

        if (diffMins < 1) return "Just now";
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffHours < 24) return `${diffHours}h ago`;
        if (diffDays < 7) return `${diffDays}d ago`;
        return postDate.toLocaleDateString();
    };

    const handleUnsave = async (postId) => {
        if (!currentUser) return;

        try {
            await postHandler.unsavePost(currentUser.id, postId);
            // Remove from UI
            setSavedPosts(savedPosts.filter((post) => post.post_id !== postId));
        } catch (err) {
            console.error("Failed to unsave post:", err);
            alert("Failed to remove post from collection.");
        }
    };

    return (
        <div className="min-h-screen relative overflow-hidden bg-[#0d1117]">
            {/* 🌌 Animated Background */}
            <div className="fixed inset-0 pointer-events-none z-0">
                <Galaxy />
                <div
                    className="absolute inset-0 opacity-5"
                    style={{
                        backgroundImage: `url(${starryNightBg})`,
                        backgroundSize: "cover",
                        backgroundPosition: "center",
                    }}
                />
            </div>

            {/* Main Content */}
            <div className="relative z-10">
                <Header
                    searchQuery=""
                    onSearchChange={() => { }}
                    onClearSearch={() => { }}
                    showSearch={false}
                />

                <main className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-24 pb-12">
                    {/* Page Header */}
                    <div className="mb-8">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center space-x-4">
                                <button
                                    onClick={() => navigate("/posts")}
                                    className="text-gray-400 hover:text-white transition-colors p-2 hover:bg-white/5 rounded-lg"
                                >
                                    <i className="fas fa-arrow-left text-xl"></i>
                                </button>
                                <div>
                                    <h1 className="text-3xl font-bold text-white flex items-center space-x-3">
                                        <i className="fas fa-bookmark text-blue-400"></i>
                                        <span>Saved Posts</span>
                                    </h1>
                                    <p className="text-gray-400 text-sm mt-1">
                                        Your personal collection of saved posts
                                    </p>
                                </div>
                            </div>
                            {!loading && savedPosts.length > 0 && (
                                <div className="bg-blue-500/20 text-blue-400 px-4 py-2 rounded-full text-sm font-semibold border border-blue-500/30">
                                    {savedPosts.length} {savedPosts.length === 1 ? "Post" : "Posts"}
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Loading State */}
                    {loading && (
                        <div className="flex flex-col items-center justify-center py-20">
                            <div className="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent mb-4"></div>
                            <p className="text-gray-400 text-lg">Loading your saved posts...</p>
                        </div>
                    )}

                    {/* Error State */}
                    {error && !loading && (
                        <div className="bg-red-500/10 border border-red-500/30 rounded-xl p-6 text-center">
                            <i className="fas fa-exclamation-triangle text-red-400 text-3xl mb-3"></i>
                            <p className="text-red-400 font-medium">{error}</p>
                            <button
                                onClick={() => currentUser && fetchSavedPosts(currentUser.id)}
                                className="mt-4 px-6 py-2 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg transition-colors"
                            >
                                Try Again
                            </button>
                        </div>
                    )}

                    {/* Empty State */}
                    {!loading && !error && savedPosts.length === 0 && (
                        <div className="flex flex-col items-center justify-center py-20">
                            <div className="relative mb-6">
                                <div className="absolute inset-0 bg-blue-500/20 blur-3xl rounded-full"></div>
                                <i className="fas fa-bookmark text-gray-600 text-8xl relative"></i>
                            </div>
                            <h2 className="text-2xl font-bold text-white mb-3">No Saved Posts Yet</h2>
                            <p className="text-gray-400 text-center max-w-md mb-6">
                                Start building your collection by saving posts that inspire or interest you.
                                Click the bookmark icon on any post to save it here.
                            </p>
                            <button
                                onClick={() => navigate("/posts")}
                                className="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-semibold rounded-lg hover:shadow-lg hover:shadow-blue-500/50 transition-all duration-300 flex items-center space-x-2"
                            >
                                <i className="fas fa-compass"></i>
                                <span>Explore Posts</span>
                            </button>
                        </div>
                    )}

                    {/* Saved Posts Grid */}
                    {!loading && !error && savedPosts.length > 0 && (
                        <div className="space-y-6">
                            {savedPosts.map((post) => (
                                <PostCard
                                    key={post.post_id}
                                    initialPost={post}
                                    currentUserId={currentUser?.id}
                                    onRefresh={() => currentUser && fetchSavedPosts(currentUser.id)}
                                    onUnsave={() => handleUnsave(post.post_id)}
                                    showSavedBadge={true}
                                />
                            ))}
                        </div>
                    )}
                </main>
            </div>
        </div>
    );
};

export default CollectionsPage;
