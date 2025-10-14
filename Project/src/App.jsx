import React from "react";
import { BrowserRouter as Router, Routes, Route, Navigate } from "react-router-dom";
import ProfilePageUser from "./Pages/ProfilePageUser";
import LoadingPage from "./Pages/LoadingPage";

function App() {
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    const timer = setTimeout(() => setLoading(false), 3000); // simulate load
    return () => clearTimeout(timer);
  }, []);

  if (loading) {
    return <LoadingPage />;
  }

  return (
    <Router>
      <div className="min-h-screen bg-background text-foreground">
        <Routes>
          <Route path="/profile" element={<ProfilePageUser />} />
        </Routes>
      </div>
    </Router>
  );
}

export default App;
