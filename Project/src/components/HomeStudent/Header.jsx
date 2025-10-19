import React from 'react';
import { Link } from 'react-router-dom';
import Logo from '../assets/Logo Png.png';

const Header = ({ onShareActivity }) => (
  <header className="bg-gray-800 shadow-xl fixed top-0 left-0 w-full z-50 h-16 flex items-center px-4"> 
    <div className="container mx-auto flex items-center justify-between max-w-8xl h-full"> 
      
      {/* 1. Logo and UniLink Text (Left) */}
      <Link to="/" className="flex items-center space-x-2 flex-shrink-0 h-full hover:opacity-80 transition-opacity">

        <img
          src={Logo}
          alt="UniLink Icon"
          className="h-25 w-25 object-contain"
        />

        <span className="text-xl font-bold text-white tracking-wide">UniLink</span>
      </Link>

      {/* 2. Search Bar and Share Activity (Center Block) */}
      {/* This block will take up the remaining flexible space in the center */}
      <div className="flex items-center flex-grow justify-center mx-8"> {/* Added mx-8 for horizontal margin */}
        <div className="relative flex-grow max-w-xl"> {/* Constrained max-width to center it better */}
          <input 
            type="text" 
            placeholder="Search activities, people, or events..." 
            // The font will be inherited from the global 'font-inter' class in App.jsx
            className="search-input w-full bg-gray-700 text-gray-300 rounded-full py-2.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" // Adjusted padding and text color
          />
          {/* Search Icon */}
          <i className="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i> {/* Smaller icon for better fit */}
        </div>
        
        {/* Share Activity Button - positioned immediately after the search bar */}
        <button 
          onClick={onShareActivity} 
          className="bg-blue-600 text-white rounded-full py-2.5 px-5 font-semibold hover:bg-blue-700 transition-colors flex items-center space-x-2 shadow-lg ml-15 flex-shrink-0" // Added ml-4 for space, py-2.5 for height
        >
          <i className="fas fa-plus text-sm"></i> {/* Smaller icon for better fit */}
          <span>Share Activity</span>
        </button>
      </div>

      {/* 3. Navigation Icons (Far Right) */}
      <nav className="flex items-center space-x-6 flex-shrink-0"> {/* Kept space-x-6 here */}
        
        {/* Bell Icon (Notifications) */}
        <div className="relative group"> {/* Removed h-full and flex items-center as parent div handles alignment */}
          <i className="fas fa-bell text-xl text-gray-400 hover:text-blue-400 transition-colors cursor-pointer"></i> {/* Adjusted icon size */}
          {/* Red notification dot - positioned more accurately */}
          <span className="absolute -top-1 right-0 h-2 w-2 bg-red-500 rounded-full border border-gray-800"></span>
        </div>

        {/* User Icon (Profile) */}
        <div className="relative group"> {/* Removed h-full and flex items-center */}
          <i className="fas fa-user-circle text-xl text-gray-400 hover:text-blue-400 transition-colors cursor-pointer"></i> {/* Adjusted icon size */}
        </div>
      </nav>
    </div>
  </header>
);

export default Header;