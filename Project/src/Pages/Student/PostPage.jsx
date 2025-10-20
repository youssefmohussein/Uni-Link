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
    <div className="flex flex-col min-h-screen bg-main text-main font-main transition-theme">
      {/* 🌟 Header */}
      <Header logoSize="large" onShareActivity={scrollToPostForm} />

      <div className="container mx-auto flex flex-grow pt-24 pb-12 px-4 md:px-6 xl:px-8 max-w-8xl gap-6">
        {/* 📁 Left Sidebar */}
                  <LeftSidebar currentFilter={filter} onFilterChange={setFilter} />
        
        {/* 📰 Main Feed */}
        <main className="flex-grow space-y-8">
          {/* ✏️ Post Form */}

            <PostForm
              newPostContent={newPostContent}
              setNewPostContent={setNewPostContent}
              newPostCategory={newPostCategory}
              setNewPostCategory={setNewPostCategory}
              handlePost={handlePost}
            />

          {/* 📜 Feed */}
          <div className="space-y-8">
            {filteredPosts.map((post) => (
                <PostCard initialPost={post} />
              
            ))}
          </div>
        </main>

        {/* 💬 Right Sidebar */}
                  <RightSidebar currentFilter={filter} onFilterChange={setFilter} />
        
      </div>
    </div>
  );
};

export default PostPage;
