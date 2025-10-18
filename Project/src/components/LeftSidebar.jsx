import React from 'react';

const LeftSidebar = ({ currentFilter, onFilterChange }) => {
    // These 'category' values must match the strings used in the PostPage.jsx filter logic
    const navItems = [
        { label: 'Home Feed', icon: 'fas fa-home', category: 'all', count: null },
        { label: 'Trending Posts', icon: 'fas fa-fire', category: 'trending', count: null },
        { label: 'Project Groups', icon: 'fas fa-user-friends', category: 'Projects', count: 3 }, // Updated category to 'Projects'
        { label: 'Questions', icon: 'fas fa-question-circle', category: 'Questions', count: 5, countColor: 'bg-red-500' },
        { label: 'Study Group', icon: 'fas fa-book-reader', category: 'Study Group', count: null }, // Updated label and category
        { label: 'Leaderboard', icon: 'fas fa-trophy', category: 'Leaderboard', count: null },
        { label: 'Settings', icon: 'fas fa-cog', category: 'Settings', count: null },
    ];

    const stats = [
        { label: 'Activities Shared', value: 23 },
        { label: 'Connections', value: 156 },
        { label: 'Events Joined', value: 8 },
    ];

    return (
        <aside className="w-56 xl:w-64 hidden lg:block mr-3 xl:mr-4 flex-shrink-0"> 
            <div className="bg-gray-800 rounded-xl shadow-2xl p-4 mb-6">
                <nav className="space-y-1">
                    {navItems.map((item) => (
                        <button
                            key={item.category}
                            // Call the function passed from PostPage to update the filter state
                            onClick={() => onFilterChange(item.category)}
                            className={`filter-btn flex items-center space-x-3 p-3 rounded-xl w-full text-left transition-colors ${
                                currentFilter === item.category 
                                    ? 'font-semibold text-blue-400 bg-blue-900 bg-opacity-40 hover:bg-blue-900'
                                    : 'font-medium text-gray-300 hover:bg-gray-700 hover:text-white'
                            }`}
                        >
                            <i className={`${item.icon} text-lg`}></i>
                            <span>{item.label}</span>
                            {item.count && (
                                <span className={`ml-auto text-white text-xs font-bold px-2 py-0.5 rounded-full ${item.countColor || 'bg-blue-600'}`}>
                                    {item.count}
                                </span>
                            )}
                        </button>
                    ))}
                </nav>
            </div>
            
            <div className="bg-gray-800 rounded-xl shadow-2xl p-4">
                <h3 className="font-bold text-white mb-4 border-b border-gray-700 pb-2">Quick Stats</h3>
                <div className="space-y-3 text-sm text-gray-300">
                    {stats.map(stat => (
                        <div key={stat.label} className="flex justify-between items-center">
                            <span>{stat.label}</span>
                            <span className="font-semibold text-blue-400">{stat.value}</span>
                        </div>
                    ))}
                </div>
            </div>
        </aside>
    );
};

export default LeftSidebar;