import React from "react";

function PostsSection({ posts }) {
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
              className="flex gap-4 items-start border border-white/10 rounded-xl p-4 bg-white/5 hover:bg-white/10 transition-all duration-300"
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
                <p className="text-gray-300 text-sm line-clamp-3">{post.content}</p>
                {post.likes_count > 0 && (
                  <div className="mt-2 flex items-center gap-1 text-xs text-gray-400">
                    <span>❤️</span>
                    <span>{post.likes_count} {post.likes_count === 1 ? 'like' : 'likes'}</span>
                  </div>
                )}
              </div>
            </article>
          ))}
        </div>
      )}
    </section>
  );
}

export default PostsSection;
