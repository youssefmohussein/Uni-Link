import React, { useState, useEffect } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import UsersTable from "../../Components/Admin_Components/UsersTable";
import UserForm from "../../Components/Admin_Components/UserForm";
import { motion } from "framer-motion";
import { apiRequest } from "../../../api/apiClient";

export default function AdminUsersPage() {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [query, setQuery] = useState("");
  const [isAdding, setIsAdding] = useState(false);
  const [editingUser, setEditingUser] = useState(null);

  // 🔹 Fetch all users from backend
  const getUsers = async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await apiRequest("index.php/getUsers", "GET");
      if (data.status !== "success") {
        throw new Error(data.message || "Failed to fetch users");
      }
      setUsers(data.data ?? []);
    } catch (err) {
      console.error(err);
      setError("Failed to load users. Please check backend connection.");
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    getUsers();
  }, []);

  // 🔹 Delete user
  const handleDeleteUser = async (id) => {
    if (!window.confirm("Are you sure you want to delete this user?")) return;
    try {
      await apiRequest(`index.php/deleteUser/${id}`, "DELETE");
      setUsers((prev) => prev.filter((u) => u.user_id !== id));
    } catch (err) {
      alert("Delete failed: " + (err.message || ""));
      console.error(err);
    }
  };

  // 🔹 Add user (from UserForm)
  const handleAddUser = async (newUserData) => {
    try {
      const res = await apiRequest("index.php/addUser", "POST", newUserData);
      if (res.status !== "success") throw new Error(res.message || "Failed to add user");
      setUsers((prev) => [res.data ?? newUserData, ...prev]);
      setIsAdding(false);
    } catch (err) {
      alert("Add user failed: " + (err.message || ""));
      console.error(err);
    }
  };

  // 🔹 Close form
  const closeForm = () => {
    setIsAdding(false);
    setEditingUser(null);
  };

  // ---------- UI states ----------
  if (loading) return <div className="text-center py-10">Loading...</div>;
  if (error) return <div className="text-center py-10 text-red-500">{error}</div>;

  // ---------- Render ----------
  return (
    <div className="flex min-h-screen font-main bg-bg text-main">
      <Sidebar />

      <main className="flex-1 p-6 space-y-6 overflow-auto">
        <div className="flex justify-between items-center mb-4">
          <h1 className="text-3xl font-bold">Users Dashboard</h1>
          <div className="flex gap-2">
            {/* Refresh Button */}
            <motion.button
              onClick={getUsers}
              className="px-4 py-2 rounded-custom text-white font-semibold shadow-lg transition duration-200 relative z-0 overflow-hidden"
              style={{
                background: "var(--accent)",
                border: "none",
                boxShadow: "0 0 10px rgba(166, 0, 255, 0.4)",
              }}
              whileHover={{
                scale: 1.05,
                filter: "brightness(1.1)",
                boxShadow: "0 0 15px rgba(190, 70, 255, 0.6)",
                transition: { duration: 0.3, ease: "easeInOut" },
              }}
              whileTap={{ scale: 0.95 }}
            >
              Refresh
            </motion.button>

            {/* Add User Button */}
            <motion.button
              onClick={() => setIsAdding(true)}
              className="px-4 py-2 rounded-custom text-white font-semibold shadow-lg transition duration-200 relative z-0 overflow-hidden"
              style={{
                background: "var(--accent)",
                border: "none",
                boxShadow: "var(--accent)",
              }}
              whileHover={{
                scale: 1.05,
                filter: "brightness(1.1)",
                boxShadow: "0 0 15px var(--accent)",
                transition: { duration: 0.3, ease: "easeInOut" },
              }}
              whileTap={{ scale: 0.95 }}
            >
              Add User
            </motion.button>
          </div>
        </div>

        {/* Users Table */}
        <UsersTable
          users={users}
          query={query}
          setQuery={setQuery}
          setEditingUser={setEditingUser}
          handleDeleteUser={handleDeleteUser}
        />

        {/* User Form Modal */}
        {(isAdding || editingUser) && (
          <UserForm
            isOpen={isAdding || !!editingUser}
            onClose={closeForm}
            initialData={editingUser}
            onSubmit={editingUser ? null : handleAddUser}
          />
        )}
      </main>
    </div>
  );
}
