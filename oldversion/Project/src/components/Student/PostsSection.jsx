import React from "react";

function PostsSection({ posts }) {
  return (
    <section className="rounded-xl border border-white/10 bg-white/5 backdrop-blur-sm p-6 shadow-lg">
      <h2 className="text-lg font-semibold text-white mb-5">📝 Blog Posts</h2>

      <div className="space-y-5">
        {posts.map((post, i) => (
          <article
            key={i}
            className="flex gap-4 items-start border border-white/10 rounded-xl p-4 bg-white/5 hover:bg-white/10 transition-all duration-300"
          >
            {post.image && (
              <img
                src={post.image}
                alt={post.title}
                className="w-20 h-20 rounded-lg object-cover flex-shrink-0"
              />
            )}
            <div>
              <h3 className="text-accent font-semibold mb-1">{post.title}</h3>
              <span className="text-xs text-gray-400 mb-2 block">
                📅 {post.date}
              </span>
              <p className="text-gray-300 text-sm line-clamp-3">{post.excerpt}</p>
            </div>
          </article>
        ))}
      </div>
    </section>
  );
}

export default PostsSection;
