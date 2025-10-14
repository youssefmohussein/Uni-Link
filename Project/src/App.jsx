import React from "react";
import ProfilePageUser from "./Pages/ProfilePageUser";
import LoadingPage from "./Pages/LoadingPage";

function App() {
  const [loading, setLoading] = React.useState(true);
  React.useEffect(() => {
    const timer = setTimeout(() => setLoading(false), 3000);
    return () => clearTimeout(timer);
  }, []);

  return (
    <div className="min-h-screen">
      {loading ? <LoadingPage /> : <ProfilePageUser />}
    </div>
  );
}

export default App;
