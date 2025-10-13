import React from "react";
import { motion } from "framer-motion";

const LoadingPage = () => {
  return (
    <div className="flex items-center justify-center h-screen bg-main font-main">
      <div className="flex flex-col items-center space-y-8">
        {/* Logo or Brand Name */}
        <motion.h1
          initial={{ opacity: 0, scale: 0.9 }}
          animate={{ opacity: [0.6, 1, 0.6], scale: [0.9, 1.05, 0.9] }}
          transition={{
            duration: 2,
            repeat: Infinity,
            ease: "easeInOut",
          }}
          className="text-accent text-4xl font-semibold tracking-wide"
        >
          YourBrand
        </motion.h1>

        {/* Spinner */}
        <motion.div
          animate={{ rotate: 360 }}
          transition={{
            repeat: Infinity,
            duration: 1.4,
            ease: "linear",
          }}
          className="w-16 h-16 border-[3px] border-t-transparent border-accent rounded-full shadow-custom"
        />

        {/* Loading Text */}
        <motion.p
          initial={{ opacity: 0.3 }}
          animate={{ opacity: [0.3, 1, 0.3] }}
          transition={{
            duration: 1.5,
            repeat: Infinity,
            ease: "easeInOut",
          }}
          className="text-muted text-lg tracking-[0.3em] uppercase"
        >
          Loading
        </motion.p>
      </div>
    </div>
  );
};

export default LoadingPage;
