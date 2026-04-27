import React from "react";

function ProfileHeader({ name, title, bio, image, faculty, year, gpa, points }) {
  return (
    <header
      className="bg-[rgba(0,0,0,0.3)] rounded-custom p-6 md:p-8 mb-8 border border-white/10 flex flex-col md:flex-row items-center gap-6"
    >
      <div className="w-28 h-28 md:w-32 md:h-32 lg:w-36 lg:h-36 rounded-full overflow-hidden border-2 border-accent/60 shadow-md shrink-0">
        <img
          src={image}
          alt={`${name} profile`}
          className="w-full h-full object-cover"
        />
      </div>

      <div className="flex-1 text-center md:text-left space-y-2">
        <h1 className="text-3xl font-semibold text-white leading-tight">
          {name}
        </h1>
        <p className="text-accent font-medium">{title}</p>
        {faculty && (
          <p className="text-sm text-gray-300">📚 {faculty}</p>
        )}
        <p className="text-muted mt-2 text-sm leading-relaxed max-w-xl">
          {bio}
        </p>

        {/* Student-specific info */}
        {(year || gpa !== undefined || points !== undefined) && (
          <div className="flex flex-wrap gap-4 mt-3 justify-center md:justify-start">
            {year && (
              <div className="bg-white/5 border border-white/10 rounded-lg px-3 py-1">
                <span className="text-xs text-gray-400">Year</span>
                <p className="text-white font-semibold">{year}</p>
              </div>
            )}
            {gpa !== undefined && gpa !== null && (
              <div className="bg-white/5 border border-white/10 rounded-lg px-3 py-1">
                <span className="text-xs text-gray-400">GPA</span>
                <p className="text-white font-semibold">{parseFloat(gpa).toFixed(2)}</p>
              </div>
            )}
            {points !== undefined && points !== null && (
              <div className="bg-white/5 border border-white/10 rounded-lg px-3 py-1">
                <span className="text-xs text-gray-400">Points</span>
                <p className="text-accent font-semibold">{points}</p>
              </div>
            )}
          </div>
        )}
      </div>
    </header>
  );
}

export default ProfileHeader;
