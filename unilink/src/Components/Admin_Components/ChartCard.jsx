// src/Components/Admin_Components/ChartCard.jsx
import React from "react";
import { motion } from "framer-motion";
import { useNavigate } from "react-router-dom";

export default function ChartCard({ title, children, path }) {
  const navigate = useNavigate();

  return (
    <motion.div
      className={`bg-panel rounded-custom p-6 border border-border shadow-md hover:shadow-lg transition-smooth ${path ? 'cursor-pointer' : ''}`}
      whileHover={{ scale: 1.01 }}
      onClick={() => path && navigate(path)}
    >
      <h3 className="text-lg font-semibold mb-4">{title}</h3>
      {children}
    </motion.div>
  );
}
