import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";

const LeftSidebar = ({ currentFilter, onFilterChange }) => {
  const navigate = useNavigate();
  const [counts, setCounts] = useState({
    projectRooms: 0,
    questions: 0
  });

  useEffect(() => {
    const fetchCounts = async () => {
      try {
        const roomsResponse = await fetch('http://localhost:8000/api/chat/rooms/total-count', {
          credentials: 'include'
        });
        const roomsData = await roomsResponse.json();

        const categoriesResponse = await fetch('http://localhost:8000/api/posts/category-counts', {
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
    const interval = setInterval(fetchCounts, 30000);
    return () => clearInterval(interval);
  }, []);

  const navItems = [
    { label: "Home", icon: "fas fa-home", category: "all", count: null },
    { label: "Trending Posts", icon: "fas fa-fire", category: "trending", count: null },
    { label: "Project Rooms", icon: "fas fa-users", category: "project-rooms", count: counts.projectRooms || null, isRoute: true, route: "/project-rooms" },
    { label: "Questions", icon: "fas fa-question-circle", category: "Questions", count: counts.questions || null, countColor: "bg-red-500" },
    {
      label: "Leaderboard",
      icon: "fas fa-trophy",
      category: "Leaderboard",
      count: null,
      action: () => setShowLeaderboard(true)
    },
  ];

  const [showLeaderboard, setShowLeaderboard] = useState(false);

  return (
    <>
      <aside className="w-56 xl:w-64 hidden lg:block mr-3 xl:mr-4 flex-shrink-0">
        <div className="mb-6 bg-[rgba(0,0,0,0.3)] border border-white/10 rounded-2xl p-4">
          <nav className="space-y-1 w-full">
            {navItems.map((item) => (
              <button
                key={item.category}
                onClick={() => {
                  if (item.action) {
                    item.action();
                  } else if (item.isRoute) {
                    navigate(item.route);
                  } else {
                    onFilterChange(item.category);
                  }
                }}
                className={`flex items-center space-x-3 p-3 rounded-lg w-full text-left transition-colors ${currentFilter === item.category && !item.action && !item.isRoute
                  ? "font-semibold text-accent bg-accent/20 hover:bg-accent/30"
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
        </div>
      </aside>

      {showLeaderboard && (
        <React.Suspense fallback={null}>
          <LeaderboardModal isOpen={showLeaderboard} onClose={() => setShowLeaderboard(false)} />
        </React.Suspense>
      )}
    </>
  );
};

const LeaderboardModal = React.lazy(() => import('../Leaderboard/LeaderboardModal'));

export default LeftSidebar;
