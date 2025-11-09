// src/Pages/Admin/AdminUsersPage.jsx
import React, { useState, useEffect } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import UserForm from "../../Components/Admin_Components/UserForm";
import UsersTable from "../../Components/Admin_Components/UsersTable";
import { motion } from "framer-motion";

export default function AdminUsersPage() {
  const [users, setUsers] = useState([]);
  const [editingUser, setEditingUser] = useState(null);
  const [isAdding, setIsAdding] = useState(false);
  const [query, setQuery] = useState("");
  const [faculties, setFaculties] = useState([]);
  const [majors, setMajors] = useState([]);

  useEffect(() => {
    // TODO: Fetch users, faculties, majors from backend
  }, []);

  const closeForm = () => { setEditingUser(null); setIsAdding(false); };
  const handleAddUser = (user) => { /* TODO: POST to backend */ closeForm(); };
  const handleEditUser = (updatedUser) => { /* TODO: PUT to backend */ closeForm(); };
  const handleDeleteUser = (id) => { if(window.confirm("Delete?")) setUsers(prev => prev.filter(u => u.user_id !== id)); };

  return (
    <div className="flex min-h-screen font-main bg-bg text-main">
      <Sidebar />

      <UserForm
        isOpen={isAdding || !!editingUser}
        onClose={closeForm}
        initialData={editingUser}
        onSubmit={editingUser ? handleEditUser : handleAddUser}
        faculties={faculties}
        majors={majors}
      />

      <main className="flex-1 p-6 space-y-6 overflow-auto">
        <div className="flex justify-between items-center mb-4">
          <h1 className="text-3xl font-bold">Users Dashboard</h1>
          <motion.button
            onClick={() => setIsAdding(true)}
            className="px-4 py-2 rounded-custom text-white font-semibold shadow-lg transition duration-200 relative z-0 overflow-hidden"
            style={{ background: "var(--accent-gradient)", border: "none", boxShadow: "0 0 10px rgba(166, 0, 255, 0.4)" }}
            whileHover={{ scale: 1.05, filter: "brightness(1.1)", boxShadow: "0 0 15px rgba(190, 70, 255, 0.6)", transition: { duration: 0.3, ease: "easeInOut" } }}
            whileTap={{ scale: 0.95 }}
          >
            Add User
          </motion.button>
        </div>

        <UsersTable
          users={users}
          faculties={faculties}
          majors={majors}
          query={query}
          setQuery={setQuery}
          setEditingUser={setEditingUser}
          handleDeleteUser={handleDeleteUser}
        />
      </main>
    </div>
  );
}
