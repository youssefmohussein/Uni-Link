import React, { useState, useEffect, useMemo } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Card from "../../Components/Admin_Components/Card";

export default function StudentForm({ isOpen, onClose, onSubmit, initialData, faculties, majors }) {
  const defaultData = {
    student_id: "",
    username: "",
    email: "",
    year: "",
    gpa: "",
    faculty_id: "",
    major_id: "",
  };

  const [formData, setFormData] = useState(initialData || defaultData);
  const isEditing = !!initialData?.student_id;

  useEffect(() => {
    setFormData(initialData || defaultData);
  }, [initialData]);

  const handleChange = (e) => {
    const { name, value } = e.target;

    setFormData((prev) => ({
      ...prev,
      [name]: value,
      ...(name === "faculty_id" ? { major_id: "" } : {}),
    }));
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

    const required = ["student_id", "username", "email", "year", "gpa"];
    for (let field of required) {
      if (!formData[field]) {
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
            className="w-full max-w-2xl"
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
                {isEditing ? "Edit Student" : "Add Student"}
              </h3>

              <form onSubmit={handleSubmit} className="grid grid-cols-2 gap-4">
                <input
                  name="student_id"
                  type="number"
                  value={formData.student_id || ""}
                  onChange={handleChange}
                  placeholder="Student ID"
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

                <input
                  name="year"
                  type="number"
                  value={formData.year || ""}
                  onChange={handleChange}
                  placeholder="Year"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition"
                />

                <input
                  name="gpa"
                  type="number"
                  step="0.01"
                  value={formData.gpa || ""}
                  onChange={handleChange}
                  placeholder="GPA"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:ring-2 focus:ring-accent outline-none transition"
                />

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
                    {isEditing ? "Save Changes" : "Add Student"}
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
