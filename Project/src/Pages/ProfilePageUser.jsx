import React, { useState } from "react";
import profileImage from "../assets/profileImage.jpg";
import project1 from "../assets/project1.jpg";
import project2 from "../assets/project2.jpg";
import ProjectCard from "../components/ProjectCard";
import ProjectModal from "../components/ProjectModal";
import SkillsSection from "../components/SkillsSection";
import ProfileHeader from "../components/ProfileHeader";

function ProfilePageUser() {
  const [isModalOpen, setIsModalOpen] = useState(false);

  return (
    <div className="max-w-7xl mx-auto p-6 bg-main text-main min-h-screen font-main">
      {/* Header */}
        <ProfileHeader 
        name="youssef"
        title="Web Developer"
        bio = "Building smooth, scalable, and creative digital experiences."
        image = {profileImage}/>

      <main className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Sidebar */}
            <aside className="space-y-8">
      {/* CV Upload */}
      <section className="bg-panel rounded-custom shadow-custom p-6">
        <h2 className="text-lg font-semibold mb-4">📄 CV Documents</h2>
        <label
          htmlFor="cvFile"
          className="block border-2 border-dashed border-muted p-6 rounded-custom text-center cursor-pointer hover:border-accent"
        >
          <input type="file" id="cvFile" hidden />
          <p className="text-main font-medium">Upload your CV</p>
          <p className="text-sm text-muted">PDF, DOC, DOCX up to 10MB</p>
        </label>
        <div className="mt-4">
          <div className="flex items-center justify-between bg-main p-3 rounded-lg">
            <div>
              <a href="#" className="font-medium text-main hover:underline">
                AhmedMohamed_Resume.pdf
              </a>
              <p className="text-sm text-muted">1.2 MB • Uploaded on 2024-01-15</p>
            </div>
            <button className="text-accent hover:opacity-80">⬇</button>
          </div>
        </div>
      </section>

      {/* Skills */}
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

            <div className="grid gap-6 lg:gap-4 xl:gap-0 md:grid-cols-2">
              <ProjectCard
                image={project1}
                title="E-Commerce Dashboard"
                description="Admin dashboard for managing e-commerce operations."
                skills={["React", "TypeScript", "Node.js", "MongoDB"]}
              />
              <ProjectCard
                image={project2}
                title="Task Management App"
                description="Collaborative task app with real-time updates."
                skills={["Vue.js", "Firebase", "Tailwind CSS"]}
              />
            </div>
          </div>
          <div className="bg-panel rounded-custom shadow-custom p-6">
             <h2 className="text-lg font-semibold mb-4">📝 Posts</h2>
            <article className="mb-4">
             <h3 className="font-medium text-main">My Journey into Full-Stack Development</h3>
             <span className="text-sm text-muted block mb-2">Published 1/20/2024</span>
             <p className="text-muted">
               Starting as a designer, I never thought I would become passionate
               about backend development. This post shares my journey...
             </p>
           </article>
           <article>
             <h3 className="font-medium text-main">Best Practices for React State Management</h3>
             <span className="text-sm text-muted block mb-2">Published 1/18/2024</span>
             <p className="text-muted">
             After working on several React projects, I have learned some valuable
             lessons about state management...
             </p>
           </article>
          </div>
        </section>
      </main>
      

      {/* Modal for Uploading Project */}
      <ProjectModal isOpen={isModalOpen} onClose={() => setIsModalOpen(false)} />
    </div>
  );
  
}

export default ProfilePageUser;
  