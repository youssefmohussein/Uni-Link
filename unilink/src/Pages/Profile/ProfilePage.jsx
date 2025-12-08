import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import ProjectCard from "../../components/Student/ProjectCard.jsx";
import ProjectModal from "../../components/Student/ProjectModal.jsx";
import SkillsSection from "../../components/Student/SkillsSection.jsx";
import ProfileHeader from "../../components/Student/ProfileHeader.jsx";
import CVSection from "../../components/Student/CvSection.jsx";
import PostsSection from "../../components/Student/PostsSection.jsx";
import Galaxy from "../../Animations/Galaxy/Galaxy";
import * as profileHandler from "../../../api/profileHandler";

function ProfilePageUser() {
  const navigate = useNavigate();
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [projects, setProjects] = useState([]);
  const [posts, setPosts] = useState([]);
  const [userProfile, setUserProfile] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [currentUserId, setCurrentUserId] = useState(null);

  useEffect(() => {
    // Get current user from localStorage
    const user = JSON.parse(localStorage.getItem('user'));
    const userId = user?.id;

    if (userId) {
      setCurrentUserId(userId);
      fetchProfileData(userId);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []); // Run only once on mount


  const fetchProfileData = async (userId) => {
    try {
      setLoading(true);
      setError(null);

      // Fetch user profile, projects, and posts - handle each independently
      const [profileResult, projectsResult, postsResult] = await Promise.allSettled([
        profileHandler.getUserProfile(userId),
        profileHandler.getUserProjects(userId),
        profileHandler.getUserPosts(userId)
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
        userPosts = postsResult.value;
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

  const addProject = (newProject) => {
    setProjects((prev) => [newProject, ...prev]);
  };


  return (
    <div className="flex flex-col min-h-screen bg-main text-main font-main transition-theme relative overflow-hidden">
      {/* 🌌 Starry Night Sky Background */}
      <div className="fixed inset-0 z-0 bg-gradient-to-b from-black via-black to-black">
        {/* Galaxy animation provides the starry effect */}
      </div>

      {/* Animated stars overlay */}
      <div className="fixed inset-0 z-0 mix-blend-screen opacity-60">
        <Galaxy
          transparent={true}
          hueShift={0}
          density={0.2}
          glowIntensity={0.3}
          saturation={0.0}
          speed={0.05}
          mouseRepulsion={true}
          repulsionStrength={0.5}
          twinkleIntensity={1.0}
          disableAnimation={false}
          rotationSpeed={0.005}
          starSpeed={0.2}
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
              onClick={fetchProfileData}
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
                    <div className="grid gap-6 md:grid-cols-2">
                      {projects.map((proj) => (
                        <ProjectCard key={proj.project_id} {...proj} />
                      ))}
                    </div>
                  )}
                </div>

                {/* Posts */}
                <PostsSection posts={posts} />
              </section>
            </main>

            {/* Modal for Uploading Project */}
            <ProjectModal
              isOpen={isModalOpen}
              onClose={() => setIsModalOpen(false)}
              addProject={addProject}
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
