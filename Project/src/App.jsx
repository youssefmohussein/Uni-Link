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
import ManageTAs from "./Pages/ManageTAs";
import ManageProfessors from "./Pages/ManageProfessors";

function App() {
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    const timer = setTimeout(() => setLoading(false), 3000);
    return () => clearTimeout(timer);
  }, []);

  if (loading) {
    return <LoadingPage />;
  }

  const AdminLayout = () => (
    <div className="min-h-screen bg-background text-foreground flex">
      <Sidebar />
      <div className="flex-1">
        <Outlet />
      </div>
    </div>
  );

  return (
    <Router>
      <Routes>
        <Route path="/" element={<Navigate to="/login" replace />} />
        <Route path="/login" element={<Login />} />
        <Route path="/signup" element={<Signup />} />
        <Route path="/forgot-password" element={<ForgotPassword />} />
        <Route path="/profile" element={<ProfilePageUser />} />
        <Route path="/admin" element={<AdminLayout />}>
        <Route index element={<Dashboard />} />
        <Route path="/admin/users" element={<AdminUserPage />} />
        <Route path="/admin/students" element={<StudentsPage />} />
        <Route path="/admin/manage-users" element={<ManageUsers />} />
        <Route path="/admin/manage-professors" element={<ManageProfessors />} />
        <Route path="/admin/manage-tas" element={<ManageTAs />} />
        </Route>
        <Route path="*" element={<Navigate to="/login" replace />} />
      </Routes>
    </Router>
  );
}

export default App;
