import React from "react";

function PostsSection({ posts }) {
  return (
    <div className="bg-panel rounded-custom shadow-custom p-6">
      <h2 className="text-lg font-semibold mb-4">📝 Posts</h2>
      {posts.map((post, i) => (
        <article key={i} className={i !== posts.length - 1 ? "mb-4" : ""}>
          <h3 className="font-medium text-main">{post.title}</h3>
          <span className="text-sm text-muted block mb-2">
            Published {post.date}
          </span>
          <p className="text-muted">{post.excerpt}</p>
        </article>
      ))}
    </div>
  );
}

export default PostsSection;
