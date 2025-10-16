import React from 'react';

const PostForm = ({ postFormRef, newPostContent, setNewPostContent, newPostCategory, setNewPostCategory, handlePost }) => (
    <div className="bg-gray-800 rounded-xl shadow-2xl p-6 mb-6" ref={postFormRef}>
        <div className="flex items-start space-x-3">
            <img src="https://placehold.co/40x40/E5E7EB/6B7280?text=U" alt="User" className="w-10 h-10 rounded-full border-2 border-blue-500" />
            <textarea 
                placeholder="What's on your mind? Share your progress, questions, or ideas..." 
                value={newPostContent}
                onChange={(e) => setNewPostContent(e.target.value)}
                className="flex-grow bg-gray-700 text-white rounded-xl p-3 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-400"
            ></textarea>
        </div>
        <div className="flex flex-col sm:flex-row justify-between items-center mt-4 space-y-3 sm:space-y-0">
            <div className="w-full sm:w-auto">
                <select 
                    value={newPostCategory}
                    onChange={(e) => setNewPostCategory(e.target.value)}
                    className="bg-gray-700 text-gray-300 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border border-gray-600"
                >
                    <option value="" disabled>Select Category</option>
                    <option value="Study Group">📚 Study Group</option>
                    <option value="Projects"> 👨‍👩‍👧‍👦 Projects</option>
                    <option value="Events">🎉 Events</option>
                    <option value="Questions">❓ Questions</option>
                    <option value="Announcement">📢 Announcement</option>
                </select>
            </div>
            
            <div className="flex space-x-6 text-gray-400 w-full sm:w-auto justify-center sm:justify-start">
                <label className="cursor-pointer hover:text-green-400 transition-colors flex items-center space-x-1 font-medium">
                    <i className="fas fa-image"></i>
                    <span>Photo</span>
                    <input type="file" accept="image/*" className="hidden" />
                </label>
                <button className="hover:text-yellow-400 transition-colors flex items-center space-x-1 font-medium"><i className="fas fa-paperclip"></i> <span>File</span></button>
                <button className="hover:text-red-400 transition-colors flex items-center space-x-1 font-medium"><i className="fas fa-calendar-alt"></i> <span>Event</span></button>
            </div>
            
            <button onClick={handlePost} className="bg-blue-600 text-white px-6 py-2.5 rounded-full font-semibold hover:bg-blue-700 transition-colors w-full sm:w-auto shadow-md">Post</button>
        </div>
    </div>
);

export default PostForm;