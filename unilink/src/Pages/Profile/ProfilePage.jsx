import React from "react";
import ProfileHeader from "../../Components/Profile_Components/ProfileHeader";
import Stats from "../../Components/Profile_Components/Stats";
import CVSection from "../../Components/Profile_Components/CVSection";
import ProjectsGrid from "../../Components/Profile_Components/ProjectsGrid";
import Navbar from "../../Components/Navbar";
import GlassCard from "../../Components/GlassCard";

export default function ProfilePage() {
  return (
    <div className="min-h-screen bg-bg text-main p-20 font-main transition-smooth">
      <div className="bg-bg text-main p-6 font-main transition-smooth">
        <Navbar/>
        <ProfileHeader />
        <Stats />
        <CVSection />
        <ProjectsGrid />
      </div>
    </div>
  );
}
