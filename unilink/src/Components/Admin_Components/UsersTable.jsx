import React, { useMemo, useState } from "react";
import { FiRefreshCw } from "react-icons/fi";
import AnimatedUserRow from "./AnimatedUserRow";
import Card from "./Card";
import Pagination from "../Admin_Components/Paganation";  // Import the pagination component

export default function UsersTable({
  users = [],
  query,
  setQuery,
  setEditingUser,
  handleDeleteUser,
  onRefresh
}) {
  const [currentPage, setCurrentPage] = useState(1);
  const rowsPerPage = 7; // Limit for users table

  // Filter users based on search query
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

  // Pagination logic
  const totalPages = Math.ceil(filtered.length / rowsPerPage);
  const paginated = filtered.slice(
    (currentPage - 1) * rowsPerPage,
    currentPage * rowsPerPage
  );

  const handlePrev = () => setCurrentPage((prev) => Math.max(prev - 1, 1));
  const handleNext = () => setCurrentPage((prev) => Math.min(prev + 1, totalPages));

  return (
    <Card>
      {/* Header with title and refresh */}
      <div className="flex items-center justify-between mb-4 px-4">
        <h2 className="text-xl font-bold text-accent">Users</h2>
        <button
          onClick={onRefresh}
          className="p-2 rounded-full hover:bg-white/10 transition"
          title="Refresh users"
        >
          <FiRefreshCw className="text-accent" size={20} />
        </button>
      </div>

      {/* Search input */}
      <input
        value={query}
        onChange={(e) => {
          setQuery(e.target.value);
          setCurrentPage(1); // Reset page when searching
        }}
        placeholder="Search by username, email, or role..."
        className="w-full mb-4 px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
      />

      {/* Table header */}
      <div className="grid grid-cols-12 gap-2 px-4 py-3 border-b border-white/10 items-center text-xs font-semibold uppercase text-accent mb-2">
        <div className="col-span-3">Username</div>
        <div className="col-span-4">Email</div>
        <div className="col-span-3">Role</div>
        <div className="col-span-2 text-right">Actions</div>
      </div>

      {/* Table rows */}
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
