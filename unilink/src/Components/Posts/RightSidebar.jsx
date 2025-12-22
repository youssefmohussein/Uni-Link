import React, { useState, useEffect } from 'react';
import GlassSurface from "../Login_Components/LiquidGlass/GlassSurface";

const RightSidebar = ({ currentFilter, onFilterChange }) => {
  const [categoryCounts, setCategoryCounts] = useState({
    'Questions': 0,
    'Events': 0,
    'Announcements': 0,
    'Projects': 0
  });
  const [totalCount, setTotalCount] = useState(0);

  useEffect(() => {
    // Fetch category counts from the backend
    const fetchCategoryCounts = async () => {
      try {
        const response = await fetch('http://localhost/backend/api/posts/category-counts', {
          credentials: 'include'
        });
        const data = await response.json();

        if (data.status === 'success' && Array.isArray(data.data)) {
          const counts = {};
          let total = 0;

          data.data.forEach(item => {
            counts[item.category] = parseInt(item.count);
            total += parseInt(item.count);
          });

          setCategoryCounts(counts);
          setTotalCount(total);
        }
      } catch (error) {
        console.error('Failed to fetch category counts:', error);
      }
    };

    fetchCategoryCounts();
    // Refresh counts every 30 seconds
    const interval = setInterval(fetchCategoryCounts, 30000);
    return () => clearInterval(interval);
  }, []);

  const categories = [
    { tag: '#Questions', posts: categoryCounts['Questions'] || 0, filter: 'Questions' },
    { tag: '#CampusEvents', posts: categoryCounts['Events'] || 0, filter: 'Events' },
    { tag: '#Announcements', posts: categoryCounts['Announcements'] || 0, filter: 'Announcements' },
    { tag: '#Projects', posts: categoryCounts['Projects'] || 0, filter: 'Projects' },
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
              <span className="text-gray-500 text-xs bg-black/30 px-2 py-0.5 rounded-full">
                {totalCount}
              </span>
            </div>
          </nav>
        </div>
      </GlassSurface>
    </aside>
  );
};

export default RightSidebar;