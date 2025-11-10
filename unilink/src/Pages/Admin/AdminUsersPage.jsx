import React, { useState, useEffect } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import UsersTable from "../../Components/Admin_Components/UsersTable";
import UserForm from "../../Components/Admin_Components/UserForm";
import { motion } from "framer-motion";
import * as userHandler from "../../../api/userHandler";

export default function AdminUsersPage() {
  const [users, setUsers] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [query, setQuery] = useState("");
  const [isAdding, setIsAdding] = useState(false);
  const [editingUser, setEditingUser] = useState(null); // 🟢 For editing
  const [faculties, setFaculties] = useState([]);
  const [majors, setMajors] = useState([]);

  // Fetch users
  const getUsersFromService = async () => {
    try {
      setLoading(true);
      const data = await userHandler.getUsers();
      setUsers(data);
      setError(null);
    } catch (err) {
      setError("Failed to fetch users");
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  // Fetch faculties and majors if needed
  const getFacultiesAndMajors = async () => {
    try {
      const [facData, majData] = await Promise.all([
        userHandler.getFaculties(),
        userHandler.getMajors(),
      ]);
      setFaculties(facData);
      setMajors(majData);
    } catch (err) {
      console.error("Failed to load faculties/majors", err);
    }
  };

  useEffect(() => {
    getUsersFromService();
    getFacultiesAndMajors();
  }, []);

  // Add User
  const handleAddUser = async (formData) => {
    try {
      await userHandler.addUser(formData);
      await getUsersFromService();
      setIsAdding(false);
      alert("User added successfully");
    } catch (err) {
      console.error(err);
      alert("Failed to add user: " + (err.message || ""));
    }
  };

  // Update User
  const handleUpdateUser = async (formData) => {
    try {
      await userHandler.updateUser(formData);
      await getUsersFromService();
      setEditingUser(null);
      alert("User updated successfully");
    } catch (err) {
      console.error(err);
      alert("Failed to update user: " + (err.message || ""));
    }
  };

  // Delete User
  const handleDeleteUser = async (user_id) => {
    if (!window.confirm("Are you sure you want to delete this user?")) return;
    try {
      await userHandler.deleteUser(user_id);
      await getUsersFromService();
      alert("User deleted successfully");
    } catch (err) {
      console.error(err);
      alert("Failed to delete user: " + (err.message || ""));
    }
  };

  return (
    <div className="flex bg-dark min-h-screen text-white">
      <Sidebar />

      <motion.div
        initial={{ opacity: 0, y: 10 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.4 }}
        className="flex-1 p-8"
      >
        <div className="flex justify-between items-center mb-6">
          <h1 className="text-2xl font-bold">Manage Users</h1>
          <button
            onClick={() => setIsAdding(true)}
            className="px-4 py-2 bg-accent rounded-lg font-medium hover:bg-accent/80"
          >
            + Add User
          </button>
        </div>

        {loading ? (
          <p className="text-gray-400">Loading users...</p>
        ) : error ? (
          <p className="text-red-400">{error}</p>
        ) : (
          <UsersTable
            users={users}
            query={query}
            setQuery={setQuery}
            setEditingUser={setEditingUser}
            handleDeleteUser={handleDeleteUser}
            onRefresh={getUsersFromService}
          />
        )}

        {/* Add User Form */}
        <UserForm
          isOpen={isAdding}
          onClose={() => setIsAdding(false)}
          onSubmit={handleAddUser}
          faculties={faculties}
          majors={majors}
        />

        {/* Edit User Form */}
        <UserForm
          isOpen={!!editingUser}
          onClose={() => setEditingUser(null)}
          onSubmit={handleUpdateUser}
          initialData={editingUser}
          faculties={faculties}
          majors={majors}
        />
      </motion.div>
    </div>
  );
}
