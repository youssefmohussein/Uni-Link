import React, { useState, useRef, useEffect } from "react";
import Header from "../../components/Posts/Header";
import LeftSidebar from "../../components/Posts/LeftSidebar";
import RightSidebar from "../../components/Posts/RightSidebar";
import PostCard from "../../components/Posts/PostCard";
import PostForm from "../../components/Posts/PostForm";
import * as postHandler from "../../../api/postHandler";

const PostPage = () => {
  const [posts, setPosts] = useState([]);
  const [filter, setFilter] = useState("all");
  const [newPostContent, setNewPostContent] = useState("");
  const [newPostCategory, setNewPostCategory] = useState("");
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const postFormRef = useRef(null);

  // TODO: Replace with actual session user_id once authentication is integrated
  const TEMP_USER_ID = 1;
  const TEMP_FACULTY_ID = 1;

  // Fetch posts from backend on mount
  useEffect(() => {
    fetchPosts();
  }, []);

  const fetchPosts = async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await postHandler.getAllPosts();

      // Transform backend data to match PostCard expected format
      const transformedPosts = data.map((post) => ({
        id: post.post_id,
        user: {
          name: post.author_name || "Unknown User",
          major: post.faculty_name || "Unknown Faculty",
          profilePic: `https://placehold.co/40x40/E5E7EB/6B7280?text=${(post.author_name || "U")[0]}`,
        },
        timeAgo: formatTimeAgo(post.created_at),
        category: post.category,
        content: post.content,
        image: null, // TODO: Add media support when needed
        reactions: post.likes_count || 0,
        isReacted: false, // TODO: Check if current user has liked
        isTrending: false, // TODO: Add trending logic
        comments: [],
        post_id: post.post_id, // Keep original ID for API calls
      }));

      setPosts(transformedPosts);
    } catch (err) {
      console.error("Failed to fetch posts:", err);
      setError("Failed to load posts. Please try again later.");
    } finally {
      setLoading(false);
    }
  };

  // Helper function to format timestamps
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

  const scrollToPostForm = () => {
    postFormRef.current?.scrollIntoView({ behavior: "smooth" });
  };

  const handlePost = async () => {
    if (!newPostContent.trim() || !newPostCategory) {
      alert("Please enter content and select a category before posting.");
      return;
    }

    try {
      // Create post via API
      const post_id = await postHandler.addPost({
        author_id: TEMP_USER_ID,
        faculty_id: TEMP_FACULTY_ID,
        category: newPostCategory,
        content: newPostContent,
        status: "Published",
      });

      // Refresh posts to show the new one
      await fetchPosts();

      // Clear form
      setNewPostContent("");
      setNewPostCategory("");

      alert("Post created successfully!");
    } catch (err) {
      console.error("Failed to create post:", err);
      alert("Failed to create post: " + (err.message || "Unknown error"));
    }
  };

  const filteredPosts = posts.filter((post) => {
    if (filter === "all") return true;
    if (filter === "trending") return post.isTrending;
    return post.category === filter;
  });

  return (
    <div className="flex flex-col min-h-screen bg-main text-main font-main transition-theme">
      {/* 🌟 Header */}
      <Header logoSize="large" onShareActivity={scrollToPostForm} />

      <div className="container mx-auto flex flex-grow pt-24 pb-12 px-4 md:px-6 xl:px-8 max-w-8xl gap-6">
        {/* 📁 Left Sidebar */}
        <LeftSidebar currentFilter={filter} onFilterChange={setFilter} />

        {/* 📰 Main Feed */}
        <main className="flex-grow space-y-8">
          {/* ✏️ Post Form */}
          <PostForm
            newPostContent={newPostContent}
            setNewPostContent={setNewPostContent}
            newPostCategory={newPostCategory}
            setNewPostCategory={setNewPostCategory}
            handlePost={handlePost}
          />

          {/* 📜 Feed */}
          {loading ? (
            <div className="text-center py-12">
              <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-accent"></div>
              <p className="mt-4 text-muted">Loading posts...</p>
            </div>
          ) : error ? (
            <div className="bg-panel rounded-custom shadow-custom p-6 text-center">
              <p className="text-red-500 mb-4">{error}</p>
              <button
                onClick={fetchPosts}
                className="px-4 py-2 bg-accent text-white rounded-custom hover:bg-accent/80 transition-colors"
              >
                Retry
              </button>
            </div>
          ) : filteredPosts.length === 0 ? (
            <div className="bg-panel rounded-custom shadow-custom p-6 text-center">
              <p className="text-muted">No posts found. Be the first to share something!</p>
            </div>
          ) : (
            <div className="space-y-8">
              {filteredPosts.map((post) => (
                <PostCard
                  key={post.id}
                  initialPost={post}
                  onRefresh={fetchPosts}
                  currentUserId={TEMP_USER_ID}
                />
              ))}
            </div>
          )}
        </main>

        {/* 💬 Right Sidebar */}
        <RightSidebar currentFilter={filter} onFilterChange={setFilter} />
      </div>
    </div>
  );
};

export default PostPage;
