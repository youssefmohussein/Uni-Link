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
  const [faculties, setFaculties] = useState([]);
  const [majors, setMajors] = useState([]);

  // 🔹 Fetch users
  const getUsers = async () => {
    try {
      setLoading(true);
      setError(null);
      const data = await apiRequest("index.php/getUsers", "GET");
      if (data.status !== "success") throw new Error(data.message || "Failed to fetch users");
      setUsers(data.data ?? []);
    } catch (err) {
      console.error(err);
      setError("Failed to load users. Check backend.");
    } finally {
      setLoading(false);
    }
  };
  const handleRefresh = () => {
  getUsers();
};
  // 🔹 Fetch faculties and majors
  const getFacultiesAndMajors = async () => {
    try {
      const fData = await apiRequest("index.php/getAllFaculties", "GET");
      const mData = await apiRequest("index.php/getAllMajors", "GET");
      setFaculties(fData.data ?? []);
      setMajors(mData.data ?? []);
    } catch (err) {
      console.error(err);
    }
  };

  useEffect(() => {
    getUsers();
    getFacultiesAndMajors();
  }, []);

  // 🔹 Delete user
    const handleDeleteUser = async (user_id) => {
    if (!window.confirm("Are you sure you want to delete this user?")) return;

    try {
      const res = await apiRequest("index.php/deleteUser", "POST", { user_id });
      if (res.status !== "success") throw new Error(res.message || "Delete failed");

      // Update state
      setUsers((prev) => prev.filter((u) => u.user_id !== user_id));
    } catch (err) {
      alert("Delete failed: " + (err.message || ""));
      console.error(err);
    }
  };

  // 🔹 Add user
  const handleAddUser = async (formData) => {
    try {
      const res = await apiRequest("index.php/addUser", "POST", formData);
      if (res.status !== "success") throw new Error(res.message || "Failed to add user");

      // Add new user to state
      setUsers((prev) => [
        { ...formData, user_id: res.user_id }, // backend returns user_id
        ...prev
      ]);
      setIsAdding(false);
    } catch (err) {
      alert("Add user failed: " + (err.message || ""));
      console.error(err);
    }
  };

  if (loading) return <div className="text-center py-10">Loading...</div>;
  if (error) return <div className="text-center py-10 text-red-500">{error}</div>;

  return (
    <div className="flex min-h-screen font-main bg-bg text-main">
      <Sidebar />

      <main className="flex-1 p-6 space-y-6 overflow-auto">
        <div className="flex justify-between items-center mb-4">
          <h1 className="text-3xl font-bold">Users Dashboard</h1>
          <div className="flex gap-2">
            <motion.button
              onClick={() => setIsAdding(true)}
              className="px-4 py-2 rounded-custom text-white bg-accent shadow-lg"
            >
              Add User
            </motion.button>
          </div>
        </div>

        <UsersTable
          users={users}
          query={query}
          setQuery={setQuery}
          handleDeleteUser={handleDeleteUser}
          onRefresh={handleRefresh}
        />

        <UserForm
          isOpen={isAdding}
          onClose={() => setIsAdding(false)}
          onSubmit={handleAddUser}
          faculties={faculties}
          majors={majors}
        />
      </main>
    </div>
  );
}
