// src/Components/Admin_Components/Card.jsx - UNCHANGED

import React from "react";
import { motion } from "framer-motion";

export default function Card({ title, children, className = "" }) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 10 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.4 }}
      whileHover={{ y: -3, boxShadow: "0 15px 25px rgba(0,0,0,0.5)" }}
      className={`
        bg-panel 
        text-main 
        rounded-custom 
        p-5 
        border border-white/10 
        font-main 
        shadow-lg 
        transition-all 
        duration-300
        ${className}
      `}
    >
      {title && (
        <h3
          className="text-lg font-semibold mb-4"
          style={{
            background: "linear-gradient(135deg, var(--accent), var(--accent-alt))",
            WebkitBackgroundClip: "text",
            WebkitTextFillColor: "transparent",
          }}
        >
          {title}
        </h3>
      )}
      <div className="text-main">{children}</div>
    </motion.div>
  );
}