import React from "react";
import { motion } from "framer-motion";

/**
 * Card Component
 * A reusable UI container with subtle animation.
 * Props:
 *  - title: string (optional)
 *  - children: JSX content
 *  - className: optional extra classes
 */
export default function Card({ title, children, className = "" }) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 10 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.3, ease: "easeOut" }}
      whileHover={{
        y: -4,
        boxShadow: "0 12px 25px rgba(0, 0, 0, 0.35)",
      }}
      className={`
        bg-panel 
        text-main 
        rounded-custom 
        p-5 
        border border-white/10 
        font-main 
        shadow-md 
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
