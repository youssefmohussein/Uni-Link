import React, { useState } from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import AdminUsersPage from "./Pages/Admin/AdminUsersPage";
import AdminStudentsPage from "./Pages/Admin/AdminStudentsPage";
import AdminProfessorPage from "./Pages/Admin/AdminProfessorPage";
import AdminAdminPage from "./Pages/Admin/AdminAdminPage";
import AdminDashboardPage from "./Pages/Admin/AdminDashboardPage";
import AdminUniversityPage from "./Pages/Admin/AdminUniversityPage";
import LoginPage from "./Pages/Login/LoginPage";
import HomePage from "./Pages/HomePage";
import AboutUsPage from "./Pages/AboutUs/AboutUsPage";
import LoadingPage from "./Pages/loadingPage";
import "./index.css"; // Must be at the top

export default function App() {
  const [isLoading, setIsLoading] = useState(true);

  return (
    <>
      {isLoading && <LoadingPage onComplete={() => setIsLoading(false)} />}
      <Router>
        <Routes>
          <Route path="/" element={<HomePage />} />
          <Route path="/admin/dashboard" element={<AdminDashboardPage />} />
          <Route path="/admin/users" element={<AdminUsersPage />} />
          <Route path="/admin/students" element={<AdminStudentsPage />} />
          <Route path="/admin/manage-professors" element={<AdminProfessorPage />} />
          <Route path="/admin/admin" element={<AdminAdminPage />} />
          <Route path="/admin/university" element={<AdminUniversityPage />} />
          <Route path="/login" element={<LoginPage />} />
          <Route path="/about" element={<AboutUsPage />} />
          <Route path="*" element={<AdminUsersPage />} />
        </Routes>
      </Router>
    </>
  );
}
