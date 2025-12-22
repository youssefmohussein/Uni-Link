import React, { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { FiBell, FiUser, FiPlus, FiSearch, FiX } from "react-icons/fi";
import Logo from "../../Uni-Link-Logo.webp";
import Notifications from "../Notifications/Notifications";
import { getUnreadCount } from "../../services/notificationService";

const Header = ({ onShareActivity, onSearch, searchQuery, onClearSearch, hideShareButton = false }) => {
  const [showNotifications, setShowNotifications] = useState(false);
  const [unreadCount, setUnreadCount] = useState(0);
  const [user, setUser] = useState(null);
  const navigate = useNavigate();

  // Get user from localStorage
  useEffect(() => {
    const userData = localStorage.getItem('user');
    if (userData) {
      try {
        const parsedUser = JSON.parse(userData);
        console.log("Header: User data loaded:", parsedUser);
        setUser(parsedUser);
      } catch (e) {
        console.error("Header: Error parsing user data:", e);
      }
    }
  }, []);

  // Fetch unread count on mount and periodically
  useEffect(() => {
    const fetchUnreadCount = async () => {
      try {
        console.log("Header: Fetching unread count...");
        const data = await getUnreadCount();
        console.log("Header: Unread count response:", data);

        if (data.status === 'success') {
          const count = data.data?.unread_count || 0;
          console.log("Header: Setting unread count to:", count);
          setUnreadCount(count);
        }
      } catch (error) {
        console.error('Header: Error fetching unread count:', error);
      }
    };

    fetchUnreadCount();

    // Poll every 30 seconds
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
