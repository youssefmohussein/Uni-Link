import React, { useState } from "react";
import ProfileHeader from "../../components/Admin/ProfileHeader";
import SettingSection from "../../components/Admin/SettingSection";
import ThemeToggle from "../../components/Admin/ThemeToggle";

export default function AdminSettings() {
  const [theme, setTheme] = useState("dark");

  const toggleTheme = () => {
    const newTheme = theme === "dark" ? "light" : "dark";
    setTheme(newTheme);
    document.body.classList.toggle("light");
  };

  return (
    <div className="min-h-screen bg-main text-main font-main p-8">
      <div className="max-w-3xl mx-auto bg-panel shadow-custom rounded-custom p-6 animate-fade-in">
        <ProfileHeader />
        <div className="mt-8 space-y-6">
          <SettingSection
            title="Edit Profile Information"
            description="Update your name, email, and other details."
            buttonText="Edit Info"
          />
          <SettingSection
            title="Change Profile Photo"
            description="Upload a new profile picture."
            buttonText="Edit Photo"
          />
          <SettingSection
            title="Change Password"
            description="Secure your account by updating your password."
            buttonText="Change Password"
          />
          <ThemeToggle theme={theme} toggleTheme={toggleTheme} />
        </div>
      </div>
    </div>
  );
}
