import React, { useState, useEffect } from "react";
import * as postHandler from "../../../api/postHandler";

const PostCard = ({ initialPost, onRefresh, currentUserId }) => {
  const [post, setPost] = useState(initialPost);
  const [showComments, setShowComments] = useState(false);
  const [comments, setComments] = useState([]);
  const [newComment, setNewComment] = useState("");
  const [loadingComments, setLoadingComments] = useState(false);
  const [loadingInteraction, setLoadingInteraction] = useState(false);
  const [loadingCommentSubmit, setLoadingCommentSubmit] = useState(false);

  // 🌈 Border color based on category, using accent-friendly tones
  let borderColor = "border-accent/40";
  if (post.category === "Questions") borderColor = "border-blue-500/60";
  if (post.category === "Events") borderColor = "border-green-500/60";
  if (post.category === "Projects") borderColor = "border-purple-500/60";

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
        // Remove like - would need interaction_id, for now just toggle
        // TODO: Store interaction_id when fetching posts to enable unlike
        console.warn("Unlike functionality requires interaction_id from backend");
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

      // Refresh comments
      await fetchComments();

      // Update comment count in post
      setPost((prev) => ({
        ...prev,
        comments: [...prev.comments, { content: newComment }], // Temporary update
      }));
    } catch (err) {
      console.error("Failed to add comment:", err);
      alert("Failed to add comment: " + (err.message || "Unknown error"));
    } finally {
      setLoadingCommentSubmit(false);
    }
  };

  return (
    <div
      className={`backdrop-blur-xl bg-white/10 dark:bg-black/20 rounded-custom shadow-2xl p-6 border border-white/20 ${borderColor} transition-all duration-300 hover:shadow-accent/20 hover:border-white/30 hover:bg-white/15`}
      style={{
        backdropFilter: 'blur(20px) saturate(180%)',
        WebkitBackdropFilter: 'blur(20px) saturate(180%)',
      }}
    >
      {/* 👤 Post Header */}
      <div className="flex items-center space-x-3 mb-4">
        <img
          src={post.user.profilePic}
          alt="Profile"
          className="w-10 h-10 rounded-full border-2 border-accent/30"
        />
        <div className="flex-grow">
          <span className="font-semibold text-main">{post.user.name}</span>
          <span className="text-muted text-sm block">
            {post.user.major} • {post.timeAgo}
          </span>
        </div>
        <i className="fas fa-ellipsis-h text-muted hover:text-accent cursor-pointer"></i>
      </div>

      {/* 🏷️ Category */}
      <div className="mb-4">
        <span className="inline-block bg-accent/20 text-accent text-xs font-semibold px-3 py-1 rounded-full">
          #{post.category.replace(/\s/g, "")}
        </span>
      </div>

      {/* 📝 Content */}
      <p className="text-main/90 leading-relaxed mb-4">{post.content}</p>

      {/* 📸 Media Gallery */}
      {post.media && post.media.length > 0 && (
        <div className={`grid gap-2 mb-4 ${post.media.length === 1 ? 'grid-cols-1' :
          post.media.length === 2 ? 'grid-cols-2' :
            'grid-cols-2 md:grid-cols-3'
          }`}>
          {post.media.map((item, index) => (
            <div key={item.media_id || index} className="relative">
              {item.type === 'Image' ? (
                <img
                  src={item.url}
                  alt={`Post media ${index + 1}`}
                  className="w-full h-48 object-cover rounded-custom shadow-custom cursor-pointer hover:opacity-90 transition-opacity"
                  onClick={() => window.open(item.url, '_blank')}
                />
              ) : item.type === 'Video' ? (
                <video
                  src={item.url}
                  controls
                  className="w-full h-48 object-cover rounded-custom shadow-custom"
                >
                  Your browser does not support the video tag.
                </video>
              ) : null}
            </div>
          ))}
        </div>
      )}

      {/* 💬 Reaction Bar */}
      <div className="reaction-bar flex items-center justify-between text-muted border-t border-accent/10 pt-3 mt-4">
        <div className="flex items-center space-x-6">
          {/* 👍 Reaction */}
          <button
            onClick={toggleReaction}
            disabled={loadingInteraction}
            className={`flex items-center space-x-2 transition-all duration-200 font-medium ${post.isReacted
              ? "text-accent"
              : "hover:text-accent text-muted transition-colors"
              } ${loadingInteraction ? "opacity-50 cursor-not-allowed" : ""}`}
          >
            <i className={`${post.isReacted ? "fas" : "far"} fa-thumbs-up`}></i>
            <span>{post.reactions} Reactions</span>
          </button>

          {/* 💭 Comment Toggle */}
          <button
            onClick={() => setShowComments(!showComments)}
            className="flex items-center space-x-2 hover:text-accent transition-all duration-200 font-medium"
          >
            <i className={`${showComments ? "fas" : "far"} fa-comment`}></i>
            <span>
              {comments.length || post.comments.length} Comment
              {(comments.length || post.comments.length) !== 1 ? "s" : ""}
            </span>
          </button>
        </div>
      </div>

      {/* 🗨️ Comments Section */}
      {showComments && (
        <div className="comment-section mt-4 space-y-4 animate-fade-in">
          {/* ✏️ Add Comment */}
          <div className="flex items-start space-x-2 pt-2">
            <img
              src={`https://placehold.co/30x30/E5E7EB/6B7280?text=U`}
              alt="User"
              className="w-8 h-8 rounded-full"
            />
            <input
              type="text"
              placeholder="Write a comment..."
              value={newComment}
              onChange={(e) => setNewComment(e.target.value)}
              onKeyPress={(e) => e.key === "Enter" && handleCommentSubmit()}
              disabled={loadingCommentSubmit}
              className="flex-grow bg-panel text-main rounded-full py-2 px-4 text-sm border border-accent/20 focus:outline-none focus:ring-1 focus:ring-accent/40 transition-theme disabled:opacity-50"
            />
            <button
              onClick={handleCommentSubmit}
              disabled={loadingCommentSubmit || !newComment.trim()}
              className="text-accent hover:text-accent/80 font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {loadingCommentSubmit ? "..." : "Send"}
            </button>
          </div>

          {/* 💬 Existing Comments */}
          {loadingComments ? (
            <div className="text-center py-4">
              <div className="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-accent"></div>
            </div>
          ) : comments.length === 0 ? (
            <p className="text-muted text-sm text-center py-2">No comments yet. Be the first to comment!</p>
          ) : (
            comments.map((comment, index) => (
              <div
                key={comment.comment_id || index}
                className="flex items-start space-x-3 p-3 backdrop-blur-lg bg-white/5 dark:bg-black/10 border border-white/10 rounded-custom shadow-lg transition-all duration-200 hover:bg-white/10"
                style={{
                  backdropFilter: 'blur(12px) saturate(150%)',
                  WebkitBackdropFilter: 'blur(12px) saturate(150%)',
                }}
              >
                <img
                  src={comment.userPic}
                  alt="Commenter"
                  className="w-8 h-8 rounded-full"
                />
                <div>
                  <p className="text-main font-medium text-sm">
                    {comment.userName}{" "}
                    <span className="text-muted font-normal text-xs ml-2">
                      {comment.timeAgo}
                    </span>
                  </p>
                  <p className="text-main/80 text-sm mt-1">{comment.content}</p>
                </div>
              </div>
            ))
          )}
        </div>
      )}
    </div>
  );
};

export default PostCard;
