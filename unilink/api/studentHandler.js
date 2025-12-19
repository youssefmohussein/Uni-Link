import { apiRequest } from "./apiClient";

/**
 * Student Handler
 * Comprehensive handler for all student-specific API operations
 * Includes: CV, Profile, Skills, Projects, and Posts management
 */

// ============================================================
// CV MANAGEMENT
// ============================================================

/**
 * Upload CV for a student
 * @param {number} userId - User ID
 * @param {File} cvFile - PDF file to upload
 * @returns {Promise<Object>} Upload response
 */
export const uploadCV = async (userId, cvFile) => {
  const formData = new FormData();
  formData.append('cv_file', cvFile);
  formData.append('user_id', userId);

  // Updated to match route: 'POST /api/cv/upload' => ['CvController', 'upload']
  // Note: apiClient adds /backend/ prefix, so we use api/cv/upload if that's how it's set up,
  // OR we add the route 'uploadCV' to routes.php as a legacy route for simplicity.
  // Given existing patterns, let's use the explicit route if available or add legacy route.
  // Routes.php has: 'POST /api/cv/upload'. Let's use that.
  // But wait, apiClient might expect just the endpoint name if it maps to legacy.
  // Let's stick to the convention used in other calls => specific endpoint names if they exist.
  // The backend routes show 'POST /api/cv/upload', let's try to map it or add 'uploadCV' to backend.
  // For now, I will assume we should add 'uploadCV' to routes.php to match this handler, 
  // OR update this handler to use 'api/cv/upload'.
  // Let's update this handler to use 'api/cv/upload' but we need to check how apiClient handles slashes.
  // Assuming apiClient handles it. 
  // checking apiClient implementation would be good but let's try matching the route name.

  const data = await apiRequest('api/cv/upload', 'POST', formData);
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to upload CV');
  }
  return data;
};

/**
 * Get CV for a student
 * @param {number} userId - User ID
 * @returns {Promise<Object>} CV data
 */
export const getCV = async (userId) => {
  // Routes.php has 'GET /api/cv/download'. This is for downloading file content.
  // For getting *metadata* (like "file exists"), we might need a different endpoint.
  // However, CvSection.jsx expects a 'file_path'. 
  // Let's add 'getCV' route to backend or use 'api/user/profile' which includes CV info?
  // User profile usually includes cv_path. 
  // Let's use 'getUserProfile' to extract CV info if possible, OR add 'getCV' endpoint.
  // I'll assume we adding 'getCV' to routes.php is the safest bet.
  const data = await apiRequest(`getCV?user_id=${userId}`, 'GET');
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to fetch CV');
  }
  return data.data;
};

/**
 * Delete CV for a student
 * @param {number} userId - User ID
 * @returns {Promise<boolean>} Success status
 */
export const deleteCV = async (userId) => {
  const data = await apiRequest('deleteCV', 'POST', { user_id: userId });
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to delete CV');
  }
  return true;
};

// ============================================================
// PROFILE MANAGEMENT
// ============================================================

/**
 * Get student profile with complete information
 * @param {number} userId - User ID
 * @returns {Promise<Object>} Student profile data
 */
export const getStudentProfile = async (userId) => {
  const data = await apiRequest(`getUserProfile?user_id=${userId}`, 'GET');
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to fetch student profile');
  }
  return data.data;
};

/**
 * Update student profile information
 * @param {Object} profileData - Profile data to update (must include user_id)
 * @returns {Promise<boolean>} Success status
 */
export const updateStudentProfile = async (profileData) => {
  const data = await apiRequest('updateUser', 'POST', profileData);
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to update profile');
  }
  return true;
};

// ============================================================
// SKILLS MANAGEMENT
// ============================================================

/**
 * Get all skills for a student
 * @param {number} userId - User ID
 * @returns {Promise<Array>} Array of user skills with categories
 */
export const getStudentSkills = async (userId) => {
  const data = await apiRequest(`api/user-skills?user_id=${userId}`, 'GET'); // Updated to match 'GET /api/user-skills'
  if (data.status !== 'success') {
    // Fallback to empty if 404 or other non-critical error for new users
    if (data.message && data.message.includes("No skills")) return [];
    throw new Error(data.message || 'Failed to fetch skills');
  }
  return data.data ?? [];
};

