import { apiRequest } from "./apiClient";

/**
 * Fetch all users with the role "admin"
 */
export const getAdmins = async () => {
  const data = await apiRequest("index.php/getUsers", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch users");
  
  // Filter users to only include admins
  return (data.data ?? []).filter(user => user.role?.toLowerCase() === "admin");
};

/**
 * Add a new admin
 * @param {Object} adminData
 */
export const addAdmin = async (adminData) => {
  // Ensure role is always admin
  const payload = { ...adminData, role: "admin" };
  const res = await apiRequest("index.php/addUser", "POST", payload);
  if (res.status !== "success") throw new Error(res.message || "Failed to add admin");
  return res.user_id;
};

/**
 * Update an existing admin
 * @param {Object} adminData — must include user_id
 */
export const updateAdmin = async (adminData) => {
  if (!adminData.user_id) throw new Error("Missing user_id for update");
  const res = await apiRequest("index.php/updateUser", "POST", adminData);
  if (res.status !== "success") throw new Error(res.message || "Failed to update admin");
  return true;
};

/**
 * Delete an admin by user_id
 * @param {number} user_id
 */
export const deleteAdmin = async (user_id) => {
  const res = await apiRequest("index.php/deleteUser", "POST", { user_id });
  if (res.status !== "success") throw new Error(res.message || "Failed to delete admin");
  return true;
};
