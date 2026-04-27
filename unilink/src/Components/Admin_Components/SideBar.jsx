import React, { useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { FiUsers, FiBarChart2, FiSettings, FiMenu, FiUserCheck, FiBookOpen, FiGlobe, FiLogOut } from "react-icons/fi";

export default function Sidebar() {
  const [open, setOpen] = useState(true);
  const location = useLocation();

  const menuItems = [
    { label: "Dashboard", icon: <FiBarChart2 />, path: "/admin/dashboard" },
    { label: "Users", icon: <FiUsers />, path: "/admin/users" },
    { label: "Students", icon: <FiBookOpen />, path: "/admin/students" },
    { label: "Professors", icon: <FiUserCheck />, path: "/admin/manage-professors" },
    { label: "Rooms", icon: <FiUsers />, path: "/admin/rooms" },
    { label: "Admin", icon: <FiUsers />, path: "/admin/admin" },
    { label: "University", icon: <FiGlobe />, path: "/admin/university" }
  ];

  const handleLogout = async () => {
    try {
      await fetch('http://localhost:8000/logout', {
        method: 'POST',
        credentials: 'include'
      });
      window.location.href = '/login';
    } catch (err) {
      console.error('Logout error:', err);
      window.location.href = '/login';
    }
  };

  return (
    <aside
      className={`h-screen flex flex-col border-r border-white/10 shadow-xl transition-all duration-300 ${open ? 'w-[250px]' : 'w-[70px]'}`}
      style={{ backgroundColor: "var(--panel)" }}
    >
      {/* Header */}
      <div className="flex items-center justify-between px-4 py-4 border-b relative h-16" style={{ borderColor: "rgba(255,255,255,0.1)" }}>
        {open ? (
          <>
            <h1
              className="font-bold text-lg text-accent whitespace-nowrap"
              style={{ color: "var(--accent)" }}
            >
              Admin
            </h1>
            <button
              onClick={() => setOpen(!open)}
              className="p-2 rounded-full hover:bg-white/10 transition-all duration-300"
              style={{ color: "var(--accent)" }}
            >
              <FiMenu size={22} />
            </button>
          </>
        ) : (
          <div className="flex flex-col justify-center ml-3 gap-1 cursor-pointer w-full h-full items-center" onClick={() => setOpen(true)}>
            <FiMenu size={22} style={{ color: "var(--accent)" }} />
          </div>
        )}
      </div>

      {/* Menu Items */}
      <nav className="flex-1 px-2 py-6 space-y-2 relative overflow-y-auto overflow-x-hidden">
        {menuItems.map((item) => {
          const isActive = location.pathname === item.path;

          return (
            <div key={item.label} className="relative transition-all duration-200 hover:scale-[1.02]">
              <Link
                to={item.path}
                className="flex items-center gap-4 p-3 rounded-lg cursor-pointer transition-all"
                style={{
                  backgroundColor: isActive ? "rgba(128,0,255,0.2)" : "transparent",
                  color: isActive ? "var(--accent)" : "var(--muted)",
                }}
              >
                <div
                  className="text-xl"
                  style={{ color: isActive ? "var(--accent)" : "var(--muted)" }}
                >
                  {item.icon}
                </div>

                {open && (
                  <span className="font-medium whitespace-nowrap">
                    {item.label}
                  </span>
                )}
              </Link>

              {/* Glowing Active Indicator */}
              {isActive && open && (
                <span
                  className="absolute left-0 top-0 h-full w-1 rounded-r-full"
                  style={{
                    background: "var(--accent-gradient)",
                    boxShadow: "0 0 12px var(--accent), 0 0 24px var(--accent-alt)",
                  }}
                />
              )}
            </div>
          );
        })}
      </nav>

      {/* Footer */}
      <div className="px-4 py-3 border-t space-y-3" style={{ borderColor: "rgba(255,255,255,0.1)" }}>
        {/* Logout Button */}
        <button
          onClick={handleLogout}
          className="w-full flex items-center gap-4 p-3 rounded-lg cursor-pointer transition-all hover:scale-[1.02]"
          style={{
            backgroundColor: "transparent",
            color: "var(--muted)",
          }}
        >
          <div className="text-xl">
            <FiLogOut />
          </div>
          {open && (
            <span className="font-medium whitespace-nowrap">
              Logout
            </span>
          )}
        </button>

        {/* Copyright */}
        <div className="text-xs text-muted text-center" style={{ color: "var(--muted)" }}>
          {open ? (
            <span>© 2025 Admin Panel</span>
          ) : (
            <span>©</span>
          )}
        </div>
      </div>
    </aside>
  );
}
