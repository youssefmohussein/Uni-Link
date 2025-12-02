import React from 'react';
import logo from '../Uni-Link-Logo.webp';

const Navbar = () => {
    return (
        <nav className="fixed top-0 left-0 w-full z-50 px-8 py-4 flex justify-between items-center border-b border-white/10 bg-white/5 backdrop-blur-md shadow-lg">
            {/* Left: Logo */}
            <div className="flex items-center">
                <a href="/"><img src={logo} alt="Uni-Link Logo" className="h-12 w-auto object-contain drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]" /></a>
            </div>

            {/* Center: Navigation Links */}
            <div className="hidden md:flex items-center space-x-14 ml-10">
                <a href="/" className="text-white/80 hover:text-[#008080] font-medium text-lg transition-all duration-300 hover:[text-shadow:0_0_10px_rgba(0,128,128,0.5)]">
                    Home
                </a>
                <a href="/faculties" className="text-white/80 hover:text-[#008080] font-medium text-lg transition-all duration-300 hover:[text-shadow:0_0_10px_rgba(0,128,128,0.5)]">
                    Faculties
                </a>
                <a href="/about" className="text-white/80 hover:text-[#008080] font-medium text-lg transition-all duration-300 hover:[text-shadow:0_0_10px_rgba(0,128,128,0.5)]">
                    About Us
                </a>
            </div>

            {/* Right: Auth Buttons */}
            <div className="flex items-center space-x-4">
                <button className="px-6 py-2 rounded-full text-white font-semibold border border-white/20 hover:bg-white/10 transition-all duration-300 backdrop-blur-sm">
                    <a href="/login">Log in</a>
                </button>
            </div>
        </nav>
    );
};

export default Navbar;
