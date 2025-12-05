import React, { useState } from "react";
import GlassSurface from "../Login_Components/LiquidGlass/GlassSurface";

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
    <div ref={postFormRef} className="mb-6">
      <GlassSurface
        width="100%"
        height="auto"
        borderRadius={20}
        opacity={0.5}
        blur={10}
        borderWidth={0.05}
        className="!items-start !justify-start"
      >
        <div className="w-full relative z-10">
          {/* 🧑‍🎓 User input area */}
          <div className="flex items-start space-x-4">
            <div className="relative group">
              <div className="absolute -inset-0.5 bg-gradient-to-tr from-blue-500 to-purple-500 rounded-full opacity-75 group-hover:opacity-100 transition duration-200 blur-[2px]"></div>
              <img
                src="https://placehold.co/40x40/E5E7EB/6B7280?text=U"
                alt="User"
                className="relative w-12 h-12 rounded-full border-2 border-[#0d1117] object-cover"
              />
            </div>
            <div className="flex-grow">
              <textarea
                placeholder="What's on your mind? Share your progress, ideas, or questions..."
                value={newPostContent}
                onChange={(e) => setNewPostContent(e.target.value)}
                className="w-full bg-transparent text-white text-base placeholder:text-gray-500 focus:outline-none resize-none leading-relaxed"
                rows="5"
              ></textarea>
            </div>
          </div>

          {/* 🖼️ Media Preview */}
          {selectedFiles.length > 0 && (
            <div className="mt-6 grid grid-cols-2 md:grid-cols-3 gap-3">
              {selectedFiles.map((file, index) => (
                <div key={index} className="relative group overflow-hidden rounded-xl bg-black/20">
                  <img
                    src={URL.createObjectURL(file)}
                    alt={`Preview ${index + 1}`}
                    className="w-full h-32 object-cover transform group-hover:scale-105 transition-transform duration-300"
                  />
                  <button
                    onClick={() =>
                      setSelectedFiles(selectedFiles.filter((_, i) => i !== index))
                    }
                    className="absolute top-2 right-2 bg-red-500/90 hover:bg-red-600 text-white rounded-full w-7 h-7 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 shadow-lg"
                  >
                    <i className="fas fa-times text-xs"></i>
                  </button>
                </div>
              ))}
            </div>
          )}

          {/* 🎨 Actions */}
          <div className="flex items-center justify-between mt-6 pt-5 border-t border-white/5">
            <div className="flex items-center space-x-2">
              {/* 📷 Photo/Video */}
              <label className="flex items-center space-x-2 px-4 py-2 cursor-pointer text-gray-400 hover:text-white hover:bg-white/5 rounded-full transition-all duration-200">
                <i className="fas fa-image text-lg"></i>
                <span className="hidden sm:inline font-medium text-sm">Photo/Video</span>
                <input
                  type="file"
                  accept="image/*,video/*"
                  multiple
                  onChange={(e) =>
                    setSelectedFiles([
                      ...selectedFiles,
                      ...Array.from(e.target.files),
                    ])
                  }
                  className="hidden"
                />
              </label>

              {/* 🏷️ Category Selector */}
              <div className="relative">
                <select
                  value={newPostCategory}
                  onChange={(e) => setNewPostCategory(e.target.value)}
                  className="bg-black/20 text-white border border-white/10 rounded-full py-2 px-4 pr-8 text-sm font-medium focus:outline-none focus:border-blue-500/50 transition-all cursor-pointer appearance-none hover:bg-black/30"
                  style={{
                    backgroundImage: `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%239CA3AF' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E")`,
                    backgroundPosition: 'right 0.5rem center',
                    backgroundRepeat: 'no-repeat',
                    backgroundSize: '1.5em 1.5em',
                  }}
                >
                  <option value="" className="bg-[#0d1117] text-white">Select Category</option>
                  <option value="General" className="bg-[#0d1117] text-white">General</option>
                  <option value="Questions" className="bg-[#0d1117] text-white">Questions</option>
                  <option value="Events" className="bg-[#0d1117] text-white">Events</option>
                  <option value="Projects" className="bg-[#0d1117] text-white">Projects</option>
                  <option value="Announcements" className="bg-[#0d1117] text-white">Announcements</option>
                </select>
              </div>
            </div>

            {/* ✅ Post Button */}
            <button
              onClick={handlePost}
              disabled={!newPostContent.trim() || !newPostCategory}
              className="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-2.5 px-8 rounded-full shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none active:scale-95"
            >
              Post
            </button>
          </div>
        </div>
      </GlassSurface>
    </div>
  );
};

export default PostForm;