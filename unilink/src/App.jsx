import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import AdminUsersPage from "./Pages/Admin/AdminUsersPage";

export default function App() {
  return (
    <Router>
      <Routes>
        <Route path="/admin/users" element={<AdminUsersPage />} />
        <Route path="*" element={<AdminUsersPage />} />
      </Routes>
    </Router>
  );
}
