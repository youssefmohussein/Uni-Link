import React, { useState, useEffect, useMemo } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import ProfessorTable from "../../Components/Admin_Components/ProfessorTable";
import ProfessorForm from "../../Components/Admin_Components/ProfessorForm";
import ConfirmationModal from "../../Components/Common/ConfirmationModal";
import { motion } from "framer-motion";
import * as professorHandler from "../../../api/professorHandler";
import * as userHandler from "../../../api/userHandler"; // for faculties + majors

export default function AdminProfessorPage() {
  const [professors, setProfessors] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [query, setQuery] = useState("");
  const [editingProfessor, setEditingProfessor] = useState(null);
  const [deleteModal, setDeleteModal] = useState({ isOpen: false, id: null });

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

  const rankOptions = useMemo(() => {
    const unique = new Set(
      (professors || [])
        .map((p) => p.academic_rank)
        .filter((rank) => typeof rank === "string" && rank.trim().length > 0)
    );
    return Array.from(unique).sort((a, b) => a.localeCompare(b));
  }, [professors]);

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
  const handleDeleteProfessor = async () => {
    try {
      await professorHandler.deleteProfessor(deleteModal.id);
      await getProfessorsFromService();
      // alert("Professor deleted successfully"); // Removed alert
    } catch (err) {
      console.error(err);
      alert("Failed to delete professor: " + (err.message || ""));
    }
  };

  const openDeleteModal = (user_id) => {
    setDeleteModal({ isOpen: true, id: user_id });
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
            handleDeleteProfessor={openDeleteModal}
            onRefresh={getProfessorsFromService}
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
          rankOptions={rankOptions}
        />

        <ConfirmationModal
          isOpen={deleteModal.isOpen}
          onClose={() => setDeleteModal({ isOpen: false, id: null })}
          onConfirm={handleDeleteProfessor}
          title="Delete Professor"
          message="Are you sure you want to delete this professor? This action cannot be undone."
          confirmText="Delete Professor"
        />
      </motion.div>
    </div>
  );
}
