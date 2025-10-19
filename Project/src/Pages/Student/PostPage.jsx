import React, { useState, useRef } from 'react';
import Header from '../../components/HomeStudent/Header';
import LeftSidebar from '../../components/HomeStudent/LeftSidebar';
import RightSidebar from '../../components/HomeStudent/RightSidebar';
import PostCard from '../../components/HomeStudent/PostCard';
import PostForm from '../../components/HomeStudent/PostForm';

// Initial dummy data for the posts
const initialPosts = [
  // IMPORTANT: Ensure the 'category' fields match the filter strings in LeftSidebar/RightSidebar
  {
    id: 1,
    user: { name: 'Alex Chen', major: 'Computer Science', profilePic: "https://placehold.co/40x40/E5E7EB/6B7280?text=A" },
    timeAgo: '2h ago',
    category: 'Study Group', // Matches a category name
    content: 'Had an amazing study session at the library today! Working on our final CS project with the team. The new collaborative spaces are fantastic for group work. Anyone else working on something similar? Would love to exchange ideas and maybe collaborate! 💻📚',
    image: "https://placehold.co/700x400/374151/D1D5DB?text=Group+Study",
    reactions: 13,
    isReacted: false,
    isTrending: false,
    comments: [
        { userName: 'Sarah Jones', content: 'Looks awesome! We should try that room next time. Good luck with the project! 👍', timeAgo: '5m ago', userPic: "https://placehold.co/30x30/E5E7EB/6B7280?text=S" }
    ],
  },
  {
    id: 2,
    user: { name: 'Maria Rodriguez', major: 'Sports Science', profilePic: "https://placehold.co/40x40/E5E7EB/6B7280?text=M" },
    timeAgo: '4h ago',
    category: 'Events', // Matches a category name
    content: 'Just finished an incredible basketball match with the university team! We won 70-55 against the visiting team. The energy from everyone was amazing. Thanks to all who came to support us! Next game is this Friday - hope to see more of you there cheering us on! 🏀',
    image: "https://placehold.co/700x400/10B981/D1D5DB?text=Basketball+Game",
    reactions: 28,
    isReacted: false,
    isTrending: true,
    comments: [
        { userName: 'Tom Baker', content: 'Amazing win, team! So proud of you all. See you Friday! 🎉', timeAgo: '1h ago', userPic: "https://placehold.co/30x30/E5E7EB/6B7280?text=T" }
    ],
  },
  {
    id: 3,
    user: { name: 'John Doe', major: 'Engineering', profilePic: "https://placehold.co/40x40/E5E7EB/6B7280?text=J" },
    timeAgo: '1d ago',
    category: 'Projects', // Example post for Projects filter
    content: 'My final year project on sustainable city planning is complete! Presentation next week. Wish me luck! 🏙️',
    image: null, 
    reactions: 10,
    isReacted: false,
    isTrending: false,
    comments: [],
  },
];


const PostPage = () => {
    const [posts, setPosts] = useState(initialPosts);
    const [filter, setFilter] = useState('all'); // State to hold the current category filter
    const postFormRef = useRef(null);
    const [newPostContent, setNewPostContent] = useState('');
    const [newPostCategory, setNewPostCategory] = useState('');

    const scrollToPostForm = () => {
        postFormRef.current?.scrollIntoView({ behavior: 'smooth' });
    };
    
    const handlePost = () => {
        if (!newPostContent.trim() || !newPostCategory) {
            alert('Please enter some content and select a category before posting.');
            return;
        }

        const newPost = {
            id: Date.now(),
            user: { name: 'Current User (You)', major: 'Your Major', profilePic: "https://placehold.co/40x40/E5E7EB/6B7280?text=U" },
            timeAgo: 'Just now',
            category: newPostCategory,
            content: newPostContent,
            image: null, 
            reactions: 0,
            isReacted: false,
            isTrending: false,
            comments: [],
        };

        setPosts([newPost, ...posts]); // Add new post to the beginning of the list
        setNewPostContent('');
        setNewPostCategory('');
    };

    // Filter logic: checks for 'all', 'trending', or a specific category string
    const filteredPosts = posts.filter(post => {
        if (filter === 'all') return true;
        if (filter === 'trending') return post.isTrending;
        
        // This is the core category filter logic
        return post.category === filter;
    });

    return (
        <div className="flex flex-col min-h-screen">
            <Header onShareActivity={scrollToPostForm} />
            
            {/* IMPORTANT: The pt-20 class pushes the content down to make room for the fixed header */}
            <div className="container mx-auto flex flex-grow pt-20 pb-12 px-4 md:px-6 xl:px-8 max-w-8xl"> 
                
                {/* Left Sidebar */}
                <LeftSidebar currentFilter={filter} onFilterChange={setFilter} />

                <main className="flex-grow mx-3 xl:mx-4"> 
                    
                    {/* Welcome Banner */}
                    <div className="bg-gray-800 rounded-xl shadow-2xl p-8 mb-6 text-center">
                        <h2 className="text-3xl font-extrabold mb-3 text-white">Welcome to Uni-Link<span className="text-4xl">👋</span></h2>
                        <p className="text-gray-300 mb-6 max-w-xl mx-auto">Share your university experiences, connect with fellow students, and discover exciting activities happening around campus.</p>
                        <div className="flex flex-col md:flex-row space-y-4 md:space-y-0 md:space-x-4 justify-center">
                            <button onClick={scrollToPostForm} className="flex items-center justify-center space-x-2 bg-blue-600 text-white rounded-xl py-3 px-8 font-semibold hover:bg-blue-700 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                                <i className="fas fa-plus text-xl"></i>
                                <span>Share Activity</span>
                            </button>
                            <button className="flex items-center justify-center space-x-2 bg-green-500 text-white rounded-xl py-3 px-8 font-semibold hover:bg-green-600 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                                <i className="fas fa-calendar-plus text-xl"></i>
                                <span>Join Events</span>
                            </button>
                            <button className="flex items-center justify-center space-x-2 bg-purple-600 text-white rounded-xl py-3 px-8 font-semibold hover:bg-purple-700 transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                                <i className="fas fa-users text-xl"></i>
                                <span>Find Your Group</span>
                            </button>
                        </div>
                    </div>

                    {/* Post Creation Form */}
                    <PostForm 
                        postFormRef={postFormRef} 
                        newPostContent={newPostContent} 
                        setNewPostContent={setNewPostContent}
                        newPostCategory={newPostCategory}
                        setNewPostCategory={setNewPostCategory}
                        handlePost={handlePost}
                    />

                    {/* Activity Feed */}
                    <div className="space-y-6">
                        {/* Render all posts that pass the current filter */}
                        {filteredPosts.map(post => (
                            <PostCard key={post.id} initialPost={post} />
                        ))}
                    </div>
                </main>

                {/* Right Sidebar - now receives the filter props */}
                <RightSidebar currentFilter={filter} onFilterChange={setFilter} />
                
            </div>
        </div>
    );
};

export default PostPage;