// Profile API Service
// Handles profile-related API calls including ProfileFacade

import { apiCall } from '../config/api';

/**
 * Get full user profile (uses ProfileFacade on backend)
 * Returns aggregated data: user, skills, projects, posts, cv, stats
 * @param {number} userId - User ID (optional, defaults to current user)
 * @returns {Promise} Complete profile data
 */
export const getFullProfile = async (userId = null) => {
    const endpoint = userId
        ? `/api/profile/full?user_id=${userId}`
        : '/api/profile/full';

    const response = await apiCall(endpoint);
    return response.json();
};

/**
 * Get public profile (limited information)
 * @param {number} userId - User ID
 * @returns {Promise} Public profile data
 */
export const getPublicProfile = async (userId) => {
    const response = await apiCall(`/api/profile/public?user_id=${userId}`);
    return response.json();
};

/**
 * Get user profile (basic info)
 * @returns {Promise} User profile
 */
export const getUserProfile = async () => {
    const response = await apiCall('/api/user/profile');
    return response.json();
};

/**
 * Update user profile
 * @param {object} profileData - Profile data to update
 * @returns {Promise} Response
 */
export const updateProfile = async (profileData) => {
    const response = await apiCall('/api/user', {
        method: 'PUT',
        body: JSON.stringify(profileData)
    });
    return response.json();
};

export default {
    getFullProfile,
    getPublicProfile,
    getUserProfile,
    updateProfile
};
