import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Card from "./Card";

export default function FacultyFormModal({
  isOpen,
  onClose,
  onSubmit,
  initialData,
}) {
  const defaultData = {
    faculty_id: "",
    faculty_name: "",
  };

  const [formData, setFormData] = useState(defaultData);
  const isEditing = !!initialData?.faculty_id;

  useEffect(() => {
    setFormData(initialData || defaultData);
  }, [initialData]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!formData.faculty_name || !formData.faculty_name.trim()) {
      alert("Please fill out faculty name");
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
                {isEditing ? "Edit Faculty" : "Add Faculty"}
              </h3>

              <form onSubmit={handleSubmit} className="space-y-4">
                {isEditing && (
                  <input
                    name="faculty_id"
                    type="number"
                    placeholder="Faculty ID"
                    value={formData.faculty_id || ""}
                    onChange={handleChange}
                    disabled
                    className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 opacity-50 cursor-not-allowed"
                  />
                )}

                <input
                  name="faculty_name"
                  type="text"
                  placeholder="Faculty Name"
                  value={formData.faculty_name || formData.name || ""}
                  onChange={handleChange}
                  required
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
                />

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
                    {isEditing ? "Save Changes" : "Add Faculty"}
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
