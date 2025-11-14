import React, { useState, useEffect } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import StudentsTable from "../../Components/Admin_Components/StudentsTable";
import StudentForm from "../../Components/Admin_Components/StudentForm";
import { motion } from "framer-motion";
import * as studentHandler from "../../../api/studentHandler";

export default function AdminStudentsPage() {
  const [students, setStudents] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [query, setQuery] = useState("");
  const [editingStudent, setEditingStudent] = useState(null);

  const [faculties, setFaculties] = useState([]);
  const [majors, setMajors] = useState([]);

  // Fetch students
  const getStudentsFromService = async () => {
    try {
      setLoading(true);
      const data = await studentHandler.getStudents();
      setStudents(data);
      setError(null);
    } catch (err) {
      setError("Failed to fetch students");
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  // Fetch faculties and majors
  const getFacultiesAndMajors = async () => {
    try {
      const [facData, majData] = await Promise.all([
        studentHandler.getAllFaculties(),
        studentHandler.getAllMajors(),
      ]);
      setFaculties(facData);
      setMajors(majData);
    } catch (err) {
      console.error("Failed to load faculties/majors", err);
    }
  };

  useEffect(() => {
    getStudentsFromService();
    getFacultiesAndMajors();
  }, []);

  // Update student
  const handleUpdateStudent = async (formData) => {
    try {
      await studentHandler.updateStudent(formData);
      await getStudentsFromService();
      setEditingStudent(null);
      alert("Student updated successfully");
    } catch (err) {
      console.error(err);
      alert("Failed to update student: " + (err.message || ""));
    }
  };

  // Delete student
  const handleDeleteStudent = async (student_id) => {
    if (!window.confirm("Are you sure you want to delete this student?")) return;
    try {
      await studentHandler.deleteStudent(student_id);
      await getStudentsFromService();
      alert("Student deleted successfully");
    } catch (err) {
      console.error(err);
      alert("Failed to delete student: " + (err.message || ""));
    }
  };

  return (
    <div className="flex bg-dark min-h-screen text-white">
      <Sidebar />

      <motion.div
        initial={{ opacity: 0, y: 10 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.4 }}
        className="flex-1 p-8"
      >
        <h1 className="text-2xl font-bold mb-6">Manage Students</h1>

        {loading ? (
          <p className="text-gray-400">Loading students...</p>
        ) : error ? (
          <p className="text-red-400">{error}</p>
        ) : (
          <StudentsTable
            students={students}
            query={query}
            setQuery={setQuery}
            onRefresh={getStudentsFromService}
            setEditingStudent={setEditingStudent}
            handleDeleteStudent={handleDeleteStudent}
            faculties={faculties}
            majors={majors}
          />
        )}

        {/* Edit Student Form */}
        <StudentForm
          isOpen={!!editingStudent}
          onClose={() => setEditingStudent(null)}
          onSubmit={handleUpdateStudent}
          initialData={editingStudent}
          faculties={faculties}
          majors={majors}
        />
      </motion.div>
    </div>
  );
}
