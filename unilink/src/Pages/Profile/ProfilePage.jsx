import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import ProjectCard from "../../Components/Student/ProjectCard.jsx";
import ProjectModal from "../../Components/Student/ProjectModal.jsx";
import EditProjectModal from "../../Components/Student/EditProjectModal.jsx";
import SkillsSection from "../../Components/Student/SkillsSection.jsx";
import ProfileHeader from "../../Components/Student/ProfileHeader.jsx";
import CVSection from "../../Components/Student/CvSection.jsx";
import PostsSection from "../../Components/Student/PostsSection.jsx";
import Galaxy from "../../Animations/Galaxy/Galaxy";
import * as profileHandler from "../../../api/profileHandler";
import * as studentHandler from "../../../api/studentHandler";

function ProfilePageUser() {
  const navigate = useNavigate();
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [isEditModalOpen, setIsEditModalOpen] = useState(false);
  const [editingProject, setEditingProject] = useState(null);
  const [projects, setProjects] = useState([]);
  const [posts, setPosts] = useState([]);
  const [userProfile, setUserProfile] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [currentUserId, setCurrentUserId] = useState(null);

  useEffect(() => {
    // Fetch current user from backend session
    const loadUserFromSession = async () => {
      try {
        const response = await fetch('http://localhost/backend/check-session', {
          method: 'GET',
          credentials: 'include', // Important: send cookies
          headers: {
            'Content-Type': 'application/json'
          }
        });

        const response_data = await response.json();

        console.log("Session response:", response_data); // Debug log

        // Backend returns {status, message, data: {authenticated, user}}
        const sessionData = response_data.data || response_data;

        if (sessionData.authenticated && sessionData.user && sessionData.user.id) {
          const userId = Number(sessionData.user.id); // Ensure it's a number
          console.log("Extracted userId:", userId, typeof userId); // Debug log
          setCurrentUserId(userId);
          await fetchProfileData(userId);
        } else {
          // Not authenticated
          console.log("Not authenticated, session data:", sessionData); // Debug log
          setLoading(false);
          setError("Please login to view your profile.");
        }
      } catch (error) {
        console.error("Failed to check session:", error);
        setLoading(false);
        setError("Please login to view your profile.");
      }
    };

    loadUserFromSession();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []); // Run only once on mount


  const fetchProfileData = async (userId) => {
    try {
      setLoading(true);
      setError(null);

      // Fetch user profile, projects, and posts - handle each independently
      const [profileResult, projectsResult, postsResult] = await Promise.allSettled([
        studentHandler.getStudentProfile(userId),
        studentHandler.getStudentProjects(userId),
        studentHandler.getStudentPosts(userId)
      ]);

      // Handle profile data
      if (profileResult.status === 'fulfilled') {
        setUserProfile(profileResult.value);
      } else {
        console.error("Failed to fetch profile:", profileResult.reason);
        setError("Failed to load profile information.");
        setLoading(false);
        return; // Profile is essential, stop if it fails
      }

      // Handle projects data (optional)
      let userProjects = [];
      if (projectsResult.status === 'fulfilled') {
        userProjects = projectsResult.value;
      } else {
        console.error("Failed to fetch projects:", projectsResult.reason);
        // Don't stop, just show empty projects
      }

      // Transform projects to match ProjectCard expected format
      const transformedProjects = userProjects.map(proj => ({
        project_id: proj.project_id,
        title: proj.title,
        description: proj.description,
        status: proj.status,
        grade: proj.grade,
        file_path: proj.file_path,
        supervisor_name: proj.supervisor_name,
        created_at: proj.created_at
      }));
      setProjects(transformedProjects);

      // Handle posts data (optional)
      let userPosts = [];
      if (postsResult.status === 'fulfilled') {
        // Check if the response has a data property (standard API format) or is the array directly
        const postsData = postsResult.value;
        userPosts = Array.isArray(postsData) ? postsData : (postsData.data || []);
      } else {
        console.error("Failed to fetch posts:", postsResult.reason);
        // Don't stop, just show empty posts
      }

      // Transform posts for display
      const transformedPosts = userPosts.map(post => ({
        post_id: post.post_id,
        title: post.category, // Use category as title
        date: new Date(post.created_at).toISOString().split('T')[0],
        content: post.content,
        category: post.category,
        likes_count: post.likes_count || 0
      }));
      setPosts(transformedPosts);

    } catch (err) {
      console.error("Failed to fetch profile data:", err);
      setError("Failed to load profile. Please try again later.");
    } finally {
      setLoading(false);
    }
  };

  const handleEditProject = (project) => {
    setEditingProject(project);
    setIsEditModalOpen(true);
  };


  return (
    <div className="flex flex-col min-h-screen bg-main text-main font-main transition-theme relative overflow-hidden">
      {/* 🌌 Starry Night Sky Background */}
      <div className="fixed inset-0 z-0 bg-gradient-to-b from-black via-black to-black">
        {/* Galaxy animation provides the starry effect */}
      </div>

      {/* Animated stars overlay - Static purple/blue galaxy like About Us */}
      <div className="fixed inset-0 z-0 mix-blend-screen opacity-60">
        <Galaxy
          transparent={true}
          hueShift={260}
          density={1.5}
          glowIntensity={0.1}
          saturation={0.6}
          speed={0.15}
          mouseRepulsion={false}
          disableAnimation={false}
        />
      </div>


      {/* Content Layer */}
      <div className="relative z-10 max-w-7xl mx-auto p-6 w-full">
        {loading ? (
          <div className="text-center py-12">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-accent"></div>
            <p className="mt-4 text-muted">Loading profile...</p>
          </div>
        ) : error ? (
          <div className="backdrop-blur-xl bg-white/10 dark:bg-black/20 rounded-custom shadow-2xl p-6 text-center border border-white/20">
            <p className="text-red-500 mb-4">{error}</p>
            <button
              onClick={() => fetchProfileData(currentUserId)}
              className="px-4 py-2 bg-accent text-white rounded-custom hover:bg-accent/80 transition-colors"
            >
              Retry
            </button>
          </div>
        ) : (
          <>
            {/* Header */}
            <ProfileHeader
              name={userProfile?.username || "User"}
              title={userProfile?.major_name || userProfile?.job_title || "Student"}
              bio={userProfile?.bio || "No bio available"}
              image={userProfile?.profile_image || `https://placehold.co/150x150/E5E7EB/6B7280?text=${(userProfile?.username || "U")[0]}`}
              faculty={userProfile?.faculty_name}
              year={userProfile?.year}
              gpa={userProfile?.gpa}
              points={userProfile?.points}
            />

            <main className="grid grid-cols-1 lg:grid-cols-3 gap-8">
              {/* Sidebar */}
              <aside className="space-y-8">
                <CVSection userId={currentUserId} />
                <SkillsSection userId={currentUserId} />
              </aside>

              {/* Projects & Posts */}
              <section className="lg:col-span-2 space-y-8">
                {/* Projects */}
                <div className="backdrop-blur-xl bg-white/10 dark:bg-black/20 rounded-custom shadow-2xl p-6 border border-white/20">
                  <div className="flex justify-between items-center mb-4">
                    <h2 className="text-lg font-semibold text-white">🚀 Projects</h2>
                    <button
                      onClick={() => setIsModalOpen(true)}
                      className="bg-accent text-white px-3 py-1 rounded hover:opacity-80"
                    >
                      + Upload Project
                    </button>
                  </div>

                  {projects.length === 0 ? (
                    <p className="text-muted text-center py-8">No projects yet. Upload your first project!</p>
                  ) : (
                    <div className="flex flex-col gap-6 w-full max-w-6xl">
                      {projects.map((proj) => (
                        <div key={proj.project_id} className="w-full">
                          <ProjectCard
                            {...proj}
                            userId={currentUserId}
                            onDelete={async (id) => {
                              setProjects(projects.filter(p => p.project_id !== id));
                              // Refresh from backend to ensure consistency
                              if (currentUserId) {
                                await fetchProfileData(currentUserId);
                              }
                            }}
                            onEdit={handleEditProject}
                          />
                        </div>
                      ))}
                    </div>
                  )}
                </div>

                {/* Posts */}
                <PostsSection
                  posts={posts}
                  onRefresh={() => fetchProfileData(currentUserId)}
                  userId={currentUserId}
                />
              </section>
            </main>

            {/* Modal for Uploading Project */}
            <ProjectModal
              isOpen={isModalOpen}
              onClose={() => setIsModalOpen(false)}
              userId={currentUserId}
              onSuccess={fetchProfileData}
            />

            {/* Modal for Editing Project */}
            <EditProjectModal
              isOpen={isEditModalOpen}
              onClose={() => {
                setIsEditModalOpen(false);
                setEditingProject(null);
              }}
              project={editingProject}
              userId={currentUserId}
              onSuccess={fetchProfileData}
            />
          </>
        )}
      </div>
    </div>
  );
}

export default ProfilePageUser;
