import React, { useState } from "react";

const PostCard = ({ initialPost }) => {
  const [post, setPost] = useState(initialPost);
  const [showComments, setShowComments] = useState(false);

  // 🌈 Border color based on category, using accent-friendly tones
  let borderColor = "border-accent/40";
  if (post.category === "Study Group") borderColor = "border-blue-500/60";
  if (post.category === "Sports") borderColor = "border-green-500/60";

  const toggleReaction = () => {
    setPost((prev) => ({
      ...prev,
      isReacted: !prev.isReacted,
      reactions: prev.isReacted ? prev.reactions - 1 : prev.reactions + 1,
    }));
  };

  return (
    <div
      className={`bg-panel rounded-custom shadow-custom p-6 border-l-4 ${borderColor} transition-theme hover-glow`}
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
      {post.image && (
        <img
          src={post.image}
          alt="Post"
          className="w-full rounded-custom mb-4 shadow-custom"
        />
      )}

      {/* 💬 Reaction Bar */}
      <div className="reaction-bar flex items-center justify-between text-muted border-t border-accent/10 pt-3 mt-4">
        <div className="flex items-center space-x-6">
          {/* 👍 Reaction */}
          <button
            onClick={toggleReaction}
            className={`flex items-center space-x-2 transition-all duration-200 font-medium ${
              post.isReacted
                ? "text-accent"
                : "hover:text-accent text-muted transition-colors"
            }`}
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
              {post.comments.length} Comment
              {post.comments.length !== 1 ? "s" : ""}
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
              src="https://placehold.co/30x30/E5E7EB/6B7280?text=U"
              alt="User"
              className="w-8 h-8 rounded-full"
            />
            <input
              type="text"
              placeholder="Write a comment..."
              className="flex-grow bg-panel text-main rounded-full py-2 px-4 text-sm border border-accent/20 focus:outline-none focus:ring-1 focus:ring-accent/40 transition-theme"
            />
            <button className="text-accent hover:text-accent/80 font-semibold text-sm">
              Send
            </button>
          </div>

          {/* 💬 Existing Comments */}
          {post.comments.map((comment, index) => (
            <div
              key={index}
              className="flex items-start space-x-3 p-3 bg-panel border border-accent/10 rounded-custom shadow-sm transition-theme"
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
          ))}
        </div>
      )}
    </div>
  );
};

export default PostCard;
