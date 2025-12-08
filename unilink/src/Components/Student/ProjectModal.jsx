import React, { useState } from "react";
import * as studentHandler from "../../../api/studentHandler";

function ProjectModal({ isOpen, onClose, addProject, userId, onSuccess }) {
  const [form, setForm] = useState({
    title: "",
    description: "",
    skills: "",
    team: "",
    code: "",
    docs: "",
    file: null,
  });
  const [uploading, setUploading] = useState(false);

  if (!isOpen) return null;

  const handleChange = (e) => {
    const { name, value, files } = e.target;
    setForm({
      ...form,
      [name]: files ? files[0] : value,
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (!form.title.trim() || !form.description.trim()) {
      alert("Please fill in title and description");
      return;
    }

    try {
      setUploading(true);

      // Prepare project data for upload
      const projectData = {
        user_id: userId,
        title: form.title.trim(),
        description: form.description.trim(),
        status: 'Pending' // Default status
      };

      // Add optional file if provided
      if (form.file) {
        projectData.project_file = form.file;
      }

      // Upload project to backend
      const uploadedProject = await studentHandler.uploadProject(projectData);

      alert("Project uploaded successfully!");

      // Reset form
      setForm({
        title: "",
        description: "",
        skills: "",
        team: "",
        code: "",
        docs: "",
        file: null,
      });

      // Call onSuccess to refresh the project list
      if (onSuccess) {
        await onSuccess();
      }

      onClose();
    } catch (err) {
      console.error("Failed to upload project:", err);
      alert(err.message || "Failed to upload project. Please try again.");
    } finally {
      setUploading(false);
    }
  };

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div className="bg-panel p-8 rounded-2xl shadow-2xl w-full max-w-2xl animate-fadeIn">
        {/* Header */}
        <h2 className="text-2xl font-semibold text-main mb-6 border-b border-muted pb-3">
          🚀 Upload New Project
        </h2>

        <form onSubmit={handleSubmit} className="space-y-5">
          {/* Title + Skills */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input
              type="text"
              name="title"
              placeholder="Project Title"
              value={form.title}
              onChange={handleChange}
              className="w-full p-3 rounded-xl bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none placeholder:text-muted"
            />
            <input
              type="text"
              name="skills"
              placeholder="Skills (comma separated)"
              value={form.skills}
              onChange={handleChange}
              className="w-full p-3 rounded-xl bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none placeholder:text-muted"
            />
          </div>

          <textarea
            name="description"
            placeholder="Project Description"
            rows="3"
            value={form.description}
            onChange={handleChange}
            className="w-full p-3 rounded-xl bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none placeholder:text-muted"
          />

          <label className="block w-full cursor-pointer">
            <span className="text-sm text-muted">Upload Project File (PDF, ZIP, etc.)</span>
            <input
              type="file"
              name="file"
              accept=".pdf,.zip,.rar,.doc,.docx"
              onChange={handleChange}
              className="mt-2 block w-full text-sm text-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-accent file:text-white hover:file:bg-accent/80"
            />
          </label>


          <div className="flex justify-end gap-3 pt-4">
            <button
              type="button"
              onClick={onClose}
              disabled={uploading}
              className="px-5 py-2 rounded-xl bg-muted/20 text-muted hover:bg-muted/30 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              Cancel
            </button>
            <button
              type="submit"
              disabled={uploading}
              className="px-6 py-2 rounded-xl bg-accent text-white font-semibold shadow-md hover:shadow-lg hover:bg-accent/90 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
              {uploading ? "Uploading..." : "Upload 🚀"}
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default ProjectModal;
