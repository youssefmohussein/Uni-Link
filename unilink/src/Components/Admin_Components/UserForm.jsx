// src/Pages/Admin/components/UserForm.jsx
import React, { useState, useEffect, useMemo } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Card from "../../Components/Admin_Components/Card";

export default function UserForm({ isOpen, onClose, onSubmit, initialData, faculties, majors }) {
  const defaultData = {
    user_id: "",
    username: "",
    email: "",
    password: "",
    phone: "",
    profile_image: "",
    bio: "",
    job_title: "",
    role: "Student",
    faculty_id: "",
    major_id: "",
    faculty_name: "",
    major_name: ""
  };

  const [formData, setFormData] = useState(initialData || defaultData);
  const isEditing = !!initialData?.user_id;

  useEffect(() => {
    setFormData(initialData || defaultData);
  }, [initialData]);

  const handleChange = (e) => {
    const { name, value, files } = e.target;

    if (name === "profile_image" && files) {
      const reader = new FileReader();
      reader.onloadend = () =>
        setFormData((prev) => ({ ...prev, profile_image: reader.result }));
      reader.readAsDataURL(files[0]);
    } else {
      setFormData((prev) => ({
        ...prev,
        [name]: value,
        // Reset major if faculty changes
        ...(name === "faculty_id" ? { major_id: "" } : {})
      }));
    }
  };

  // 🔥 Filter majors based on selected faculty
  const filteredMajors = useMemo(() => {
    if (!formData.faculty_id) return majors || [];
    return (majors || []).filter(
      (m) => String(m.faculty_id) === String(formData.faculty_id)
    );
  }, [majors, formData.faculty_id]);

  const handleSubmit = (e) => {
    e.preventDefault();

    const required = ["user_id", "username", "email", "password", "role"];
    for (let field of required) {
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
                  WebkitTextFillColor: "transparent",
                }}
              >
                {isEditing ? "Edit User" : "Add User"}
              </h3>

              <form onSubmit={handleSubmit} className="grid grid-cols-2 gap-4">
                <input
                  name="user_id"
                  type="number"
                  value={formData.user_id || ""}
                  onChange={handleChange}
                  placeholder="User ID"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition"
                />

                <input
                  name="username"
                  type="text"
                  value={formData.username || ""}
                  onChange={handleChange}
                  placeholder="Username"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition"
                />

                <input
                  name="email"
                  type="email"
                  value={formData.email || ""}
                  onChange={handleChange}
                  placeholder="Email"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition"
                />

                {!isEditing && (
                  <input
                    name="password"
                    type="password"
                    value={formData.password || ""}
                    onChange={handleChange}
                    placeholder="Password"
                    className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition"
                  />
                )}

                <input
                  name="phone"
                  type="text"
                  value={formData.phone || ""}
                  onChange={handleChange}
                  placeholder="Phone"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition"
                />

                <input
                  name="job_title"
                  type="text"
                  value={formData.job_title || ""}
                  onChange={handleChange}
                  placeholder="Job Title"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition"
                />

                <input
                  name="bio"
                  type="text"
                  value={formData.bio || ""}
                  onChange={handleChange}
                  placeholder="Bio"
                  className="col-span-2 w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition"
                />

                <select
                  name="role"
                  value={formData.role}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition"
                >
                  {["Student", "Professor", "Admin"].map((r) => (
                    <option key={r} value={r}>
                      {r}
                    </option>
                  ))}
                </select>

                <select
                  name="faculty_id"
                  value={formData.faculty_id || ""}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition"
                >
                  <option value="">Select Faculty</option>
                  {(faculties || []).map((f) => (
                    <option key={f.faculty_id} value={f.faculty_id}>
                      {f.faculty_name}
                    </option>
                  ))}
                </select>

                <select
                  name="major_id"
                  value={formData.major_id || ""}
                  onChange={handleChange}
                  disabled={!formData.faculty_id}
                  className={`w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition ${
                    !formData.faculty_id ? "opacity-50 cursor-not-allowed" : ""
                  }`}
                >
                  <option value="">Select Major</option>
                  {filteredMajors.map((m) => (
                    <option key={m.major_id} value={m.major_id}>
                      {m.major_name}
                    </option>
                  ))}
                </select>

                <input
                  type="file"
                  name="profile_image"
                  accept="image/*"
                  onChange={handleChange}
                  className="col-span-2 w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
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
