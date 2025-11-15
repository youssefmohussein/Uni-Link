import React, { useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Card from "./Card";

export default function MajorForm({
  isOpen,
  onClose,
  onSubmit,
  initialData = null,
  faculty_id,
}) {
  const [majorName, setMajorName] = useState("");

  useEffect(() => {
    if (initialData) {
      setMajorName(initialData.major_name);
    } else {
      setMajorName("");
    }
  }, [initialData]);

  const handleSubmit = () => {
    if (!majorName.trim()) {
      alert("Major name cannot be empty.");
      return;
    }

    const payload = {
      major_name: majorName,
      faculty_id,
    };

    if (initialData) payload.major_id = initialData.major_id;

    onSubmit(payload);
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <motion.div
          initial={{ opacity: 0 }}
          animate={{ opacity: 1 }}
          exit={{ opacity: 0 }}
          className="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        >
          <motion.div
            initial={{ scale: 0.9, opacity: 0 }}
            animate={{ scale: 1, opacity: 1 }}
            exit={{ scale: 0.9, opacity: 0 }}
            className="w-full max-w-md"
          >
            <Card>
              <h2 className="text-xl font-bold mb-4 text-accent">
                {initialData ? "Edit Major" : "Add Major"}
              </h2>

              <div className="space-y-3">
                <div>
                  <label className="block mb-1 text-sm text-gray-300">
                    Major Name
                  </label>
                  <input
                    type="text"
                    value={majorName}
                    onChange={(e) => setMajorName(e.target.value)}
                    className="w-full px-3 py-2 rounded bg-white/10 focus:outline-none"
                    placeholder="Enter major name"
                  />
                </div>

                {/* Faculty ID (hidden) */}
                <input type="hidden" value={faculty_id} readOnly />
              </div>

              <div className="flex justify-end gap-3 mt-6">
                <button
                  className="px-4 py-2 bg-gray-600 rounded-lg hover:bg-gray-500"
                  onClick={onClose}
                >
                  Cancel
                </button>

                <button
                  className="px-4 py-2 bg-accent rounded-lg hover:bg-accent/80"
                  onClick={handleSubmit}
                >
                  {initialData ? "Save Changes" : "Add Major"}
                </button>
              </div>
            </Card>
          </motion.div>
        </motion.div>
      )}
    </AnimatePresence>
  );
}
