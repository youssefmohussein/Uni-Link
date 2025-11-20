import { apiRequest } from "./apiClient";

/* ============================================================
   PROFESSOR HANDLER
   Handles: users, professors, faculties, majors
============================================================ */

/**
 * Fetch ALL users (just like getUsers, optional)
 */
export const getUsers = async () => {
  const data = await apiRequest("index.php/getUsers", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch users");
  return data.data ?? [];
};

/**
 * Fetch ONLY professors
 * Uses the ProfessorController's getAllProfessors endpoint
 * Returns professor + user + faculty + major info
 */
export const getProfessors = async () => {
  const data = await apiRequest("index.php/getAllProfessors", "GET");
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
  const res = await apiRequest("index.php/addProfessor", "POST", professorData);
  if (res.status !== "success") throw new Error(res.message || "Failed to add professor");
  return true;
};

/**
 * Update existing professor record
 * @param {Object} professorData - must include professor_id, optional academic_rank/office_location
 */
export const updateProfessor = async (professorData) => {
  if (!professorData.professor_id) throw new Error("Missing professor_id for update");
  const res = await apiRequest("index.php/updateProfessor", "POST", professorData);
  if (res.status !== "success") throw new Error(res.message || "Failed to update professor");
  return true;
};

/**
 * Delete a professor (actually deletes user, Professor record cascades)
 * @param {number} user_id
 */
export const deleteProfessor = async (user_id) => {
  const res = await apiRequest("index.php/deleteUser", "POST", { user_id });
  if (res.status !== "success") throw new Error(res.message || "Failed to delete professor");
  return true;
};
