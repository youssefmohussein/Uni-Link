import { apiRequest } from "./apiClient";

/**
 * Fetch all users with the role "professor"
 */
export const getProfessors = async () => {
  const data = await apiRequest("index.php/getUsers", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch users");

  // Filter only users whose role is "professor"
  return (data.data ?? []).filter(user => user.role?.toLowerCase() === "professor");
};

/**
 * Add a new professor
 * @param {Object} professorData
 */
export const addProfessor = async (professorData) => {
  // Ensure role is always professor
  const payload = { ...professorData, role: "professor" };
  const res = await apiRequest("index.php/addUser", "POST", payload);
  if (res.status !== "success") throw new Error(res.message || "Failed to add professor");
  return res.user_id;
};

/**
 * Update an existing professor
 * @param {Object} professorData — must include user_id
 */
export const updateProfessor = async (professorData) => {
  if (!professorData.user_id) throw new Error("Missing user_id for update");
  const res = await apiRequest("index.php/updateUser", "POST", professorData);
  if (res.status !== "success") throw new Error(res.message || "Failed to update professor");
  return true;
};

/**
 * Delete a professor by user_id
 * @param {number} user_id
 */
export const deleteProfessor = async (user_id) => {
  const res = await apiRequest("index.php/deleteUser", "POST", { user_id });
  if (res.status !== "success") throw new Error(res.message || "Failed to delete professor");
  return true;
};


export const getAllFaculties = async () => {
  const res = await apiRequest("index.php/getAllFaculties", "GET");
  if (res.status !== "success") throw new Error(res.message || "Failed to fetch faculties");
  return res.data ?? [];
};


export const getAllMajors = async () => {
  const res = await apiRequest("index.php/getAllMajors", "GET");
  if (res.status !== "success") throw new Error(res.message || "Failed to fetch majors");
  return res.data ?? [];
};