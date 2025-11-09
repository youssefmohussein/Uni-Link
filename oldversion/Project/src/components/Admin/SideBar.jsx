import React, { useState } from "react";
import { Link, useLocation } from "react-router-dom";
import {
  FiUsers,
  FiBarChart2,
  FiSettings,
  FiMenu,
  FiUserCheck,
  FiBookOpen,
} from "react-icons/fi";

export default function Sidebar() {
  const [open, setOpen] = useState(true);
  const location = useLocation();

  const menuItems = [
    { label: "Dashboard", icon: <FiBarChart2 />, path: "/admin/dashboard" },
    { label: "Users", icon: <FiUsers />, path: "/admin/users" },
    { label: "Students", icon: <FiBookOpen />, path: "/admin/students" },
    { label: "Professors", icon: <FiUserCheck />, path: "/admin/manage-professors" },
    { label: "Admin", icon: <FiUsers />, path: "/admin/manage-tas" },
    { label: "Settings", icon: <FiSettings />, path: "/admin/settings" },
  ];

  return (
    <aside
      className={`bg-panel shadow-custom h-screen transition-all duration-200 ${
        open ? "w-64" : "w-20" 
      } flex flex-col border-r border-white/10 font-main`}
    >
      
      <div className="flex items-center justify-between px-4 py-4 border-b border-white/10">
        <h1
          className={`font-bold text-lg text-accent transition-all duration-300 ${
            open ? "opacity-100" : "opacity-0 w-0"
          }`}
        >
          Admin
        </h1>
        <button
          onClick={() => setOpen(!open)}
          className="p-2 rounded-custom hover:bg-accent/10 text-accent transition"
        >
          <FiMenu size={20} />
        </button>
      </div>

      <nav className="flex-1 px-3 py-6 space-y-2">
        {menuItems.map((item) => {
          const isActive = location.pathname === item.path;
          return (
            <Link
              key={item.label}
              to={item.path}
              className={`flex items-center gap-3 p-3 rounded-custom cursor-pointer transition-all ${
                isActive
                  ? "bg-accent/20 text-accent border-r-2 border-accent"
                  : "hover:bg-panel/70 text-muted hover:text-accent"
              }`}
            >
              <div
                className={`${
                  isActive ? "text-accent" : "text-muted group-hover:text-accent"
                }`}
              >
                {item.icon}
              </div>
              {open && <span className="font-medium">{item.label}</span>}
            </Link>
          );
        })}
      </nav>

    
      <div className="px-4 py-3 border-t border-white/10 text-xs text-muted">
        {open ? "© 2025 Admin Panel" : "©"}
      </div>
    </aside>
  );
}
