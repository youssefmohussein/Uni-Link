import React, { useState, useEffect } from "react";
import { FiArrowLeft } from "react-icons/fi";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import FacultyForm from "../../Components/Admin_Components/FacultyForm";
import MajorForm from "../../Components/Admin_Components/MajorForm";
import FacultiesTable from "../../Components/Admin_Components/FacultyForm";
import MajorsTable from "../../Components/Admin_Components/MajorForm";
import * as handler from "../../../api/facultyandmajorHandler";

export default function AdminAdminPage() {
  const [faculties, setFaculties] = useState([]);
  const [loading, setLoading] = useState(true);
  const [isFacultyFormOpen, setIsFacultyFormOpen] = useState(false);
  const [editFacultyData, setEditFacultyData] = useState(null);

  const [isMajorView, setIsMajorView] = useState(false);
  const [selectedFaculty, setSelectedFaculty] = useState(null);
  const [majors, setMajors] = useState([]);
  const [isMajorFormOpen, setIsMajorFormOpen] = useState(false);
  const [editMajorData, setEditMajorData] = useState(null);

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

  const handleOpenAddFaculty = () => {
    setEditFacultyData(null);
    setIsFacultyFormOpen(true);
  };

  const handleOpenEditFaculty = (f) => {
    setEditFacultyData(f);
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

  const handleDeleteFaculty = async (id) => {
    if (!window.confirm("Delete this faculty?")) return;
    try {
      await handler.deleteFaculty(id);
      fetchFaculties();
    } catch (err) {
      alert(err.message);
    }
  };

  const loadMajors = async (faculty_id) => {
    try {
      const data = await handler.getAllMajors();
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

  const handleDeleteMajor = async (id) => {
    if (!window.confirm("Delete this major?")) return;
    try {
      await handler.deleteMajor(id);
      await loadMajors(selectedFaculty.faculty_id);
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
          </>
        ) : (
          <>
            <div className="flex justify-between items-center mb-4">
              <button
                onClick={handleBackToFaculties}
                className="p-2 rounded-full cursor-pointer text-accent transition-all duration-200 hover:scale-110 hover:drop-shadow-[0_0_6px_currentColor] hover:bg-white/10 flex items-center justify-center gap-2"
                title="Back to Faculties"
              >
                <FiArrowLeft size={18} />
              </button>
            </div>
            <MajorsTable
              majors={majors}
              facultyName={selectedFaculty?.faculty_name}
              onAddMajor={handleAddMajor}
              onEditMajor={handleEditMajor}
              onDeleteMajor={handleDeleteMajor}
            />
          </>
        )}
      </div>
    </div>
  );
}
