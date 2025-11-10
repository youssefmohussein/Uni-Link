import { apiRequest } from "./apiClient";

/**
 * Fetch all users
 */
export const getUsers = async () => {
  const data = await apiRequest("index.php/getUsers", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch users");
  return data.data ?? [];
};

/**
 * Add a new user
 * @param {Object} userData
 */
export const addUser = async (userData) => {
  const res = await apiRequest("index.php/addUser", "POST", userData);
  if (res.status !== "success") throw new Error(res.message || "Failed to add user");
  return res.user_id;
};

/**
 * 🟢 Update an existing user
 * @param {Object} userData — must include user_id
 */
export const updateUser = async (userData) => {
  if (!userData.user_id) throw new Error("Missing user_id for update");
  const res = await apiRequest("index.php/updateUser", "POST", userData);
  if (res.status !== "success") throw new Error(res.message || "Failed to update user");
  return true;
};

/**
 * Delete a user by user_id
 * @param {number} user_id
 */
export const deleteUser = async (user_id) => {
  const res = await apiRequest("index.php/deleteUser", "POST", { user_id });
  if (res.status !== "success") throw new Error(res.message || "Delete failed");
  return true;
};

/**
 * Fetch all faculties
 */
export const getAllFaculties = async () => {
  const res = await apiRequest("index.php/getAllFaculties", "GET");
  return res.data ?? [];
};

/**
 * Fetch all majors
 */
export const getAllMajors = async () => {
  const res = await apiRequest("index.php/getAllMajors", "GET");
  return res.data ?? [];
};
