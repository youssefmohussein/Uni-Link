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
import PostPage from "./Pages/PostPage";
import ProfilePageProfessor from "./Pages/ProfilePageProfessor";
import { apiRequest } from "./utils/apiClient"; // ✅ add this import


function App() {
  const [loading, setLoading] = React.useState(true);
  const [backendMsg, setBackendMsg] = React.useState("");

  React.useEffect(() => {
    // Step 1: fake loading delay
    const timer = setTimeout(() => setLoading(false), 2000);

    // Step 2: test backend connection
    apiRequest("test.php")
      .then((data) => setBackendMsg(data.status))
      .catch(() => setBackendMsg("❌ Cannot connect to backend"));

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
        <Route path="/" element={<Navigate to="/login" replace />} />
        <Route path="/home" element={<PostPage />} />
         <Route  path="/professorprofile" element={<ProfilePageProfessor/>}/>
        <Route path="/admin/manage-tas" element={<ManageTAs />} />
        </Route>
        <Route path="*" element={<Navigate to="/login" replace />} />
      </Routes>
      <div className="min-h-screen bg-background text-foreground">
        {/* Temporary Test Message */}
        <div className="p-4 text-center text-sm bg-blue-100 text-blue-700">
          Backend status: {backendMsg || "Checking..."}
        </div>
        <Routes>
          
        </Routes>
      </div>
    </Router>
  );
}

export default App;
