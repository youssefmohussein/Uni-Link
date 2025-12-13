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
  return data.data ?? [];
};

/**
 * Fetch ONLY professors
 * Uses the ProfessorController's getAllProfessors endpoint
 * Returns professor + user + faculty + major info
 */
export const getProfessors = async () => {
  const data = await apiRequest("getAllProfessors", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch professors");
  return data.data ?? [];
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
  const res = await apiRequest("addProfessor", "POST", professorData);
  if (res.status !== "success") throw new Error(res.message || "Failed to add professor");
  return true;
};

/**
 * Update existing professor record
 * @param {Object} professorData - must include professor_id, optional academic_rank/office_location
 */
export const updateProfessor = async (professorData) => {
  if (!professorData.professor_id) throw new Error("Missing professor_id for update");
  const res = await apiRequest("updateProfessor", "POST", professorData);
  if (res.status !== "success") throw new Error(res.message || "Failed to update professor");
  return true;
};

/**
 * Delete a professor (actually deletes user, Professor record cascades)
 * @param {number} user_id
 */
export const deleteProfessor = async (user_id) => {
  const res = await apiRequest("deleteUser", "POST", { user_id });
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
