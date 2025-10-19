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
      "Had an amazing study session at the library today! Working on our final CS project with the team. The new collaborative spaces are fantastic for group work. Anyone else working on something similar? Would love to exchange ideas and maybe collaborate! 💻📚",
    image: "https://placehold.co/700x400/374151/D1D5DB?text=Group+Study",
    reactions: 13,
    isReacted: false,
    isTrending: false,
    comments: [
      {
        userName: "Sarah Jones",
        content: "Looks awesome! We should try that room next time. Good luck with the project! 👍",
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
      "Just finished an incredible basketball match with the university team! We won 70-55 against the visiting team. The energy from everyone was amazing. Thanks to all who came to support us! Next game is this Friday - hope to see more of you there cheering us on! 🏀",
    image: "https://placehold.co/700x400/10B981/D1D5DB?text=Basketball+Game",
    reactions: 28,
    isReacted: false,
    isTrending: true,
    comments: [
      {
        userName: "Tom Baker",
        content: "Amazing win, team! So proud of you all. See you Friday! 🎉",
        timeAgo: "1h ago",
        userPic: "https://placehold.co/30x30/E5E7EB/6B7280?text=T",
      },
    ],
  },
  {
    id: 3,
    user: { name: "John Doe", major: "Engineering", profilePic: "https://placehold.co/40x40/E5E7EB/6B7280?text=J" },
    timeAgo: "1d ago",
    category: "Projects",
    content: "My final year project on sustainable city planning is complete! Presentation next week. Wish me luck! 🏙️",
    image: null,
    reactions: 10,
    isReacted: false,
    isTrending: false,
    comments: [],
  },
];

const PostPage = () => {
  const [posts, setPosts] = useState(initialPosts);
  const [filter, setFilter] = useState("all");
  const postFormRef = useRef(null);
  const [newPostContent, setNewPostContent] = useState("");
  const [newPostCategory, setNewPostCategory] = useState("");

  const scrollToPostForm = () => {
    postFormRef.current?.scrollIntoView({ behavior: "smooth" });
  };

  const handlePost = () => {
    if (!newPostContent.trim() || !newPostCategory) {
      alert("Please enter some content and select a category before posting.");
      return;
    }

    const newPost = {
      id: Date.now(),
      user: { name: "Current User (You)", major: "Your Major", profilePic: "https://placehold.co/40x40/E5E7EB/6B7280?text=U" },
      timeAgo: "Just now",
      category: newPostCategory,
      content: newPostContent,
      image: null,
      reactions: 0,
      isReacted: false,
      isTrending: false,
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
    <div className="flex flex-col min-h-screen bg-main text-main font-main">
      <Header onShareActivity={scrollToPostForm} />

      <div className="container mx-auto flex flex-grow pt-20 pb-12 px-4 md:px-6 xl:px-8 max-w-8xl">
        {/* Left Sidebar */}
        <LeftSidebar currentFilter={filter} onFilterChange={setFilter} />

        {/* Main content */}
        <main className="flex-grow mx-3 xl:mx-4 animate-fade-in">
          {/* Welcome Banner */}
          {/* <div className="bg-panel rounded-custom shadow-custom p-8 mb-6 text-center border border-gray-800">
            <h2 className="text-3xl font-extrabold mb-3 text-accent">
              Welcome to Uni-Link <span className="text-4xl">👋</span>
            </h2>
            <p className="text-muted mb-6 max-w-xl mx-auto">
              Share your university experiences, connect with fellow students, and discover exciting activities happening around campus.
            </p>
            <div className="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 justify-center">
              <button
                onClick={scrollToPostForm}
                className="flex items-center justify-center space-x-2 bg-accent text-white rounded-custom py-3 px-8 font-semibold hover:bg-blue-500 transition-all shadow-md hover:shadow-lg transform hover:scale-105"
              >
                <i className="fas fa-plus text-xl"></i>
                <span>Share Activity</span>
              </button>
              <button className="flex items-center justify-center space-x-2 bg-green-600 text-white rounded-custom py-3 px-8 font-semibold hover:bg-green-700 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                <i className="fas fa-calendar-plus text-xl"></i>
                <span>Join Events</span>
              </button>
              <button className="flex items-center justify-center space-x-2 bg-purple-700 text-white rounded-custom py-3 px-8 font-semibold hover:bg-purple-800 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                <i className="fas fa-users text-xl"></i>
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
          <div className="space-y-6">
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
