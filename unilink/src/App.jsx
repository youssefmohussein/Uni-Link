import React, { useState } from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import AdminUsersPage from "./Pages/Admin/AdminUsersPage";
import AdminStudentsPage from "./Pages/Admin/AdminStudentsPage";
import AdminProfessorPage from "./Pages/Admin/AdminProfessorPage";
import AdminAdminPage from "./Pages/Admin/AdminAdminPage";
import AdminDashboardPage from "./Pages/Admin/AdminDashboardPage";
import AdminUniversityPage from "./Pages/Admin/AdminUniversityPage";
import AdminRoomsPage from "./Pages/Admin/AdminRoomsPage";
import LoginPage from "./Pages/Login/LoginPage";
import HomePage from "./Pages/HomePage";
import AboutUsPage from "./Pages/AboutUs/AboutUsPage";
import FacultiesPage from "./Pages/FacultiesPage";

import FacultyDetailsPage from "./Pages/FacultyDetailsPage";
import LoadingPage from "./Pages/loadingPage";
import ProfilePage from "./Pages/Profile/ProfilePage";
import ProtectedRoute from "./Components/ProtectedRoute";
import PostPage from "./Pages/Posts/PostPage";
import CollectionsPage from "./Pages/Collections/Collections";
import ProfessorPage from "./Pages/Professor/ProfessorPage";
import ProjectRoomsPage from "./Pages/ProjectRooms/ProjectRoomsPage";
import ProjectChatPage from "./Pages/ProjectRooms/ProjectChatPage";
import { Toaster } from "react-hot-toast";
import "./index.css"; // Must be at the top

export default function App() {
  const [isLoading, setIsLoading] = useState(() => {
    // Check if it's the home page
    const isHomePage = window.location.pathname === '/';
    // Check if it's the first visit in this session
    const isFirstVisit = !sessionStorage.getItem('hasVisited');

    // Show loading if it's the home page OR if it's the first visit ever
    return isHomePage || isFirstVisit;
  });

  React.useEffect(() => {
    // Mark as visited so subsequent refreshes on non-home pages don't trigger loading
    sessionStorage.setItem('hasVisited', 'true');
  }, []);

  return (
    <>
      {isLoading && <LoadingPage onComplete={() => setIsLoading(false)} />}
      <Toaster position="top-center" reverseOrder={false} />
      <Router>
        <Routes>
          <Route path="/" element={<HomePage />} />
          <Route path="/faculties" element={<FacultiesPage />} />
          <Route path="/faculty/:idOrName" element={<FacultyDetailsPage />} />
          <Route
            path="/admin/dashboard"
            element={
              <ProtectedRoute requiredRole="Admin">
                <AdminDashboardPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/users"
            element={
              <ProtectedRoute requiredRole="Admin">
                <AdminUsersPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/students"
            element={
              <ProtectedRoute requiredRole="Admin">
                <AdminStudentsPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/manage-professors"
            element={
              <ProtectedRoute requiredRole="Admin">
                <AdminProfessorPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/admin"
            element={
              <ProtectedRoute requiredRole="Admin">
                <AdminAdminPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/university"
            element={
              <ProtectedRoute requiredRole="Admin">
                <AdminUniversityPage />
              </ProtectedRoute>
            }
          />
          <Route
            path="/admin/rooms"
            element={
              <ProtectedRoute requiredRole="Admin">
                <AdminRoomsPage />
              </ProtectedRoute>
            }
          />
          <Route path="/login" element={<LoginPage />} />
          <Route path="/profile" element={<ProfilePage />} />
          <Route path="/about" element={<AboutUsPage />} />
          <Route path="/posts" element={<PostPage />} />
          <Route path="/collections" element={<CollectionsPage />} />
          <Route path="/professor" element={<ProfessorPage />} />
          <Route path="/project-rooms" element={<ProjectRoomsPage />} />
          <Route path="/project-room/:id" element={<ProjectChatPage />} />
          <Route path="*" element={<HomePage />} />
        </Routes>
      </Router>
      {/* joe */}
    </>
  );
}
