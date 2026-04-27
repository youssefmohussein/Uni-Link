// src/Components/Admin_Components/ChartCard.jsx
import React from "react";

import { useNavigate } from "react-router-dom";

export default function ChartCard({ title, children, path }) {
  const navigate = useNavigate();

  return (
    <div
      className={`bg-panel rounded-custom p-6 border border-border shadow-md hover:shadow-lg transition-smooth ${path ? 'cursor-pointer' : ''}`}
     
      onClick={() => path && navigate(path)}
    >
      <h3 className="text-lg font-semibold mb-4">{title}</h3>
      {children}
    </div>
  );
}
