import React from "react";

export default function SettingSection({ title, description, buttonText }) {
  return (
    <div className="bg-main bg-opacity-30 rounded-custom p-4 flex justify-between items-center shadow-custom">
      <div>
        <h2 className="text-lg font-semibold text-main">{title}</h2>
        <p className="text-muted text-sm">{description}</p>
      </div>
      <button className="bg-accent text-white px-4 py-2 rounded-custom hover:opacity-80 transition">
        {buttonText}
      </button>
    </div>
  );
}
