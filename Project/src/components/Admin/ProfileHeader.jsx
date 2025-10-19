import React from "react";

export default function ProfileHeader() {
  return (
    <div className="flex flex-col items-center text-center">
      <div className="w-24 h-24 bg-accent rounded-full mb-4 shadow-custom">
      <img alt="youssef profile" class="w-full h-full object-cover" src="/src/assets/profileImage.jpg"></img>
      </div>
      <h1 className="text-2xl font-semibold text-main">Admin Name</h1>
      <p className="text-muted text-sm">admin@portal.com</p>
    </div>
  );
}
