import React, { useState } from "react";
import { Link, useLocation } from "react-router-dom";
import { FiUsers, FiBarChart2, FiSettings, FiMenu, FiUserCheck, FiBookOpen } from "react-icons/fi";
import { motion, AnimatePresence } from "framer-motion";

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

  // Sidebar expand/collapse animation
  const sidebarVariants = {
    open: { width: 250, transition: { type: "spring", stiffness: 220, damping: 20 } },
    closed: { width: 70, transition: { type: "spring", stiffness: 220, damping: 20 } },
  };

  // Menu item hover & tap animation
  const itemVariants = {
    hover: { scale: 1.05, backgroundColor: "rgba(128,0,255,0.15)" },
    tap: { scale: 0.95 },
  };

  return (
    <motion.aside
      animate={open ? "open" : "closed"}
      variants={sidebarVariants}
      className="h-screen flex flex-col border-r border-white/10 shadow-xl"
      style={{ backgroundColor: "var(--panel)" }}
    >
      {/* Header */}
      <div className="flex items-center justify-between px-4 py-4 border-b relative" style={{ borderColor: "rgba(255,255,255,0.1)" }}>
        {open ? (
          <>
            <motion.h1
              animate={{ opacity: 1 }}
              transition={{ duration: 0.3 }}
              className="font-bold text-lg text-accent whitespace-nowrap"
              style={{ color: "var(--accent)" }}
            >
              Admin
            </motion.h1>
            <button
              onClick={() => setOpen(!open)}
              className="p-2 rounded-full hover:bg-white/10 transition-all duration-300"
              style={{ color: "var(--accent)" }}
            >
              <FiMenu size={22} />
            </button>
          </>
        ) : (
          // Hamburger 3-dashes when closed
          <div className="flex flex-col justify-center ml-3 gap-1 cursor-pointer" onClick={() => setOpen(true)}>
            {[0, 1, 2].map((i) => (
              <motion.span
                key={i}
                className="block w-5 h-0.5 rounded-full bg-accent"
                animate={{ x: [0, 2, 0], opacity: [0.6, 1, 0.6] }}
                transition={{ repeat: Infinity, duration: 0.8 + i * 0.1, ease: "easeInOut" }}
              />
            ))}
          </div>
        )}
      </div>

      {/* Menu Items */}
      <nav className="flex-1 px-2 py-6 space-y-2 relative">
        {menuItems.map((item) => {
          const isActive = location.pathname === item.path;

          return (
            <motion.div key={item.label} variants={itemVariants} whileHover="hover" whileTap="tap" className="relative">
              <Link
                to={item.path}
                className="flex items-center gap-4 p-3 rounded-lg cursor-pointer transition-all"
                style={{
                  backgroundColor: isActive ? "rgba(128,0,255,0.2)" : "transparent",
                  color: isActive ? "var(--accent)" : "var(--muted)",
                }}
              >
                <motion.div
                  animate={isActive ? { rotate: [0, 15, -15, 0] } : { rotate: 0 }}
                  transition={{ repeat: isActive ? Infinity : 0, duration: 1.2 }}
                  className="text-xl"
                  style={{ color: isActive ? "var(--accent)" : "var(--muted)" }}
                >
                  {item.icon}
                </motion.div>

                <AnimatePresence>
                  {open && (
                    <motion.span
                      initial={{ opacity: 0, x: -20 }}
                      animate={{ opacity: 1, x: 0 }}
                      exit={{ opacity: 0, x: -20 }}
                      className="font-medium"
                    >
                      {item.label}
                    </motion.span>
                  )}
                </AnimatePresence>
              </Link>

              {/* Glowing Active Indicator */}
              {isActive && open && (
                <motion.span
                  layoutId="active-indicator"
                  className="absolute left-0 top-0 h-full w-1 rounded-r-full"
                  style={{
                    background: "var(--accent-gradient)",
                    boxShadow: "0 0 12px var(--accent), 0 0 24px var(--accent-alt)",
                  }}
                />
              )}
            </motion.div>
          );
        })}
      </nav>

      {/* Footer */}
      <div className="px-4 py-3 text-xs text-muted border-t" style={{ color: "var(--muted)", borderColor: "rgba(255,255,255,0.1)" }}>
        <AnimatePresence>
          {open ? (
            <motion.span initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}>
              © 2025 Admin Panel
            </motion.span>
          ) : (
            <motion.span initial={{ opacity: 0 }} animate={{ opacity: 1 }} exit={{ opacity: 0 }}>
              ©
            </motion.span>
          )}
        </AnimatePresence>
      </div>
    </motion.aside>
  );
}
