import React, { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import { FiBell } from "react-icons/fi";
import logo from '../Uni-Link-Logo.webp';
import Notifications from "./Notifications/Notifications";
import { getUnreadCount } from "../services/notificationService";

const Navbar = () => {
    const navigate = useNavigate();
    const [showNotifications, setShowNotifications] = useState(false);
    const [unreadCount, setUnreadCount] = useState(0);
    const [user, setUser] = useState(null);

    // Get user from localStorage
    useEffect(() => {
        const userData = localStorage.getItem('user');
        if (userData) {
            try {
                const parsedUser = JSON.parse(userData);
                setUser(parsedUser);
            } catch (e) {
                console.error("Navbar: Error parsing user data:", e);
            }
        }
    }, []);

    // Fetch unread count periodically
    useEffect(() => {
        const fetchUnreadCount = async () => {
            try {
                const data = await getUnreadCount();
                if (data.status === 'success') {
                    setUnreadCount(data.data?.unread_count || 0);
                }
            } catch (error) {
                console.error('Navbar: Error fetching unread count:', error);
            }
        };

        fetchUnreadCount();
        const interval = setInterval(fetchUnreadCount, 30000);
        return () => clearInterval(interval);
    }, []);

    const handleLogout = () => {
        localStorage.removeItem('user');
        localStorage.removeItem('token');
        navigate('/login');
    };

    const handleProfileClick = () => {
        if (user?.role === 'Admin') {
            navigate('/admin/dashboard');
        } else {
            navigate('/profile');
        }
    };

    return (
        <nav className="fixed top-0 left-0 w-full z-40 px-8 py-4 flex justify-between items-center border-b border-white/10 bg-white/5 backdrop-blur-md shadow-lg">
            {/* Left: Logo */}
            <div className="flex items-center">
                <a href="/"><img src={logo} alt="Uni-Link Logo" className="h-12 w-auto object-contain drop-shadow-[0_0_10px_rgba(255,255,255,0.3)]" /></a>
            </div>

            {/* Center: Navigation Links */}
            <div className="hidden md:flex items-center space-x-14 ml-10">
                <a href="/" className="text-white/80 hover:text-accent font-medium text-lg transition-all duration-300 hover:[text-shadow:0_0_10px_rgba(88,166,255,0.5)]">
                    Home
                </a>
                <a href="/faculties" className="text-white/80 hover:text-accent font-medium text-lg transition-all duration-300 hover:[text-shadow:0_0_10px_rgba(88,166,255,0.5)]">
                    Faculties
                </a>
                <a href="/posts" className="text-white/80 hover:text-accent font-medium text-lg transition-all duration-300 hover:[text-shadow:0_0_10px_rgba(88,166,255,0.5)]">
                    Posts
                </a>
                <a href="/project-rooms" className="text-white/80 hover:text-accent font-medium text-lg transition-all duration-300 hover:[text-shadow:0_0_10px_rgba(88,166,255,0.5)]">
                    Rooms
                </a>
                <a href="/about" className="text-white/80 hover:text-accent font-medium text-lg transition-all duration-300 hover:[text-shadow:0_0_10px_rgba(88,166,255,0.5)]">
                    About Us
                </a>
            </div>

            {/* Right: Auth or Profile */}
            <div className="flex items-center space-x-4">
                {user ? (
                    <div className="flex items-center space-x-4">
                        {/* Notification Bell */}
                        <button
                            onClick={() => setShowNotifications(!showNotifications)}
                            className="relative group p-2 rounded-full hover:bg-white/10 transition-colors"
                        >
                            <FiBell className="text-white/80 group-hover:text-accent text-xl" />
                            {unreadCount > 0 && (
                                <span className="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full"></span>
                            )}
                        </button>

                        <div className="flex items-center space-x-3 border-l border-white/10 pl-4">
                            {/* Username */}
                            <span className="text-sm text-white/80">{user.username || user.name}</span>

                            {/* Profile Icon */}
                            <button
                                onClick={handleProfileClick}
                                className="p-2 rounded-full hover:bg-white/10 transition-colors"
                                title={user.role === 'Admin' ? 'Admin Dashboard' : 'Profile'}
                            >
                                {user.role === 'Admin' ? (
                                    <i className="fa-solid fa-user-tie text-white/80 hover:text-accent text-lg"></i>
                                ) : (
                                    <i className="fa-solid fa-user text-white/80 hover:text-accent text-lg"></i>
                                )}
                            </button>

                            {/* Logout Icon */}
                            <button
                                onClick={handleLogout}
                                className="p-2 rounded-full hover:bg-white/10 transition-colors"
                                title="Logout"
                            >
                                <i className="fa-solid fa-arrow-right-from-bracket text-white/80 hover:text-red-400 text-lg"></i>
                            </button>
                        </div>
                    </div>
                ) : (
                    <button className="px-6 py-2 rounded-full text-white font-semibold border border-white/20 hover:bg-white/10 transition-all duration-300 backdrop-blur-sm">
                        <a href="/login">Log in</a>
                    </button>
                )}
            </div>

            {/* Notifications Panel */}
            <Notifications
                isOpen={showNotifications}
                onClose={() => setShowNotifications(false)}
            />
        </nav>
    );
};

export default Navbar;

