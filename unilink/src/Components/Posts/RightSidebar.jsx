import React from 'react';
import GlassSurface from "../Login_Components/LiquidGlass/GlassSurface";

const RightSidebar = ({ currentFilter, onFilterChange }) => {
  const categories = [
    { tag: '#StudyGroup', posts: 30, filter: 'Study Group' },
    { tag: '#CampusEvents', posts: 22, filter: 'Events' },
    { tag: '#Announcements', posts: 15, filter: 'Announcement' },
    { tag: '#Projects', posts: 35, filter: 'Projects' },
  ];

  const connections = [
    { name: 'Emma Thompson', major: 'Psychology', mutual: 12, profilePic: "https://placehold.co/40x40/E5E7EB/6B7280?text=E" },
    { name: 'James Wilson', major: 'Engineering', mutual: 8, profilePic: "https://placehold.co/40x40/E5E7EB/6B7280?text=J" },
  ];

  return (
    <aside className="w-60 xl:w-72 hidden md:block ml-4 flex-shrink-0">

      {/* Categories */}
      <GlassSurface
        width="100%"
        height="auto"
        borderRadius={20}
        opacity={0.5}
        blur={10}
        borderWidth={0.05}
        className="mb-6 !items-start !justify-start"
      >
        <div className="w-full relative z-10">
          <h3 className="font-bold text-white mb-4 border-b border-white/10 pb-2">Post Categories</h3>
          <nav className="space-y-3">
            {categories.map((cat, index) => (
              <div key={index} className="flex items-center justify-between">
                <button
                  onClick={() => onFilterChange(cat.filter)}
                  className={`text-left font-medium text-sm transition-all duration-200 p-1.5 rounded-lg w-full
                    ${currentFilter === cat.filter
                      ? 'text-blue-400 bg-blue-500/20 border border-blue-600/30'
                      : 'text-gray-300 hover:text-blue-400 hover:bg-white/10'
                    }`}
                >
                  {cat.tag}
                </button>
                <span className="text-gray-500 text-xs bg-black/30 px-2 py-0.5 rounded-full">
                  {cat.posts}
                </span>
              </div>
            ))}

            {/* All Posts */}
            <div className="flex items-center justify-between pt-3 border-t border-white/10 mt-3">
              <button
                onClick={() => onFilterChange('all')}
                className={`text-left transition-colors font-semibold text-sm p-1 rounded-lg w-full ${currentFilter === 'all'
                  ? 'text-white bg-blue-600/30 border border-blue-500/40'
                  : 'text-gray-300 hover:text-white hover:bg-white/10'
                  }`}
              >
                #AllPosts
              </button>
            </div>
          </nav>
        </div>
      </GlassSurface>

      {/* Connections */}
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
          <h3 className="font-bold text-white mb-4 border-b border-white/10 pb-2">
            Suggested Connections
          </h3>
          <div className="space-y-4">
            {connections.map((person, index) => (
              <div key={index} className="flex items-center justify-between">
                <div className="flex items-center space-x-3">
                  <img
                    src={person.profilePic}
                    alt="Profile"
                    className="w-10 h-10 rounded-full border-2 border-gray-600"
                  />
                  <div>
                    <span className="block font-medium text-white text-sm">{person.name}</span>
                    <span className="block text-gray-400 text-xs">
                      {person.major} • {person.mutual} mutual
                    </span>
                  </div>
                </div>
                <button className="bg-blue-600 text-white text-xs font-semibold px-3 py-1 rounded-full hover:bg-blue-500 transition-all shadow-sm">
                  Connect
                </button>
              </div>
            ))}
          </div>
        </div>
      </GlassSurface>
    </aside>
  );
};

export default RightSidebar;