import React, { useState } from "react";
import GlareHover from "../../Animations/GlareHover/GlareHover";
import * as studentHandler from "../../../api/studentHandler";

function ProjectCard({ project_id, title, description, status, grade, skills, supervisor_name, created_at, file_path, onDelete, onEdit, userId }) {
  const [deleting, setDeleting] = useState(false);

  // Status color mapping
  const statusColors = {
    'Pending': 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
    'Approved': 'bg-green-500/20 text-green-300 border-green-500/30',
    'Rejected': 'bg-red-500/20 text-red-300 border-red-500/30'
  };

  const handleDelete = async (e) => {
    e.stopPropagation();

    if (!userId) {
      alert("User ID is missing. Please refresh the page.");
      return;
    }

    if (!confirm(`Are you sure you want to delete "${title}"?`)) {
      return;
    }

    try {
      setDeleting(true);

      const response = await fetch('http://localhost/backend/index.php/deleteProject', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'include',
        body: JSON.stringify({
          project_id: project_id,
          owner_id: userId
        })
      });

      const data = await response.json();
      if (data.status !== 'success') {
        throw new Error(data.message || 'Failed to delete project');
      }

      if (onDelete) onDelete(project_id);
    } catch (err) {
      console.error("Failed to delete project:", err);
      alert(err.message || "Failed to delete project");
    } finally {
      setDeleting(false);
    }
  };

  const handleEdit = (e) => {
    e.stopPropagation();
    console.log("Edit clicked for project:", project_id, title);
    console.log("onEdit function:", onEdit);
    if (onEdit) {
      onEdit({ project_id, title, description, status, grade, file_path, supervisor_name, created_at });
    } else {
      console.error("onEdit prop is not defined!");
    }
  };

  return (
    <GlareHover
      glareColor="#ffffff"
      glareOpacity={0.1}
      glareAngle={-30}
      glareSize={300}
      transitionDuration={800}
      className="w-full"
    >
      <article
        className="
          relative
          overflow-hidden
          border border-white/10 
          bg-gradient-to-br from-gray-800/40 to-gray-900/40
          backdrop-blur-md
          shadow-lg
          transition-all duration-300
          rounded-2xl
        "
        style={{
          backdropFilter: 'blur(16px) saturate(180%)',
          WebkitBackdropFilter: 'blur(16px) saturate(180%)'
        }}
      >
        {/* Status Badge - Absolutely Positioned Top-Right */}
        {status && (
          <div className="absolute top-4 right-10 z-10">
            <span className={`px-3 py-1.5 text-xs rounded-lg border font-semibold ${statusColors[status] || 'bg-gray-500/20 text-gray-300 border-gray-500/30'}`}>
              {status}
            </span>
          </div>
        )}

        {/* Horizontal Layout: Main Content (Left) + Action Buttons (Right) */}
        <div className="flex flex-row items-center h-full w-full">

          {/* 🔹 MAIN CONTENT SECTION */}
          <div className="flex-1 px-20 py-10">
            <div className="flex flex-col justify-center space-y-5 max-w-5xl">

              {/* Project Name & Description */}
              <div className="space-y-3">
                <h3 className="text-2xl font-bold text-white">
                  {title}
                </h3>
                <p className="text-base text-gray-300 leading-relaxed line-clamp-3 max-w-4xl">
                  {description}
                </p>
              </div>

              {/* Created, Supervisor, Grade (Horizontal Line) */}
              <div className="flex items-center gap-12 text-sm flex-wrap">
                {/* Created */}
                <div className="flex items-center gap-3">
                  <span className="text-gray-400 min-w-[70px]">Created:</span>
                  <span className="text-white font-semibold">
                    {created_at ? new Date(created_at).toLocaleDateString('en-US', {
                      month: 'short',
                      day: 'numeric',
                      year: 'numeric'
                    }) : '—'}
                  </span>
                </div>

                {/* Supervisor */}
                <div className="flex items-center gap-3">
                  <span className="text-gray-400 min-w-[80px]">Supervisor:</span>
                  <span className="text-white font-semibold">
                    {supervisor_name || '—'}
                  </span>
                </div>

                {/* Grade */}
                <div className="flex items-center gap-3">
                  <span className="text-gray-400 min-w-[50px]">Grade:</span>
                  <span className="text-accent font-bold text-base">
                    {grade !== null && grade !== undefined ? grade : '—'}
                  </span>
                </div>
              </div>
            </div>
          </div>

          {/* 🔹 RIGHT SIDE — Action Icons (No Background) */}
          <div className="flex items-center gap-5 pr-10 relative z-20">
            {/* Edit Icon */}
            <button
              type="button"
              onClick={handleEdit}
              className="text-gray-400 hover:text-purple-400 transition-colors p-2 cursor-pointer"
              title="Edit project"
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
              </svg>
            </button>

            {/* Delete Icon */}
            <button
              type="button"
              onClick={handleDelete}
              disabled={deleting}
              className="text-gray-400 hover:text-red-400 transition-colors disabled:opacity-50 disabled:cursor-not-allowed p-2 cursor-pointer"
              title="Delete project"
            >
              {deleting ? (
                <span className="text-xs">...</span>
              ) : (
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              )}
            </button>
          </div>
        </div>
      </article>
    </GlareHover>
  );
}

export default ProjectCard;
