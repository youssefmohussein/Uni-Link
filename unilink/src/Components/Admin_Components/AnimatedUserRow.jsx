// src/Pages/Admin/components/AnimatedUserRow.jsx
import React from "react";
import { motion } from "framer-motion";
import { FaEdit, FaTrash } from "react-icons/fa";

export default function AnimatedUserRow({ u, faculties, majors, setEditingUser, handleDeleteUser, index }) {
  const facultyName = (faculties || []).find(f => f.id === u.faculty_id)?.name;
  const majorName = (majors || []).find(m => m.id === u.major_id)?.name;

  const itemVariants = {
    hidden: { opacity: 0, height: 0, paddingTop: 0, paddingBottom: 0, marginTop: 0, marginBottom: 0 },
    visible: { 
      opacity: 1, height: "auto", paddingTop: 12, paddingBottom: 12,
      transition: { delay: index * 0.05, duration: 0.3, height: { duration: 0.3 } }
    },
    exit: { opacity: 0, height: 0, paddingTop: 0, paddingBottom: 0, transition: { duration: 0.3 } }
  };

  return (
    <motion.div
      initial="hidden"
      animate="visible"
      exit="exit"
      variants={itemVariants}
      custom={index}
      className="grid grid-cols-12 gap-2 border-b border-white/10 items-center hover:bg-white/5 transition duration-150"
    >
      <div className="col-span-2 text-white font-medium truncate">{u.username}</div>
      <div className="col-span-2 text-white/80 truncate">{u.email}</div>
      <div className="col-span-2 text-white/80">{u.role}</div>
      <div className="col-span-2 text-white/80 truncate">{facultyName}</div>
      <div className="col-span-2 text-white/80 truncate">{majorName}</div>
      <div className="col-span-2 flex gap-2 justify-end">
        <button onClick={() => setEditingUser(u)} className="text-yellow-400 p-1 rounded-full hover:bg-yellow-400/20 transition" title="Edit"><FaEdit /></button>
        <button onClick={() => handleDeleteUser(u.user_id)} className="text-red-500 p-1 rounded-full hover:bg-red-500/20 transition" title="Delete"><FaTrash /></button>
      </div>
    </motion.div>
  );
}
