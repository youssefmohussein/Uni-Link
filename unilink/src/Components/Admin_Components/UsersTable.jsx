import React, { useMemo } from "react";
import { FiRefreshCw } from "react-icons/fi";
import AnimatedUserRow from "./AnimatedUserRow";
import Card from "./Card"; // adjust path if necessary

/**
 * UsersTable - display list of users with search & actions
 * Props:
 *  - users: array of user objects
 *  - query: string
 *  - setQuery: function
 *  - setEditingUser: function
 *  - handleDeleteUser: function
 *  - onRefresh: function (called when refresh button clicked)
 */
export default function UsersTable({ users, query, setQuery, setEditingUser, handleDeleteUser, onRefresh }) {
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
        onChange={(e) => setQuery(e.target.value)}
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
      {filtered.map((u, index) => (
        <AnimatedUserRow
          key={u.user_id}
          u={u}
          setEditingUser={setEditingUser}
          handleDeleteUser={handleDeleteUser}
          index={index}
        />
      ))}

      {/* Empty state */}
      {filtered.length === 0 && (
        <div className="text-center py-10 text-white/50 transition-opacity duration-500">
          No users found.
        </div>
      )}
    </Card>
  );
}
