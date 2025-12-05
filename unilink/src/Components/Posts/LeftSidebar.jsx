import React from "react";
import GlassSurface from "../Login_Components/LiquidGlass/GlassSurface";

const LeftSidebar = ({ currentFilter, onFilterChange }) => {
  const navItems = [
    { label: "Home Feed", icon: "fas fa-home", category: "all", count: null },
    { label: "Trending Posts", icon: "fas fa-fire", category: "trending", count: null },
    { label: "Project Groups", icon: "fas fa-user-friends", category: "Projects", count: 3 },
    { label: "Questions", icon: "fas fa-question-circle", category: "Questions", count: 5, countColor: "bg-red-500" },
    { label: "Study Group", icon: "fas fa-book-reader", category: "Study Group", count: null },
    { label: "Leaderboard", icon: "fas fa-trophy", category: "Leaderboard", count: null },
    { label: "Settings", icon: "fas fa-cog", category: "Settings", count: null },
  ];

  const stats = [
    { label: "Activities Shared", value: 23 },
    { label: "Connections", value: 156 },
    { label: "Events Joined", value: 8 },
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
              onClick={() => onFilterChange(item.category)}
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

      {/* 📊 Quick Stats */}
      <GlassSurface
        width="100%"
        height="auto"
        borderRadius={20}
        opacity={0.5}
        blur={10}
        borderWidth={0.05}
        className="!items-start !justify-start"
      >
        <div className="w-full relative z-10">
          <h3 className="font-semibold text-accent mb-4 border-b border-white/10 pb-2">
            Quick Stats
          </h3>
          <div className="space-y-3 text-sm text-main">
            {stats.map((stat) => (
              <div key={stat.label} className="flex justify-between items-center">
                <span>{stat.label}</span>
                <span className="font-semibold text-accent">{stat.value}</span>
              </div>
            ))}
          </div>
        </div>
      </GlassSurface>
    </aside>
  );
};

export default LeftSidebar;