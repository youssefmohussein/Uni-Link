import React from "react";
import { BrowserRouter as Router, Routes, Route, Navigate } from "react-router-dom";
import LoadingPage from "./Pages/LoadingPage";
import Login from "./Pages/Login";
import Signup from "./Pages/Signup";
import ForgotPassword from "./Pages/ForgotPassword";
import ProfilePageUser from "./Pages/ProfilePageUser";
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

  return (
    <Router>
      <div className="min-h-screen bg-background text-foreground">
        {/* Temporary Test Message */}
        <div className="p-4 text-center text-sm bg-blue-100 text-blue-700">
          Backend status: {backendMsg || "Checking..."}
        </div>

        <Routes>
          <Route path="/" element={<Navigate to="/login" replace />} />
          <Route path="/login" element={<Login />} />
          <Route path="/signup" element={<Signup />} />
          <Route path="/forgot-password" element={<ForgotPassword />} />
          <Route path="/profile" element={<ProfilePageUser />} />
          <Route path="*" element={<Navigate to="/login" replace />} />
        </Routes>
      </div>
    </Router>
  );
}

export default App;
