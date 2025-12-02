import React, { useState } from "react";

const PostForm = ({
  postFormRef,
  newPostContent,
  setNewPostContent,
  newPostCategory,
  setNewPostCategory,
  handlePost,
  selectedFiles,
  setSelectedFiles,
}) => {
  const [previewUrls, setPreviewUrls] = useState([]);

  const handleFileSelect = (e) => {
    const files = Array.from(e.target.files);
    setSelectedFiles(files);

    // Create preview URLs
    const urls = files.map((file) => {
      if (file.type.startsWith("image/")) {
        return { type: "image", url: URL.createObjectURL(file), name: file.name };
      } else if (file.type.startsWith("video/")) {
        return { type: "video", url: URL.createObjectURL(file), name: file.name };
      }
      return null;
    }).filter(Boolean);

    setPreviewUrls(urls);
  };

  const removeFile = (index) => {
    const newFiles = selectedFiles.filter((_, i) => i !== index);
    const newPreviews = previewUrls.filter((_, i) => i !== index);

    // Revoke the URL to free memory
    URL.revokeObjectURL(previewUrls[index].url);

    setSelectedFiles(newFiles);
    setPreviewUrls(newPreviews);
  };

  return (
    <div
      ref={postFormRef}
      className="backdrop-blur-xl bg-white/10 dark:bg-black/20 rounded-custom shadow-2xl p-6 mb-6 border border-white/20 transition-all duration-300 hover:shadow-accent/20 hover:border-white/30 hover:bg-white/15"
      style={{
        backdropFilter: 'blur(20px) saturate(180%)',
        WebkitBackdropFilter: 'blur(20px) saturate(180%)',
      }}
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

      {/* 📸 Media Previews */}
      {previewUrls.length > 0 && (
        <div className="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
          {previewUrls.map((preview, index) => (
            <div key={index} className="relative group">
              {preview.type === "image" ? (
                <img
                  src={preview.url}
                  alt={preview.name}
                  className="w-full h-32 object-cover rounded-lg border border-accent/20"
                />
              ) : (
                <video
                  src={preview.url}
                  className="w-full h-32 object-cover rounded-lg border border-accent/20"
                  controls
                />
              )}
              <button
                onClick={() => removeFile(index)}
                className="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
              >
                ✕
              </button>
              <div className="absolute bottom-0 left-0 right-0 bg-black/50 text-white text-xs p-1 rounded-b-lg truncate">
                {preview.name}
              </div>
            </div>
          ))}
        </div>
      )}

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
            <span>Photo/Video</span>
            <input
              type="file"
              accept="image/*,video/*"
              multiple
              onChange={handleFileSelect}
              className="hidden"
            />
          </label>
          {selectedFiles.length > 0 && (
            <span className="text-accent font-semibold">
              {selectedFiles.length} file{selectedFiles.length > 1 ? "s" : ""} selected
            </span>
          )}
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
};

export default PostForm;
