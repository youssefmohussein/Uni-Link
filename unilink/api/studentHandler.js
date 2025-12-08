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

  const data = await apiRequest('index.php/uploadCV', 'POST', formData);
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
  const data = await apiRequest(`index.php/getCV?user_id=${userId}`, 'GET');
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
  const data = await apiRequest('index.php/deleteCV', 'POST', { user_id: userId });
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
  const data = await apiRequest(`index.php/getUserProfile?user_id=${userId}`, 'GET');
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
  const data = await apiRequest('index.php/updateUser', 'POST', profileData);
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
  const data = await apiRequest('index.php/getUserSkills', 'POST', { user_id: userId });
  if (data.status !== 'success') {
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
  const data = await apiRequest('index.php/addUserSkills', 'POST', {
    user_id: userId,
    skills: skills
  });
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to add skills');
  }
  return true;
};

/**
 * Remove a skill from a student
 * @param {number} userId - User ID
 * @param {number} skillId - Skill ID to remove
 * @returns {Promise<boolean>} Success status
 */
export const removeStudentSkill = async (userId, skillId) => {
  const data = await apiRequest('index.php/removeUserSkill', 'POST', {
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
  const data = await apiRequest('index.php/getAllSkills', 'GET');
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to fetch all skills');
  }
  return data.data ?? [];
};

/**
 * Get all skill categories
 * @returns {Promise<Array>} Array of skill categories
 */
export const getSkillCategories = async () => {
  const data = await apiRequest('index.php/getAllSkillCategories', 'GET');
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to fetch skill categories');
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
  const data = await apiRequest(`index.php/getUserProjects?user_id=${userId}`, 'GET');
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

  const data = await apiRequest('index.php/uploadProject', 'POST', formData);
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
  const data = await apiRequest('index.php/updateProject', 'POST', projectData);
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
  const data = await apiRequest('index.php/deleteProject', 'POST', { project_id: projectId });
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
  const data = await apiRequest(`index.php/getUserPosts?user_id=${userId}`, 'GET');
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
  const data = await apiRequest('index.php/createPost', 'POST', postData);
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
  const data = await apiRequest('index.php/updatePost', 'POST', postData);
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
  const data = await apiRequest('index.php/deletePost', 'POST', { post_id: postId });
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
  const data = await apiRequest('index.php/getAllStudents', 'GET');
  if (data.status !== 'success') {
    throw new Error(data.message || 'Failed to fetch students');
  }
  return data.data ?? [];
};

/**
 * Get all users (admin function)
 * @returns {Promise<Array>} Array of all users
 */
export const getUsers = async () => {
  const data = await apiRequest('index.php/getUsers', 'GET');
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
  const res = await apiRequest('index.php/addUser', 'POST', userData);
  if (res.status !== 'success') {
    throw new Error(res.message || 'Failed to add user');
  }
  return res.user_id;
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
  const res = await apiRequest('index.php/updateUser', 'POST', userData);
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
  const res = await apiRequest('index.php/deleteUser', 'POST', { user_id: userId });
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
  const res = await apiRequest('index.php/getAllFaculties', 'GET');
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
  const res = await apiRequest('index.php/getAllMajors', 'GET');
  if (res.status !== 'success') {
    throw new Error(res.message || 'Failed to fetch majors');
  }
  return res.data ?? [];
};