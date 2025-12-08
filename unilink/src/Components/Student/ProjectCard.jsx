import React, { useState } from "react";
import GlareHover from "../../Animations/GlareHover/GlareHover";
import * as studentHandler from "../../../api/studentHandler";

function ProjectCard({ project_id, title, description, status, grade, skills, supervisor_name, created_at, file_path, onDelete, userId }) {
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

  const handleDownload = (e) => {
    e.stopPropagation();
    if (file_path) {
      const downloadUrl = `http://localhost/backend/${file_path}`;
      const link = document.createElement('a');
      link.href = downloadUrl;
      link.download = file_path.split('/').pop();
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
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
          min-h-[180px]
        "
        style={{
          backdropFilter: 'blur(16px) saturate(180%)',
          WebkitBackdropFilter: 'blur(16px) saturate(180%)'
        }}
      >
        {/* Status Badge - Absolutely Positioned Top-Right */}
        {status && (
          <div className="absolute top-4 right-4 z-10">
            <span className={`px-3 py-1.5 text-xs rounded-lg border font-semibold ${statusColors[status] || 'bg-gray-500/20 text-gray-300 border-gray-500/30'}`}>
              {status}
            </span>
          </div>
        )}

        {/* Horizontal Layout: Delete Button (Left) + Main Content (Right) */}
        <div className="flex flex-row items-stretch h-full">

          {/* 🔹 LEFT SIDE — Delete Action */}
          <div className="w-20 flex items-center justify-center border-r border-white/10">
            <button
              onClick={handleDelete}
              disabled={deleting}
              className="w-12 h-12 bg-red-500/20 text-red-400 border border-red-500/30 rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center"
              title="Delete project"
            >
              {deleting ? (
                <span className="text-xs">...</span>
              ) : (
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              )}
            </button>
          </div>

          {/* 🔹 MAIN CONTENT SECTION (Center Area) */}
          <div className="flex-1 p-6 pr-24">
            <div className="flex flex-col justify-center h-full space-y-4">

              {/* Project Name & Description */}
              <div>
                <h3 className="text-xl font-bold text-white mb-2">
                  {title}
                </h3>
                <p className="text-sm text-gray-300 leading-relaxed line-clamp-2">
                  {description}
                </p>
              </div>

              {/* Created, Supervisor, Grade (Horizontal Line) */}
              <div className="flex items-center gap-6 text-sm">
                {/* Created */}
                <div className="flex items-center gap-2">
                  <span className="text-gray-400">Created:</span>
                  <span className="text-white font-semibold">
                    {created_at ? new Date(created_at).toLocaleDateString('en-US', {
                      month: 'short',
                      day: 'numeric',
                      year: 'numeric'
                    }) : '—'}
                  </span>
                </div>

                {/* Supervisor */}
                <div className="flex items-center gap-2">
                  <span className="text-gray-400">Supervisor:</span>
                  <span className="text-white font-semibold">
                    {supervisor_name || '—'}
                  </span>
                </div>

                {/* Grade */}
                <div className="flex items-center gap-2">
                  <span className="text-gray-400">Grade:</span>
                  <span className="text-accent font-bold">
                    {grade !== null && grade !== undefined ? grade : '—'}
                  </span>
                </div>
              </div>

              {/* Attachment (Bottom Area) */}
              <div className="pt-2 border-t border-white/10">
                {file_path ? (
                  <button
                    onClick={handleDownload}
                    className="text-sm text-blue-400 hover:text-blue-300 transition-colors font-medium"
                  >
                    Download Attachment
                  </button>
                ) : (
                  <span className="text-sm text-gray-500">No attachments</span>
                )}
              </div>
            </div>
          </div>
        </div>
      </article>
    </GlareHover>
  );
}

export default ProjectCard;
