import React, { useState, useRef } from "react";
import Header from "../../components/HomeStudent/Header";
import LeftSidebar from "../../components/HomeStudent/LeftSidebar";
import RightSidebar from "../../components/HomeStudent/RightSidebar";
import PostCard from "../../components/HomeStudent/PostCard";
import PostForm from "../../components/HomeStudent/PostForm";

const initialPosts = [
  {
    id: 1,
    user: { name: "Alex Chen", major: "Computer Science", profilePic: "https://placehold.co/40x40/E5E7EB/6B7280?text=A" },
    timeAgo: "2h ago",
    category: "Study Group",
    content:
      "Had an amazing study session at the library today! Working on our final CS project with the team. Anyone else working on something similar? 💻📚",
    image: "https://placehold.co/700x400/374151/D1D5DB?text=Group+Study",
    reactions: 13,
    isReacted: false,
    isTrending: false,
    comments: [
      {
        userName: "Sarah Jones",
        content: "Looks awesome! We should try that room next time. 👍",
        timeAgo: "5m ago",
        userPic: "https://placehold.co/30x30/E5E7EB/6B7280?text=S",
      },
    ],
  },
  {
    id: 2,
    user: { name: "Maria Rodriguez", major: "Sports Science", profilePic: "https://placehold.co/40x40/E5E7EB/6B7280?text=M" },
    timeAgo: "4h ago",
    category: "Events",
    content:
      "Just finished an incredible basketball match! We won 70-55 against the visiting team. Thanks to all who supported! 🏀",
    image: "https://placehold.co/700x400/10B981/D1D5DB?text=Basketball+Game",
    reactions: 28,
    isReacted: false,
    isTrending: true,
    comments: [
      {
        userName: "Tom Baker",
        content: "Amazing win, team! 🎉",
        timeAgo: "1h ago",
        userPic: "https://placehold.co/30x30/E5E7EB/6B7280?text=T",
      },
    ],
  },
];

const PostPage = () => {
  const [posts, setPosts] = useState(initialPosts);
  const [filter, setFilter] = useState("all");
  const [newPostContent, setNewPostContent] = useState("");
  const [newPostCategory, setNewPostCategory] = useState("");
  const postFormRef = useRef(null);

  const scrollToPostForm = () => {
    postFormRef.current?.scrollIntoView({ behavior: "smooth" });
  };

  const handlePost = () => {
    if (!newPostContent.trim() || !newPostCategory) {
      alert("Please enter content and select a category before posting.");
      return;
    }

    const newPost = {
      id: Date.now(),
      user: { name: "You", major: "Student", profilePic: "https://placehold.co/40x40/2563EB/FFFFFF?text=Y" },
      timeAgo: "Just now",
      category: newPostCategory,
      content: newPostContent,
      image: null,
      reactions: 0,
      isReacted: false,
      comments: [],
    };

    setPosts([newPost, ...posts]);
    setNewPostContent("");
    setNewPostCategory("");
  };

  const filteredPosts = posts.filter((post) => {
    if (filter === "all") return true;
    if (filter === "trending") return post.isTrending;
    return post.category === filter;
  });

  return (
    <div className="flex flex-col min-h-screen bg-gradient-to-b from-gray-900 via-gray-950 to-black text-white">
      <Header logoSize="large" onShareActivity={scrollToPostForm} />

      <div className="container mx-auto flex flex-grow pt-24 pb-12 px-4 md:px-6 xl:px-8 max-w-8xl space-x-6">
        {/* Left Sidebar */}
        <LeftSidebar currentFilter={filter} onFilterChange={setFilter} />

        {/* Main Feed */}
        <main className="flex-grow space-y-8">
          {/* Welcome Section */}
          {/* <div className="bg-gray-800/70 backdrop-blur-lg rounded-2xl shadow-2xl p-10 text-center border border-gray-700">
            <h2 className="text-4xl font-extrabold mb-3 text-blue-400 tracking-tight">
              Welcome to Uni-Link 👋
            </h2>
            <p className="text-gray-300 mb-6 max-w-xl mx-auto text-lg">
              Share your university experiences, connect with others, and join exciting campus activities.
            </p>
            <div className="flex flex-wrap justify-center gap-4">
              <button
                onClick={scrollToPostForm}
                className="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-blue-700 text-white rounded-xl py-3 px-10 font-semibold shadow-lg hover:scale-105 transition-transform duration-200"
              >
                <i className="fas fa-share-alt text-lg"></i>
                <span>Share Activity</span>
              </button>
              <button className="flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-green-700 text-white rounded-xl py-3 px-10 font-semibold shadow-lg hover:scale-105 transition-transform duration-200">
                <i className="fas fa-calendar-plus text-lg"></i>
                <span>Join Events</span>
              </button>
              <button className="flex items-center justify-center gap-2 bg-gradient-to-r from-purple-500 to-purple-700 text-white rounded-xl py-3 px-10 font-semibold shadow-lg hover:scale-105 transition-transform duration-200">
                <i className="fas fa-users text-lg"></i>
                <span>Find Your Group</span>
              </button>
            </div>
          </div> */}

          {/* Post Form */}
          <div ref={postFormRef}>
            <PostForm
              newPostContent={newPostContent}
              setNewPostContent={setNewPostContent}
              newPostCategory={newPostCategory}
              setNewPostCategory={setNewPostCategory}
              handlePost={handlePost}
            />
          </div>

          {/* Feed */}
          <div className="space-y-8">
            {filteredPosts.map((post) => (
              <PostCard key={post.id} initialPost={post} />
            ))}
          </div>
        </main>

        {/* Right Sidebar */}
        <RightSidebar currentFilter={filter} onFilterChange={setFilter} />
      </div>
    </div>
  );
};

export default PostPage;
