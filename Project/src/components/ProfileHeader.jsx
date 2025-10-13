import React from "react";

function ProfileHeader({ name, title, bio, image }) {
  return (
    <header className="bg-panel rounded-custom shadow-custom p-6 mb-8">
      <div className="flex items-center gap-6">
        
        {/* Fixed Image on Left */}
        <div className="flex-shrink-0 w-28 h-28 md:w-32 md:h-32 lg:w-40 lg:h-40 rounded-full overflow-hidden border-4 border-accent">
          <img
            src={image}
            alt={`${name} Profile`}
            className="w-full h-full object-cover"
          />
        </div>

        {/* Text Block beside image */}
        <div className="flex-1 min-w-0">
          <h1 className="text-2xl font-bold text-main">{name}</h1>
          <p className="text-accent font-medium">{title}</p>
          <p className="text-muted mt-2 leading-relaxed max-w-2xl">{bio}</p>
        </div>

      </div>
    </header>
  );
}

export default ProfileHeader;
