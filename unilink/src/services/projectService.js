// Project API Service
// Handles project-related API calls including Command Pattern integration

import { apiCall } from '../config/api';

/**
 * Get all projects
 * @returns {Promise} Projects data
 */
export const getAllProjects = async () => {
    const response = await apiCall('/api/projects');
    return response.json();
};

/**
 * Get user's projects
 * @returns {Promise} User projects
 */
export const getUserProjects = async () => {
    const response = await apiCall('/getUserProjects');
    return response.json();
};

/**
 * Upload a project
 * @param {object} projectData - Project data including file
 * @returns {Promise} Response
 */
export const uploadProject = async (projectData) => {
    const formData = new FormData();
    Object.keys(projectData).forEach(key => {
        formData.append(key, projectData[key]);
    });

    const response = await apiCall('/uploadProject', {
        method: 'POST',
        headers: {}, // Remove Content-Type for FormData
        body: formData
    });
    return response.json();
};

/**
 * Update a project
 * @param {object} projectData - Project data to update
 * @returns {Promise} Response
 */
export const updateProject = async (projectData) => {
    const response = await apiCall('/updateProject', {
        method: 'POST',
        body: JSON.stringify(projectData)
    });
    return response.json();
};

/**
 * Delete a project
 * @param {number} projectId - Project ID
 * @returns {Promise} Response
 */
export const deleteProject = async (projectId) => {
    const response = await apiCall('/deleteProject', {
        method: 'POST',
        body: JSON.stringify({ project_id: projectId })
    });
    return response.json();
};

/**
 * Approve a project (Professor only - uses Command Pattern)
 * @param {number} projectId - Project ID
 * @param {number} score - Project score
 * @param {string} comment - Review comment
 * @returns {Promise} Response
 */
export const approveProject = async (projectId, score = null, comment = null) => {
    const response = await apiCall('/api/projects/approve', {
        method: 'POST',
        body: JSON.stringify({ project_id: projectId, score, comment })
    });
    return response.json();
};

/**
 * Reject a project (Professor only - uses Command Pattern)
 * @param {number} projectId - Project ID
 * @param {string} comment - Rejection reason
 * @returns {Promise} Response
 */
export const rejectProject = async (projectId, comment = null) => {
    const response = await apiCall('/api/projects/reject', {
        method: 'POST',
        body: JSON.stringify({ project_id: projectId, comment })
    });
    return response.json();
};

/**
 * Grade a project (Professor only - uses Command Pattern)
 * @param {number} projectId - Project ID
 * @param {number} grade - Project grade
 * @param {string} comment - Grading comment
 * @returns {Promise} Response
 */
export const gradeProject = async (projectId, grade, comment = null) => {
    const response = await apiCall('/api/projects/grade', {
        method: 'POST',
        body: JSON.stringify({ project_id: projectId, grade, comment })
    });
    return response.json();
};

export default {
    getAllProjects,
    getUserProjects,
    uploadProject,
    updateProject,
    deleteProject,
    approveProject,
    rejectProject,
    gradeProject
};
