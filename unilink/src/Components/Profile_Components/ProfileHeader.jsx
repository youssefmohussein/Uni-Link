export default function ProfileHeader() {
  return (
    <div className="flex items-center gap-4 mb-8 font-main transition-smooth">
      <img
        src="Public/Images/Logo_png.png"
        alt="Profile"
        className="
          w-40 h-40 rounded-full 
          border-4 
          object-cover 
          border-[var(--accent)] 
          shadow-lg
          transition-smooth
        "
      />

      <div>
        <h1 className="text-3xl font-semibold text-main font-secondary">
          OMAR EHAB
        </h1>

        <p className="text-accent-alt text-xl">
          Senior Full-Stack Developer
        </p>

        <p className="text-muted max-w-xl leading-relaxed">
          Passionate about creating innovative solutions and building scalable
          applications. Experienced in React, Node.js, TypeScript, and cloud
          technologies.
        </p>

        <div className="flex gap-4 mt-2 text-sm text-accent-alt" >
          <span>📍 San Francisco, CA</span>
          <span>🔗 Omar.dev</span>
          <span>📅 Joined January 2023</span>
        </div>
      </div>
    </div>
  );
}
