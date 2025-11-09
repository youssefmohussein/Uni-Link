import React, { useState } from "react";
function ProjectReviewSection() {
  const [searchTerm, setSearchTerm] = useState("");
  const [filterStatus, setFilterStatus] = useState("all");
  const [projects, setProjects] = useState([
    {
      id: 1,
      title: "E-Commerce Dashboard",
      student: "Ahmed Mohamed",
      description: "Admin dashboard for managing e-commerce operations with React and Node.js",
      skills: ["React", "TypeScript", "Node.js", "MongoDB"],
      status: "pending",
      submittedDate: "2024-01-20",
      team: ["Ahmed Mohamed", "Fatima Ali"],
      github: "https://github.com/ahmed/ecommerce-dashboard",
      rating: null,
      comments: []
    },
    {
      id: 2,
      title: "Task Management App",
      student: "Omar Hassan",
      description: "Collaborative task app with real-time updates using Vue.js and Firebase",
      skills: ["Vue.js", "Firebase", "Tailwind CSS", "JavaScript"],
      status: "approved",
      submittedDate: "2024-01-18",
      team: ["Omar Hassan"],
      github: "https://github.com/omar/task-manager",
      rating: 4.5,
      comments: ["Great implementation!", "Well documented code"]
    },
    {
      id: 3,
      title: "Weather Analytics Platform",
      student: "Mariam Ahmed",
      description: "Data visualization platform for weather patterns using Python and D3.js",
      skills: ["Python", "D3.js", "Flask", "SQLite"],
      status: "rejected",
      submittedDate: "2024-01-15",
      team: ["Mariam Ahmed", "Youssef Omar"],
      github: "https://github.com/mariam/weather-analytics",
      rating: 2.5,
      comments: ["Needs better error handling", "UI could be improved"]
    }
  ]);

  const [selectedProject, setSelectedProject] = useState(null);
  const [reviewModal, setReviewModal] = useState(false);

  const filteredProjects = projects.filter(project => {
    const matchesSearch = project.title.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         project.student.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         project.skills.some(skill => skill.toLowerCase().includes(searchTerm.toLowerCase()));
    const matchesFilter = filterStatus === "all" || project.status === filterStatus;
    return matchesSearch && matchesFilter;
  });

  const handleReview = (projectId, action, rating, comment) => {
    setProjects(projects.map(project => 
      project.id === projectId 
        ? { 
            ...project, 
            status: action, 
            rating: action === "approved" ? rating : null,
            comments: comment ? [...project.comments, comment] : project.comments
          }
        : project
    ));
    setReviewModal(false);
    setSelectedProject(null);
  };

  const getStatusColor = (status) => {
    switch (status) {
      case "approved": return "text-green-400 bg-green-400/20";
      case "rejected": return "text-red-400 bg-red-400/20";
      case "pending": return "text-yellow-400 bg-yellow-400/20";
      default: return "text-muted bg-main/20";
    }
  };

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-xl font-semibold">📋 Project Reviews</h2>
        <div className="flex gap-4">
          <input
            type="text"
            placeholder="Search projects..."
            className="px-4 py-2 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
          <select
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value)}
            className="px-4 py-2 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
          >
            <option value="all">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>
      </div>
      <div className="space-y-4">
        {filteredProjects.map((project) => (
          <div key={project.id} className="border border-muted/30 rounded-custom p-4 hover:border-accent/50 transition">
            <div className="flex justify-between items-start mb-3">
              <div>
                <h3 className="font-semibold text-main">{project.title}</h3>
                <p className="text-sm text-muted">by {project.student}</p>
              </div>
              <div className="flex items-center gap-3">
                <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(project.status)}`}>
                  {project.status.charAt(0).toUpperCase() + project.status.slice(1)}
                </span>
                <button
                  onClick={() => {
                    setSelectedProject(project);
                    setReviewModal(true);
                  }}
                  className="bg-accent text-white px-3 py-1 rounded-custom hover:opacity-80 text-sm"
                >
                  {project.status === "pending" ? "Review" : "View"}
                </button>
              </div>
            </div>
            
            <p className="text-muted text-sm mb-3">{project.description}</p>
            
            <div className="flex flex-wrap gap-2 mb-3">
              {project.skills.map((skill, index) => (
                <span key={index} className="bg-accent/20 text-accent px-2 py-1 rounded text-xs">
                  {skill}
                </span>
              ))}
            </div>
            
            <div className="flex justify-between items-center text-xs text-muted">
              <span>Submitted: {project.submittedDate}</span>
              <span>Team: {project.team.join(", ")}</span>
              {project.rating && <span>Rating: {project.rating}/5</span>}
            </div>
          </div>
        ))}
      </div>

   
      {reviewModal && selectedProject && (
        <ReviewModal
          project={selectedProject}
          onClose={() => setReviewModal(false)}
          onSubmit={handleReview}
        />
      )}
    </div>
  );
}

function ReviewModal({ project, onClose, onSubmit }) {
  const [rating, setRating] = useState(project.rating || 3);
  const [comment, setComment] = useState("");

  const handleSubmit = (action) => {
    onSubmit(project.id, action, rating, comment);
  };

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div className="bg-panel p-8 rounded-2xl shadow-2xl w-full max-w-2xl">
        <h3 className="text-xl font-semibold mb-4">Review: {project.title}</h3>
        
        <div className="mb-6">
          <p className="text-muted mb-4">{project.description}</p>
          
          <div className="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label className="block text-sm font-medium mb-2">Student</label>
              <p className="text-main">{project.student}</p>
            </div>
            <div>
              <label className="block text-sm font-medium mb-2">Team</label>
              <p className="text-main">{project.team.join(", ")}</p>
            </div>
          </div>
          
          <div className="mb-4">
            <label className="block text-sm font-medium mb-2">GitHub Repository</label>
            <a href={project.github} target="_blank" rel="noopener noreferrer" className="text-accent hover:underline">
              {project.github}
            </a>
          </div>
        </div>

        <div className="mb-6">
          <label className="block text-sm font-medium mb-2">Rating (1-5)</label>
          <div className="flex gap-1">
            {[1, 2, 3, 4, 5].map((star) => (
              <button
                key={star}
                onClick={() => setRating(star)}
                className={`text-2xl ${star <= rating ? "text-yellow-400" : "text-muted"}`}
              >
                ★
              </button>
            ))}
          </div>
        </div>

        <div className="mb-6">
          <label className="block text-sm font-medium mb-2">Comments</label>
          <textarea
            value={comment}
            onChange={(e) => setComment(e.target.value)}
            placeholder="Add your feedback..."
            className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            rows="3"
          />
        </div>

        <div className="flex justify-end gap-3">
          <button
            onClick={onClose}
            className="px-5 py-2 rounded-custom bg-muted/20 text-muted hover:bg-muted/30 transition"
          >
            Cancel
          </button>
          <button
            onClick={() => handleSubmit("rejected")}
            className="px-5 py-2 rounded-custom bg-red-500 text-white hover:bg-red-600 transition"
          >
            Reject
          </button>
          <button
            onClick={() => handleSubmit("approved")}
            className="px-5 py-2 rounded-custom bg-green-500 text-white hover:bg-green-600 transition"
          >
            Approve
          </button>
        </div>
      </div>
    </div>
  );
}

export default ProjectReviewSection;






