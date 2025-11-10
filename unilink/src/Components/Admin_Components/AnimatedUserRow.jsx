import React from "react";

/**
 * AnimatedUserRow - simplified user row for UsersTable
 * Props:
 *  - u: user object
 *  - setEditingUser(user)
 *  - handleDeleteUser(id)
 */
export default function AnimatedUserRow({ u, setEditingUser, handleDeleteUser, index }) {
  return (
    <div
      className={`grid grid-cols-12 gap-2 px-4 py-3 items-center border-b border-white/5 hover:bg-white/5 transition duration-150 ${
        index % 2 === 0 ? "bg-white/2" : ""
      }`}
    >
      <div className="col-span-3 truncate">{u.username || "—"}</div>
      <div className="col-span-4 truncate">{u.email || "—"}</div>
      <div className="col-span-3 truncate capitalize">{u.role || "—"}</div>

      <div className="col-span-2 text-right space-x-2">
        <button
          onClick={() => setEditingUser(u)}
          className="px-2 py-1 rounded text-sm bg-white/10 hover:bg-white/20 transition"
        >
          Edit
        </button>
        <button
          onClick={() => handleDeleteUser(u.user_id)}
          className="px-2 py-1 rounded text-sm bg-red-600 hover:bg-red-700 transition"
        >
          Delete
        </button>
      </div>
    </div>
  );
}
