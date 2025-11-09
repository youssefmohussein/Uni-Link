// src/Pages/Admin/components/UserForm.jsx
import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Card from "../../Components/Admin_Components/Card";

export default function UserForm({ isOpen, onClose, onSubmit, initialData, faculties, majors }) {
  const defaultData = {
    user_id: "", username: "", email: "", password: "", phone: "", 
    profile_image: null, bio: "", job_title: "", role: "Student", 
    faculty_id: "", major_id: ""
  };
  const [formData, setFormData] = useState(initialData || defaultData);
  const isEditing = !!initialData?.user_id;

  useEffect(() => {
    setFormData(initialData || defaultData);
  }, [initialData]);

  const handleChange = (e) => {
    const { name, value, files } = e.target;
    if (name === "profile_image" && files) {
      setFormData(prev => ({ ...prev, profile_image: files[0] }));
    } else {
      setFormData(prev => ({ ...prev, [name]: value }));
    }
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    const requiredFields = ["username", "email", "password", "role"];
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
                  { name: "username", type: "text", placeholder: "Username" },
                  { name: "email", type: "email", placeholder: "Email" },
                  { name: "password", type: "password", placeholder: "Password" },
                  { name: "phone", type: "text", placeholder: "Phone" },
                  { name: "job_title", type: "text", placeholder: "Job Title" },
                  { name: "bio", type: "text", placeholder: "Bio", full: true }
                ].map(input => (
                  <input
                    key={input.name}
                    name={input.name}
                    type={input.type}
                    value={formData[input.name] || ""}
                    onChange={handleChange}
                    placeholder={input.placeholder}
                    className={`w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition ${input.full ? "col-span-2" : ""}`}
                  />
                ))}

                <select
                  name="role"
                  value={formData.role}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
                >
                  {["Student", "Professor", "Admin"].map(role => <option key={role} value={role}>{role}</option>)}
                </select>

                <select
                  name="faculty_id"
                  value={formData.faculty_id}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
                >
                  <option value="">Select Faculty</option>
                  {(faculties || []).map(f => <option key={f.id} value={f.id}>{f.name}</option>)}
                </select>

                <select
                  name="major_id"
                  value={formData.major_id}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
                >
                  <option value="">Select Major</option>
                  {(majors || []).map(m => <option key={m.id} value={m.id}>{m.name}</option>)}
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
