import React, { useMemo, useState } from "react";
import { motion, AnimatePresence } from "framer-motion";
import { FiRefreshCw } from "react-icons/fi";
import Card from "./Card";
import Pagination from "../Admin_Components/Paganation";

export default function AdminTable({
  admins = [],
  query = "",
  setQuery,
  onRefresh,
  onUpdateStatus
}) {
  const [currentPage, setCurrentPage] = useState(1);
  const [passwordModal, setPasswordModal] = useState({ open: false, admin_id: null, newStatus: "" });
  const [passwordInput, setPasswordInput] = useState("");

  const rowsPerPage = 7;

  const filtered = useMemo(() => {
    if (!query.trim()) return admins;
    const q = query.toLowerCase();
    return admins.filter((a) =>
      [String(a.admin_id), a.email, a.status].some((field) => field?.toLowerCase().includes(q))
    );
  }, [admins, query]);

  const totalPages = Math.max(Math.ceil(filtered.length / rowsPerPage), 1);
  const paginated = filtered.slice(
    (currentPage - 1) * rowsPerPage,
    currentPage * rowsPerPage
  );

  const handlePrev = () => setCurrentPage((p) => Math.max(p - 1, 1));
  const handleNext = () => setCurrentPage((p) => Math.min(p + 1, totalPages));

  const openPasswordModal = (admin_id, newStatus) => {
    setPasswordModal({ open: true, admin_id, newStatus });
    setPasswordInput("");
  };

  const handleConfirmPassword = async () => {
    if (!passwordInput) return;

    // Validate password
    if (passwordInput !== "amr diab") {
      alert("❌ Error: Incorrect password");
      return;
    }

    try {
      await onUpdateStatus({
        admin_id: passwordModal.admin_id,
        status: passwordModal.newStatus
      });
      setPasswordModal({ open: false, admin_id: null, newStatus: "" });
    } catch (err) {
      alert(err.message || "Failed to update status");
    }
  };

  const handleCancel = () => {
    setPasswordModal({ open: false, admin_id: null, newStatus: "" });
    setPasswordInput("");
  };

  return (
    <Card>
      {/* Header */}
      <div className="flex items-center justify-between mb-4 px-4">
        <h2 className="text-xl font-bold text-accent">Admins</h2>
        <button
          onClick={onRefresh}
          className="p-2 rounded-custom cursor-pointer text-accent transition-smooth hover:scale-110 hover:drop-shadow-lg hover:bg-accent-hover"
          title="Refresh list"
        >
          <FiRefreshCw size={20} />
        </button>
      </div>

      {/* Search */}
      <input
        value={query}
        onChange={(e) => {
          setQuery(e.target.value);
          setCurrentPage(1);
        }}
        placeholder="Search by ID, email, or status..."
        className="w-full mb-4 px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition-smooth"
      />

      {/* Table Header */}
      <div className="grid grid-cols-9 gap-2 px-4 py-3 border-b border-white/10 text-xs font-semibold uppercase text-accent">
        <div className="col-span-2">ID</div>
        <div className="col-span-3">Email</div>
        <div className="col-span-2">Created At</div>
        <div className="col-span-2">Status</div>
      </div>

      {/* Table Rows */}
      {paginated.length > 0 ? (
        paginated.map((a) => (
          <div
            key={a.admin_id}
            className="grid grid-cols-9 gap-2 px-4 py-3 border-b border-white/10 items-center text-sm text-white/80 hover:bg-hover-bg transition-smooth"
          >
            <div className="col-span-2 truncate">{a.admin_id}</div>
            <div className="col-span-3 truncate">{a.email}</div>
            <div className="col-span-2 truncate">{a.created_at}</div>
            <div className="col-span-2">
              <select
                value={a.status}
                onChange={(e) => openPasswordModal(a.admin_id, e.target.value)}
                className="w-full px-3 py-1 bg-panel border border-white/20 rounded-custom text-main focus:ring-2 focus:ring-accent outline-none transition-smooth"
              >
                <option value="Active">Active</option>
                <option value="Disabled">Disabled</option>
              </select>
            </div>
          </div>
        ))
      ) : (
        <div className="text-center py-10 text-white/50 transition-opacity duration-500">
          No admins found.
        </div>
      )}

      {/* Pagination */}
      <Pagination
        currentPage={currentPage}
        totalPages={totalPages}
        onPrev={handlePrev}
        onNext={handleNext}
      />

      {/* Password Modal */}
      <AnimatePresence>
        {passwordModal.open && (
          <motion.div
            className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm p-4"
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
          >
            <motion.div
              className="w-full max-w-md"
              initial={{ scale: 0.95, opacity: 0 }}
              animate={{ scale: 1, opacity: 1 }}
              exit={{ scale: 0.95, opacity: 0 }}
            >
              <Card>
                <h3
                  className="text-2xl font-bold mb-4 text-center"
                  style={{
                    background: "linear-gradient(135deg, var(--accent), var(--accent-alt))",
                    WebkitBackgroundClip: "text",
                    WebkitTextFillColor: "transparent"
                  }}
                >
                  Confirm Password
                </h3>
                <p className="text-main text-center mb-4 text-sm">
                  Enter your password to confirm status change
                </p>
                <input
                  type="password"
                  value={passwordInput}
                  onChange={(e) => setPasswordInput(e.target.value)}
                  placeholder="Password"
                  className="w-full px-4 py-2 mb-4 rounded-custom border border-white/20 bg-bg text-main focus:ring-2 focus:ring-accent outline-none text-center"
                />
                <div className="flex justify-end gap-3">
                  <button
                    onClick={handleCancel}
                    className="px-4 py-2 rounded-custom bg-hover-bg text-main font-semibold hover:bg-accent-hover transition-smooth"
                  >
                    Cancel
                  </button>
                  <button
                    onClick={handleConfirmPassword}
                    className="px-4 py-2 rounded-custom bg-accent text-dark font-semibold hover:bg-accent-hover transition-smooth"
                  >
                    Confirm
                  </button>
                </div>
              </Card>
            </motion.div>
          </motion.div>
        )}
      </AnimatePresence>
    </Card>
  );
}
