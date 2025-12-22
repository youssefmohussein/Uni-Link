import { apiRequest } from "./apiClient";

/**
 * Fetch all users
 */
export const getUsers = async () => {
  const data = await apiRequest("getUsers", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch users");

  const payload = data.data;
  if (Array.isArray(payload)) return payload;
  if (payload && Array.isArray(payload.data)) return payload.data;
  return [];
};

/**
 * Add a new user
 * @param {Object} userData
 */
export const addUser = async (userData) => {
  const res = await apiRequest("/api/user", "POST", userData);
  if (res.status !== "success") throw new Error(res.message || "Failed to add user");
  return res.data ? res.data.user_id : true;
};

/**
 * 🟢 Update an existing user
 * @param {Object} userData — must include user_id
 */
export const updateUser = async (userData) => {
  if (!userData.user_id) throw new Error("Missing user_id for update");
  const res = await apiRequest("/api/user", "PUT", userData);
  if (res.status !== "success") throw new Error(res.message || "Failed to update user");
  return true;
};

/**
 * Delete a user by user_id
 * @param {number} user_id
 */
export const deleteUser = async (user_id) => {
  const res = await apiRequest("/api/user", "DELETE", { user_id });
  if (res.status !== "success") throw new Error(res.message || "Delete failed");
  return true;
};

/**
 * Fetch all faculties
 */
export const getAllFaculties = async () => {
  const res = await apiRequest("getAllFaculties", "GET");
  return res.data ?? [];
};

/**
 * Fetch all majors
 */
export const getAllMajors = async () => {
  const res = await apiRequest("getAllMajors", "GET");
  // Handle nested data structure: data.data
  if (res.data?.data && Array.isArray(res.data.data)) {
    return res.data.data;
  }
  return res.data ?? [];
};

/**
 * Search users by username
 * @param {string} query
 */
export const searchUsers = async (query) => {
  const res = await apiRequest(`/api/users/search?q=${encodeURIComponent(query)}`, "GET");
  if (res.status !== "success") throw new Error(res.message || "Search failed");
  return res.data;
};



