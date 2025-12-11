import { apiRequest } from "./apiClient";

/**
 * Profile Handler
 * Handles all profile-related API operations
 */

/**
 * Get user profile with complete information
 * @param {number} userId - User ID
 * @returns {Promise<Object>} User profile data
 */
export const getUserProfile = async (userId) => {
    const data = await apiRequest(`getUserProfile?user_id=${userId}`, "GET");
    if (data.status !== "success") {
        throw new Error(data.message || "Failed to fetch user profile");
    }
    return data.data;
};

/**
 * Get all projects for a specific user
 * @param {number} userId - User ID
 * @returns {Promise<Array>} Array of user projects
 */
export const getUserProjects = async (userId) => {
    const data = await apiRequest(`getUserProjects?user_id=${userId}`, "GET");
    if (data.status !== "success") {
        throw new Error(data.message || "Failed to fetch user projects");
    }
    return data.data ?? [];
};

/**
 * Get all posts for a specific user
 * @param {number} userId - User ID
 * @returns {Promise<Array>} Array of user posts
 */
export const getUserPosts = async (userId) => {
    const data = await apiRequest(`getUserPosts?user_id=${userId}`, "GET");
    if (data.status !== "success") {
        throw new Error(data.message || "Failed to fetch user posts");
    }
    return data.data ?? [];
};

/**
 * Get user skills (uses existing endpoint)
 * @param {number} userId - User ID
 * @returns {Promise<Array>} Array of user skills
 */
export const getUserSkills = async (userId) => {
    const data = await apiRequest("getUserSkills", "POST", { user_id: userId });
    if (data.status !== "success") {
        throw new Error(data.message || "Failed to fetch user skills");
    }
    return data.data ?? [];
};

/**
 * Add skills to user
 * @param {number} userId - User ID
 * @param {Array} skills - Array of skill objects with skill_id
 * @returns {Promise<boolean>} Success status
 */
export const addUserSkills = async (userId, skills) => {
    const data = await apiRequest("addUserSkills", "POST", {
        user_id: userId,
        skills: skills,
    });
    if (data.status !== "success") {
        throw new Error(data.message || "Failed to add skills");
    }
    return true;
};

/**
 * Update user profile information
 * @param {Object} userData - User data to update
 * @returns {Promise<boolean>} Success status
 */
export const updateUserProfile = async (userData) => {
    const data = await apiRequest("updateUser", "POST", userData);
    if (data.status !== "success") {
        throw new Error(data.message || "Failed to update profile");
    }
    return true;
};