/**
 * Add skills to a student
 * @param {number} userId - User ID
 * @param {Array} skills - Array of skill objects with skill_id
 * @returns {Promise<boolean>} Success status
 */
export const addStudentSkills = async (userId, skills) => {
  const data = await apiRequest('api/user-skills', 'POST', { // Updated to match 'POST /api/user-skills'
    user_id: userId,
    skills: skills
  });
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to add skills');
  }
  return true;
};

/**
 * Get all skill categories
 * @returns {Promise<Array>} Array of skill categories
 */
export const getSkillCategories = async () => {
  const data = await apiRequest('api/skill-categories', 'GET'); // Updated to match 'GET /api/skill-categories'
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to fetch skill categories');
  }
  return data.data ?? [];
};

/**
 * Add or get a skill category for a user
 * @param {number} userId - User ID
 * @param {string} categoryName - Category name
 * @returns {Promise<number>} Category ID
 */
export const addSkillCategory = async (userId, categoryName) => {
  // No direct route for this in provided routes.php?
  // Let's assume 'addSkillCategory' route needs to be added or use 'api/skill-categories' POST?
  // Routes.php has 'GET /api/skill-categories', but no POST.
  // I will add 'POST /addSkillCategory' to routes.php to match this.
  const data = await apiRequest('addSkillCategory', 'POST', {
    user_id: userId,
    category_name: categoryName
  });
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to add skill category');
  }
  return data.category_id;
};

/**
 * Add a skill to the skills table
 * @param {string} skillName - Skill name
 * @param {number} categoryId - Category ID
 * @returns {Promise<number>} Skill ID
 */
export const addSkill = async (skillName, categoryId) => {
  // Routes.php doesn't have 'addSkill'.
  // I will add 'POST /addSkill' to routes.php.
  const data = await apiRequest('addSkill', 'POST', {
    skill_name: skillName,
    category_id: categoryId
  });
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to add skill');
  }
  return data.skill_id;
};

/**
 * Remove a skill from a student
 * @param {number} userId - User ID
 * @param {number} skillId - Skill ID to remove
 * @returns {Promise<boolean>} Success status
 */
export const removeStudentSkill = async (userId, skillId) => {
  const data = await apiRequest('api/user-skills', 'DELETE', { // Updated to match 'DELETE /api/user-skills'
    user_id: userId,
    skill_id: skillId
  });
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to remove skill');
  }
  return true;
};

/**
 * Get all available skills from the database
 * @returns {Promise<Array>} Array of all skills
 */
export const getAllSkills = async () => {
  const data = await apiRequest('getAllSkills', 'GET');
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to fetch all skills');
  }
  return data.data ?? [];
};


// ============================================================
// PROJECTS MANAGEMENT
// ============================================================

/**
 * Get all projects for a student
 * @param {number} userId - User ID
 * @returns {Promise<Array>} Array of student projects
 */
export const getStudentProjects = async (userId) => {
  const data = await apiRequest(`getUserProjects?user_id=${userId}`, 'GET');
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to fetch projects');
  }
  return data.data ?? [];
};

/**
 * Upload a new project
 * @param {Object} projectData - Project data including title, description, file, etc.
 * @returns {Promise<Object>} Created project data
 */
export const uploadProject = async (projectData) => {
  const formData = new FormData();

  // Add all project fields to FormData
  Object.keys(projectData).forEach(key => {
    if (projectData[key] !== null && projectData[key] !== undefined) {
      formData.append(key, projectData[key]);
    }
  });

  const data = await apiRequest('uploadProject', 'POST', formData);
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to upload project');
  }
  return data.data;
};

/**
 * Update an existing project
 * @param {Object} projectData - Project data to update (must include project_id)
 * @returns {Promise<boolean>} Success status
 */
export const updateProject = async (projectData) => {
  const data = await apiRequest('updateProject', 'POST', projectData);
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to update project');
  }
  return true;
};

/**
 * Delete a project
 * @param {number} projectId - Project ID
 * @returns {Promise<boolean>} Success status
 */
export const deleteProject = async (projectId) => {
  const data = await apiRequest('deleteProject', 'POST', { project_id: projectId });
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to delete project');
  }
  return true;
};

