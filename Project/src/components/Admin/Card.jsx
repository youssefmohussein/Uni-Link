import React from "react";

export default function Card({ title, children }) {
  return (
    <div className="bg-panel text-main shadow-custom rounded-custom p-4 border border-white/10 font-main">
      {title && (
        <h3 className="text-lg font-semibold mb-3 text-accent">
          {title}
        </h3>
      )}
      <div>{children}</div>
    </div>
  );
}
