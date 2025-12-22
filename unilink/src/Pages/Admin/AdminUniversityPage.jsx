import React, { useState, useEffect } from "react";
import { FiArrowLeft } from "react-icons/fi";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import FacultyForm from "../../Components/Admin_Components/FacultyFormModal";
import MajorFormModal from "../../Components/Admin_Components/MajorFormModal";
import FacultiesTable from "../../Components/Admin_Components/FacultyForm";
import MajorsTable from "../../Components/Admin_Components/MajorForm";
import ConfirmationModal from "../../Components/Common/ConfirmationModal";
import * as handler from "../../../api/facultyandmajorHandler";

export default function AdminUniversityPage() {
  const [faculties, setFaculties] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isFacultyFormOpen, setIsFacultyFormOpen] = useState(false);
  const [editFacultyData, setEditFacultyData] = useState(null);

  const [isMajorView, setIsMajorView] = useState(false);
  const [selectedFaculty, setSelectedFaculty] = useState(null);
  const [majors, setMajors] = useState([]);
  const [isMajorFormOpen, setIsMajorFormOpen] = useState(false);
  const [editMajorData, setEditMajorData] = useState(null);

  const [deleteModal, setDeleteModal] = useState({ isOpen: false, type: null, id: null });

  // Fetch all faculties
  const fetchFaculties = async () => {
    setLoading(true);
    try {
      const data = await handler.getAllFaculties();
      setFaculties(data);
    } catch (err) {
      console.error(err);
      alert("Failed to load faculties");
    }
    setLoading(false);
  };

  useEffect(() => {
    fetchFaculties();
  }, []);

  // Faculty actions
  const handleOpenAddFaculty = () => {
    setEditFacultyData(null);
    setIsFacultyFormOpen(true);
  };

  const handleOpenEditFaculty = (faculty) => {
    setEditFacultyData(faculty);
    setIsFacultyFormOpen(true);
  };

  const handleSubmitFaculty = async (payload) => {
    try {
      if (payload.faculty_id) await handler.updateFaculty(payload);
      else await handler.addFaculty(payload);
      setIsFacultyFormOpen(false);
      fetchFaculties();
    } catch (err) {
      alert(err.message);
    }
  };

  const handleDeleteFaculty = (id) => {
    setDeleteModal({ isOpen: true, type: 'FACULTY', id });
  };

  // Major actions
  const loadMajors = async (faculty_id) => {
    try {
      const response = await handler.getAllMajors();
      // Handle nested data structure: response.data or response.data.data
      const data = Array.isArray(response) ? response : (response.data?.data || response.data || []);
      setMajors(data.filter((m) => m.faculty_id === faculty_id));
    } catch (err) {
      console.error(err);
      alert("Failed to load majors");
    }
  };

  const handleViewMajors = async (faculty) => {
    setSelectedFaculty(faculty);
    await loadMajors(faculty.faculty_id);
    setIsMajorView(true);
  };

  const handleBackToFaculties = () => {
    setIsMajorView(false);
    setSelectedFaculty(null);
    setMajors([]);
  };

  const handleAddMajor = () => {
    setEditMajorData(null);
    setIsMajorFormOpen(true);
  };

  const handleEditMajor = (major) => {
    setEditMajorData(major);
    setIsMajorFormOpen(true);
  };

  const handleSubmitMajor = async (payload) => {
    try {
      if (payload.major_id) await handler.updateMajor(payload);
      else await handler.addMajor(payload);
      setIsMajorFormOpen(false);
      await loadMajors(selectedFaculty.faculty_id);
    } catch (err) {
      alert(err.message);
    }
  };

  const handleDeleteMajor = (id) => {
    setDeleteModal({ isOpen: true, type: 'MAJOR', id });
  };

  const handleConfirmDelete = async () => {
    try {
      if (deleteModal.type === 'FACULTY') {
        await handler.deleteFaculty(deleteModal.id);
        fetchFaculties();
      } else if (deleteModal.type === 'MAJOR') {
        await handler.deleteMajor(deleteModal.id);
        await loadMajors(selectedFaculty.faculty_id);
      }
    } catch (err) {
      alert(err.message);
    }
  };

  return (
    <div className="flex bg-bg min-h-screen text-main font-main">
      <Sidebar />
      <div className="flex-1 p-6">
        {!isMajorView ? (
          <>
            <FacultiesTable
              faculties={faculties}
              onAddFaculty={handleOpenAddFaculty}
              onEditFaculty={handleOpenEditFaculty}
              onDeleteFaculty={handleDeleteFaculty}
              onViewMajors={handleViewMajors}
              onRefresh={fetchFaculties}
            />

            {/* Faculty Form Modal */}
            <FacultyForm
              isOpen={isFacultyFormOpen}
              onClose={() => setIsFacultyFormOpen(false)}
              onSubmit={handleSubmitFaculty}
              initialData={editFacultyData}
            />
          </>
        ) : (
          <>
            <div className="flex justify-between items-center mb-4">
              <button
                onClick={handleBackToFaculties}
                className="p-2 rounded-full cursor-pointer text-accent transition-all duration-200 hover:scale-110 hover:drop-shadow-lg hover:bg-white/10 flex items-center gap-2"
                title="Back to Faculties"
              >
                <FiArrowLeft size={18} />
                Back
              </button>
            </div>

            <MajorsTable
              majors={majors}
              facultyName={selectedFaculty?.name}
              onAddMajor={handleAddMajor}
              onEditMajor={handleEditMajor}
              onDeleteMajor={handleDeleteMajor}
            />

            {/* Major Form Modal */}
            <MajorFormModal
              isOpen={isMajorFormOpen}
              onClose={() => setIsMajorFormOpen(false)}
              onSubmit={handleSubmitMajor}
              initialData={editMajorData}
              selectedFaculty={selectedFaculty}
              faculties={faculties}
            />
          </>
        )}
      </div>

      <ConfirmationModal
        isOpen={deleteModal.isOpen}
        onClose={() => setDeleteModal({ isOpen: false, type: null, id: null })}
        onConfirm={handleConfirmDelete}
        title={deleteModal.type === 'FACULTY' ? "Delete Faculty" : "Delete Major"}
        message={deleteModal.type === 'FACULTY'
          ? "Are you sure you want to delete this faculty? All associated majors and users might be affected."
          : "Are you sure you want to delete this major?"}
        confirmText="Delete"
      />
    </div>
  );
}
