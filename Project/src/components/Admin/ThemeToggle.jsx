import React from "react";

export default function ThemeToggle({ theme, toggleTheme }) {
  return (
    <div className="flex justify-between items-center bg-main bg-opacity-30 p-4 rounded-custom shadow-custom">
      <div>
        <h2 className="text-lg font-semibold text-main">Theme</h2>
        <p className="text-muted text-sm">Switch between light and dark mode.</p>
      </div>
      <button
        onClick={toggleTheme}
        className="bg-accent text-white px-4 py-2 rounded-custom hover:opacity-80 transition"
      >
        {theme === "dark" ? "Light Mode ☀️" : "Dark Mode 🌙"}
      </button>
    </div>
  );
}
