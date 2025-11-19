import React, { useMemo, useState, useEffect } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Card from "../../Components/Admin_Components/Card";

const DEFAULT_RANKS = ["Teaching Assistant", "Assistant Professor", "Professor"];

export default function ProfessorForm({
  isOpen,
  onClose,
  onSubmit,
  initialData,
  faculties = [],
  majors = [],
  rankOptions = []
}) {
  const defaultData = {
    user_id: "",
    professor_id: "",
    username: "",
    email: "",
    faculty_id: "",
    major_id: "",
    academic_rank: "",
    office_location: "",
    phone: "",
    job_title: "",
    profile_image: "",
    bio: "",
    role: "professor"
  };

  const [formData, setFormData] = useState(defaultData);
  const isEditing = Boolean(initialData);

  const normalizeId = (value) =>
    value === null || value === undefined || value === "" ? "" : String(value);

  useEffect(() => {
    if (initialData) {
      setFormData({
        ...defaultData,
        ...initialData,
        user_id: initialData.user_id ?? initialData.professor_id ?? "",
        professor_id: initialData.professor_id ?? initialData.user_id ?? "",
        faculty_id: normalizeId(initialData.faculty_id),
        major_id: normalizeId(initialData.major_id),
        academic_rank: initialData.academic_rank ?? ""
      });
    } else {
      setFormData(defaultData);
    }
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
    return majors.filter((m) => String(m.faculty_id) === String(formData.faculty_id));
  }, [formData.faculty_id, majors]);

  const rankChoices = useMemo(() => {
    const list = [
      ...DEFAULT_RANKS,
      ...rankOptions,
      formData.academic_rank || ""
    ]
      .map((rank) => (rank || "").trim())
      .filter(Boolean);
    return Array.from(new Set(list));
  }, [rankOptions, formData.academic_rank]);

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!formData.username || !formData.email) {
      alert("Username and email are required");
      return;
    }

    const payload = {
      ...formData,
      user_id: formData.user_id || formData.professor_id,
      faculty_id: formData.faculty_id ? Number(formData.faculty_id) : null,
      major_id: formData.major_id ? Number(formData.major_id) : null
    };

    onSubmit(payload);
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
              <h3 className="text-2xl font-bold mb-6" style={{
                background: "linear-gradient(135deg, var(--accent), var(--accent-alt))",
                WebkitBackgroundClip: "text",
                WebkitTextFillColor: "transparent"
              }}>
                {isEditing ? "Edit Professor" : "Add Professor"}
              </h3>

              <form onSubmit={handleSubmit} className="grid grid-cols-2 gap-4">
                
                {/* User ID - auto-filled, not editable */}
                <input
                  name="user_id"
                  type="text"
                  placeholder="User ID"
                  value={formData.user_id || ""}
                  disabled
                  className="px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                <input
                  name="username"
                  type="text"
                  placeholder="Username"
                  value={formData.username}
                  readOnly
                  className="px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/70 cursor-not-allowed"
                />

                <input
                  name="email"
                  type="email"
                  placeholder="Email"
                  value={formData.email}
                  readOnly
                  className="px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/70 cursor-not-allowed"
                />

                <select
                  name="academic_rank"
                  value={formData.academic_rank}
                  onChange={handleChange}
                  className="px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                >
                  <option value="">Select Academic Rank</option>
                  {rankChoices.map((rank) => (
                    <option key={rank} value={rank}>
                      {rank}
                    </option>
                  ))}
                </select>

                <input
                  name="office_location"
                  type="text"
                  placeholder="Office Location"
                  value={formData.office_location}
                  onChange={handleChange}
                  className="px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                <select
                  name="faculty_id"
                  value={formData.faculty_id || ""}
                  onChange={handleChange}
                  className="px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                >
                  <option value="">Select Faculty</option>
                  {faculties.map((f) => (
                    <option key={f.faculty_id} value={String(f.faculty_id)}>
                      {f.faculty_name}
                    </option>
                  ))}
                </select>

                <select
                  name="major_id"
                  disabled={!formData.faculty_id}
                  value={formData.major_id || ""}
                  onChange={handleChange}
                  className={`px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 ${
                    !formData.faculty_id ? "opacity-50 cursor-not-allowed" : ""
                  }`}
                >
                  <option value="">Select Major</option>
                  {filteredMajors.map((m) => (
                    <option key={m.major_id} value={String(m.major_id)}>
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
