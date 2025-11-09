// src/Pages/Admin/components/UsersTable.jsx
import React, { useMemo } from "react";
import AnimatedUserRow from "./AnimatedUserRow";
import Card from "../../Components/Admin_Components/Card";

export default function UsersTable({ users, faculties, majors, query, setEditingUser, handleDeleteUser, setQuery }) {
  const filtered = useMemo(() => {
    if (!query.trim()) return users;
    const q = query.toLowerCase();
    return users.filter(u => u.username.toLowerCase().includes(q) || u.email.toLowerCase().includes(q) || u.role.toLowerCase().includes(q));
  }, [users, query]);

  return (
    <Card>
      <input
        value={query}
        onChange={e => setQuery(e.target.value)}
        placeholder="Search by name, email, or role..."
        className="w-full mb-4 px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
      />

      <div className="grid grid-cols-12 gap-2 px-4 py-3 border-b border-white/10 items-center text-xs font-semibold uppercase text-accent-alt mb-2">
        <div className="col-span-2">Username</div>
        <div className="col-span-2">Email</div>
        <div className="col-span-2">Role</div>
        <div className="col-span-2">Faculty</div>
        <div className="col-span-2">Major</div>
        <div className="col-span-2 text-right">Actions</div>
      </div>

      {filtered.map((u, index) => (
        <AnimatedUserRow
          key={u.user_id}
          u={u}
          faculties={faculties}
          majors={majors}
          setEditingUser={setEditingUser}
          handleDeleteUser={handleDeleteUser}
          index={index}
        />
      ))}

      {filtered.length === 0 && (
        <div className="text-center py-10 text-white/50 transition-opacity duration-500">
          No users found matching your search.
        </div>
      )}
    </Card>
  );
}
