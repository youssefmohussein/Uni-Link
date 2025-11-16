import React from "react";
import { motion } from "framer-motion";
import { FiEdit2, FiTrash2 } from "react-icons/fi";

export default function AnimatedUserRow({ u, setEditingUser, handleDeleteUser, index }) {
  return (
    <motion.div
      initial={{ opacity: 0, y: 10 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ delay: index * 0.03 }}
      className="grid grid-cols-12 gap-2 px-4 py-2 border-b border-white/10 items-center text-sm text-white/70 hover:bg-white/5"
    >
      <div className="col-span-3 truncate">{u.username}</div>
      <div className="col-span-4 truncate">{u.email}</div>
      <div className="col-span-3 truncate">{u.role}</div>

      <div className="col-span-2 flex justify-end gap-3">

        {/* Edit Button */}
        <button
          onClick={() => setEditingUser(u)}
          className="
            p-2 rounded cursor-pointer
            text-accent
            transition-all duration-200
            hover:scale-110
            hover:drop-shadow-[0_0_6px_currentColor]
          "
          title="Edit user"
        >
          <FiEdit2 size={16} />
        </button>

        {/* Delete Button */}
        <button
          onClick={() => handleDeleteUser(u.user_id)}
          className="
            p-2 rounded cursor-pointer
            text-red-500
            transition-all duration-200
            hover:scale-110
            hover:drop-shadow-[0_0_6px_currentColor]
          "
          title="Delete user"
        >
          <FiTrash2 size={16} />
        </button>

      </div>
    </motion.div>
  );
}
