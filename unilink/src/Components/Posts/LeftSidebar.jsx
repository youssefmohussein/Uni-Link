import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import GlassSurface from "../Login_Components/LiquidGlass/GlassSurface";

const LeftSidebar = ({ currentFilter, onFilterChange }) => {
  const navigate = useNavigate();
  const [counts, setCounts] = useState({
    projectRooms: 0,
    questions: 0
  });

  useEffect(() => {
    // Fetch dynamic counts from the backend
    const fetchCounts = async () => {
      try {
        // Fetch project rooms count
        const roomsResponse = await fetch('http://localhost/backend/api/chat/rooms/total-count', {
          credentials: 'include'
        });
        const roomsData = await roomsResponse.json();

        // Fetch questions count from category counts
        const categoriesResponse = await fetch('http://localhost/backend/api/posts/category-counts', {
          credentials: 'include'
        });
        const categoriesData = await categoriesResponse.json();

        let questionsCount = 0;
        if (categoriesData.status === 'success' && Array.isArray(categoriesData.data)) {
          const questionsCategory = categoriesData.data.find(item => item.category === 'Questions');
          questionsCount = questionsCategory ? parseInt(questionsCategory.count) : 0;
        }

        setCounts({
          projectRooms: roomsData.status === 'success' ? (roomsData.data?.count || 0) : 0,
          questions: questionsCount
        });
      } catch (error) {
        console.error('Failed to fetch sidebar counts:', error);
      }
    };

    fetchCounts();
    // Refresh counts every 30 seconds
    const interval = setInterval(fetchCounts, 30000);
    return () => clearInterval(interval);
  }, []);

  const navItems = [
    { label: "Home", icon: "fas fa-home", category: "all", count: null },
    { label: "Trending Posts", icon: "fas fa-fire", category: "trending", count: null },
    { label: "Project Rooms", icon: "fas fa-users", category: "project-rooms", count: counts.projectRooms || null, isRoute: true, route: "/project-rooms" },
    { label: "Questions", icon: "fas fa-question-circle", category: "Questions", count: counts.questions || null, countColor: "bg-red-500" },
    { label: "Leaderboard", icon: "fas fa-trophy", category: "Leaderboard", count: null },
  ];

  return (
    <aside className="w-56 xl:w-64 hidden lg:block mr-3 xl:mr-4 flex-shrink-0">
      {/* 📁 Navigation */}
      <GlassSurface
        width="100%"
        height="auto"
        borderRadius={20}
        opacity={0.5}
        blur={10}
        borderWidth={0.05}
        className="mb-6 !items-start !justify-start"
      >
        <nav className="space-y-1 w-full relative z-10">
          {navItems.map((item) => (
            <button
              key={item.category}
              onClick={() => item.isRoute ? navigate(item.route) : onFilterChange(item.category)}
              className={`flex items-center space-x-3 p-3 rounded-lg w-full text-left transition-all duration-300 ${currentFilter === item.category
                ? "font-semibold text-accent bg-accent/20 hover:bg-accent/30 shadow-md"
                : "font-medium text-main hover:bg-white/10 hover:text-accent"
                }`}
            >
              <i className={`${item.icon} text-lg`}></i>
              <span>{item.label}</span>
              {item.count && (
                <span
                  className={`ml-auto text-white text-xs font-bold px-2 py-0.5 rounded-full ${item.countColor || "bg-accent"
                    }`}
                >
                  {item.count}
                </span>
              )}
            </button>
          ))}
        </nav>
      </GlassSurface>
    </aside>
  );
};

export default LeftSidebar;