import React from "react";
import { BrowserRouter as Router, Routes, Route, Navigate } from "react-router-dom";
// Utility
import { apiRequest } from "./utils/apiClient";

// Public Pages
import Home from "./Pages/HomePage/Home";
import About from "./Pages/HomePage/About";
import ContactUs from "./Pages/HomePage/ContactUs";
import MajorsPage from "./Pages/HomePage/MajorsPage";
import Login from "./Pages/Login/Login";
import Signup from "./Pages/Login/Signup";
import ForgotPassword from "./Pages/Login/ForgotPassword";

// Common/Feature Pages
import LoadingPage from "./Pages/features/LoadingPage";

// Student Pages
import ProfilePageUser from "./Pages/Student/ProfilePageUser";
import PostPage from "./Pages/Student/PostPage";

// Professor Pages
import ProfilePageProfessor from "./Pages/Professor/ProfilePageProfessor";

// Admin Pages
import Dashboard from "./Pages/Admin/Dashboard";
import AdminUserPage from "./Pages/Admin/AdminUsersPage";
import StudentsPage from "./Pages/Admin/StudentsPage";
import ManageUsers from "./Pages/Admin/ManageUsers";
import ManageTAs from "./Pages/Admin/ManageAdmin";
import ManageProfessors from "./Pages/Admin/ManageProfessors";

function App() {
  const [loading, setLoading] = React.useState(true);
  const [backendMsg, setBackendMsg] = React.useState("");

  React.useEffect(() => {
    const timer = setTimeout(() => setLoading(false), 2000);

    apiRequest("test.php")
      .then((data) => setBackendMsg(data.status))
      .catch(() => setBackendMsg("❌ Cannot connect to backend"));

    return () => clearTimeout(timer);
  }, []);

  if (loading) return <LoadingPage />;

  return (
    <Router>
      <div className="p-4 text-center text-sm bg-blue-100 text-blue-700">
        Backend status: {backendMsg || "Checking..."}
      </div>

      <Routes>
        {/* Public Routes */}
        <Route path="/" element={<Home />} />
        <Route path="/login" element={<Login />} />
        <Route path="/signup" element={<Signup />} />
        <Route path="/about" element={<About />} />
        <Route path="/contact" element={<ContactUs />} />
        <Route path="/majors" element={<MajorsPage />} />
        <Route path="/forgot-password" element={<ForgotPassword />} />
        <Route path="/profile" element={<ProfilePageUser />} />
        <Route path="/home" element={<PostPage />} />
        <Route path="/professorprofile" element={<ProfilePageProfessor />} />

        {/* Admin Routes — no Sidebar, no nested layout */}
        <Route path="/admin/dashboard" element={<Dashboard />} />
        <Route path="/admin/users" element={<AdminUserPage />} />
        <Route path="/admin/students" element={<StudentsPage />} />
        <Route path="/admin/manage-users" element={<ManageUsers />} />
        <Route path="/admin/manage-professors" element={<ManageProfessors />} />
        <Route path="/admin/manage-tas" element={<ManageTAs />} />

        {/* Catch-All */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Router>
  );
}

export default App;
