// src/Components/Admin_Components/ChartCard.jsx
import React from "react";
import { motion } from "framer-motion";

export default function ChartCard({ title, children }) {
  return (
    <motion.div
      className="bg-panel rounded-custom p-6 border border-border shadow-md hover:shadow-lg transition-smooth"
      whileHover={{ scale: 1.01 }}
    >
      <h3 className="text-lg font-semibold mb-4">{title}</h3>
      {children}
    </motion.div>
  );
}
