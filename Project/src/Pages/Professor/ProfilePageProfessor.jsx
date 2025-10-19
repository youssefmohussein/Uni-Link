/*Imports the react library, which is necessary for creating the components of React */
import React, { useState } from "react";
/*Imports the profile image from the assets folder*/
import profileImage from "../../assets/profileImage.jpg";
/*Imports component responsible for review section*/
import ProjectReviewSection from "../../components/Professor/ProjectReviewSection";
/*Imports component responsible for Q&A section*/
import QASection from "../../components/Professor/QASection";
/*Imports component responsible for project rooms section*/
import ProjectRoomsSection from "../../components/Professor/ProjectRoomsSection";
/*Imports component responsible for post management section*/
import PostManagementSection from "../../components/Professor/PostManagementSection";
/*Imports component responsible for analytics section*/
import AnalyticsSection from "../../components/Professor/AnalyticsSection";



/* Profile Page for Professor */
/*Defines a react functional component named ProfilePageProfessor as it is main component of the page*/
function ProfilePageProfessor() {
  const [activeSection, setActiveSection] = useState("projects");

  const sections = [
    { id: "projects", label: "📋 Review Projects", icon: "📋" },
    { id: "qa", label: "💬 Q&A", icon: "💬" },
    { id: "rooms", label: "🏠 Project Rooms", icon: "🏠" },
    { id: "posts", label: "📝 Manage Posts", icon: "📝" },
    { id: "analytics", label: "📊 Analytics", icon: "📊" },
  ];
/* Function to render the section based on the active section */
  const renderSection = () => {
    switch (activeSection) {
      case "projects":
        return <ProjectReviewSection />;
      case "qa":
        return <QASection />;
      case "rooms":
        return <ProjectRoomsSection />;
      case "posts":
        return <PostManagementSection />;
      case "analytics":
        return <AnalyticsSection />;
      default:
        return <ProjectReviewSection />;
    }
  };

  /* Main function to render the page */  
  return (
    <div className="max-w-7xl mx-auto p-6 bg-main text-main min-h-screen font-main">
      {/* Header */}
      <header className="bg-panel rounded-custom shadow-custom p-6 mb-8">
        <div className="flex items-start gap-6">
          <div className="w-24 md:w-32 lg:w-40 aspect-square rounded-full overflow-hidden border-4 border-accent mx-auto">
            <img
              src={profileImage}
              alt="Professor Profile"
              className="w-full h-full object-cover"
            />
          </div>
          <div>
            <h1 className="text-2xl font-bold text-main">Dr. Sarah Johnson</h1>
            <p className="text-accent font-medium">Computer Science Professor</p>
            <p className="text-muted mt-2 leading-relaxed">
              Dedicated to fostering innovation and guiding students in their
              software development journey. Specialized in full-stack development,
              project management, and emerging technologies.
            </p>
          </div>
        </div>
      </header>

      <div className="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {/* Navigation Sidebar */}
        <aside className="lg:col-span-1">
          <div className="bg-panel rounded-custom shadow-custom p-6 sticky top-6">
            <h2 className="text-lg font-semibold mb-4 text-accent">Professor Dashboard</h2>
            <nav className="space-y-2">
              {sections.map((section) => (
                <button
                  key={section.id}
                  onClick={() => setActiveSection(section.id)}
                  className={`w-full text-left p-3 rounded-custom transition-all duration-200 ${
                    activeSection === section.id
                      ? "bg-accent text-white shadow-md"
                      : "hover:bg-main/20 text-muted hover:text-main"
                  }`}
                >
                  <span className="mr-2">{section.icon}</span>
                  {section.label}
                </button>
              ))}
            </nav>
            
            {/* Quick Stats */}
            <div className="mt-8 pt-6 border-t border-muted/20">
              <h3 className="text-sm font-medium text-muted mb-3">Quick Stats</h3>
              <div className="space-y-2 text-sm">
                <div className="flex justify-between">
                  <span className="text-muted">Active Projects:</span>
                  <span className="text-accent font-medium">24</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-muted">Pending Reviews:</span>
                  <span className="text-accent font-medium">8</span>
                </div>
                <div className="flex justify-between">
                  <span className="text-muted">Open Q&A:</span>
                  <span className="text-accent font-medium">12</span>
                </div>
              </div>
            </div>
          </div>
        </aside>

        {/* Main Content */}
        <main className="lg:col-span-3">
          <div className="bg-panel rounded-custom shadow-custom p-6">
            {/*Render the section based on the active section*/}
            {renderSection()}
          </div>
        </main>
      </div>
    </div>
  );
}


/*Export component to be imported and used in other files*/
export default ProfilePageProfessor;






