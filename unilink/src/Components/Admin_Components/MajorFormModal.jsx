import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Card from "./Card";

export default function MajorFormModal({
  isOpen,
  onClose,
  onSubmit,
  initialData,
  faculties = [],
  selectedFaculty = null,
}) {
  const [formData, setFormData] = useState({
    major_id: "",
    name: "",
    faculty_id: selectedFaculty?.faculty_id || "",
  });
  const isEditing = !!initialData?.major_id;

  useEffect(() => {
    if (initialData) {
      setFormData({
        major_id: initialData.major_id || "",
        name: initialData.name || "",
        faculty_id: initialData.faculty_id || "",
      });
    } else {
      // For new major, use selectedFaculty if available
      setFormData({
        major_id: "",
        name: "",
        faculty_id: selectedFaculty?.faculty_id || "",
      });
    }
  }, [initialData, selectedFaculty]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!formData.name || !formData.name.trim()) {
      alert("Please fill out major name");
      return;
    }
    if (!formData.faculty_id) {
      alert("Please select a faculty");
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
                {isEditing ? "Edit Major" : "Add Major"}
              </h3>

              <form onSubmit={handleSubmit} className="space-y-4">
                {isEditing && (
                  <input
                    name="major_id"
                    type="number"
                    placeholder="Major ID"
                    value={formData.major_id || ""}
                    onChange={handleChange}
                    disabled
                    className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 opacity-50 cursor-not-allowed"
                  />
                )}

                <input
                  name="name"
                  type="text"
                  placeholder="Major Name"
                  value={formData.name || ""}
                  onChange={handleChange}
                  required
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
                />

                <select
                  name="faculty_id"
                  value={formData.faculty_id || ""}
                  onChange={handleChange}
                  disabled={!!selectedFaculty && !isEditing}
                  required
                  className={`w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition cursor-pointer ${selectedFaculty && !isEditing ? "opacity-50 cursor-not-allowed" : ""
                    }`}
                >
                  <option value="">Select Faculty</option>
                  {faculties.map((f) => (
                    <option key={f.faculty_id} value={f.faculty_id}>
                      {f.name}
                    </option>
                  ))}
                </select>

                <div className="flex justify-end gap-3 mt-6">
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
                    {isEditing ? "Save Changes" : "Add Major"}
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
