import { apiRequest } from "./apiClient";

/**
 * Get all projects for grading
 * @param {number|null} facultyId - Optional faculty filter
 * @param {string} status - Filter: 'all', 'graded', 'not_graded'
 * @returns {Promise<Array>} Projects list
 */
export const getProjects = async (facultyId = null, status = 'all') => {
    let url = `api/grading/projects?status=${status}`;
    if (facultyId) {
        url += `&faculty_id=${facultyId}`;
    }
    const data = await apiRequest(url, 'GET');
    return data.data || [];
};

/**
 * Grade a project
 * @param {number} projectId - Project ID
 * @param {number} grade - Grade (0-100)
 * @param {string|null} comments - Optional comments
 * @param {string} status - Project status (APPROVED, REJECTED, PENDING)
 * @returns {Promise<Object>} Response
 */
export const gradeProject = async (projectId, grade, comments = null, status = 'APPROVED') => {
    const data = await apiRequest('api/grading/grade', 'POST', {
        project_id: projectId,
        grade,
        comments,
        status
    });
    return data;
};
