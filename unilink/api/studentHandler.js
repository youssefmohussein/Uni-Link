import { apiRequest } from "./apiClient";

/* ============================================================
   STUDENT HANDLER
   Handles: users, students, faculties, majors
   ============================================================ */

/**
 * Fetch ALL users
 */
export const getUsers = async () => {
  const data = await apiRequest("index.php/getUsers", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch users");
  return data.data ?? [];
};

/**
 * Fetch ONLY students (role === "student")
 */
export const getStudents = async () => {
  const data = await apiRequest("index.php/getUsers", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch students");

  const students = (data.data ?? []).filter(
    user => user.role?.toLowerCase() === "student"
  );

  return students;
};

/* ============================================================
   USER MANAGEMENT (Add / Update / Delete)
   ============================================================ */

/**
 * Add a new user
 */
export const addStudent = async (userData) => {
  const res = await apiRequest("index.php/addUser", "POST", userData);
  if (res.status !== "success") throw new Error(res.message || "Failed to add user");
  return res.user_id;
};

/**
 * Update existing user
 */
export const updateStudent = async (userData) => {
  if (!userData.user_id) throw new Error("Missing user_id for update");
  const res = await apiRequest("index.php/updateUser", "POST", userData);
  if (res.status !== "success") throw new Error(res.message || "Failed to update user");
  return true;
};

/**
 * Delete a user
 */
export const deleteStudent = async (user_id) => {
  const res = await apiRequest("index.php/deleteUser", "POST", { user_id });
  if (res.status !== "success") throw new Error(res.message || "Delete failed");
  return true;
};

/* ============================================================
   FACULTY / MAJOR
   ============================================================ */

/**
 * Get all faculties
 */
export const getAllFaculties = async () => {
  const res = await apiRequest("index.php/getAllFaculties", "GET");
  if (res.status !== "success") throw new Error(res.message || "Failed to fetch faculties");
  return res.data ?? [];
};

/**
 * Get all majors
 */
export const getAllMajors = async () => {
  const res = await apiRequest("index.php/getAllMajors", "GET");
  if (res.status !== "success") throw new Error(res.message || "Failed to fetch majors");
  return res.data ?? [];
};
