import React, { useState } from "react";
import profileImage from "../assets/profileImage.jpg";
import project1 from "../assets/project1.jpg";
import project2 from "../assets/project2.jpg";
import ProjectCard from "../components/ProjectCard";
import ProjectModal from "../components/ProjectModal";
import SkillsSection from "../components/SkillsSection";
import ProfileHeader from "../components/ProfileHeader";
import CVSection from "../components/CvSection";
import PostsSection from "../components/PostsSection";

function ProfilePageUser() {
  const [isModalOpen, setIsModalOpen] = useState(false);

  // ✅ Store all projects here
  const [projects, setProjects] = useState([
    {
      image: project1,
      title: "E-Commerce Dashboard",
      description: "Admin dashboard for managing e-commerce operations.",
      skills: ["React", "TypeScript", "Node.js", "MongoDB"],
    },
    {
      image: project2,
      title: "Task Management App",
      description: "Collaborative task app with real-time updates.",
      skills: ["Vue.js", "Firebase", "Tailwind CSS"],
    },
  ]);

  // ✅ Function to add new project
  const addProject = (newProject) => {
    setProjects((prev) => [newProject, ...prev]); // adds new project on top
  };

  return (
    <div className="max-w-7xl mx-auto p-6 bg-main text-main min-h-screen font-main">
      {/* Header */}
      <ProfileHeader
        name="youssef"
        title="Web Developer"
        bio="Building smooth, scalable, and creative digital experiences."
        image={profileImage}
      />

      <main className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Sidebar */}
        <aside className="space-y-8">
          <CVSection />
          <SkillsSection />
        </aside>

        {/* Projects & Posts */}
        <section className="lg:col-span-2 space-y-8">
          {/* Projects */}
          <div className="bg-panel rounded-custom shadow-custom p-6">
            <div className="flex justify-between items-center mb-4">
              <h2 className="text-lg font-semibold">🚀 Projects</h2>
              <button
                onClick={() => setIsModalOpen(true)}
                className="bg-accent text-white px-3 py-1 rounded hover:opacity-80"
              >
                + Upload Project
              </button>
            </div>

            <div className="grid gap-6 md:grid-cols-2">
              {projects.map((proj, index) => (
                <ProjectCard key={index} {...proj} />
              ))}
            </div>
          </div>

          {/* Posts */}
          <PostsSection
            posts={[
              {
                title: "My Journey into Full-Stack Development",
                date: "2024-01-20",
                content:
                  "Starting as a designer, I never thought I would become passionate about backend development...",
              },
              {
                title: "Best Practices for React State Management",
                date: "2024-01-18",
                content:
                  "After working on several React projects, I have learned some valuable lessons about state management...",
              },
            ]}
          />
        </section>
      </main>

      {/* Modal for Uploading Project */}
      <ProjectModal
        isOpen={isModalOpen}
        onClose={() => setIsModalOpen(false)}
        addProject={addProject} // ✅ pass function
      />
    </div>
  );
}

export default ProfilePageUser;
