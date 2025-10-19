import React from 'react';
import { Link } from 'react-router-dom';
import Logo from '../assets/Logo Png.png';

const HomeHeader = () => {
  const isLoggedIn = false;

  return (
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

        <nav className="flex items-center space-x-8 flex-shrink-0">
          <Link
            to="/about"
            className="text-gray-300 hover:text-blue-400 transition-colors font-medium"
          >
            About
          </Link>

          <a
            href="mailto:ali2306123@miuegypt.edu.eg"
            className="text-gray-300 hover:text-blue-400 transition-colors font-medium"
          >
            Contact Us
          </a>

          {isLoggedIn ? (
            <Link
              to="/profile"
              className="relative group"
            >
              <div className="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold hover:bg-blue-700 transition-colors">
                <i className="fas fa-user"></i>
              </div>
            </Link>
          ) : (
            <div className="flex items-center space-x-4">
              <Link
                to="/login"
                className="text-gray-300 hover:text-blue-400 transition-colors font-medium"
              >
                Login
              </Link>
              <Link
                to="/signup"
                className="bg-blue-600 text-white rounded-full py-2 px-6 font-semibold hover:bg-blue-700 transition-colors shadow-lg"
              >
                Sign Up
              </Link>
            </div>
          )}
        </nav>
      </div>
    </header>
  );
};

export default HomeHeader;
