import { apiRequest } from "./apiClient";

/* ============================================================
   STUDENT HANDLER
   Handles: users, students, faculties, majors
   ============================================================ */

/**
 * Fetch ALL users (This is the original getUsers)
 */
export const getUsers = async () => {
  const data = await apiRequest("index.php/getUsers", "GET");
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch users");
  return data.data ?? [];
};

/**
 * Fetch ONLY students (role === "student")
 * --- THIS FUNCTION IS NOW FIXED ---
 * It now calls /getAllStudents to get data from the Student table
 * (joined with Users) as defined in your StudentController.php.
 * This will include year, gpa, points, etc.
 */
export const getStudents = async () => {
  // CHANGED: This now points to your correct PHP endpoint
  const data = await apiRequest("index.php/getAllStudents", "GET"); 
  if (data.status !== "success") throw new Error(data.message || "Failed to fetch students");

  // CHANGED: No client-side filtering needed, the API gives us the right data.
  return data.data ?? [];
};

/* ============================================================
   USER MANAGEMENT (Add / Update / Delete)
   (Note: These functions manage the 'Users' table. 
    Your StudentController handles the 'Student' table.)
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