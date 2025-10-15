// src/components/PostCreator.jsx

import React, { useState } from 'react';


const PostCreator = ({ onPostSubmit }) => {
  // 1. State Management for the Form
  const [content, setContent] = useState('');
  const [category, setCategory] = useState('');
  const [photoFile, setPhotoFile] = useState(null);

  
  const categories = [
    { value: 'Study Group', label: '📚 Study Group' },
    { value: 'Projects', label: '👨‍👩‍👧‍👦 Projects' },
    { value: 'Events', label: '🎉 Events' },
    { value: 'Questions', label: '❓ Questions' },
    { value: 'Announcement', label: '📢 Announcement' },
  ];

  // 2. Form Submission Handler
  const handleSubmit = (e) => {
    e.preventDefault();
    
    if (!content.trim()) {
      alert('Please enter some content before posting.');
      return;
    }
    
    if (!category) {
      alert('Please select a category before posting.');
      return;
    }

    
    onPostSubmit({ 
      content, 
      category, 
      hasPhoto: !!photoFile,
      timestamp: 'Just now'
    });

   
    setContent('');
    setCategory('');
    setPhotoFile(null);
  };
  
  return (
    <div className="bg-gray-800 rounded-xl shadow-2xl p-6 mb-6" id="new-post-form">
      
      {/* 5. The form element for proper submission handling */}
      <form onSubmit={handleSubmit}>
        <div className="flex items-start space-x-3">
          {/* User Avatar */}
          <img 
            src="https://placehold.co/40x40/E5E7EB/6B7280?text=U" 
            alt="User" 
            className="w-10 h-10 rounded-full border-2 border-blue-500"
          />
          
          {/* Post Content Textarea */}
          <textarea
            id="post-content"
            placeholder="What's on your mind? Share your progress, questions, or ideas..."
            className="flex-grow bg-gray-700 text-white rounded-xl p-3 resize-none focus:outline-none focus:ring-2 focus:ring-blue-500 placeholder-gray-400"
            value={content}
            onChange={(e) => setContent(e.target.value)}
            rows="3" 
          />
        </div>

        <div className="flex flex-col sm:flex-row justify-between items-center mt-4 space-y-3 sm:space-y-0">
          
          {/* Category Select Dropdown */}
          <div className="w-full sm:w-auto">
            <select
              id="post-category"
              className="bg-gray-700 text-gray-300 py-2.5 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 border border-gray-600"
              value={category}
              onChange={(e) => setCategory(e.target.value)}
            >
              <option value="" disabled>Select Category</option>
              {categories.map((cat) => (
                <option key={cat.value} value={cat.value}>{cat.label}</option>
              ))}
            </select>
          </div>
          
          {/* Attachment Buttons */}
          <div className="flex space-x-6 text-gray-400 w-full sm:w-auto justify-center sm:justify-start">
            
            {/* Photo Input (with custom label) */}
            <label htmlFor="post-photo-input" className={`cursor-pointer transition-colors flex items-center space-x-1 font-medium ${photoFile ? 'text-green-400' : 'hover:text-green-400'}`}>
              <i className="fas fa-image"></i>
              <span>{photoFile ? 'Photo (1 attached)' : 'Photo'}</span>
              <input
                type="file"
                id="post-photo-input"
                accept="image/*"
                className="hidden"
                onChange={(e) => setPhotoFile(e.target.files[0])}
              />
            </label>

            {/* File Button (placeholder for now) */}
            <button type="button" className="hover:text-yellow-400 transition-colors flex items-center space-x-1 font-medium">
              <i className="fas fa-paperclip"></i> 
              <span>File</span>
            </button>

            {/* Event Button (placeholder for now) */}
            <button type="button" className="hover:text-red-400 transition-colors flex items-center space-x-1 font-medium">
              <i className="fas fa-calendar-alt"></i> 
              <span>Event</span>
            </button>
          </div>
          
          {/* Post Button */}
          <button 
            type="submit" 
            className="bg-blue-600 text-white px-6 py-2.5 rounded-full font-semibold hover:bg-blue-700 transition-colors w-full sm:w-auto shadow-md"
            disabled={!content.trim() || !category} 
          >
            Post
          </button>
        </div>
      </form>
    </div>
  );
};

export default PostCreator;