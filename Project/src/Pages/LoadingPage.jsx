import React from "react";
import { motion } from "framer-motion";
import logo from "../assets/logo.jpg";

const LoadingPage = () => {
  return (
    <motion.div
      initial={{ opacity: 0 }}
      animate={{ opacity: 1 }}
      exit={{ opacity: 0 }}
      transition={{ duration:1}}
      className="flex items-center justify-center h-screen bg-main font-main relative overflow-hidden"
    >
      {/* Animated gradient background shimmer */}
      <motion.div
        animate={{
          backgroundPosition: ["0% 50%", "100% 50%", "0% 50%"],
        }}
        transition={{
          duration: 6,
          repeat: Infinity,
          ease: "linear",
        }}
        className="absolute inset-0 bg-[linear-gradient(135deg,var(--panel),var(--bg),var(--panel))] bg-[length:200%_200%] opacity-20"
      />

      {/* Outer animated glow ring */}
      <motion.div
        animate={{
          scale: [1, 1.2, 1],
          opacity: [0.4, 0.1, 0.4],
        }}
        transition={{
          duration: 2.5,
          repeat: Infinity,
          ease: "easeInOut",
        }}
        className="absolute w-72 h-72 rounded-full bg-accent/10 blur-3xl"
      />

      <div className="flex flex-col items-center space-y-10 z-10">
        {/* 3D gradient spinning ring */}
        <motion.div
          animate={{ rotate: 360 }}
          transition={{
            repeat: Infinity,
            duration: 2.2,
            ease: "linear",
          }}
          className="w-32 h-32 rounded-full border-[3px] border-t-transparent border-b-transparent border-l-accent border-r-[#1f6feb] flex items-center justify-center shadow-[0_0_25px_rgba(88,166,255,0.3)]"
        >
          {/* Logo */}
          <motion.img
            src={logo}
            alt="Logo"
            initial={{ scale: 0.8, opacity: 0 }}
            animate={{
              scale: [0.9, 1.05, 0.9],
              opacity: [0.7, 1, 0.7],
            }}
            transition={{
              duration: 2,
              repeat: Infinity,
              ease: "easeInOut",
            }}
            className="w-16 h-16 object-cover rounded-full shadow-[0_0_20px_rgba(88,166,255,0.4)]"
          />
        </motion.div>

        {/* Loading text */}
        <motion.p
          initial={{ opacity: 0.3 }}
          animate={{ opacity: [0.3, 1, 0.3] }}
          transition={{
            duration: 1.5,
            repeat: Infinity,
            ease: "easeInOut",
          }}
          className="text-muted text-lg tracking-[0.35em] uppercase"
        >
          Loading
        </motion.p>
      </div>
    </motion.div>
  );
};

export default LoadingPage;
