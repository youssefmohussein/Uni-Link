import React from 'react';
import { Link } from 'react-router-dom';
import Logo from '../../assets/Logo Png.png';

const Header = ({ onShareActivity }) => (
  <header className="bg-gray-800 shadow-xl fixed top-0 left-0 w-full z-50 h-16 flex items-center px-4"> 
    <div className="container mx-auto flex items-center justify-between max-w-8xl h-full"> 
      
      
      <Link to="/" className="flex items-center space-x-2 flex-shrink-0 h-full hover:opacity-80 transition-opacity">

        <img
          src={Logo}
          alt="UniLink Icon"
          className="h-25 w-25 object-contain"
        />

        <span className="text-xl font-bold text-white tracking-wide">UniLink</span>
      </Link>

    
      <div className="flex items-center flex-grow justify-center mx-8">
        <div className="relative flex-grow max-w-xl"> 
          <input 
            type="text" 
            placeholder="Search activities, people, or events..." 
            
            className="search-input w-full bg-gray-700 text-gray-300 rounded-full py-2.5 pl-10 pr-4 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" 
          />
          
          <i className="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i> 
        </div>
        
        
        <button 
          onClick={onShareActivity} 
          className="bg-blue-600 text-white rounded-full py-2.5 px-5 font-semibold hover:bg-blue-700 transition-colors flex items-center space-x-2 shadow-lg ml-15 flex-shrink-0" 
        >
          <i className="fas fa-plus text-sm"></i> 
          <span>Share Activity</span>
        </button>
      </div>

      
      <nav className="flex items-center space-x-6 flex-shrink-0"> 
        
        
        <div className="relative group"> 
          <i className="fas fa-bell text-xl text-gray-400 hover:text-blue-400 transition-colors cursor-pointer"></i>
        </div>

        <Link to="/profile" className="relative group"> 
          <i className="fas fa-user-circle text-xl text-gray-400 hover:text-blue-400 transition-colors cursor-pointer"></i>
        </Link>
      </nav>
    </div>
  </header>
);

export default Header;