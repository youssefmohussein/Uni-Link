import React, { useState, useRef, useEffect, useCallback } from "react";
import { useNavigate, useSearchParams, useParams } from "react-router-dom";
import { API_BASE_URL } from "../../../config/api";
import Header from "../../Components/Posts/Header";
import LeftSidebar from "../../Components/Posts/LeftSidebar";
import RightSidebar from "../../Components/Posts/RightSidebar";
import PostCard from "../../Components/Posts/PostCard";
import PostForm from "../../Components/Posts/PostForm";
import Galaxy from "../../Animations/Galaxy/Galaxy";
import starryNightBg from "../../assets/starry_night_user.jpg";
import * as postHandler from "../../../api/postHandler";

const PostPage = () => {
  const [posts, setPosts] = useState([]);
  const [filter, setFilter] = useState("all");
  const [newPostContent, setNewPostContent] = useState("");
  const [newPostCategory, setNewPostCategory] = useState("");
  const [selectedFiles, setSelectedFiles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [searchQuery, setSearchQuery] = useState("");
  const [searchResults, setSearchResults] = useState([]);
  const [isSearching, setIsSearching] = useState(false);
  const [isPostModalOpen, setIsPostModalOpen] = useState(false);
  const postFormRef = useRef(null);
  const searchTimeoutRef = useRef(null);
  const navigate = useNavigate();

  // Get current user from storage
  const user = JSON.parse(localStorage.getItem('user'));

  // Use actual user values or null (redirect handles null)
  const currentUserId = user?.id || 1;
  const currentFacultyId = user?.faculty_id || 1; // Fallback if missing

  // Ref to prevent multiple simultaneous fetches
  const isFetchingRef = useRef(false);
  const hasInitializedRef = useRef(false);

  // Check auth and fetch posts
  const fetchPosts = useCallback(async () => {
    // Prevent multiple simultaneous fetches
    if (isFetchingRef.current) {
      console.log('⏳ Already fetching, skipping...');
      return;
    }

    try {
      isFetchingRef.current = true;
      setLoading(true);
      setError(null);
      const data = await postHandler.getAllPosts();

      // Debug: Log first post to check media structure
      if (data.length > 0) {
        console.log("Sample post data:", data[0]);
        console.log("Media data:", data[0].media);
        if (data[0].media && data[0].media.length > 0) {
          console.log("First media item:", data[0].media[0]);
          console.log("Media path:", data[0].media[0].media_path);
          console.log("Constructed URL:", `${API_BASE_URL}${data[0].media[0].media_path || data[0].media[0].path}`);
        }
      }

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
        media: post.media && Array.isArray(post.media) && post.media.length > 0
          ? post.media.map(m => {
            // Ensure path starts with / for URL construction
            const mediaPath = m.media_path || m.path || '';
            const normalizedPath = mediaPath.startsWith('/') ? mediaPath : `/${mediaPath}`;
            const url = `${API_BASE_URL}${normalizedPath}`;

            console.log('Media transformation:', {
              original_path: mediaPath,
              normalized_path: normalizedPath,
              final_url: url
            });

            return {
              media_id: m.media_id,
              type: m.media_type || m.type, // Handle both formats
              url: url
            };
          })
          : [],
        reactions: post.likes_count || 0,
        isReacted: false, // TODO: Check if current user has liked
        isTrending: false, // TODO: Add trending logic
        comments: [], // Array for storing comment objects when fetched
        commentsCount: post.comments_count || 0, // Initial count from backend
        post_id: post.post_id, // Keep original ID for API calls
      }));

      setPosts(transformedPosts);
    } catch (err) {
      console.error("Failed to fetch posts:", err);
      setError("Failed to load posts. Please try again later.");
    } finally {
      setLoading(false);
      isFetchingRef.current = false;
    }
  }, []);

  useEffect(() => {
    // Only run once on initial mount
    if (hasInitializedRef.current) {
      return;
    }

    if (!user) {
      alert("Please log in to access Posts");
      navigate('/login');
      return;
    }

    hasInitializedRef.current = true;
    fetchPosts();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []); // Only run once on mount

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

  const openPostModal = () => {
    setIsPostModalOpen(true);
  };

  const closePostModal = () => {
    setIsPostModalOpen(false);
  };

  const handlePost = async () => {
    if (!newPostContent.trim() || !newPostCategory) {
      alert("Please enter content and select a category before posting.");
      return;
    }

    try {
      // Create post via API
      const post_id = await postHandler.addPost({
        author_id: currentUserId,
        faculty_id: currentFacultyId,
        category: newPostCategory,
        content: newPostContent,
        status: "Published",
      });

      // Upload media if files are selected
      if (selectedFiles.length > 0) {
        try {
          await postHandler.uploadPostMedia(post_id, selectedFiles);
        } catch (uploadErr) {
          console.error("Failed to upload media:", uploadErr);
          alert("Post created but media upload failed: " + uploadErr.message);
        }
      }

      // Refresh posts to show the new one
      await fetchPosts();

      // Clear form
      setNewPostContent("");
      setNewPostCategory("");
      setSelectedFiles([]);

      alert("Post created successfully!");
    } catch (err) {
      console.error("Failed to create post:", err);
      alert("Failed to create post: " + (err.message || "Unknown error"));
    }
  };

  // Search handler with debouncing
  const handleSearch = (query) => {
    setSearchQuery(query);
    setError(null); // Clear any previous errors

    // Clear previous timeout
    if (searchTimeoutRef.current) {
      clearTimeout(searchTimeoutRef.current);
    }

    // If query is empty, clear search results
    if (!query || !query.trim()) {
      setSearchResults([]);
      setIsSearching(false);
      return;
    }

    // Set searching state
    setIsSearching(true);

    // Debounce search with 300ms delay
    searchTimeoutRef.current = setTimeout(async () => {
      try {
        const results = await postHandler.searchPosts(query);

        // Transform search results to match PostCard expected format
        const transformedResults = results.map((post) => ({
          id: post.post_id,
          user: {
            name: post.author_name || "Unknown User",
            major: post.faculty_name || "Unknown Faculty",
            profilePic: `https://placehold.co/40x40/E5E7EB/6B7280?text=${(post.author_name || "U")[0]}`,
          },
          timeAgo: formatTimeAgo(post.created_at),
          category: post.category,
          content: post.content,
          media: post.media && Array.isArray(post.media) && post.media.length > 0
            ? post.media.map(m => {
              // Ensure path starts with / for URL construction
              const mediaPath = m.media_path || m.path || '';
              const normalizedPath = mediaPath.startsWith('/') ? mediaPath : `/${mediaPath}`;
              return {
                media_id: m.media_id,
                type: m.media_type || m.type, // Handle both formats
                url: `${API_BASE_URL}${normalizedPath}`
              };
            })
            : [],
          reactions: post.likes_count || 0,
          isReacted: false,
          isTrending: false,
          comments: [],
          commentsCount: post.comments_count || 0,
          post_id: post.post_id,
        }));

        setSearchResults(transformedResults);
        setError(null); // Clear error on successful search
      } catch (err) {
        console.error("Search failed:", err);
        // Only show error for actual failures, not empty results
        setSearchResults([]);
      } finally {
        setIsSearching(false);
      }
    }, 300);
  };

  // Clear search handler
  const handleClearSearch = () => {
    setSearchQuery("");
    setSearchResults([]);
    setIsSearching(false);
    if (searchTimeoutRef.current) {
      clearTimeout(searchTimeoutRef.current);
    }
  };

  // Determine which posts to display
  const [searchParams, setSearchParams] = useSearchParams();
  const { id: routePostId } = useParams(); // Get ID from route /post/:id
  const sharedPostId = searchParams.get('id') || routePostId; // Support both ?id=1 and /post/1

  let displayPosts = searchQuery ? searchResults : posts;

  if (sharedPostId) {
    displayPosts = displayPosts.filter(p => p.post_id == sharedPostId);
  }

  const filteredPosts = displayPosts.filter((post) => {
    if (sharedPostId) return true;
    if (filter === "all") return true;
    if (filter === "trending") return post.isTrending;
    return post.category === filter;
  });

  if (!user) return null; // Prevent rendering while redirecting

  return (
    <div className="flex flex-col min-h-screen bg-main text-main font-main transition-theme relative overflow-hidden">
      {/* 🌌 Starry Night Sky Background */}
      {/* Black background for galaxy */}
      <div
        className="fixed inset-0 z-0"
        style={{
          background: '#000000',
        }}
      />

      {/* Animated stars overlay - Subtle twinkling on top of the image */}
      <div className="fixed inset-0 z-0 mix-blend-screen opacity-60">
        <Galaxy
          transparent={true}
          hueShift={0}             // Keep stars white/natural to blend with image
          density={0.8}            // Low density, just adding accents
          glowIntensity={0.3}      // Very low glow to avoid "huge shine"
          saturation={0.0}         // White stars
          speed={0.05}             // Very slow movement
          mouseRepulsion={true}
          repulsionStrength={0.5}
          twinkleIntensity={1.0}   // High twinkle for effect
          disableAnimation={false}
          rotationSpeed={0.005}    // Almost static rotation
          starSpeed={0.2}
        />
      </div>

      {/* Content Layer */}
      <div className="relative z-10 flex flex-col min-h-screen">
        {/* 🌟 Header */}
        <Header
          logoSize="large"
          onShareActivity={openPostModal}
          onSearch={handleSearch}
          searchQuery={searchQuery}
          onClearSearch={handleClearSearch}
        />

        <div className="container mx-auto flex flex-grow pt-24 pb-12 px-4 md:px-6 xl:px-8 max-w-8xl gap-6">
          {/* 📁 Left Sidebar */}
          <LeftSidebar currentFilter={filter} onFilterChange={setFilter} />

          {/* 📰 Main Feed */}
          <main className="flex-grow space-y-8">
            {/* ✏️ Post Form - Removed from inline flow */}
            {/* <PostForm ... /> */}

            {/* 📜 Feed */}
            {loading ? (
              <div className="text-center py-12">
                <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-accent"></div>
                <p className="mt-4 text-muted">Loading posts...</p>
              </div>
            ) : error ? (
              <div className="backdrop-blur-xl bg-white/10 dark:bg-black/20 rounded-custom shadow-2xl p-6 text-center border border-white/20" style={{ backdropFilter: 'blur(20px) saturate(180%)', WebkitBackdropFilter: 'blur(20px) saturate(180%)' }}>
                <p className="text-red-500 mb-4">{error}</p>
                <button
                  onClick={fetchPosts}
                  className="px-4 py-2 bg-accent text-white rounded-custom hover:bg-accent/80 transition-colors"
                >
                  Retry
                </button>
              </div>
            ) : filteredPosts.length === 0 ? (
              <div className="backdrop-blur-xl bg-white/10 dark:bg-black/20 rounded-custom shadow-2xl p-6 text-center border border-white/20" style={{ backdropFilter: 'blur(20px) saturate(180%)', WebkitBackdropFilter: 'blur(20px) saturate(180%)' }}>
                <p className="text-muted">
                  {searchQuery
                    ? `No posts found matching "${searchQuery}". Try a different search term.`
                    : "No posts found. Be the first to share something!"}
                </p>
              </div>
            ) : (
              <div className="space-y-8">
                {filteredPosts.map((post) => (
                  <PostCard
                    key={post.id}
                    initialPost={post}
                    onRefresh={fetchPosts}
                    currentUserId={currentUserId}
                  />
                ))}
              </div>
            )}
          </main>

          {/* 💬 Right Sidebar */}
          <RightSidebar currentFilter={filter} onFilterChange={setFilter} />
        </div>
      </div>

      {/* 📝 Post Creation Modal */}
      {
        isPostModalOpen && (
          <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm animate-fade-in">
            <div className="w-full max-w-2xl relative animate-scale-in">
              <button
                onClick={closePostModal}
                className="absolute -top-10 right-0 text-white hover:text-accent transition-colors"
              >
                <i className="fas fa-times text-2xl"></i>
              </button>
              <PostForm
                postFormRef={postFormRef}
                newPostContent={newPostContent}
                setNewPostContent={setNewPostContent}
                newPostCategory={newPostCategory}
                setNewPostCategory={setNewPostCategory}
                handlePost={() => {
                  handlePost();
                  closePostModal();
                }}
                selectedFiles={selectedFiles}
                setSelectedFiles={setSelectedFiles}
              />
            </div>
          </div>
        )
      }
    </div >
  );
};

export default PostPage;
