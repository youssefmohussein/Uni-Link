import React, { useState, useMemo } from "react";
import { FaEdit, FaTrash } from "react-icons/fa";
import Card from "../../Components/Admin_Components/Card";
import Sidebar from "../../Components/Admin_Components/Sidebar";

// --- User Form Modal ---
function UserForm({ isOpen, onClose, onSubmit, initialData }) {
  const [formData, setFormData] = useState(initialData || { name: "", id: "", email: "", phone: "", department: "", year: "", role: "Student" });
  const isEditing = !!initialData?.id;

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData(prev => ({ ...prev, [name]: name === "year" ? parseInt(value) || "" : value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!formData.name || !formData.email || !formData.department || !formData.role || (!isEditing && !formData.id)) {
      alert("Please fill out all required fields.");
      return;
    }
    onSubmit(formData);
  };

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-70 z-50 flex justify-center items-center p-4 backdrop-blur-sm">
      <Card className="w-full max-w-xl p-6 shadow-2xl rounded-lg">
        <h3 className="text-2xl font-bold text-accent mb-6">{isEditing ? "Edit User" : "Add User"}</h3>
        <form onSubmit={handleSubmit} className="space-y-4">
          <input name="name" value={formData.name} onChange={handleChange} placeholder="Full Name" className="w-full px-3 py-2 border rounded" />
          <input name="id" value={formData.id} onChange={handleChange} placeholder="ID" disabled={isEditing} className="w-full px-3 py-2 border rounded" />
          <input name="email" value={formData.email} onChange={handleChange} placeholder="Email" className="w-full px-3 py-2 border rounded" />
          <input name="department" value={formData.department} onChange={handleChange} placeholder="Department" className="w-full px-3 py-2 border rounded" />
          <input name="year" type="number" value={formData.year} onChange={handleChange} placeholder="Year" className="w-full px-3 py-2 border rounded" />
          <select name="role" value={formData.role} onChange={handleChange} className="w-full px-3 py-2 border rounded">
            <option value="Student">Student</option>
            <option value="TA">TA</option>
            <option value="Professor">Professor</option>
          </select>
          <div className="flex justify-end gap-3 mt-4">
            <button type="button" onClick={onClose} className="px-4 py-2 border rounded">Cancel</button>
            <button type="submit" className="px-4 py-2 bg-blue-600 text-white rounded">{isEditing ? "Save Changes" : "Add User"}</button>
          </div>
        </form>
      </Card>
    </div>
  );
}

// --- Admin Users Page ---
export default function AdminUsersPage() {
  const [users, setUsers] = useState([
    { id: 1, name: "John Doe", email: "john@example.com", role: "Student", department: "CS", year: 2025 },
    { id: 2, name: "Jane Smith", email: "jane@example.com", role: "TA", department: "Math", year: 2024 },
  ]);

  const [editingUser, setEditingUser] = useState(null);
  const [isAdding, setIsAdding] = useState(false);
  const [query, setQuery] = useState("");

  const closeForm = () => {
    setEditingUser(null);
    setIsAdding(false);
  };

  const handleAddUser = (user) => {
    setUsers(prev => [...prev, { ...user, id: Date.now() }]);
    closeForm();
  };

  const handleEditUser = (updatedUser) => {
    setUsers(prev => prev.map(u => (u.id === updatedUser.id ? updatedUser : u)));
    closeForm();
  };

  const handleDeleteUser = (id) => {
    if (window.confirm("Delete this user?")) {
      setUsers(prev => prev.filter(u => u.id !== id));
    }
  };

  const filtered = useMemo(() => {
    if (!query.trim()) return users;
    const q = query.toLowerCase();
    return users.filter(u => Object.values(u).some(v => String(v).toLowerCase().includes(q)));
  }, [users, query]);

  return (
    <div className="flex min-h-screen bg-main font-main text-main">
      <Sidebar />

      <UserForm 
        isOpen={isAdding || !!editingUser} 
        onClose={closeForm} 
        initialData={editingUser} 
        onSubmit={editingUser ? handleEditUser : handleAddUser} 
      />

      <main className="flex-1 p-6 space-y-6 overflow-auto">
        <div className="flex justify-between items-center mb-4">
          <h1 className="text-3xl font-bold">Users Dashboard</h1>
          <button onClick={() => setIsAdding(true)} className="px-4 py-2 bg-blue-600 text-white rounded">Add User</button>
        </div>

        <Card>
          <input value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Search..." className="w-full mb-4 px-3 py-2 border rounded" />
          <table className="min-w-full table-auto border-collapse">
            <thead>
              <tr className="bg-panel-light">
                <th className="border px-4 py-2">Name</th>
                <th className="border px-4 py-2">Email</th>
                <th className="border px-4 py-2">Role</th>
                <th className="border px-4 py-2">Department</th>
                <th className="border px-4 py-2">Year</th>
                <th className="border px-4 py-2">Actions</th>
              </tr>
            </thead>
            <tbody>
              {filtered.map(u => (
                <tr key={u.id} className="hover:bg-panel-light">
                  <td className="border px-4 py-2">{u.name}</td>
                  <td className="border px-4 py-2">{u.email}</td>
                  <td className="border px-4 py-2">{u.role}</td>
                  <td className="border px-4 py-2">{u.department}</td>
                  <td className="border px-4 py-2">{u.year}</td>
                  <td className="border px-4 py-2 flex gap-2">
                    <button onClick={() => setEditingUser(u)} className="text-yellow-500"><FaEdit /></button>
                    <button onClick={() => handleDeleteUser(u.id)} className="text-red-500"><FaTrash /></button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </Card>
      </main>
    </div>
  );
}
