import React from "react";
import { motion } from "framer-motion";

function ProfileHeader({ name, title, bio, image }) {
  return (
    <motion.header
      initial={{ opacity: 0, y: -8 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5, ease: "easeOut" }}
      className="bg-panel rounded-custom shadow-custom p-6 md:p-8 mb-8 border border-white/10 flex flex-col md:flex-row items-center gap-6"
    >
      {/* Profile Image */}
      <motion.div
        whileHover={{ scale: 1.04 }}
        transition={{ duration: 0.3 }}
        className="w-28 h-28 md:w-32 md:h-32 lg:w-36 lg:h-36 rounded-full overflow-hidden border-2 border-accent/60 shadow-md"
      >
        <img
          src={image}
          alt={`${name} profile`}
          className="w-full h-full object-cover"
        />
      </motion.div>

      {/* Text Info */}
      <div className="flex-1 text-center md:text-left space-y-1">
        <h1 className="text-3xl font-semibold text-main leading-tight">
          {name}
        </h1>
        <p className="text-accent font-medium">{title}</p>
        <p className="text-muted mt-2 text-sm leading-relaxed max-w-xl">
          {bio}
        </p>
      </div>
    </motion.header>
  );
}

export default ProfileHeader;
