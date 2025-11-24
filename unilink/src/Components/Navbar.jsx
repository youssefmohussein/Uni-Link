import React from 'react';
import logo from '../Uni-Link-Logo.webp';

const Navbar = () => {
    return (
        <nav className="fixed top-0 left-0 w-full z-50 px-8 py-4 flex justify-between items-center border-b border-white/10 bg-white/5 backdrop-blur-md shadow-lg">
            {/* Left: Logo */}
            <div className="flex items-center">
                <img src={logo} alt="Uni-Link Logo" className="h-12 w-auto object-contain drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]" />
            </div>

            {/* Center: Navigation Links */}
            <div className="hidden md:flex items-center space-x-12 ml-40">
                <a href="/" className="text-white/80 hover:text-white font-medium text-lg transition-all duration-300 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.5)]">
                    Home
                </a>
                <a href="/faculties" className="text-white/80 hover:text-white font-medium text-lg transition-all duration-300 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.5)]">
                    Faculties
                </a>
                <a href="/about" className="text-white/80 hover:text-white font-medium text-lg transition-all duration-300 hover:drop-shadow-[0_0_8px_rgba(255,255,255,0.5)]">
                    About Us
                </a>
            </div>

            {/* Right: Auth Buttons */}
            <div className="flex items-center space-x-4">
                <button className="px-6 py-2 rounded-full text-white font-semibold border border-white/20 hover:bg-white/10 transition-all duration-300 backdrop-blur-sm">
                    Log in
                </button>
                <button className="px-6 py-2 rounded-full text-white font-semibold bg-gradient-to-r from-cyan-500/80 to-blue-600/80 hover:from-cyan-400 hover:to-blue-500 border border-white/20 shadow-[0_0_15px_rgba(0,200,255,0.3)] hover:shadow-[0_0_25px_rgba(0,200,255,0.5)] transition-all duration-300 backdrop-blur-sm">
                    Sign up
                </button>
            </div>
        </nav>
    );
};

export default Navbar;
