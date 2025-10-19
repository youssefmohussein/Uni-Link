import React from 'react';
import { Link } from 'react-router-dom';

export default function Footer() {
  return (
    <footer className="bg-gray-900/50 backdrop-blur-sm border-t border-white/10 py-12 px-4">
      <div className="container mx-auto max-w-7xl">
        <div className="flex flex-col items-center gap-6">
          <nav className="flex flex-wrap justify-center gap-8 text-sm">
            <Link to="/" className="text-gray-400 hover:text-[#58a6ff] transition-colors duration-300">
              Home
            </Link>
            <Link to="/about" className="text-gray-400 hover:text-[#58a6ff] transition-colors duration-300">
              About
            </Link>
            <Link to="/contact" className="text-gray-400 hover:text-[#58a6ff] transition-colors duration-300">
              Contact
            </Link>
            <Link to="/privacy" className="text-gray-400 hover:text-[#58a6ff] transition-colors duration-300">
              Privacy Policy
            </Link>
            <Link to="/terms" className="text-gray-400 hover:text-[#58a6ff] transition-colors duration-300">
              Terms
            </Link>
          </nav>

          <div className="h-px w-full max-w-md bg-gradient-to-r from-transparent via-white/20 to-transparent"></div>

          <p className="text-gray-500 text-sm text-center">
            © 2025 University Multi-Major Portal. All Rights Reserved.
          </p>
        </div>
      </div>
    </footer>
  );
}
