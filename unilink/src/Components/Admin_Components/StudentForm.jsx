import React, { useState, useEffect, useMemo } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Card from "../../Components/Admin_Components/Card";

export default function StudentForm({
  isOpen,
  onClose,
  onSubmit,
  initialData,
  faculties,
  majors,
}) {
  const defaultData = {
    username: "",
    email: "",
    year: "",
    gpa: "",
    faculty_id: "",
    major_id: "",
  };

  const isEditing = !!initialData?.student_id;
  const [formData, setFormData] = useState(defaultData);

  useEffect(() => {
    if (isEditing) {
      setFormData({
        username: initialData.username || "",
        email: initialData.email || "",
        year: initialData.year || "",
        gpa: initialData.gpa || "",
        faculty_id: initialData.faculty_id || "",
        major_id: initialData.major_id || "",
        student_id: initialData.student_id,
      });
    } else {
      setFormData({ ...defaultData });
    }
  }, [initialData]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    if (name === "gpa") {
      let g = parseFloat(value);
      if (isNaN(g)) g = "";
      if (g < 0) g = 0;
      if (g > 4) g = 4;
      setFormData((prev) => ({ ...prev, gpa: g }));
      return;
    }
    setFormData((prev) => ({
      ...prev,
      [name]: value,
      ...(name === "faculty_id" ? { major_id: "" } : {}),
    }));
  };

  const filteredMajors = useMemo(() => {
    if (!formData.faculty_id) return [];
    return (majors || []).filter(
      (m) => String(m.faculty_id) === String(formData.faculty_id)
    );
  }, [majors, formData.faculty_id]);

  const handleSubmit = (e) => {
    e.preventDefault();
    const required = ["username", "email", "year"];
    for (let f of required) {
      if (!formData[f]) return alert(`Please fill out ${f}`);
    }
    if (!isEditing) {
      onSubmit({
        username: formData.username,
        email: formData.email,
        faculty_id: formData.faculty_id,
        major_id: formData.major_id,
        role: "Student",
        year: formData.year,
        gpa: formData.gpa || 0,
      });
    } else {
      onSubmit({
        user_id: formData.student_id,
        student_id: formData.student_id,
        username: formData.username,
        email: formData.email,
        faculty_id: formData.faculty_id,
        major_id: formData.major_id,
        year: formData.year,
        gpa: formData.gpa,
      });
    }
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
            <Card className="p-6">
              {/* Header */}
              <h3
                className="text-2xl font-bold mb-6 text-center"
                style={{
                  background:
                    "linear-gradient(135deg, var(--accent), var(--accent-alt))",
                  WebkitBackgroundClip: "text",
                  WebkitTextFillColor: "transparent",
                }}
              >
                {isEditing ? "Edit Student" : "Add Student"}
              </h3>

              {/* Form */}
              <form onSubmit={handleSubmit} className="grid grid-cols-2 gap-4">

                <input
                  name="username"
                  value={formData.username}
                  onChange={handleChange}
                  placeholder="Username"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:outline-none focus:ring-2 focus:ring-accent transition"
                />
                <input
                  name="email"
                  type="email"
                  value={formData.email}
                  onChange={handleChange}
                  placeholder="Email"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:outline-none focus:ring-2 focus:ring-accent transition"
                />
                <select
                  name="faculty_id"
                  value={formData.faculty_id}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:outline-none focus:ring-2 focus:ring-accent transition cursor-pointer"
                >
                  <option value="">Select Faculty</option>
                  {faculties?.map((f) => (
                    <option key={f.faculty_id} value={f.faculty_id}>
                      {f.faculty_name}
                    </option>
                  ))}
                </select>
                <select
                  name="major_id"
                  value={formData.major_id}
                  onChange={handleChange}
                  disabled={!formData.faculty_id}
                  className={`w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:outline-none focus:ring-2 focus:ring-accent transition cursor-pointer ${
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
                  name="year"
                  type="number"
                  value={formData.year}
                  onChange={handleChange}
                  placeholder="Year"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:outline-none focus:ring-2 focus:ring-accent transition"
                />
                <input
                  name="gpa"
                  type="number"
                  step="0.01"
                  min="0"
                  max="4"
                  value={formData.gpa}
                  onChange={handleChange}
                  placeholder="GPA"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 focus:outline-none focus:ring-2 focus:ring-accent transition"
                />

                {/* Buttons */}
                <div className="col-span-2 flex justify-end gap-3 mt-4">
                  <button
                    type="button"
                    onClick={onClose}
                    className="px-4 py-2 rounded-lg border-2 border-white text-white bg-transparent hover:scale-105 hover:drop-shadow-[0_0_6px_white,0_0_12px_white] transition"
                  >
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="px-4 py-2 rounded-lg border-2 border-accent text-accent bg-transparent hover:scale-105 hover:drop-shadow-[0_0_6px_currentColor,0_0_12px_currentColor] transition"
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
