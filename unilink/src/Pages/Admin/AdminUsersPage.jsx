// src/Pages/Admin/AdminUsersPage.jsx - FINAL WORKING CODE

import React, { useState, useMemo } from "react";
import { FaEdit, FaTrash } from "react-icons/fa";
import Card from "../../Components/Admin_Components/Card";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import AnimatedList from "../../Animations/AnimatedList/AnimatedList";
import { motion, AnimatePresence } from "framer-motion";

// --- User Form Modal (UNCHANGED) ---
function UserForm({ isOpen, onClose, onSubmit, initialData, faculties, majors }) {
  const defaultData = {
    user_id: "", username: "", email: "", password: "", phone: "", 
    profile_image: null, bio: "", job_title: "", role: "Student", 
    faculty_id: "", major_id: ""
  };
  const [formData, setFormData] = useState(initialData || defaultData);
  const isEditing = !!initialData?.user_id;

  const handleChange = (e) => {
    const { name, value, files } = e.target;
    if (name === "profile_image" && files) {
      setFormData((prev) => ({ ...prev, profile_image: files[0] }));
    } else {
      setFormData((prev) => ({ ...prev, [name]: value }));
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const requiredFields = ["user_id", "username", "email", "password", "role"];
    for (let field of requiredFields) {
      if (!formData[field] && !isEditing) {
        alert(`Please fill out ${field}`);
        return;
      }
    }
    onSubmit(formData);
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <motion.div
          className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-70 backdrop-blur-sm p-4"
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
        >
          <motion.div
            initial={{ scale: 0.95, opacity: 0 }}
            animate={{ scale: 1, opacity: 1 }}
            exit={{ scale: 0.95, opacity: 0 }}
            transition={{ duration: 0.3 }}
            className="w-full max-w-3xl"
          >
            <Card>
              <h3
                className="text-2xl font-bold mb-6"
                style={{
                  background: "linear-gradient(135deg, var(--accent), var(--accent-alt))",
                  WebkitBackgroundClip: "text",
                  WebkitTextFillColor: "transparent"
                }}
              >
                {isEditing ? "Edit User" : "Add User"}
              </h3>
              <form onSubmit={handleSubmit} className="grid grid-cols-2 gap-4">
                {[
                  { name: "user_id", type: "number", placeholder: "User ID", disabled: isEditing },
                  { name: "username", type: "text", placeholder: "Username" },
                  { name: "email", type: "email", placeholder: "Email" },
                  { name: "password", type: "password", placeholder: "Password" },
                  { name: "phone", type: "text", placeholder: "Phone" },
                  { name: "job_title", type: "text", placeholder: "Job Title" },
                  { name: "bio", type: "text", placeholder: "Bio", full: true }
                ].map((input) => (
                  <input
                    key={input.name}
                    name={input.name}
                    type={input.type}
                    value={formData[input.name] || ""}
                    onChange={handleChange}
                    placeholder={input.placeholder}
                    disabled={input.disabled}
                    className={`w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition ${input.full ? "col-span-2" : ""}`}
                  />
                ))}

                <select
                  name="role"
                  value={formData.role}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
                >
                  {["Student", "Professor", "Admin"].map((role) => (
                    <option key={role} value={role}>{role}</option>
                  ))}
                </select>

                <select
                  name="faculty_id"
                  value={formData.faculty_id}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
                >
                  <option value="">Select Faculty</option>
                  {faculties.map((f) => (
                    <option key={f.id} value={f.id}>{f.name}</option>
                  ))}
                </select>

                <select
                  name="major_id"
                  value={formData.major_id}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
                >
                  <option value="">Select Major</option>
                  {majors.map((m) => (
                    <option key={m.id} value={m.id}>{m.name}</option>
                  ))}
                </select>

                <input
                  type="file"
                  name="profile_image"
                  accept="image/*"
                  onChange={handleChange}
                  className="col-span-2 w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main"
                />

                <div className="col-span-2 flex justify-end gap-3 mt-4">
                  <button
                    type="button"
                    onClick={onClose}
                    className="px-4 py-2 rounded-custom border border-white/20 hover:bg-white/10 transition"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="px-4 py-2 rounded-custom bg-accent hover:brightness-110 transition"
                  >
                    {isEditing ? "Save Changes" : "Add User"}
                  </button>
                </div>
              </form>
            </Card>
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}

// --- Component: Animated User Row (Animation Fix) ---
const AnimatedUserRow = ({ u, faculties, majors, setEditingUser, handleDeleteUser, index }) => {
    const facultyName = faculties.find(f => f.id === u.faculty_id)?.name;
    const majorName = majors.find(m => m.id === u.major_id)?.name;

    const itemVariants = {
        hidden: { 
          opacity: 0, 
          height: 0,
          paddingTop: 0, 
          paddingBottom: 0,
          marginTop: 0,
          marginBottom: 0
        },
        visible: { 
            opacity: 1, 
            height: 'auto',
            paddingTop: 12, 
            paddingBottom: 12, 
            marginTop: 0,
            marginBottom: 0,
            transition: { 
                delay: index * 0.05, 
                duration: 0.3,
                height: { duration: 0.3 }
            }
        },
        exit: { 
            opacity: 0, 
            height: 0,
            paddingTop: 0, 
            paddingBottom: 0, 
            marginTop: 0, 
            marginBottom: 0,
            transition: { 
                duration: 0.3 
            }
        },
    };

    return (
        <motion.div
            initial="hidden"
            animate="visible"
            exit="exit"
            variants={itemVariants}
            custom={index}
            className="grid grid-cols-12 gap-2 border-b border-white/10 items-center hover:bg-white/5 transition duration-150"
        >
            <div className="col-span-2 text-white font-medium truncate">{u.username}</div>
            <div className="col-span-2 text-white/80 truncate">{u.email}</div>
            <div className="col-span-2 text-white/80">{u.role}</div>
            <div className="col-span-2 text-white/80 truncate">{facultyName}</div>
            <div className="col-span-2 text-white/80 truncate">{majorName}</div>
            <div className="col-span-2 flex gap-2 justify-end">
                <button 
                    onClick={() => setEditingUser(u)} 
                    className="text-yellow-400 p-1 rounded-full hover:bg-yellow-400/20 transition"
                    title="Edit"
                >
                    <FaEdit />
                </button>
                <button 
                    onClick={() => handleDeleteUser(u.user_id)} 
                    className="text-red-500 p-1 rounded-full hover:bg-red-500/20 transition"
                    title="Delete"
                >
                    <FaTrash />
                </button>
            </div>
        </motion.div>
    );
};


// --- Admin Users Page (MAIN COMPONENT) ---
export default function AdminUsersPage() {
  const [users, setUsers] = useState([
    {
      user_id: 1, username: "John Doe", email: "john@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "CS Student", job_title: "Student", role: "Student", faculty_id: 1, major_id: 1
    },
    {
      user_id: 2, username: "Alice Smith", email: "alice@example.com", password: "12345", phone: "0987654321", 
      profile_image: null, bio: "Math Student", job_title: "Student", role: "Professor", faculty_id: 2, major_id: 2
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    },
    {
      user_id: 3, username: "Bob Johnson", email: "bob@example.com", password: "12345", phone: "1234567890", 
      profile_image: null, bio: "IT Admin", job_title: "Admin", role: "Admin", faculty_id: 1, major_id: 1
    }
  ]);

  const [editingUser, setEditingUser] = useState(null);
  const [isAdding, setIsAdding] = useState(false);
  const [query, setQuery] = useState("");

  const faculties = [{ id: 1, name: "Engineering" }, { id: 2, name: "Science" }];
  const majors = [{ id: 1, name: "Computer Science" }, { id: 2, name: "Math" }];

  const closeForm = () => {
    setEditingUser(null);
    setIsAdding(false);
  };

  const handleAddUser = (user) => {
    const newId = Math.max(0, ...users.map(u => u.user_id)) + 1;
    setUsers((prev) => [...prev, { ...user, user_id: newId }]); 
    closeForm();
  };

  const handleEditUser = (updatedUser) => {
    setUsers((prev) => prev.map((u) => (u.user_id === updatedUser.user_id ? updatedUser : u)));
    closeForm();
  };

  const handleDeleteUser = (id) => {
    if (window.confirm("Are you sure you want to delete this user?")) {
      setUsers((prev) => prev.filter((u) => u.user_id !== id));
    }
  };

  const filtered = useMemo(() => {
    if (!query.trim()) return users;
    const q = query.toLowerCase();
    return users.filter((u) => (
        u.username.toLowerCase().includes(q) ||
        u.email.toLowerCase().includes(q) ||
        u.role.toLowerCase().includes(q)
    ));
  }, [users, query]);

  return (
    <div className="flex min-h-screen font-main bg-bg text-main">
      <Sidebar />

      <UserForm
        isOpen={isAdding || !!editingUser}
        onClose={closeForm}
        initialData={editingUser}
        onSubmit={editingUser ? handleEditUser : handleAddUser}
        faculties={faculties}
        majors={majors}
      />

      <main className="flex-1 p-6 space-y-6 overflow-auto">
        <div className="flex justify-between items-center mb-4">
          <h1 className="text-3xl font-bold">Users Dashboard</h1>
          
          {/* THE FIXED "ADD USER" BUTTON WITH GRADIENT */}
          <motion.button
            onClick={() => setIsAdding(true)}
            className="
                px-4 py-2 
                rounded-custom 
                text-white 
                font-semibold 
                shadow-lg 
                transition 
                duration-200 
                relative 
                z-0
                overflow-hidden
            "
            style={{
                // *** FIX: Now correctly referencing your CSS variable for the gradient ***
                background: "var(--accent-gradient)", 
                border: "none", 
                // Optional: Added a subtle box-shadow for a 'glowing' effect, matching your screenshot
                boxShadow: "0 0 10px rgba(166, 0, 255, 0.4)",
            }}
            whileHover={{ 
                scale: 1.05,
                filter: "brightness(1.1)", // Brighten on hover
                boxShadow: "0 0 15px rgba(190, 70, 255, 0.6)", // Enhance glow on hover
                transition: { duration: 0.3, ease: "easeInOut" }
            }} 
            whileTap={{ scale: 0.95 }}
          >
            Add User
          </motion.button>
        </div>

        <Card>
          <input
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Search by name, email, or role..."
            className="w-full mb-4 px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
          />

          {/* Table Header */}
          <div className="grid grid-cols-12 gap-2 px-4 py-3 border-b border-white/10 items-center text-xs font-semibold uppercase text-accent-alt mb-2">
            <div className="col-span-2">Username</div>
            <div className="col-span-2">Email</div>
            <div className="col-span-2">Role</div>
            <div className="col-span-2">Faculty</div>
            <div className="col-span-2">Major</div>
            <div className="col-span-2 text-right">Actions</div>
          </div>
          
          <AnimatedList
  items={filtered}
  renderItem={(u, index) => (
    <AnimatedUserRow
      key={u.user_id}
      u={u}
      faculties={faculties}
      majors={majors}
      setEditingUser={setEditingUser}
      handleDeleteUser={handleDeleteUser}
      index={index}
    />
  )}
  once={false} // 👈 change to false to re-animate on scroll
/>



          {filtered.length === 0 && (
            <div className="text-center py-10 text-white/50 transition-opacity duration-500">
                No users found matching your search.
            </div>
          )}
        </Card>
      </main>
    </div>
  );
}