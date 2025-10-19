import React from "react";
import { BrowserRouter as Router, Routes, Route, Navigate, Outlet } from "react-router-dom";
import LoadingPage from "./Pages/LoadingPage";
import Login from "./Pages/Login";
import Signup from "./Pages/Signup";
import ForgotPassword from "./Pages/ForgotPassword";
import ProfilePageUser from "./Pages/ProfilePageUser";
import Dashboard from "./Pages/Dashboard";
import AdminUserPage from "./Pages/AdminUsersPage";
import StudentsPage from "./Pages/StudentsPage";
import Sidebar from "./components/SideBar";
import ManageUsers from "./Pages/ManageUsers";
import ManageTAs from "./Pages/ManageAdmin";
import ManageProfessors from "./Pages/ManageProfessors";
import PostPage from "./Pages/PostPage";
import ProfilePageProfessor from "./Pages/ProfilePageProfessor";
import Home from "./Pages/Home";
import About from "./Pages/About";
import ContactUs from "./Pages/ContactUs";
import MajorsPage from "./Pages/MajorsPage";
import { apiRequest } from "./utils/apiClient";

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

  const AdminLayout = () => (
    <div className="min-h-screen bg-background text-foreground flex">
      <Sidebar />
      <div className="flex-1">
        <Outlet />
      </div>
    </div>
  )

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

        {/* Admin Routes */}
        <Route path="/admin" element={<AdminLayout />}>
          <Route index element={<Dashboard />} />
          <Route path="users" element={<AdminUserPage />} />
          <Route path="students" element={<StudentsPage />} />
          <Route path="manage-users" element={<ManageUsers />} />
          <Route path="manage-professors" element={<ManageProfessors />} />
          <Route path="manage-tas" element={<ManageTAs />} />
        </Route>

        {/* Catch-All */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Router>
  );
}

export default App;
