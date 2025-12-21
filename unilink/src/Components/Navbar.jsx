import React from 'react';
import logo from '../Uni-Link-Logo.webp';
import authHandler from '../handlers/authHandler';

const Navbar = () => {
    const [user, setUser] = React.useState(null);

    React.useEffect(() => {
        const userData = localStorage.getItem('user');
        if (userData) {
            setUser(JSON.parse(userData));
        }

        // Listen for storage changes (for login/logout in other tabs/logic)
        const handleStorage = () => {
            const updated = localStorage.getItem('user');
            setUser(updated ? JSON.parse(updated) : null);
        };
        window.addEventListener('storage', handleStorage);
        return () => window.removeEventListener('storage', handleStorage);
    }, []);

    const handleLogout = async () => {
        await authHandler.logout();
        setUser(null);
        window.location.href = '/login';
    };

    const handleProfileClick = () => {
        if (!user) return;
        const role = user.role?.toUpperCase();
        if (role === 'ADMIN') {
            window.location.href = '/admin/dashboard';
        } else {
            window.location.href = '/profile';
        }
    };

    const renderUserIcon = () => {
        if (user.profile_image) {
            return (
                <img
                    src={user.profile_image}
                    alt={user.username}
                    className="h-10 w-10 rounded-full object-cover border border-white/20 shadow-inner group-hover:border-accent transition-colors duration-300"
                />
            );
        }

        const isAdmin = user.role?.toUpperCase() === 'ADMIN';
        return (
            <div className="h-10 w-10 rounded-full flex items-center justify-center bg-white/10 border border-white/20 group-hover:border-accent transition-all duration-300">
                <i className={`fa-solid ${isAdmin ? 'fa-user-gear' : 'fa-circle-user'} text-2xl text-white/80 group-hover:text-accent`}></i>
            </div>
        );
    };

    return (
        <nav className="fixed top-0 left-0 w-full z-50 px-8 py-4 flex justify-between items-center border-b border-white/10 bg-white/5 backdrop-blur-md shadow-lg">
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
                <a href="/about" className="text-white/80 hover:text-accent font-medium text-lg transition-all duration-300 hover:[text-shadow:0_0_10px_rgba(88,166,255,0.5)]">
                    About Us
                </a>
            </div>

            {/* Right: Auth Status */}
            <div className="flex items-center space-x-6">
                {user ? (
                    <div className="flex items-center space-x-4">
                        <div
                            onClick={handleProfileClick}
                            className="flex items-center space-x-3 pr-4 border-r border-white/10 cursor-pointer group"
                        >
                            {renderUserIcon()}
                            <span className="text-white font-bold text-lg hidden lg:block group-hover:text-accent transition-colors duration-300">
                                {user.username}
                            </span>
                        </div>
                        <button
                            onClick={handleLogout}
                            title="Logout"
                            className="text-white/60 hover:text-red-400 transition-colors duration-300 text-xl"
                        >
                            <i className="fa-solid fa-right-from-bracket"></i>
                        </button>
                    </div>
                ) : (
                    <button className="px-6 py-2 rounded-full text-white font-semibold border border-white/20 hover:bg-white/10 transition-all duration-300 backdrop-blur-sm">
                        <a href="/login">Log in</a>
                    </button>
                )}
            </div>
        </nav>
    );
};

export default Navbar;
