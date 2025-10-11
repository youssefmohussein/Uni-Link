import React from "react";

function ProjectCard({ image, title, description, skills }) {
  return (
    <article className="border border-muted rounded-custom overflow-hidden shadow-sm hover:shadow-custom transition">
      <img
        src={image}
        alt={title}
        className="w-full h-48 object-cover border-b border-muted"
      />
      <div className="p-4">
        <h3 className="font-semibold text-main">{title}</h3>
        <p className="text-sm text-muted">{description}</p>
        <div className="flex flex-wrap gap-2 mt-2 text-xs">
          {skills.map((skill, index) => (
            <span key={index} className="bg-main px-2 py-1 rounded">
              {skill}
            </span>
          ))}
        </div>
      </div>
    </article>
  );
}

export default ProjectCard;
