import React, { useState, useEffect } from "react";
import * as postHandler from "../../../api/postHandler";
import GlassSurface from "../Login_Components/LiquidGlass/GlassSurface";

const PostCard = ({ initialPost, onRefresh, currentUserId, onUnsave, showSavedBadge }) => {
  const [post, setPost] = useState(initialPost);
  const [showComments, setShowComments] = useState(false);
  const [comments, setComments] = useState([]);
  const [commentsCount, setCommentsCount] = useState(initialPost.commentsCount || 0);
  const [newComment, setNewComment] = useState("");
  const [loadingComments, setLoadingComments] = useState(false);
  const [loadingInteraction, setLoadingInteraction] = useState(false);
  const [loadingCommentSubmit, setLoadingCommentSubmit] = useState(false);
  const [isSaved, setIsSaved] = useState(initialPost.isSaved || false);
  const [loadingSave, setLoadingSave] = useState(false);

  // 🌈 Border color based on category, using accent-friendly tones
  let borderColor = "border-accent/40";
  if (post.category === "Questions") borderColor = "border-blue-500/60";
  if (post.category === "Events") borderColor = "border-green-500/60";
  if (post.category === "Projects") borderColor = "border-purple-500/60";

  // Sync comment count when post data updates
  useEffect(() => {
    if (initialPost.commentsCount !== undefined) {
      setCommentsCount(initialPost.commentsCount);
    }
  }, [initialPost.commentsCount]);

  // Fetch comments when comments section is opened
  useEffect(() => {
    if (showComments && comments.length === 0) {
      fetchComments();
    }
  }, [showComments]);

  const fetchComments = async () => {
    try {
      setLoadingComments(true);
      const data = await postHandler.getCommentsByPost(post.post_id);

      // Transform comments to match UI format
      const transformedComments = data.map((comment) => ({
        comment_id: comment.comment_id,
        userName: comment.username || "Unknown User",
        content: comment.content,
        timeAgo: formatTimeAgo(comment.created_at),
        userPic: `https://placehold.co/30x30/E5E7EB/6B7280?text=${(comment.username || "U")[0]}`,
      }));

      setComments(transformedComments);
      // Update count based on fetched comments
      setCommentsCount(transformedComments.length);
    } catch (err) {
      console.error("Failed to fetch comments:", err);
    } finally {
      setLoadingComments(false);
    }
  };

  const formatTimeAgo = (timestamp) => {
    if (!timestamp) return "Just now";

    const now = new Date();
    const commentDate = new Date(timestamp);
    const diffMs = now - commentDate;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMins / 60);
    const diffDays = Math.floor(diffHours / 24);

    if (diffMins < 1) return "Just now";
    if (diffMins < 60) return `${diffMins}m ago`;
    if (diffHours < 24) return `${diffHours}h ago`;
    if (diffDays < 7) return `${diffDays}d ago`;
    return commentDate.toLocaleDateString();
  };

  const toggleReaction = async () => {
    if (loadingInteraction) return; // Prevent double-clicks

    // Optimistic UI update
    const previousState = { ...post };
    setPost((prev) => ({
      ...prev,
      isReacted: !prev.isReacted,
      reactions: prev.isReacted ? prev.reactions - 1 : prev.reactions + 1,
    }));

    try {
      setLoadingInteraction(true);

      if (!post.isReacted) {
        // Add like
        await postHandler.addInteraction(post.post_id, currentUserId, "Like");
      } else {
        // Remove like
        await postHandler.deleteInteraction(post.post_id, "Like");
      }

      // Refresh to get accurate count
      if (onRefresh) {
        await onRefresh();
      }
    } catch (err) {
      console.error("Failed to toggle reaction:", err);
      // Rollback on error
      setPost(previousState);
      alert("Failed to update reaction. Please try again.");
    } finally {
      setLoadingInteraction(false);
    }
  };

  const handleCommentSubmit = async () => {
    if (!newComment.trim()) return;

    try {
      setLoadingCommentSubmit(true);

      await postHandler.addComment(post.post_id, currentUserId, newComment.trim());

      // Clear input
      setNewComment("");

      // Increment comment count immediately (optimistic update)
      setCommentsCount(prev => prev + 1);

      // Refresh comments to get the new one
      await fetchComments();
    } catch (err) {
      console.error("Failed to add comment:", err);
      alert("Failed to add comment: " + (err.message || "Unknown error"));
    } finally {
      setLoadingCommentSubmit(false);
    }
  };

  const toggleSave = async () => {
    if (loadingSave || !currentUserId) return;

    // Optimistic UI update
    const previousState = isSaved;
    setIsSaved(!isSaved);

    try {
      setLoadingSave(true);

      if (!isSaved) {
        // Save post
        await postHandler.savePost(currentUserId, post.post_id);
      } else {
        // Unsave post
        await postHandler.unsavePost(currentUserId, post.post_id);
        // If on collections page, trigger removal
        if (onUnsave) {
          onUnsave(post.post_id);
        }
      }
    } catch (err) {
      console.error("Failed to toggle save:", err);
      // Rollback on error
      setIsSaved(previousState);
      alert("Failed to " + (isSaved ? "unsave" : "save") + " post. Please try again.");
    } finally {
      setLoadingSave(false);
    }
  };

  return (
    <GlassSurface
      width="100%"
      height="auto"
      borderRadius={20}
      opacity={0.5}
      blur={10}
      borderWidth={0.05}
      className="mb-6 !items-start !justify-start"
    >
      <div className="w-full relative z-10">
        {/* 👤 Post Header */}
        <div className="flex items-center justify-between mb-5">
          <div className="flex items-center space-x-4">
            <div className="relative group cursor-pointer">
              <div className="absolute -inset-0.5 bg-gradient-to-tr from-blue-500 to-purple-500 rounded-full opacity-75 group-hover:opacity-100 transition duration-200 blur-[2px]"></div>
              <img
                src={post.user.profilePic}
                alt="Profile"
                className="relative w-12 h-12 rounded-full border-2 border-[#0d1117] object-cover"
              />
            </div>
            <div>
              <h3 className="font-bold text-white text-lg leading-tight hover:text-blue-400 transition-colors cursor-pointer">
                {post.user.name}
              </h3>
              <div className="flex items-center text-gray-400 text-xs mt-0.5 space-x-2">
                <span>{post.user.major}</span>
                <span className="w-1 h-1 bg-gray-600 rounded-full"></span>
                <span>{post.timeAgo}</span>
                {showSavedBadge && post.savedAt && (
                  <>
                    <span className="w-1 h-1 bg-gray-600 rounded-full"></span>
                    <span className="text-blue-400">Saved {post.savedAt}</span>
                  </>
                )}
              </div>
            </div>
          </div>

          <button className="text-gray-400 hover:text-white p-2 rounded-full hover:bg-white/10 transition-colors">
            <i className="fas fa-ellipsis-h text-lg"></i>
          </button>
        </div>

        {/* 🏷️ Category Badge */}
        <div className="mb-4">
          <span className={`
            inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase
            bg-gradient-to-r from-blue-500/10 to-purple-500/10 border border-blue-500/20 text-blue-400
            hover:border-blue-500/40 transition-colors cursor-pointer
          `}>
            #{post.category.replace(/\s/g, "")}
          </span>
        </div>

        {/* 📝 Content */}
        <div className="mb-5">
          <p className="text-gray-200 text-[15px] leading-relaxed whitespace-pre-line">
            {post.content}
          </p>
        </div>

        {/* 📸 Media Gallery */}
        {post.media && Array.isArray(post.media) && post.media.length > 0 && (
          <div className={`grid gap-3 mb-6 rounded-xl overflow-hidden ${post.media.length === 1 ? 'grid-cols-1' :
            post.media.length === 2 ? 'grid-cols-2' :
              'grid-cols-2 md:grid-cols-3'
            }`}>
            {post.media.map((item, index) => {
              // Normalize media type (backend uses 'IMAGE', frontend expects 'Image')
              const mediaType = (item.type || '').toUpperCase();
              const isImage = mediaType === 'IMAGE';
              const isVideo = mediaType === 'VIDEO';

              // Debug logging
              console.log('Rendering media:', {
                media_id: item.media_id,
                type: item.type,
                normalizedType: mediaType,
                url: item.url,
                isImage,
                isVideo
              });

              return (
                <div key={item.media_id || index} className="relative group overflow-hidden bg-black/20 rounded-lg">
                  {isImage ? (
                    <div className={`overflow-hidden w-full ${post.media.length === 1 ? 'max-h-96' : 'h-48 md:h-64'}`}>
                      <img
                        src={item.url}
                        alt={`Post media ${index + 1}`}
                        className="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500 cursor-pointer"
                        onClick={() => window.open(item.url, '_blank')}
                        onLoad={() => console.log('Image loaded successfully:', item.url)}
                        onError={(e) => {
                          const errorInfo = {
                            url: item.url,
                            media_id: item.media_id,
                            type: item.type,
                            error: e.target.error ? e.target.error.message : 'Unknown error'
                          };
                          console.error('Failed to load image:', errorInfo);
                          console.error('Attempted URL:', item.url);

                          // Show error placeholder instead of hiding
                          e.target.style.display = 'none';
                          const errorDiv = document.createElement('div');
                          errorDiv.className = 'flex flex-col items-center justify-center w-full h-full bg-white/5 text-gray-400 p-8 text-center';
                          errorDiv.innerHTML = '<i class="fas fa-image-slash text-3xl mb-2"></i><span class="text-xs">Image not found</span>';
                          e.target.parentElement.appendChild(errorDiv);
                        }}
                      />
                    </div>
                  ) : isVideo ? (
                    <video
                      src={item.url}
                      controls
                      className={`w-full ${post.media.length === 1 ? 'max-h-96' : 'h-48 md:h-64'} object-cover`}
                      onLoadStart={() => console.log('Video loading:', item.url)}
                      onError={(e) => {
                        console.error('Failed to load video:', {
                          url: item.url,
                          media_id: item.media_id,
                          type: item.type
                        });
                        e.target.style.display = 'none';
                      }}
                    >
                      Your browser does not support the video tag.
                    </video>
                  ) : (
                    <div className="p-4 text-gray-400 text-sm">
                      Unknown media type: {item.type || 'undefined'} (normalized: {mediaType})
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        )}

        {/* 💬 Reaction Bar */}
        <div className="flex items-center justify-between pt-4 border-t border-white/5">
          <div className="flex items-center space-x-4">
            {/* 👍 Reaction */}
            <button
              onClick={toggleReaction}
              disabled={loadingInteraction}
              className={`
                flex items-center space-x-2 px-4 py-2 rounded-full transition-all duration-200
                ${post.isReacted
                  ? "bg-blue-500/20 text-blue-400"
                  : "hover:bg-white/5 text-gray-400 hover:text-white"
                }
                ${loadingInteraction ? "opacity-50 cursor-not-allowed" : "active:scale-95"}
              `}
            >
              <i className={`${post.isReacted ? "fas" : "far"} fa-thumbs-up text-lg`}></i>
              <span className="font-medium text-sm">{post.reactions}</span>
            </button>

            {/* 💭 Comment Toggle */}
            <button
              onClick={() => setShowComments(!showComments)}
              className={`
                flex items-center space-x-2 px-4 py-2 rounded-full transition-all duration-200
                ${showComments ? "bg-white/10 text-white" : "hover:bg-white/5 text-gray-400 hover:text-white"}
                active:scale-95
              `}
            >
              <i className={`${showComments ? "fas" : "far"} fa-comment text-lg`}></i>
              <span className="font-medium text-sm">
                {commentsCount}
              </span>
            </button>
          </div>

          <div className="flex items-center space-x-1">
            {/* Bookmark/Save Button */}
            <button
              onClick={toggleSave}
              disabled={loadingSave}
              className={`
                p-2 rounded-full transition-all duration-200
                ${isSaved
                  ? "text-blue-400 hover:text-blue-300 bg-blue-500/10 hover:bg-blue-500/20"
                  : "text-gray-400 hover:text-white hover:bg-white/5"
                }
                ${loadingSave ? "opacity-50 cursor-not-allowed" : "active:scale-95"}
              `}
              title={isSaved ? "Unsave post" : "Save post"}
            >
              <i className={`${isSaved ? "fas" : "far"} fa-bookmark`}></i>
            </button>

            {/* Share Button */}
            <button
              onClick={async () => {
                const shareUrl = `${window.location.origin}/posts?id=${post.post_id}`;
                if (navigator.share) {
                  try {
                    await navigator.share({
                      title: `Check out this post by ${post.user.name}`,
                      text: post.content.substring(0, 100) + '...',
                      url: shareUrl,
                    });
                  } catch (err) {
                    console.log('Error sharing:', err);
                  }
                } else {
                  navigator.clipboard.writeText(shareUrl);
                  alert('Link copied to clipboard!');
                }
              }}
              className="p-2 rounded-full text-gray-400 hover:text-white hover:bg-white/5 transition-all duration-200 active:scale-95"
              title="Share post"
            >
              <i className="fas fa-share-alt"></i>
            </button>
          </div>
        </div>

        {/* 🗨️ Comments Section */}
        {showComments && (
          <div className="mt-6 space-y-5 animate-fade-in">
            {/* ✏️ Add Comment */}
            <div className="flex items-center space-x-3 pt-2">
              <img
                src={`https://placehold.co/30x30/E5E7EB/6B7280?text=U`}
                alt="User"
                className="w-9 h-9 rounded-full border border-white/10"
              />
              <div className="flex-grow relative">
                <input
                  type="text"
                  placeholder="Write a comment..."
                  value={newComment}
                  onChange={(e) => setNewComment(e.target.value)}
                  onKeyPress={(e) => e.key === "Enter" && handleCommentSubmit()}
                  disabled={loadingCommentSubmit}
                  className="w-full bg-black/20 text-white rounded-full py-2.5 pl-4 pr-12 text-sm border border-white/10 focus:outline-none focus:border-blue-500/50 focus:bg-black/30 transition-all placeholder:text-gray-500"
                />
                <button
                  onClick={handleCommentSubmit}
                  disabled={loadingCommentSubmit || !newComment.trim()}
                  className="absolute right-2 top-1/2 -translate-y-1/2 text-blue-400 hover:text-blue-300 p-1.5 rounded-full hover:bg-blue-500/10 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                  <i className="fas fa-paper-plane text-sm"></i>
                </button>
              </div>
            </div>

            {/* 💬 Existing Comments */}
            {loadingComments ? (
              <div className="text-center py-4">
                <div className="inline-block animate-spin rounded-full h-5 w-5 border-2 border-blue-500 border-t-transparent"></div>
              </div>
            ) : comments.length === 0 ? (
              <p className="text-gray-500 text-sm text-center py-2">No comments yet. Be the first to share your thoughts!</p>
            ) : (
              <div className="space-y-4">
                {comments.map((comment, index) => (
                  <div key={comment.comment_id || index} className="flex space-x-3 group">
                    <img
                      src={comment.userPic}
                      alt="Commenter"
                      className="w-8 h-8 rounded-full border border-white/5 mt-1"
                    />
                    <div className="flex-grow">
                      <div className="bg-white/5 rounded-2xl px-4 py-2.5 inline-block min-w-[200px]">
                        <div className="flex items-center justify-between mb-1">
                          <span className="font-semibold text-white text-sm">{comment.userName}</span>
                          <span className="text-gray-500 text-xs">{comment.timeAgo}</span>
                        </div>
                        <p className="text-gray-300 text-sm leading-relaxed">{comment.content}</p>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        )}
      </div>
    </GlassSurface>
  );
};

export default PostCard;