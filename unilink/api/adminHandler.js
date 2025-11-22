import { API_BASE_URL } from "../config/api";
import { apiRequest } from "./apiClient";


export const getUsers = async () => {
  const data = await apiRequest("index.php/getUsers", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch users");
  return data.data ?? [];
};


export const getAdmins = async () => {
  const data = await apiRequest("index.php/getAllAdmins", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to get admins");
  return data.data ?? [];
};


export const updateAdmin = async (adminData) => {
  if (!adminData.admin_id) throw new Error("Missing admin_id for admin update");

  const data = await apiRequest("index.php/updateAdmin", "POST", adminData);
  if (data.status !== "success") throw new Error(data.message || "Failed to update admin");

  return true;
};
