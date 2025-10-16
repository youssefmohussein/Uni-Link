import React, { useState } from "react";

/* Post Management Section for Professor with current data without any database connection */
function PostManagementSection() {
  const [searchTerm, setSearchTerm] = useState("");
  const [filterStatus, setFilterStatus] = useState("all");
  const [posts, setPosts] = useState([
    {
      id: 1,
      title: "Best Practices for React Development",
      content: "In this post, I'll share some essential best practices for React development that every student should know...",
      author: "Dr. Sarah Johnson",
      status: "published",
      publishDate: "2024-01-20",
      views: 245,
      likes: 18,
      comments: 7,
      tags: ["React", "JavaScript", "Best Practices"],
      category: "Tutorial"
    },
    {
      id: 2,
      title: "Database Design Principles",
      content: "Understanding database design is crucial for building scalable applications. Here are the key principles...",
      author: "Dr. Sarah Johnson",
      status: "published",
      publishDate: "2024-01-18",
      views: 189,
      likes: 12,
      comments: 4,
      tags: ["Database", "SQL", "Design"],
      category: "Educational"
    },
    {
      id: 3,
      title: "Upcoming Project Deadlines",
      content: "Just a reminder about the upcoming project deadlines for this semester. Please make sure to submit your projects on time...",
      author: "Dr. Sarah Johnson",
      status: "draft",
      publishDate: null,
      views: 0,
      likes: 0,
      comments: 0,
      tags: ["Announcement", "Deadlines"],
      category: "Announcement"
    },
    {
      id: 4,
      title: "Introduction to Machine Learning",
      content: "Machine learning is becoming increasingly important in software development. Let's explore the basics...",
      author: "Dr. Sarah Johnson",
      status: "scheduled",
      publishDate: "2024-01-25",
      views: 0,
      likes: 0,
      comments: 0,
      tags: ["Machine Learning", "AI", "Python"],
      category: "Tutorial"
    }
  ]);

  const [selectedPost, setSelectedPost] = useState(null);
  const [postModal, setPostModal] = useState(false);
  const [createPostModal, setCreatePostModal] = useState(false);

  const filteredPosts = posts.filter(post => {
    const matchesSearch = post.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         post.content.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         post.tags.some(tag => tag.toLowerCase().includes(searchTerm.toLowerCase()));
    const matchesFilter = filterStatus === "all" || post.status === filterStatus;
    return matchesSearch && matchesFilter;
  });

  const getStatusColor = (status) => {
    switch (status) {
      case "published": return "text-green-400 bg-green-400/20";
      case "draft": return "text-yellow-400 bg-yellow-400/20";
      case "scheduled": return "text-blue-400 bg-blue-400/20";
      case "archived": return "text-gray-400 bg-gray-400/20";
      default: return "text-muted bg-main/20";
    }
  };

  const handleCreatePost = (postData) => {
    const newPost = {
      id: posts.length + 1,
      ...postData,
      author: "Dr. Sarah Johnson",
      views: 0,
      likes: 0,
      comments: 0,
      publishDate: postData.status === "published" ? new Date().toISOString().split('T')[0] : null
    };
    setPosts([...posts, newPost]);
    setCreatePostModal(false);
  };

  const handleUpdatePost = (postId, updates) => {
    setPosts(posts.map(post => 
      post.id === postId 
        ? { 
            ...post, 
            ...updates,
            publishDate: updates.status === "published" && !post.publishDate 
              ? new Date().toISOString().split('T')[0] 
              : post.publishDate
          }
        : post
    ));
    setPostModal(false);
    setSelectedPost(null);
  };

  const handleDeletePost = (postId) => {
    setPosts(posts.filter(post => post.id !== postId));
    setPostModal(false);
    setSelectedPost(null);
  };

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-xl font-semibold">📝 Manage Posts</h2>
        <div className="flex gap-4">
          <button
            onClick={() => setCreatePostModal(true)}
            className="bg-accent text-white px-4 py-2 rounded-custom hover:opacity-80"
          >
            + Create Post
          </button>
          <input
            type="text"
            placeholder="Search posts..."
            className="px-4 py-2 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
          <select
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value)}
            className="px-4 py-2 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
          >
            <option value="all">All Status</option>
            <option value="published">Published</option>
            <option value="draft">Draft</option>
            <option value="scheduled">Scheduled</option>
            <option value="archived">Archived</option>
          </select>
        </div>
      </div>

      {/* Posts List */}
      <div className="space-y-4">
        {filteredPosts.map((post) => (
          <div key={post.id} className="border border-muted/30 rounded-custom p-4 hover:border-accent/50 transition">
            <div className="flex justify-between items-start mb-3">
              <div className="flex-1">
                <h3 className="font-semibold text-main mb-2">{post.title}</h3>
                <p className="text-muted text-sm mb-3 line-clamp-2">{post.content}</p>
              </div>
              <div className="flex items-center gap-3 ml-4">
                <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(post.status)}`}>
                  {post.status.charAt(0).toUpperCase() + post.status.slice(1)}
                </span>
                <button
                  onClick={() => {
                    setSelectedPost(post);
                    setPostModal(true);
                  }}
                  className="bg-accent text-white px-3 py-1 rounded-custom hover:opacity-80 text-sm"
                >
                  Edit
                </button>
              </div>
            </div>
            
            <div className="flex flex-wrap gap-2 mb-3">
              {post.tags.map((tag, index) => (
                <span key={index} className="bg-accent/20 text-accent px-2 py-1 rounded text-xs">
                  {tag}
                </span>
              ))}
              <span className="bg-main/20 text-muted px-2 py-1 rounded text-xs">
                {post.category}
              </span>
            </div>
            
            <div className="flex justify-between items-center text-sm text-muted">
              <div className="flex gap-4">
                <span>👁 {post.views}</span>
                <span>❤️ {post.likes}</span>
                <span>💬 {post.comments}</span>
              </div>
              <span>
                {post.publishDate ? `Published: ${post.publishDate}` : 'Not published'}
              </span>
            </div>
          </div>
        ))}
      </div>

      {/* Post Editor Modal */}
      {postModal && selectedPost && (
        <PostEditorModal
          post={selectedPost}
          onClose={() => setPostModal(false)}
          onUpdate={handleUpdatePost}
          onDelete={handleDeletePost}
        />
      )}

      {/* Create Post Modal */}
      {createPostModal && (
        <CreatePostModal
          onClose={() => setCreatePostModal(false)}
          onSubmit={handleCreatePost}
        />
      )}
    </div>
  );
}

function PostEditorModal({ post, onClose, onUpdate, onDelete }) {
  const [formData, setFormData] = useState({
    title: post.title,
    content: post.content,
    status: post.status,
    tags: post.tags.join(", "),
    category: post.category
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    const updatedPost = {
      ...formData,
      tags: formData.tags.split(",").map(tag => tag.trim()).filter(tag => tag)
    };
    onUpdate(post.id, updatedPost);
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div className="bg-panel p-8 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div className="flex justify-between items-center mb-6">
          <h3 className="text-xl font-semibold">Edit Post</h3>
          <div className="flex gap-2">
            <button
              onClick={() => onDelete(post.id)}
              className="px-4 py-2 rounded-custom bg-red-500 text-white hover:bg-red-600 transition"
            >
              Delete
            </button>
            <button
              onClick={onClose}
              className="text-muted hover:text-main text-2xl"
            >
              ×
            </button>
          </div>
        </div>
        
        <form onSubmit={handleSubmit} className="space-y-4">
          <input
            type="text"
            name="title"
            value={formData.title}
            onChange={handleChange}
            className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            required
          />
          
          <div className="grid grid-cols-2 gap-4">
            <select
              name="status"
              value={formData.status}
              onChange={handleChange}
              className="p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            >
              <option value="draft">Draft</option>
              <option value="published">Published</option>
              <option value="scheduled">Scheduled</option>
              <option value="archived">Archived</option>
            </select>
            
            <select
              name="category"
              value={formData.category}
              onChange={handleChange}
              className="p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            >
              <option value="Tutorial">Tutorial</option>
              <option value="Educational">Educational</option>
              <option value="Announcement">Announcement</option>
              <option value="News">News</option>
            </select>
          </div>
          
          <textarea
            name="content"
            value={formData.content}
            onChange={handleChange}
            className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            rows="10"
            required
          />
          
          <input
            type="text"
            name="tags"
            value={formData.tags}
            onChange={handleChange}
            placeholder="Tags (comma separated)"
            className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
          />
          
          <div className="flex justify-end gap-3">
            <button
              type="button"
              onClick={onClose}
              className="px-5 py-2 rounded-custom bg-muted/20 text-muted hover:bg-muted/30 transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              className="px-5 py-2 rounded-custom bg-accent text-white hover:bg-accent/90 transition"
            >
              Update Post
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

function CreatePostModal({ onClose, onSubmit }) {
  const [formData, setFormData] = useState({
    title: "",
    content: "",
    status: "draft",
    tags: "",
    category: "Tutorial"
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    const newPost = {
      ...formData,
      tags: formData.tags.split(",").map(tag => tag.trim()).filter(tag => tag)
    };
    onSubmit(newPost);
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div className="bg-panel p-8 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <h3 className="text-xl font-semibold mb-6">Create New Post</h3>
        
        <form onSubmit={handleSubmit} className="space-y-4">
          <input
            type="text"
            name="title"
            placeholder="Post Title"
            value={formData.title}
            onChange={handleChange}
            className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            required
          />
          
          <div className="grid grid-cols-2 gap-4">
            <select
              name="status"
              value={formData.status}
              onChange={handleChange}
              className="p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            >
              <option value="draft">Draft</option>
              <option value="published">Published</option>
              <option value="scheduled">Scheduled</option>
            </select>
            
            <select
              name="category"
              value={formData.category}
              onChange={handleChange}
              className="p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            >
              <option value="Tutorial">Tutorial</option>
              <option value="Educational">Educational</option>
              <option value="Announcement">Announcement</option>
              <option value="News">News</option>
            </select>
          </div>
          
          <textarea
            name="content"
            placeholder="Post Content"
            value={formData.content}
            onChange={handleChange}
            className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            rows="10"
            required
          />
          
          <input
            type="text"
            name="tags"
            value={formData.tags}
            onChange={handleChange}
            placeholder="Tags (comma separated)"
            className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
          />
          
          <div className="flex justify-end gap-3">
            <button
              type="button"
              onClick={onClose}
              className="px-5 py-2 rounded-custom bg-muted/20 text-muted hover:bg-muted/30 transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              className="px-5 py-2 rounded-custom bg-accent text-white hover:bg-accent/90 transition"
            >
              Create Post
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default PostManagementSection;






