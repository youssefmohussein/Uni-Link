import React, { useState, useEffect, useMemo } from "react";
import { motion, AnimatePresence } from "framer-motion";
import Card from "../../Components/Admin_Components/Card";

export default function UserForm({
  isOpen,
  onClose,
  onSubmit,
  initialData,
  faculties,
  majors,
}) {
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
    major_name: "",
    year: "",
    gpa: "0.0",
  };

  const [formData, setFormData] = useState(defaultData);
  const isEditing = !!initialData?.user_id;

  // Sync with initial data
  useEffect(() => {
    setFormData({ ...defaultData, ...initialData });
  }, [initialData]);

  // GPA auto-lock when student and year = 1
  useEffect(() => {
    if (formData.role === "Student" && formData.year === "1") {
      setFormData((prev) => ({ ...prev, gpa: "0.0" }));
    }
  }, [formData.year, formData.role]);

  const handleChange = (e) => {
    const { name, value, files } = e.target;

    // Handle image upload
    if (name === "profile_image" && files?.[0]) {
      const reader = new FileReader();
      reader.onloadend = () =>
        setFormData((prev) => ({ ...prev, profile_image: reader.result }));
      reader.readAsDataURL(files[0]);
      return;
    }

    // Lock GPA if Student + Year = 1
    if (name === "gpa" && formData.role === "Student" && formData.year === "1") {
      return;
    }

    setFormData((prev) => ({
      ...prev,
      [name]: value,
      ...(name === "faculty_id" ? { major_id: "" } : {}),
    }));
  };

  // Filter majors by faculty
  const filteredMajors = useMemo(() => {
    if (!formData.faculty_id) return [];
    return (majors || []).filter(
      (m) => String(m.faculty_id) === String(formData.faculty_id)
    );
  }, [majors, formData.faculty_id]);

  const handleSubmit = (e) => {
    e.preventDefault();

    const required = ["user_id", "username", "email", "password", "role"];
    if (!isEditing) {
      for (let field of required) {
        if (!formData[field]) {
          alert(`Please fill out ${field}`);
          return;
        }
      }
    }

    onSubmit(formData);
  };

  // Helper flags
  const isStudent = formData.role === "Student";
  const isProfessor = formData.role === "Professor";
  const isAdmin = formData.role === "Admin";

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
                  background:
                    "linear-gradient(135deg, var(--accent), var(--accent-alt))",
                  WebkitBackgroundClip: "text",
                  WebkitTextFillColor: "transparent",
                }}
              >
                {isEditing ? "Edit User" : "Add User"}
              </h3>

              <form onSubmit={handleSubmit} className="grid grid-cols-2 gap-4">
                {/* USER ID */}
                <input
                  name="user_id"
                  type="number"
                  value={formData.user_id || ""}
                  onChange={handleChange}
                  placeholder="User ID"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                {/* Username */}
                <input
                  name="username"
                  type="text"
                  value={formData.username || ""}
                  onChange={handleChange}
                  placeholder="Username"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                {/* Email */}
                <input
                  name="email"
                  type="email"
                  value={formData.email || ""}
                  onChange={handleChange}
                  placeholder="Email"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                {/* Password */}
                {!isEditing && (
                  <input
                    name="password"
                    type="password"
                    value={formData.password || ""}
                    onChange={handleChange}
                    placeholder="Password"
                    className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                  />
                )}

                {/* Phone */}
                <input
                  name="phone"
                  type="text"
                  value={formData.phone || ""}
                  onChange={handleChange}
                  placeholder="Phone"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                {/* Job Title */}
                <input
                  name="job_title"
                  type="text"
                  value={formData.job_title || ""}
                  onChange={handleChange}
                  placeholder="Job Title"
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                {/* Bio */}
                <input
                  name="bio"
                  type="text"
                  value={formData.bio || ""}
                  onChange={handleChange}
                  placeholder="Bio"
                  className="col-span-2 w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                {/* ROLE */}
                <select
                  name="role"
                  value={formData.role}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                >
                  <option value="Student">Student</option>
                  <option value="Professor">Professor</option>
                  <option value="Admin">Admin</option>
                </select>

                {/* Faculty */}
                <select
                  name="faculty_id"
                  value={formData.faculty_id || ""}
                  onChange={handleChange}
                  className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                >
                  <option value="">Select Faculty</option>
                  {(faculties || []).map((f) => (
                    <option key={f.faculty_id} value={f.faculty_id}>
                      {f.faculty_name}
                    </option>
                  ))}
                </select>

                {/* Major */}
                <select
                  name="major_id"
                  value={formData.major_id || ""}
                  onChange={handleChange}
                  disabled={!formData.faculty_id}
                  className={`w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 ${
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

                {/* STUDENT-ONLY: YEAR */}
                {isStudent && (
                  <>
                    <select
                      name="year"
                      value={formData.year || ""}
                      onChange={handleChange}
                      className="w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                    >
                      <option value="">Select Year</option>
                      <option value="1">1st Year</option>
                      <option value="2">2nd Year</option>
                      <option value="3">3rd Year</option>
                      <option value="4">4th Year</option>
                    </select>

                    {/* GPA */}
                    <input
                      name="gpa"
                      type="number"
                      step="0.01"
                      value={formData.gpa}
                      disabled={formData.year === "1"}
                      onChange={handleChange}
                      placeholder="GPA"
                      className={`w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50 ${
                        formData.year === "1"
                          ? "opacity-50 cursor-not-allowed"
                          : ""
                      }`}
                    />
                  </>
                )}

                {/* PROFESSOR / ADMIN hidden GPA + YEAR */}
                {(isProfessor || isAdmin) && (
                  <>
                    <input type="hidden" name="gpa" value={formData.gpa} />
                    <input type="hidden" name="year" value={formData.year} />
                  </>
                )}

                {/* IMAGE UPLOAD */}
                <input
                  type="file"
                  name="profile_image"
                  accept="image/*"
                  onChange={handleChange}
                  className="col-span-2 w-full px-3 py-2 rounded-custom border border-white/20 bg-panel text-white/50"
                />

                {/* BUTTONS */}
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
