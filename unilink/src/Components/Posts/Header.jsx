import React, { useState, useEffect } from "react";
import { Link, useNavigate, useNavigate } from "react-router-dom";
import { FiBell, FiUser, FiPlus, FiSearch, FiX } from "react-icons/fi";
import Logo from "../../Uni-Link-Logo.webp";

const Header = ({ onShareActivity, onSearch, searchQuery, onClearSearch, hideShareButton = false }) => {
  return (
    <header className="fixed top-0 left-0 w-full z-50 bg-gradient-to-r from-[#0d1117] to-[#161b22] shadow-lg border-b border-[#21262d] backdrop-blur-md">
      <div className="max-w-7xl mx-auto flex items-center justify-between px-6 h-16 text-white font-main">

        {/* Left: Logo and UniLink */}
        <Link to="/" className="flex items-center space-x-3 group">
          <img
            src={Logo}
            alt="UniLink Logo"
            className="h-12 w-12 object-contain drop-shadow-[0_0_15px_rgba(88,166,255,0.4)] transition-transform duration-200 group-hover:scale-110"
          />
          <span className="text-2xl font-bold bg-gradient-to-r from-[#58a6ff] to-[#3b82f6] bg-clip-text text-transparent tracking-wide">
            UniLink
          </span>
        </Link>
        {/* Search Bar */}
        <div className="flex-1 max-w-xl mx-8 relative hidden md:block">
          <div className="relative group z-50">
            <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <FiSearch className="text-gray-500 group-focus-within:text-accent transition-colors" />
            </div>
            <input
              type="text"
              className="block w-full pl-10 pr-3 py-2 border border-[#30363d] rounded-full leading-5 bg-[#0d1117] text-gray-300 placeholder-gray-500 focus:outline-none focus:bg-[#161b22] focus:border-accent focus:ring-1 focus:ring-accent sm:text-sm transition-all duration-200"
              placeholder="Search posts or people..."
              value={searchQuery || ""}
              onChange={handleInputChange}
              onFocus={() => searchQuery && suggestions.length > 0 && setShowSuggestions(true)}
              onBlur={() => setTimeout(() => setShowSuggestions(false), 200)} // Delay to allow click
            />
            {searchQuery && (
              <button
                onClick={() => {
                  if (onClearSearch) onClearSearch();
                  setSuggestions([]);
                  setShowSuggestions(false);
                }}
                className="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-white"
              >
                <FiX />
              </button>
            )}

            {/* Suggestions Dropdown */}
            {showSuggestions && suggestions.length > 0 && (
              <div className="absolute top-full left-0 right-0 mt-2 bg-[#161b22] border border-[#30363d] rounded-xl shadow-2xl overflow-hidden animate-fade-in z-50">
                <div className="p-2 border-b border-[#30363d] bg-[#0d1117] text-[10px] uppercase tracking-widest text-gray-500 font-bold">
                  People
                </div>
                {suggestions.map((u) => (
                  <div
                    key={u.user_id}
                    onMouseDown={() => {
                      navigate(`/profile?user_id=${u.user_id}`);
                      setShowSuggestions(false);
                      if (onClearSearch) onClearSearch();
                    }}
                    className="flex items-center gap-3 p-3 cursor-pointer hover:bg-[#21262d] transition-colors group"
                  >
                    <div className="w-8 h-8 rounded-full overflow-hidden border border-gray-600 group-hover:border-accent shrink-0">
                      {u.profile_picture ? (
                        // Correct relative path handling logic or use full URL if confirmed
                        <img src={`http://localhost/backend/${u.profile_picture}`} alt={u.username} className="w-full h-full object-cover" />
                      ) : (
                        <div className="w-full h-full bg-gray-700 flex items-center justify-center text-xs font-bold">
                          {u.username.charAt(0).toUpperCase()}
                        </div>
                      )}
                    </div>
                    <div>
                      <p className="text-sm font-bold text-gray-200 group-hover:text-accent">{u.username}</p>
                      <p className="text-xs text-gray-500">{u.major_name || u.role}</p>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>

        {/* Right: Actions */}
        <div className="flex items-center space-x-4">

          {/* Share Button */}
          {!hideShareButton && (
            <button
              onClick={onShareActivity}
              className="flex items-center justify-center bg-[#58a6ff] hover:bg-[#3b82f6] text-white rounded-full px-6 py-2 font-semibold text-sm 
              shadow-[0_4px_10px_rgba(88,166,255,0.3)] hover:shadow-[0_0_20px_rgba(88,166,255,0.5)] transition-all duration-200"
            >
              <FiPlus className="mr-2 text-base" />
              Share
            </button>
          )}

          {/* Notification Bell */}
          <button
            onClick={() => {
              console.log("Header: Bell clicked, current unread count:", unreadCount);
              setShowNotifications(!showNotifications);
            }}
            className="relative group p-2 rounded-full hover:bg-[#21262d] transition-colors"
          >
            <FiBell className="text-[#9ca3af] group-hover:text-[#58a6ff] text-lg" />
            {unreadCount > 0 && (
              <span className="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
            )}
          </button>

          {/* User Profile Section */}
          {user && (
            <div className="flex items-center space-x-3 border-l border-white/10 pl-4">
              {/* Username */}
              <span className="text-sm text-gray-300">{user.username || user.name}</span>

              {/* Profile Icon */}
              <button
                onClick={handleProfileClick}
                className="p-2 rounded-full hover:bg-[#21262d] transition-colors"
                title={user.role === 'Admin' ? 'Admin Dashboard' : 'Profile'}
              >
                {user.role === 'Admin' ? (
                  <i className="fa-solid fa-user-tie text-[#9ca3af] hover:text-[#58a6ff] text-lg"></i>
                ) : (
                  <i className="fa-solid fa-user text-[#9ca3af] hover:text-[#58a6ff] text-lg"></i>
                )}
              </button>

              {/* Logout Icon */}
              <button
                onClick={handleLogout}
                className="p-2 rounded-full hover:bg-[#21262d] transition-colors"
                title="Logout"
              >
                <i className="fa-solid fa-arrow-right-from-bracket text-[#9ca3af] hover:text-red-400 text-lg"></i>
              </button>
            </div>
          )}
        </div>
      </div>

      {/* Notifications Panel */}
      <Notifications
        isOpen={showNotifications}
        onClose={() => setShowNotifications(false)}
      />
    </header>
  );
};

export default Header;
