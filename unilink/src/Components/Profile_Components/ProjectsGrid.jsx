export default function ProjectsGrid() {
  return (
    <div className="mb-10 font-main transition-smooth">
      <h2 className="text-xl font-semibold mb-4 text-main font-secondary">
        Projects
      </h2>
      
      <button className="mb-4 px-4 py-2 bg-accent text-white rounded-custom hover:bg-accent-dark transition-smooth">
        Add New Project
      </button>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <div
          className="
            bg-panel p-5 rounded-custom cursor-pointer 
            transition-smooth 
            hover:bg-hover-bg shadow-lg
          "
        >

          <h3 className="font-semibold text-lg text-main font-secondary">
            E-Commerce Dashboard
          </h3>

          <p className="text-muted text-sm">
            A comprehensive admin dashboard for managing e-commerce operations.
          </p>
        </div>

        <div
          className="
            bg-panel p-5 rounded-custom cursor-pointer 
            transition-smooth 
            hover:bg-hover-bg shadow-lg
          "
        >

          <h3 className="font-semibold text-lg text-main font-secondary">
            Task Management App
          </h3>

          <p className="text-muted text-sm">
            A collaborative task management application with real-time updates.
          </p>
        </div>

      </div>
    </div>
  );
}
