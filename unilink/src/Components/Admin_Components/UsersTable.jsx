import React, { useMemo, useState } from "react";
import { FiRefreshCw, FiPlus } from "react-icons/fi";
import AnimatedUserRow from "./AnimatedUserRow";
import Card from "./Card";
import Pagination from "../Admin_Components/Paganation";

export default function UsersTable({
  users = [],
  query,
  setQuery,
  setEditingUser,
  handleDeleteUser,
  onRefresh,
  onAddUser
}) {
  const [currentPage, setCurrentPage] = useState(1);
  const rowsPerPage = 7;

  const filtered = useMemo(() => {
    if (!query.trim()) return users;
    const q = query.toLowerCase();
    return users.filter(
      (u) =>
        u.username?.toLowerCase().includes(q) ||
        u.email?.toLowerCase().includes(q) ||
        u.role?.toLowerCase().includes(q)
    );
  }, [users, query]);

  const totalPages = Math.ceil(filtered.length / rowsPerPage);
  const paginated = filtered.slice(
    (currentPage - 1) * rowsPerPage,
    currentPage * rowsPerPage
  );

  const handlePrev = () => setCurrentPage((prev) => Math.max(prev - 1, 1));
  const handleNext = () => setCurrentPage((prev) => Math.min(prev + 1, totalPages));

  return (
    <Card>
      {/* Header */}
      <div className="flex items-center justify-between mb-4 px-4">
        <h2 className="text-xl font-bold text-accent">Users</h2>
        <div className="flex gap-2">
          <button
            onClick={onRefresh}
            className="
                p-2 rounded-full cursor-pointer
                text-accent
                transition-all duration-200
                hover:scale-110
                hover:drop-shadow-[0_0_6px_currentColor]
                hover:bg-white/10
            "
            title="Refresh Users"
          >
            <FiRefreshCw size={20} />
          </button>
          <button
            onClick={onAddUser}
            className="
                p-2 rounded-full cursor-pointer
                text-accent
                transition-all duration-200
                hover:scale-110
                hover:drop-shadow-[0_0_6px_currentColor]
                hover:bg-white/10
            "
            title="Add User"
          >
            <FiPlus size={20} />
          </button>
        </div>
      </div>

      {/* Search */}
      <input
        value={query}
        onChange={(e) => {
          setQuery(e.target.value);
          setCurrentPage(1);
        }}
        placeholder="Search by username, email, or role..."
        className="w-full mb-4 px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
      />

      {/* Table Header */}
      <div className="grid grid-cols-12 gap-2 px-4 py-3 border-b border-white/10 items-center text-xs font-semibold uppercase text-accent mb-2">
        <div className="col-span-3">Username</div>
        <div className="col-span-4">Email</div>
        <div className="col-span-3">Role</div>
        <div className="col-span-2 text-right">Actions</div>
      </div>

      {/* Table Rows */}
      {paginated.map((u, index) => (
        <AnimatedUserRow
          key={u.user_id}
          u={u}
          setEditingUser={setEditingUser}
          handleDeleteUser={handleDeleteUser}
          index={index}
        />
      ))}

      {/* Empty state */}
      {paginated.length === 0 && (
        <div className="text-center py-10 text-white/50 transition-opacity duration-500">
          No users found.
        </div>
      )}

      {/* Pagination */}
      <Pagination
        currentPage={currentPage}
        totalPages={totalPages}
        onPrev={handlePrev}
        onNext={handleNext}
      />
    </Card>
  );
}
