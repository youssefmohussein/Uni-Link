import React from "react";
import { motion } from "framer-motion";

const LoadingPage = () => {
  return (
    <div className="flex items-center justify-center h-screen bg-gray-900">
      <div className="flex flex-col items-center space-y-6">
        {/* Spinner */}
        <motion.div
          animate={{ rotate: 360 }}
          transition={{
            repeat: Infinity,
            duration: 1,
            ease: "linear",
          }}
          className="w-16 h-16 border-4 border-t-transparent border-brand-gold rounded-full"
        />

        {/* Logo or Title */}
        <motion.h1
          initial={{ opacity: 0, y: 10 }}
          animate={{ opacity: 1, y: 0 }}
          transition={{ duration: 0.6, repeat: Infinity, repeatType: "reverse" }}
          className="text-white text-xl font-semibold tracking-wide"
        >
          Loading...
        </motion.h1>
      </div>
    </div>
  );
};

export default LoadingPage;
