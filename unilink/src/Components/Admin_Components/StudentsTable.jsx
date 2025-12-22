import React, { useMemo, useState } from "react";
import { FiRefreshCw, FiEdit2, FiTrash2 } from "react-icons/fi";
import Card from "./Card";
import Pagination from "../Admin_Components/Paganation";

export default function StudentsTable({
  students = [],
  query,
  setQuery,
  onRefresh,
  setEditingStudent,
  handleDeleteStudent,
  faculties = [],
  majors = []
}) {
  const [currentPage, setCurrentPage] = useState(1);
  const rowsPerPage = 4;

  const getFacultyName = (id) => {
    if (!id) return "-";
    const found = faculties.find((f) => f && f.faculty_id == id);
    return found ? (found.faculty_name || found.name) : "-";
  };

  const getMajorName = (id, student) => {
    // Check for direct property if joined
    if (student?.major_name) return student.major_name;
    if (student?.major?.name) return student.major.name;

    if (!id) return "-";
    // Loose equality check (==) to handle string/number differences
    const found = majors.find((m) => m && m.major_id == id);
    return found ? (found.name || found.major_name) : "-";
  };

  const filtered = useMemo(() => {
    if (!query.trim()) return students;
    const q = query.toLowerCase();
    return students.filter(
      (s) =>
      (s?.username?.toLowerCase().includes(q) ||
        s?.email?.toLowerCase().includes(q))
    );
  }, [students, query]);

  const totalPages = Math.ceil(filtered.length / rowsPerPage);
  const paginated = filtered.slice(
    (currentPage - 1) * rowsPerPage,
    currentPage * rowsPerPage
  );

  const handlePrev = () => setCurrentPage((prev) => Math.max(prev - 1, 1));
  const handleNext = () => setCurrentPage((prev) => Math.min(prev + 1, totalPages));

  return (
    <Card>
      {/* Header */}
      <div className="flex items-center justify-between mb-4 px-4">
        <h2 className="text-xl font-bold text-accent">Students</h2>
        <button
          onClick={onRefresh}
          className="
            p-2 rounded-full cursor-pointer
            text-accent
            transition-all duration-200
            hover:scale-110
            hover:drop-shadow-[0_0_6px_currentColor]
            hover:bg-white/10
          "
          title="Refresh"
        >
          <FiRefreshCw size={20} />
        </button>
      </div>

      {/* Search */}
      <input
        value={query}
        onChange={(e) => {
          setQuery(e.target.value);
          setCurrentPage(1);
        }}
        placeholder="Search by username or email..."
        className="w-full mb-4 px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
      />

      {/* Table Header */}
      <div className="grid grid-cols-16 gap-2 px-4 py-3 border-b border-white/10 items-center text-xs font-semibold uppercase text-accent mb-2">
        <div className="col-span-2">Username</div>
        <div className="col-span-3">Email</div>
        <div className="col-span-3">Faculty</div>
        <div className="col-span-2">Major</div>
        <div className="col-span-1">Year</div>
        <div className="col-span-1">GPA</div>
        <div className="col-span-1">Points</div>
        <div className="col-span-3 text-right">Actions</div>
      </div>

      {/* Rows */}
      {paginated.map((s) => (
        <div
          key={s?.student_id || Math.random()}
          className="grid grid-cols-16 gap-2 px-4 py-3 border-b border-white/5 hover:bg-white/5 transition text-sm items-center"
        >
          <div className="col-span-2 truncate">{s?.username || "-"}</div>
          <div className="col-span-3 truncate">{s?.email || "-"}</div>
          <div className="col-span-3 truncate">{getFacultyName(s?.faculty_id)}</div>
          <div className="col-span-2 truncate">{getMajorName(s?.major_id, s)}</div>
          <div className="col-span-1">{s?.year ?? 0}</div>
          <div className="col-span-1">{s?.gpa ?? 0}</div>
          <div className="col-span-1">{s?.points ?? 0}</div>

          {/* Actions */}
          <div className="col-span-3 flex justify-end gap-3">
            {/* Edit Button */}
            <button
              onClick={() => setEditingStudent(s)}
              className="
                p-2 rounded cursor-pointer
                text-accent
                transition-all duration-200
                hover:scale-110
                hover:drop-shadow-[0_0_6px_currentColor]
              "
              title="Edit Student"
            >
              <FiEdit2 size={16} />
            </button>

            {/* Delete Button */}
            <button
              onClick={() => handleDeleteStudent(s.user_id)}
              className="
                p-2 rounded cursor-pointer
                text-red-500
                transition-all duration-200
                hover:scale-110
                hover:drop-shadow-[0_0_6px_currentColor]
              "
              title="Delete Student"
            >
              <FiTrash2 size={16} />
            </button>
          </div>
        </div>
      ))}

      {/* Empty state */}
      {paginated.length === 0 && (
        <div className="text-center py-10 text-white/50 transition-opacity duration-500">
          No students found.
        </div>
      )}

      {/* Pagination */}
      <Pagination
        currentPage={currentPage}
        totalPages={totalPages}
        onPrev={handlePrev}
        onNext={handleNext}
      />
    </Card>
  );
}
