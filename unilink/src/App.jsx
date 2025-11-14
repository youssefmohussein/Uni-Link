import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import AdminUsersPage from "./Pages/Admin/AdminUsersPage";
import AdminStudentsPage from "./Pages/Admin/AdminStudentsPage";
import AdminProfessorPage from "./Pages/Admin/AdminProfessorPage";
import "./index.css"; // Must be at the top

export default function App() {
  return (
    <Router>
      <Routes>
        <Route path="/admin/users" element={<AdminUsersPage />} />
        <Route path="/admin/students" element={<AdminStudentsPage />} />
        <Route path="/admin/manage-professors" element={<AdminProfessorPage />} />
        <Route path="/admin/manage-tas" element={<AdminUsersPage />} />
        <Route path="/admin/settings" element={<AdminUsersPage />} />
        <Route path="*" element={<AdminUsersPage />} />
      </Routes>
    </Router>
  );
}
