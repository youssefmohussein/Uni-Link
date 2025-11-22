import React, { useState, useEffect } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import AdminTable from "../../Components/Admin_Components/AdminTable";
import { motion } from "framer-motion";
import * as adminHandler from "../../../api/adminHandler";

export default function AdminAdminPage() {
  const [admins, setAdmins] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [query, setQuery] = useState("");

  const fetchAdmins = async () => {
    try {
      setLoading(true);
      const data = await adminHandler.getAdmins();
      setAdmins(data);
      setError(null);
    } catch (err) {
      setError("Failed to fetch admins");
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchAdmins();
  }, []);

  const handleUpdateStatus = async (adminData) => {
    try {
      await adminHandler.updateAdmin(adminData);
      await fetchAdmins();
    } catch (err) {
      alert(err.message);
    }
  };

  const handleEditAdmin = (admin) => {
    // TODO: Implement edit admin functionality
    alert(`Edit admin: ${admin.email}`);
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
          <h1 className="text-2xl font-bold">Manage Admins</h1>
        </div>

        {loading ? (
          <p className="text-gray-400">Loading admins...</p>
        ) : error ? (
          <p className="text-red-400">{error}</p>
        ) : (
          <AdminTable
            admins={admins}
            query={query}
            setQuery={setQuery}
            onRefresh={fetchAdmins}
            onEditAdmin={handleEditAdmin}
            onUpdateStatus={handleUpdateStatus}
          />
        )}
      </motion.div>
    </div>
  );
}
