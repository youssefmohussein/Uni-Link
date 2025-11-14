import React, { useState, useEffect } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import ProfessorTable from "../../Components/Admin_Components/ProfessorTable";
import ProfessorForm from "../../Components/Admin_Components/ProfessorForm";
import { motion } from "framer-motion";
import * as professorHandler from "../../../api/professorHandler";
import * as userHandler from "../../../api/userHandler"; // for faculties + majors

export default function AdminProfessorPage() {
  const [professors, setProfessors] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [query, setQuery] = useState("");
  const [editingProfessor, setEditingProfessor] = useState(null);

  const [faculties, setFaculties] = useState([]);
  const [majors, setMajors] = useState([]);

  // Fetch professors
  const getProfessorsFromService = async () => {
    try {
      setLoading(true);
      const data = await professorHandler.getProfessors();
      setProfessors(data);
      setError(null);
    } catch (err) {
      setError("Failed to fetch professors");
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  // Load faculties + majors
  const getFacultiesAndMajors = async () => {
    try {
      const [facData, majData] = await Promise.all([
        userHandler.getAllFaculties(),
        userHandler.getAllMajors(),
      ]);
      setFaculties(facData);
      setMajors(majData);
    } catch (err) {
      console.error("Failed to load faculties/majors", err);
    }
  };

  useEffect(() => {
    getProfessorsFromService();
    getFacultiesAndMajors();
  }, []);

  // Update Professor
  const handleUpdateProfessor = async (formData) => {
    try {
      await professorHandler.updateProfessor(formData);
      await getProfessorsFromService();
      setEditingProfessor(null);
      alert("Professor updated successfully");
    } catch (err) {
      console.error(err);
      alert("Failed to update professor: " + (err.message || ""));
    }
  };

  // Delete Professor
  const handleDeleteProfessor = async (user_id) => {
    if (!window.confirm("Are you sure you want to delete this professor?")) return;
    try {
      await professorHandler.deleteProfessor(user_id);
      await getProfessorsFromService();
      alert("Professor deleted successfully");
    } catch (err) {
      console.error(err);
      alert("Failed to delete professor: " + (err.message || ""));
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
        <h1 className="text-2xl font-bold mb-6">Manage Professors</h1>

        {loading ? (
          <p className="text-gray-400">Loading professors...</p>
        ) : error ? (
          <p className="text-red-400">{error}</p>
        ) : (
          <ProfessorTable
            professors={professors}
            query={query}
            setQuery={setQuery}
            setEditingProfessor={setEditingProfessor}
            handleDeleteProfessor={handleDeleteProfessor}
          />
        )}

        {/* Edit Professor Form */}
        <ProfessorForm
          isOpen={!!editingProfessor}
          onClose={() => setEditingProfessor(null)}
          onSubmit={handleUpdateProfessor}
          initialData={editingProfessor}
          faculties={faculties}
          majors={majors}
        />
      </motion.div>
    </div>
  );
}
