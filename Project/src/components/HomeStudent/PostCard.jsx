import React, { useState } from 'react';

const PostCard = ({ initialPost }) => {
  const [post, setPost] = useState(initialPost);
  const [showComments, setShowComments] = useState(false);
  
  let borderColor = 'border-gray-600';
  if (post.category === 'Study Group') borderColor = 'border-blue-600';
  if (post.category === 'Sports') borderColor = 'border-green-600';
  
  const toggleReaction = () => {
    setPost(prevPost => ({
      ...prevPost,
      isReacted: !prevPost.isReacted,
      reactions: prevPost.isReacted ? prevPost.reactions - 1 : prevPost.reactions + 1
    }));
  };

  return (
    <div className={`bg-gray-800 rounded-xl shadow-2xl p-6 post-item border-l-4 ${borderColor}`}>
      
      {/* Post Header */}
      <div className="flex items-center space-x-3 mb-4">
        <img src={post.user.profilePic} alt="Profile" className="w-10 h-10 rounded-full border-2 border-gray-600" />
        <div className="flex-grow">
          <span className="font-semibold text-white">{post.user.name}</span>
          <span className="text-gray-400 text-sm block">{post.user.major} • {post.timeAgo}</span>
        </div>
        <i className="fas fa-ellipsis-h text-gray-500 hover:text-white cursor-pointer"></i>
      </div>
      
      {/* Category Tag */}
      <div className="mb-4">
        <span className="inline-block bg-blue-900 text-blue-400 text-xs font-semibold px-3 py-1 rounded-full">
          #{post.category.replace(/\s/g, '')}
        </span>
      </div>
      
      {/* Post Content */}
      <p className="text-gray-300 leading-relaxed mb-4">{post.content}</p>
      {post.image && (
        <img src={post.image} alt="Post Image" className="w-full rounded-xl mb-4 shadow-lg" />
      )}
      
      {/* Reaction Bar */}
      <div className="reaction-bar flex items-center justify-between text-gray-400 border-t border-gray-700 pt-3 mt-4">
        <div className="flex items-center space-x-6">
          <button 
            onClick={toggleReaction} 
            className={`flex items-center space-x-2 transition-colors font-medium ${post.isReacted ? 'text-blue-400' : 'hover:text-blue-400'}`}
          >
            <i className={`${post.isReacted ? 'fas' : 'far'} fa-thumbs-up`}></i>
            <span className="reaction-count">{post.reactions} Reactions</span>
          </button>
          
          <button 
            onClick={() => setShowComments(!showComments)} 
            className="flex items-center space-x-2 hover:text-blue-400 transition-colors font-medium"
          >
            <i className={`${showComments ? 'fas' : 'far'} fa-comment`}></i>
            <span>{post.comments.length} Comment{post.comments.length !== 1 ? 's' : ''}</span>
          </button>
        </div>
      </div>
      
      {/* Comment Section */}
      {showComments && (
        <div className="comment-section mt-4 space-y-4">
          <div className="flex items-start space-x-2 pt-2">
            <img src="https://placehold.co/30x30/E5E7EB/6B7280?text=U" alt="User" className="w-8 h-8 rounded-full" />
            <input type="text" placeholder="Write a comment..." className="flex-grow bg-gray-700 text-white rounded-full py-2 px-4 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500" />
            <button className="text-blue-400 hover:text-blue-300 font-semibold text-sm">Send</button>
          </div>

          {post.comments.map((comment, index) => (
            <div key={index} className="flex items-start space-x-3 p-3 bg-gray-700 rounded-xl">
              <img src={comment.userPic} alt="Commenter" className="w-8 h-8 rounded-full" />
              <div>
                <p className="text-white font-medium text-sm">{comment.userName} <span className="text-gray-500 font-normal text-xs ml-2">{comment.timeAgo}</span></p>
                <p className="text-gray-300 text-sm mt-1">{comment.content}</p>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
};

export default PostCard;