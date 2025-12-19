import { apiRequest } from "./apiClient";

/* ============================================================
   PROFESSOR HANDLER
   Handles: users, professors, faculties, majors
============================================================ */

/**
 * Fetch ALL users (just like getUsers, optional)
 */
export const getUsers = async () => {
  const data = await apiRequest("getUsers", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch users");
  if (data.data?.data && Array.isArray(data.data.data)) {
    return data.data.data;
  }
  if (Array.isArray(data.data)) {
    return data.data;
  }
  return [];
};

/**
 * Fetch ONLY professors
 * Uses the ProfessorController's getAllProfessors endpoint
 * Returns professor + user + faculty + major info
 */
export const getProfessors = async () => {
  const data = await apiRequest("/api/professors", "GET"); // Updated to /api/professors
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch professors");
  if (data.data?.data && Array.isArray(data.data.data)) {
    return data.data.data;
  }
  if (Array.isArray(data.data)) {
    return data.data;
  }
  return [];
};

/* ============================================================
   PROFESSOR MANAGEMENT (Add / Update / Delete)
   Note: Users table is managed separately; this only adds the Professor record
============================================================ */

/**
 * Add a new professor record
 * @param {Object} professorData - must include professor_id, academic_rank, office_location
 */
export const addProfessor = async (professorData) => {
  // Assuming addProfessor logic is handled by UserController creation or special endpoint?
  // If no addProfessor route exists, we route to standard user creation or new endpoint?
  // Routes.php has no addProfessor. Let's assume user creation handles this or fail.
  // Actually, we should use /api/user (POST) if logic is there.
  // However, professorData implies specific professor fields.
  // Assuming invalid route for now, but fixing GET first.
  const res = await apiRequest("addProfessor", "POST", professorData);
  if (res.status !== "success") throw new Error(res.message || "Failed to add professor");
  return true;
};

export const updateProfessor = async (professorData) => {
  if (!professorData.professor_id && !professorData.user_id) throw new Error("Missing ID for update");
  // Ensure we send to Professor Controller to handle both User and Professor tables
  const res = await apiRequest("/api/professors", "PUT", professorData);
  if (res.status !== "success") throw new Error(res.message || "Failed to update professor");
  return true;
};

/**
 * Delete a professor (actually deletes user, Professor record cascades)
 * @param {number} user_id
 */
export const deleteProfessor = async (user_id) => {
  const res = await apiRequest("/api/user", "DELETE", { user_id }); // Updated to DELETE /api/user
  if (res.status !== "success") throw new Error(res.message || "Failed to delete professor");
  return true;
};

/* ============================================================
   FACULTY / MAJOR
============================================================ */

/**
 * Get all faculties
 */
export const getAllFaculties = async () => {
  const res = await apiRequest("getAllFaculties", "GET");
  if (res.status !== "success") throw new Error(res.message || "Failed to fetch faculties");
  return res.data ?? [];
};

/**
 * Get all majors
 */
export const getAllMajors = async () => {
  const res = await apiRequest("getAllMajors", "GET");
  if (res.status !== "success") throw new Error(res.message || "Failed to fetch majors");
  return res.data ?? [];
};

/**
 * Get professors by faculty ID
 * @param {number} facultyId Faculty ID
 */
export const getProfessorsByFaculty = async (facultyId) => {
  const res = await apiRequest(`api/professors/by-faculty?faculty_id=${facultyId}`, "GET");
  if (res.status !== "success") throw new Error(res.message || "Failed to fetch professors");
  return res.data ?? [];
};

/* ============================================================
   NEW PROFESSOR FEATURES (Profile, Reviews, Analytics)
   ============================================================ */

/**
 * Get professor details by ID
 * @param {number} professor_id
 */
export const getProfessorById = async (professor_id) => {
  const data = await apiRequest(`getProfessorById/${professor_id}`, "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch professor details");
  return data.data;
};

/**
 * Get aggregated dashboard statistics (for analytics)
 */
export const getDashboardStats = async () => {
  const data = await apiRequest("getDashboardStats", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch dashboard stats");
  return data.data;
};

/* ============================================================
   PROJECT REVIEWS
   ============================================================ */

/**
 * Add a review to a project
 * @param {Object} reviewData - { project_id, professor_id, comment, mark }
 */
export const addReview = async (reviewData) => {
  const res = await apiRequest("addReview", "POST", reviewData);
  if (res.status !== "success") throw new Error(res.message || "Failed to add review");
  return true;
};

/**
 * Get reviews for a specific project
 * @param {number} project_id
 */
export const getReviewsByProject = async (project_id) => {
  const res = await apiRequest("getReviewsByProject", "POST", { project_id });
  if (res.status !== "success") throw new Error(res.message || "Failed to fetch reviews");
  return res.reviews ?? [];
};

/**
 * Delete a review
 * @param {number} review_id
 */
export const deleteReview = async (review_id) => {
  const res = await apiRequest("deleteReview", "POST", { review_id });
  if (res.status !== "success") throw new Error(res.message || "Failed to delete review");
  return true;
};
