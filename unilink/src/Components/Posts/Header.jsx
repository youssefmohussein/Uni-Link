import React from "react";
import { Link } from "react-router-dom";
import { FiBell, FiUser, FiPlus, FiSearch } from "react-icons/fi";
import Logo from "../../Uni-Link-Logo.webp";

const Header = ({ onShareActivity }) => {
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

        {/* Center: Search Bar */}
        <div className="relative flex-grow max-w-lg hidden sm:flex mx-8">
          <FiSearch className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 text-lg" />
          <input
            type="text"
            placeholder="Search people, projects, or events..."
            className="w-full bg-[#0d1117]/80 text-[#c9d1d9] rounded-full py-2.5 pl-11 pr-4 
            focus:outline-none focus:ring-2 focus:ring-[#58a6ff] 
            transition-all placeholder:text-gray-500 border border-[#21262d] hover:border-[#30363d]"
          />
        </div>

        {/* Right: Actions */}
        <div className="flex items-center space-x-4">

          {/* Share Button (Blue Accent from index.css) */}
          <button
            onClick={onShareActivity}
            className="flex items-center justify-center bg-[#58a6ff] hover:bg-[#3b82f6] text-white rounded-full px-6 py-2 font-semibold text-sm 
            shadow-[0_4px_10px_rgba(88,166,255,0.3)] hover:shadow-[0_0_20px_rgba(88,166,255,0.5)] transition-all duration-200"
          >
            <FiPlus className="mr-2 text-base" />
            Share
          </button>

          {/* Notification Bell */}
          <button className="relative group p-2 rounded-full hover:bg-[#21262d] transition-colors">
            <FiBell className="text-[#9ca3af] group-hover:text-[#58a6ff] text-lg" />
            <span className="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
          </button>

          {/* User Profile Icon */}
          <Link
            to="/profile"
            className="p-2 rounded-full hover:bg-[#21262d] transition-colors"
          >
            <FiUser className="text-[#9ca3af] hover:text-[#58a6ff] text-lg" />
          </Link>
        </div>
      </div>
    </header>
  );
};

export default Header;
