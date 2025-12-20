import React, { useState } from "react";
import * as postHandler from "../../../api/postHandler";

function PostsSection({ posts, onRefresh, userId }) {
  const [deletingPostId, setDeletingPostId] = useState(null);
  const [editingPost, setEditingPost] = useState(null);
  const [editContent, setEditContent] = useState("");

  const handleDeletePost = async (postId) => {
    if (!confirm("Are you sure you want to delete this post? This action cannot be undone.")) {
      return;
    }

    try {
      setDeletingPostId(postId);
      await postHandler.deletePost(postId);
      
      if (onRefresh) {
        await onRefresh();
      }
    } catch (err) {
      console.error("Failed to delete post:", err);
      alert(err.message || "Failed to delete post. Please try again.");
    } finally {
      setDeletingPostId(null);
    }
  };

  const handleEditPost = (post) => {
    setEditingPost(post);
    setEditContent(post.content);
  };

  const handleSaveEdit = async () => {
    if (!editContent.trim()) {
      alert("Post content cannot be empty.");
      return;
    }

    try {
      await postHandler.updatePost({
        post_id: editingPost.post_id,
        content: editContent.trim()
      });

      setEditingPost(null);
      setEditContent("");
      
      if (onRefresh) {
        await onRefresh();
      }
    } catch (err) {
      console.error("Failed to update post:", err);
      alert(err.message || "Failed to update post. Please try again.");
    }
  };

  const handleCancelEdit = () => {
    setEditingPost(null);
    setEditContent("");
  };

  return (
    <section className="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm p-6 shadow-lg"
      style={{ backdropFilter: 'blur(20px) saturate(180%)', WebkitBackdropFilter: 'blur(20px) saturate(180%)' }}>
      <h2 className="text-lg font-semibold text-white mb-5">📝 Recent Posts</h2>

      {posts.length === 0 ? (
        <div className="text-center py-8">
          <p className="text-muted">No posts yet. Share your first post!</p>
        </div>
      ) : (
        <div className="space-y-5">
          {posts.map((post, i) => (
            <article
              key={post.post_id || i}
              className="flex gap-4 items-start border border-white/10 rounded-xl p-4 bg-white/5 hover:bg-white/10 transition-all duration-300 relative group"
            >
              <div className="flex-1">
                <div className="flex items-center gap-2 mb-1">
                  <h3 className="text-accent font-semibold">{post.title}</h3>
                  <span className="px-2 py-0.5 text-xs rounded-full bg-accent/20 text-accent border border-accent/30">
                    {post.category}
                  </span>
                </div>
                <span className="text-xs text-gray-400 mb-2 block">
                  📅 {post.date}
                </span>
                
                {editingPost?.post_id === post.post_id ? (
                  <div className="space-y-3">
                    <textarea
                      value={editContent}
                      onChange={(e) => setEditContent(e.target.value)}
                      className="w-full bg-black/20 text-white rounded-lg p-3 border border-white/10 focus:outline-none focus:border-accent/50 resize-none"
                      rows="4"
                      placeholder="Edit your post content..."
                    />
                    <div className="flex gap-2 justify-end">
                      <button
                        onClick={handleCancelEdit}
                        className="px-3 py-1.5 text-sm bg-white/10 text-white rounded-lg hover:bg-white/20 transition"
                      >
                        Cancel
                      </button>
                      <button
                        onClick={handleSaveEdit}
                        className="px-3 py-1.5 text-sm bg-accent text-white rounded-lg hover:bg-accent/90 transition"
                      >
                        Save
                      </button>
                    </div>
                  </div>
                ) : (
                  <>
                    <p className="text-gray-300 text-sm line-clamp-3">{post.content}</p>
                    {post.likes_count > 0 && (
                      <div className="mt-2 flex items-center gap-1 text-xs text-gray-400">
                        <span>❤️</span>
                        <span>{post.likes_count} {post.likes_count === 1 ? 'like' : 'likes'}</span>
                      </div>
                    )}
                  </>
                )}
              </div>

              {/* Action buttons - visible on hover */}
              {!editingPost && (
                <div className="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                  <button
                    onClick={() => handleEditPost(post)}
                    className="text-gray-400 hover:text-purple-400 transition-colors p-2"
                    title="Edit post"
                  >
                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                  </button>
                  <button
                    onClick={() => handleDeletePost(post.post_id)}
                    disabled={deletingPostId === post.post_id}
                    className="text-gray-400 hover:text-red-400 transition-colors p-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Delete post"
                  >
                    {deletingPostId === post.post_id ? (
                      <span className="text-xs">...</span>
                    ) : (
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    )}
                  </button>
                </div>
              )}
            </article>
          ))}
        </div>
      )}
    </section>
  );
}

export default PostsSection;