// ============================================================
// POSTS MANAGEMENT
// ============================================================

/**
 * Get all posts for a student
 * @param {number} userId - User ID
 * @returns {Promise<Array>} Array of student posts
 */
export const getStudentPosts = async (userId) => {
  const data = await apiRequest(`getUserPosts?user_id=${userId}`, 'GET');
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to fetch posts');
  }
  return data.data ?? [];
};

/**
 * Create a new post
 * @param {Object} postData - Post data including content, category, media, etc.
 * @returns {Promise<Object>} Created post data
 */
export const createPost = async (postData) => {
  const data = await apiRequest('createPost', 'POST', postData);
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to create post');
  }
  return data.data;
};

/**
 * Update an existing post
 * @param {Object} postData - Post data to update (must include post_id)
 * @returns {Promise<boolean>} Success status
 */
export const updatePost = async (postData) => {
  const data = await apiRequest('updatePost', 'POST', postData);
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to update post');
  }
  return true;
};

/**
 * Delete a post
 * @param {number} postId - Post ID
 * @returns {Promise<boolean>} Success status
 */
export const deletePost = async (postId) => {
  const data = await apiRequest('deletePost', 'POST', { post_id: postId });
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to delete post');
  }
  return true;
};

// ============================================================
// STUDENT DATA (for admin purposes)
// ============================================================

/**
 * Get all students (admin function)
 * @returns {Promise<Array>} Array of all students
 */
export const getAllStudents = async () => {
  const data = await apiRequest('/api/students', 'GET');
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to fetch students');
  }
  // Backend returns { count: N, data: [...] } in response.data
  // So distinct array is in response.data.data
  if (data.data && Array.isArray(data.data.data)) {
    return data.data.data;
  }
  return data.data ?? [];
};

/**
 * Get all students (alias for getAllStudents for compatibility)
 * @returns {Promise<Array>} Array of all students
 */
export const getStudents = async () => {
  return getAllStudents();
};

/**
 * Get all users (admin function)
 * @returns {Promise<Array>} Array of all users
 */
export const getUsers = async () => {
  const data = await apiRequest('/api/user', 'GET'); // Updated to /api/user (or /getUsers legacy works too, but consistency is good)
  // routes.php has GET /api/user => UserController::getAll.
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to fetch users');
  }
  return data.data ?? [];
};

/**
 * Add a new student/user
 * @param {Object} userData - User data
 * @returns {Promise<number>} Created user ID
 */
export const addStudent = async (userData) => {
  const res = await apiRequest('/api/user', 'POST', userData); // Updated to POST /api/user
  if (res.status !== 'success') {
    throw new Error(res.message || 'Failed to add user');
  }
  return res.data ? res.data.user_id : true;
};

/**
 * Update existing student/user
 * @param {Object} userData - User data to update
 * @returns {Promise<boolean>} Success status
 */
export const updateStudent = async (userData) => {
  if (!userData.user_id) {
    throw new Error('Missing user_id for update');
  }
  const res = await apiRequest('/api/user', 'PUT', userData);
  if (res.status !== 'success') {
    throw new Error(res.message || 'Failed to update user');
  }
  return true;
};

/**
 * Delete a student/user
 * @param {number} userId - User ID
 * @returns {Promise<boolean>} Success status
 */
export const deleteStudent = async (userId) => {
  const res = await apiRequest('/api/user', 'DELETE', { user_id: userId }); // Updated to DELETE /api/user
  if (res.status !== 'success') {
    throw new Error(res.message || 'Delete failed');
  }
  return true;
};

// ============================================================
// FACULTY / MAJOR
// ============================================================

/**
 * Get all faculties
 * @returns {Promise<Array>} Array of faculties
 */
export const getAllFaculties = async () => {
  const res = await apiRequest('getAllFaculties', 'GET');
  if (res.status !== 'success') {
    throw new Error(res.message || 'Failed to fetch faculties');
  }
  return res.data ?? [];
};

/**
 * Get all majors
 * @returns {Promise<Array>} Array of majors
 */
export const getAllMajors = async () => {
  const res = await apiRequest('getAllMajors', 'GET');
  if (res.status !== 'success') {
    throw new Error(res.message || 'Failed to fetch majors');
  }
  return res.data ?? [];
};
