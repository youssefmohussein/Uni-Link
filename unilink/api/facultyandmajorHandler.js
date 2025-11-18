import { apiRequest } from "./apiClient";

/* ============================================================
   FACULTIES
   ============================================================ */

/**
 * Fetch all faculties
 */
export const getAllFaculties = async () => {
  const res = await apiRequest("index.php/getAllFaculties", "GET");
  if (res.status !== "success")
    throw new Error(res.message || "Failed to fetch faculties");

  return res.data ?? [];
};

/**
 * Add a faculty
 * @param {Object} facultyData
 */
export const addFaculty = async (facultyData) => {
  const res = await apiRequest("index.php/addFaculty", "POST", facultyData);
  if (res.status !== "success")
    throw new Error(res.message || "Failed to add faculty");

  return res.faculty_id; // assuming backend returns this
};

/**
 * Update a faculty
 * @param {Object} facultyData — must include faculty_id
 */
export const updateFaculty = async (facultyData) => {
  if (!facultyData.faculty_id)
    throw new Error("Missing faculty_id for update");

  const res = await apiRequest("index.php/updateFaculty", "POST", facultyData);
  if (res.status !== "success")
    throw new Error(res.message || "Failed to update faculty");

  return true;
};

/**
 * Delete faculty by id
 */
export const deleteFaculty = async (faculty_id) => {
  const res = await apiRequest("index.php/deleteFaculty", "POST", { faculty_id });
  if (res.status !== "success")
    throw new Error(res.message || "Failed to delete faculty");

  return true;
};


/* ============================================================
   MAJORS
   ============================================================ */

/**
 * Fetch all majors
 */
export const getAllMajors = async () => {
  const res = await apiRequest("index.php/getAllMajors", "GET");
  if (res.status !== "success")
    throw new Error(res.message || "Failed to fetch majors");

  return res.data ?? [];
};

/**
 * Add a major
 * @param {Object} majorData
 */
export const addMajor = async (majorData) => {
  const res = await apiRequest("index.php/addMajor", "POST", majorData);
  if (res.status !== "success")
    throw new Error(res.message || "Failed to add major");

  return res.major_id; // assuming backend returns this
};

/**
 * Update a major
 * @param {Object} majorData — must include major_id
 */
export const updateMajor = async (majorData) => {
  if (!majorData.major_id)
    throw new Error("Missing major_id for update");

  const res = await apiRequest("index.php/updateMajor", "POST", majorData);
  if (res.status !== "success")
    throw new Error(res.message || "Failed to update major");

  return true;
};

/**
 * Delete a major by id
 */
export const deleteMajor = async (major_id) => {
  const res = await apiRequest("index.php/deleteMajor", "POST", { major_id });
  if (res.status !== "success")
    throw new Error(res.message || "Failed to delete major");

  return true;
};
