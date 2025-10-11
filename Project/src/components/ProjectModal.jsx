import React, { useState } from "react";

function ProjectModal({ isOpen, onClose }) {
  const [form, setForm] = useState({
    title: "",
    description: "",
    skills: "",
    team: "",
    code: "",
    docs: "",
    image: null,
  });

  if (!isOpen) return null;

  const handleChange = (e) => {
    const { name, value, files } = e.target;
    setForm({
      ...form,
      [name]: files ? files[0] : value,
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    console.log("Project Submitted:", form);
    onClose();
  };

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div className="bg-panel p-8 rounded-2xl shadow-2xl w-full max-w-2xl animate-fadeIn">
        {/* Header */}
        <h2 className="text-2xl font-semibold text-main mb-6 border-b border-muted pb-3">
          🚀 Upload New Project
        </h2>

        <form onSubmit={handleSubmit} className="space-y-5">
          {/* Row 1: Title + Skills */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input
              type="text"
              name="title"
              placeholder="Project Title"
              className="w-full p-3 rounded-xl bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none placeholder:text-muted"
              onChange={handleChange}
            />
            <input
              type="text"
              name="skills"
              placeholder="Skills (comma separated)"
              className="w-full p-3 rounded-xl bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none placeholder:text-muted"
              onChange={handleChange}
            />
          </div>

          {/* Description (Full Width) */}
          <textarea
            name="description"
            placeholder="Project Description"
            rows="3"
            className="w-full p-3 rounded-xl bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none placeholder:text-muted"
            onChange={handleChange}
          />

          {/* Team Members (Full Width) */}
          <input
            type="text"
            name="team"
            placeholder="Team Members"
            className="w-full p-3 rounded-xl bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none placeholder:text-muted"
            onChange={handleChange}
          />

          {/* Row 2: GitHub Repo + Docs */}
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input
              type="url"
              name="code"
              placeholder="GitHub Repo URL"
              className="w-full p-3 rounded-xl bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none placeholder:text-muted"
              onChange={handleChange}
            />
            <input
              type="url"
              name="docs"
              placeholder="Documentation Link"
              className="w-full p-3 rounded-xl bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none placeholder:text-muted"
              onChange={handleChange}
            />
          </div>

          {/* File Upload */}
          <label className="block w-full cursor-pointer">
            <span className="text-sm text-muted">Upload Project Image</span>
            <input
              type="file"
              name="image"
              accept="image/*"
              className="mt-2 block w-full text-sm text-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-accent file:text-white hover:file:bg-accent/80"
              onChange={handleChange}
            />
          </label>

          {/* Buttons */}
          <div className="flex justify-end gap-3 pt-4">
            <button
              type="button"
              onClick={onClose}
              className="px-5 py-2 rounded-xl bg-muted/20 text-muted hover:bg-muted/30 transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              className="px-6 py-2 rounded-xl bg-accent text-white font-semibold shadow-md hover:shadow-lg hover:bg-accent/90 transition"
            >
              Upload 🚀
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default ProjectModal;
