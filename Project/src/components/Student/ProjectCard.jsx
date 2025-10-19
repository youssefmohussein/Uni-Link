import React from "react";
import GlareHover from "../../Animations/GlareHover";
function ProjectCard({ image, title, description, skills }) {
  return (
    <GlareHover
      glareColor="#ffffff"
      glareOpacity={0.3}
      glareAngle={-30}
      glareSize={300}
      transitionDuration={800}
      className="rounded-2xl"
      style={{
        width: "340px",
        height: "370px",
      }}
    >
      <article
        className="
          rounded-2xl overflow-hidden
          border border-white/10 bg-white/5 backdrop-blur-sm
          shadow-lg hover:shadow-xl
          transition-transform duration-500
          hover:scale-[1.03] hover:-translate-y-1
          flex flex-col
          h-full
        "
      >
      
        <div className="h-48 w-full overflow-hidden border-b border-white/10">
          <img
            src={image}
            alt={title}
            className="w-full h-full object-cover"
          />
        </div>

      
        <div className="flex flex-col flex-1 justify-between p-5">
          <div>
            <h3 className="text-lg font-semibold text-white mb-2 line-clamp-1">
              {title}
            </h3>
            <p className="text-sm text-gray-300 leading-relaxed line-clamp-3">
              {description}
            </p>
          </div>

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
        </div>
      </article>
    </GlareHover>
  );
}

export default ProjectCard;
