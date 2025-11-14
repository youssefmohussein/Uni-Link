import React, { useState, useEffect, useMemo } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Card from "../../Components/Admin_Components/Card";

export default function ProfessorForm({
  isOpen,
  onClose,
  onSubmit,
  initialData,
  faculties,
  majors
}) {
  const defaultData = {
    user_id: "",
    username: "",
    email: "",
    faculty_id: "",
    major_id: "",
    role: "Professor"
  };

  const [formData, setFormData] = useState(initialData || defaultData);
  const isEditing = !!initialData?.user_id;

  useEffect(() => {
    setFormData(initialData || defaultData);
  }, [initialData]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
      ...(name === "faculty_id" ? { major_id: "" } : {})
    }));
  };

  const filteredMajors = useMemo(() => {
    if (!formData.faculty_id) return majors || [];
    return majors.filter(
      (m) => String(m.faculty_id) === String(formData.faculty_id)
    );
  }, [formData.faculty_id, majors]);

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!formData.username || !formData.email || !formData.faculty_id) {
      alert("Please fill required fields");
      return;
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
            className="w-full max-w-2xl"
            initial={{ scale: 0.95, opacity: 0 }}
            animate={{ scale: 1, opacity: 1 }}
            exit={{ scale: 0.95, opacity: 0 }}
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
                {isEditing ? "Edit Professor" : "Add Professor"}
              </h3>

              <form onSubmit={handleSubmit} className="grid grid-cols-2 gap-4">

                <input
                  name="user_id"
                  type="number"
                  placeholder="User ID"
                  value={formData.user_id}
                  onChange={handleChange}
                  className="px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                <input
                  name="username"
                  type="text"
                  placeholder="Username"
                  value={formData.username}
                  onChange={handleChange}
                  className="px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                <input
                  name="email"
                  type="email"
                  placeholder="Email"
                  value={formData.email}
                  onChange={handleChange}
                  className="px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                <select
                  name="faculty_id"
                  value={formData.faculty_id}
                  onChange={handleChange}
                  className="px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                >
                  <option value="">Select Faculty</option>
                  {faculties.map((f) => (
                    <option key={f.faculty_id} value={f.faculty_id}>
                      {f.faculty_name}
                    </option>
                  ))}
                </select>

                <select
                  name="major_id"
                  disabled={!formData.faculty_id}
                  value={formData.major_id}
                  onChange={handleChange}
                  className={`px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 ${
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
                    className="px-4 py-2 rounded-custom border border-white/20 hover:bg-white/10"
                  >
                    Cancel
                  </button>

                  <button
                    type="submit"
                    className="px-4 py-2 rounded-custom bg-accent hover:brightness-110"
                  >
                    {isEditing ? "Save Changes" : "Add Professor"}
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
