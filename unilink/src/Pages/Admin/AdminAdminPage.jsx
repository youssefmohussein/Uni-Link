import React, { useState, useEffect } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import Card from "../../Components/Admin_Components/Card";
import { motion } from "framer-motion";
import * as userHandler from "../../../api/userHandler";

// Form Components
import FacultyForm from "../../Components/Admin_Components/FacultyForm";
import MajorForm from "../../Components/Admin_Components/MajorForm";

export default function AdminAdminPage() {
  const [faculties, setFaculties] = useState([]);
  const [majors, setMajors] = useState([]);
  const [selectedFaculty, setSelectedFaculty] = useState(null);

  const [loading, setLoading] = useState(true);

  // Forms
  const [isAddingFaculty, setIsAddingFaculty] = useState(false);
  const [editingFaculty, setEditingFaculty] = useState(null);

  const [isAddingMajor, setIsAddingMajor] = useState(false);
  const [editingMajor, setEditingMajor] = useState(null);

  // Fetch faculties + majors
  const loadData = async () => {
    try {
      setLoading(true);
      const facData = await userHandler.getAllFaculties();
      setFaculties(facData);

      const majData = await userHandler.getAllMajors();
      setMajors(majData);

      setLoading(false);
    } catch (err) {
      console.error("Failed loading faculties & majors", err);
    }
  };

  useEffect(() => {
    loadData();
  }, []);

  // === Faculty CRUD ===
  const handleAddFaculty = async (formData) => {
    try {
      await userHandler.addFaculty(formData);
      setIsAddingFaculty(false);
      await loadData();
      alert("Faculty added successfully");
    } catch (err) {
      console.error(err);
      alert("Failed to add faculty");
    }
  };

  const handleUpdateFaculty = async (formData) => {
    try {
      await userHandler.updateFaculty(formData);
      setEditingFaculty(null);
      await loadData();
      alert("Faculty updated successfully");
    } catch (err) {
      console.error(err);
      alert("Failed to update faculty");
    }
  };

  const handleDeleteFaculty = async (id) => {
    if (!window.confirm("Delete this faculty?")) return;
    try {
      await userHandler.deleteFaculty(id);
      if (selectedFaculty === id) setSelectedFaculty(null);
      await loadData();
      alert("Faculty deleted");
    } catch (err) {
      console.error(err);
      alert("Failed to delete faculty");
    }
  };

  // === Major CRUD ===
  const handleAddMajor = async (formData) => {
    try {
      await userHandler.addMajor(formData);
      setIsAddingMajor(false);
      await loadData();
      alert("Major added successfully");
    } catch (err) {
      console.error(err);
      alert("Failed to add major");
    }
  };

  const handleUpdateMajor = async (formData) => {
    try {
      await userHandler.updateMajor(formData);
      setEditingMajor(null);
      await loadData();
      alert("Major updated successfully");
    } catch (err) {
      console.error(err);
      alert("Failed to update major");
    }
  };

  const handleDeleteMajor = async (major_id) => {
    if (!window.confirm("Delete this major?")) return;
    try {
      await userHandler.deleteMajor(major_id);
      await loadData();
      alert("Major deleted");
    } catch (err) {
      console.error(err);
      alert("Failed to delete major");
    }
  };

  const filteredMajors = selectedFaculty
    ? majors.filter((m) => m.faculty_id === selectedFaculty)
    : [];

  return (
    <div className="flex bg-dark min-h-screen text-white">
      <Sidebar />

      <motion.div
        initial={{ opacity: 0, y: 8 }}
        animate={{ opacity: 1, y: 0 }}
        transition={{ duration: 0.3 }}
        className="flex-1 p-8"
      >
        <h1 className="text-2xl font-bold mb-6">Manage Faculties & Majors</h1>

        {/* ======================== FACULTIES ======================== */}
        <Card className="mb-8">
          <div className="flex justify-between items-center mb-4 px-4">
            <h2 className="text-xl font-bold text-accent">Faculties</h2>
            <button
              onClick={() => setIsAddingFaculty(true)}
              className="px-4 py-2 bg-accent rounded-lg hover:bg-accent/80"
            >
              + Add Faculty
            </button>
          </div>

          {/* Faculties list */}
          <div className="space-y-2">
            {faculties.map((f) => (
              <div
                key={f.faculty_id}
                className={`flex justify-between items-center px-4 py-3 rounded-lg transition cursor-pointer ${
                  selectedFaculty === f.faculty_id
                    ? "bg-accent/20"
                    : "bg-white/5 hover:bg-white/10"
                }`}
                onClick={() => setSelectedFaculty(f.faculty_id)}
              >
                <span className="text-lg font-medium">{f.faculty_name}</span>

                <div className="flex gap-2">
                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      setEditingFaculty(f);
                    }}
                    className="px-3 py-1 bg-blue-600 rounded-md text-sm"
                  >
                    Edit
                  </button>
                  <button
                    onClick={(e) => {
                      e.stopPropagation();
                      handleDeleteFaculty(f.faculty_id);
                    }}
                    className="px-3 py-1 bg-red-600 rounded-md text-sm"
                  >
                    Delete
                  </button>
                </div>
              </div>
            ))}

            {faculties.length === 0 && (
              <p className="text-center text-white/50 py-6">
                No faculties available
              </p>
            )}
          </div>
        </Card>

        {/* ========================= MAJORS ========================== */}
        <Card>
          <div className="flex justify-between items-center mb-4 px-4">
            <h2 className="text-xl font-bold text-accent">Majors</h2>

            {selectedFaculty && (
              <button
                onClick={() => setIsAddingMajor(true)}
                className="px-4 py-2 bg-accent rounded-lg hover:bg-accent/80"
              >
                + Add Major
              </button>
            )}
          </div>

          {!selectedFaculty ? (
            <p className="text-center text-white/50 py-6">
              Select a faculty to manage its majors.
            </p>
          ) : filteredMajors.length === 0 ? (
            <p className="text-center text-white/50 py-6">
              No majors for this faculty.
            </p>
          ) : (
            <div className="space-y-2">
              {filteredMajors.map((m) => (
                <div
                  key={m.major_id}
                  className="flex justify-between items-center px-4 py-3 rounded-lg bg-white/5 hover:bg-white/10 transition"
                >
                  <span className="text-lg">{m.major_name}</span>

                  <div className="flex gap-2">
                    <button
                      onClick={() => setEditingMajor(m)}
                      className="px-3 py-1 bg-blue-600 rounded-md text-sm"
                    >
                      Edit
                    </button>
                    <button
                      onClick={() => handleDeleteMajor(m.major_id)}
                      className="px-3 py-1 bg-red-600 rounded-md text-sm"
                    >
                      Delete
                    </button>
                  </div>
                </div>
              ))}
            </div>
          )}
        </Card>

        {/* ========== FORMS ========= */}
        <FacultyForm
          isOpen={isAddingFaculty}
          onClose={() => setIsAddingFaculty(false)}
          onSubmit={handleAddFaculty}
        />

        <FacultyForm
          isOpen={!!editingFaculty}
          onClose={() => setEditingFaculty(null)}
          onSubmit={handleUpdateFaculty}
          initialData={editingFaculty}
        />

        <MajorForm
          isOpen={isAddingMajor}
          onClose={() => setIsAddingMajor(false)}
          onSubmit={handleAddMajor}
          faculty_id={selectedFaculty}
        />

        <MajorForm
          isOpen={!!editingMajor}
          onClose={() => setEditingMajor(null)}
          onSubmit={handleUpdateMajor}
          initialData={editingMajor}
          faculty_id={selectedFaculty}
        />
      </motion.div>
    </div>
  );
}
