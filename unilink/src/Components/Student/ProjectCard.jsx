import React from "react";
import GlareHover from "../../Animations/GlareHover/GlareHover";

function ProjectCard({ project_id, title, description, status, grade, skills, supervisor_name, created_at }) {
  // Status color mapping
  const statusColors = {
    'Pending': 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
    'Approved': 'bg-green-500/20 text-green-300 border-green-500/30',
    'Rejected': 'bg-red-500/20 text-red-300 border-red-500/30'
  };

  return (
    <GlareHover
      glareColor="#ffffff"
      glareOpacity={0.3}
      glareAngle={-30}
      glareSize={300}
      transitionDuration={800}
      className="rounded-2xl w-full"
    >
      <article
        className="
          rounded-2xl overflow-hidden
          border border-white/10 bg-white/5 backdrop-blur-sm
          shadow-lg hover:shadow-xl
          transition-transform duration-500
          hover:scale-[1.02] hover:-translate-y-1
          flex flex-col
          h-full
        "
        style={{ backdropFilter: 'blur(20px) saturate(180%)', WebkitBackdropFilter: 'blur(20px) saturate(180%)' }}
      >

        <div className="flex flex-col flex-1 justify-between p-5">
          <div>
            <div className="flex items-start justify-between mb-2">
              <h3 className="text-lg font-semibold text-white line-clamp-1 flex-1">
                {title}
              </h3>
              {status && (
                <span className={`px-2 py-0.5 text-xs rounded-full border ${statusColors[status] || 'bg-gray-500/20 text-gray-300 border-gray-500/30'}`}>
                  {status}
                </span>
              )}
            </div>

            <p className="text-sm text-gray-300 leading-relaxed line-clamp-3 mb-3">
              {description}
            </p>

            {/* Project metadata */}
            <div className="space-y-1 mb-3">
              {supervisor_name && (
                <p className="text-xs text-gray-400">
                  👨‍🏫 Supervisor: <span className="text-accent">{supervisor_name}</span>
                </p>
              )}
              {grade !== null && grade !== undefined && (
                <p className="text-xs text-gray-400">
                  📊 Grade: <span className="text-accent font-semibold">{grade}</span>
                </p>
              )}
              {created_at && (
                <p className="text-xs text-gray-400">
                  📅 {new Date(created_at).toLocaleDateString()}
                </p>
              )}
            </div>
          </div>

          {skills && skills.length > 0 && (
            <div className="flex flex-wrap gap-2 mt-4">
              {skills.map((skill, index) => (
                <span
                  key={index}
                  className="bg-white/10 border border-white/20 text-xs text-gray-100 px-2 py-1 rounded-lg"
                >
                  {skill}
                </span>
              ))}
            </div>
          )}
        </div>
      </article>
    </GlareHover>
  );
}

export default ProjectCard;
