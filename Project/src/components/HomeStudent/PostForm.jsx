import React from "react";

const PostForm = ({
  postFormRef,
  newPostContent,
  setNewPostContent,
  newPostCategory,
  setNewPostCategory,
  handlePost,
}) => (
  <div
    ref={postFormRef}
    className="bg-panel rounded-custom shadow-custom p-6 mb-6 border border-accent/10 transition-theme hover-glow"
  >
    {/* 🧑‍🎓 User input area */}
    <div className="flex items-start space-x-3">
      <img
        src="https://placehold.co/40x40/2563EB/FFFFFF?text=U"
        alt="User"
        className="w-10 h-10 rounded-full border-2 border-accent/60 shadow-md"
      />
      <textarea
        placeholder="What's on your mind? Share your progress, ideas, or questions..."
        value={newPostContent}
        onChange={(e) => setNewPostContent(e.target.value)}
        className="flex-grow bg-panel border border-accent/10 text-main placeholder:text-muted rounded-custom p-3 resize-none focus:outline-none focus:ring-1 focus:ring-accent/40 focus:border-accent/40 transition-theme"
        rows={3}
      ></textarea>
    </div>

    {/* ⚙️ Controls */}
    <div className="flex flex-col sm:flex-row justify-between items-center mt-5 space-y-3 sm:space-y-0">
      {/* 🏷️ Category Selector */}
      <div className="w-full sm:w-auto">
        <select
          value={newPostCategory}
          onChange={(e) => setNewPostCategory(e.target.value)}
          className="bg-panel text-main border border-accent/20 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-1 focus:ring-accent/40 transition-theme w-full sm:w-auto"
        >
          <option value="" disabled>
            Select Category
          </option>
          <option value="Study Group">📚 Study Group</option>
          <option value="Projects">👨‍💻 Projects</option>
          <option value="Events">🎉 Events</option>
          <option value="Questions">❓ Questions</option>
          <option value="Announcement">📢 Announcement</option>
        </select>
      </div>

      {/* 📎 File / Media Options */}
      <div className="flex space-x-6 text-muted w-full sm:w-auto justify-center sm:justify-start">
        <label className="cursor-pointer hover:text-green-400 transition-colors flex items-center space-x-1 font-medium">
          <i className="fas fa-image"></i>
          <span>Photo</span>
          <input type="file" accept="image/*" className="hidden" />
        </label>
        <button className="hover:text-yellow-400 transition-colors flex items-center space-x-1 font-medium">
          <i className="fas fa-paperclip"></i> <span>File</span>
        </button>
        <button className="hover:text-red-400 transition-colors flex items-center space-x-1 font-medium">
          <i className="fas fa-calendar-alt"></i> <span>Event</span>
        </button>
      </div>

      {/* 🚀 Post Button */}
      <button
        onClick={handlePost}
        className="bg-accent hover:bg-accent/80 text-white px-8 py-2.5 rounded-full font-semibold shadow-lg hover:shadow-accent/30 transition-all duration-200 w-full sm:w-auto"
      >
        Post
      </button>
    </div>
  </div>
);

export default PostForm;
