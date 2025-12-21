import React from "react";
import { Link } from "react-router-dom";
import { FiBell, FiUser, FiPlus } from "react-icons/fi";
import Logo from "../../Uni-Link-Logo.webp";
import Notifications from "../Notifications/Notifications";
import { getUnreadCount } from "../../services/notificationService";

const Header = ({ onShareActivity, onSearch, searchQuery, onClearSearch, hideShareButton = false }) => {
  const [showNotifications, setShowNotifications] = React.useState(false);
  const [unreadCount, setUnreadCount] = React.useState(0);

  React.useEffect(() => {
    const fetchCount = async () => {
      try {
        const data = await getUnreadCount();
        if (data.status === 'success') {
          setUnreadCount(data.data.unread_count);
        }
      } catch (err) {
        console.error("Failed to fetch unread count", err);
      }
    };
    fetchCount();
    const interval = setInterval(fetchCount, 30000);
    return () => clearInterval(interval);
  }, []);

  return (
    <header className="fixed top-0 left-0 w-full z-[100] bg-[#0d1117]/80 shadow-lg border-b border-white/5 backdrop-blur-xl">
      <div className="max-w-7xl mx-auto flex items-center justify-between px-6 h-16 text-white font-main">

        {/* Left: Logo and UniLink */}
        <Link to="/" className="flex items-center space-x-3 group">
          <img
            src={Logo}
            alt="UniLink Logo"
            className="h-10 w-10 object-contain drop-shadow-[0_0_15px_rgba(88,166,255,0.4)] transition-transform duration-200 group-hover:scale-110"
          />
          <span className="text-xl font-bold bg-gradient-to-r from-[#58a6ff] to-[#3b82f6] bg-clip-text text-transparent tracking-wide">
            UniLink
          </span>
        </Link>

        {/* Right: Actions */}
        <div className="flex items-center space-x-4">

          {/* Share Button (Blue Accent from index.css) */}
          {!hideShareButton && (
            <button
              onClick={onShareActivity}
              className="hidden md:flex items-center justify-center bg-[#58a6ff] hover:bg-[#3b82f6] text-white rounded-full px-5 py-1.5 font-bold text-xs 
              shadow-[0_4px_10px_rgba(88,166,255,0.2)] hover:shadow-[0_0_20px_rgba(88,166,255,0.4)] transition-all duration-200"
            >
              <FiPlus className="mr-2 text-sm" />
              Share
            </button>
          )}

          {/* Notification Bell */}
          <div className="relative">
            <button
              onClick={() => setShowNotifications(!showNotifications)}
              className={`relative p-2 rounded-xl transition-all duration-200 ${showNotifications ? 'bg-accent/20 text-accent' : 'hover:bg-white/5 text-gray-400 hover:text-white'}`}
            >
              <FiBell className="text-lg" />
              {unreadCount > 0 && (
                <span className="absolute top-1.5 right-1.5 h-2 w-2 bg-red-500 rounded-full border-2 border-[#0d1117] animate-pulse"></span>
              )}
            </button>

            {showNotifications && (
              <Notifications
                isOpen={showNotifications}
                onClose={() => setShowNotifications(false)}
              />
            )}
          </div>

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
