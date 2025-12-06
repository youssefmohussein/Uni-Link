import React, { useMemo, useState } from "react";
import { FiRefreshCw, FiEdit2, FiTrash2 } from "react-icons/fi";
import Card from "./Card";
import Pagination from "../Admin_Components/Paganation";

export default function ProfessorTable({
  professors = [],
  query = "",
  setQuery,
  setEditingProfessor,
  handleDeleteProfessor,
  onRefresh
}) {
  const [currentPage, setCurrentPage] = useState(1);
  const rowsPerPage = 7;

  // Filter professors based on search query
  const filtered = useMemo(() => {
    if (!query.trim()) return professors;
    const q = query.toLowerCase();

    return professors.filter((p) => {
      const fields = [
        p.username,
        p.email,
        p.faculty_name,
        p.major_name,
        p.academic_rank,
        p.office_location
      ];
      return fields.some((field) => field?.toLowerCase().includes(q));
    });
  }, [professors, query]);

  const totalPages = Math.max(Math.ceil(filtered.length / rowsPerPage), 1);

  // Paginate
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
        <h2 className="text-xl font-bold text-accent">Professors</h2>
        <button
          onClick={onRefresh}
          className="p-2 rounded-full cursor-pointer text-accent transition-all duration-200
                     hover:scale-110 hover:drop-shadow-[0_0_6px_currentColor] hover:bg-white/10"
          title="Refresh list"
        >
          <FiRefreshCw size={20} />
        </button>
      </div>

      {/* Search */}
      <input
        value={query}
        onChange={(e) => {
          setQuery(e.target.value);
          setCurrentPage(1); // reset pagination on search
        }}
        placeholder="Search by username, email, faculty, or major..."
        className="w-full mb-4 px-3 py-2 rounded-custom border border-white/20 bg-panel text-main
                   focus:ring-2 focus:ring-accent outline-none transition"
      />

      {/* Table Header */}
      <div
        className="grid grid-cols-14 gap-2 px-4 py-3 border-b border-white/10 
                      text-xs font-semibold uppercase text-accent"
      >
        <div className="col-span-2">Username</div>
        <div className="col-span-3">Email</div>
        <div className="col-span-2">Faculty</div>
        <div className="col-span-2">Major</div>
        <div className="col-span-2">Rank</div>
        <div className="col-span-2">Office</div>
        <div className="col-span-1 text-right">Actions</div>
      </div>

      {/* Table Rows */}
      {paginated.length > 0 ? (
        paginated.map((p) => (
          <div
            key={p.user_id}
            className="grid grid-cols-14 gap-2 px-4 py-3 border-b border-white/10 items-center text-sm text-white/80 hover:bg-white/5"
          >
            <div className="col-span-2 truncate">{p.username}</div>
            <div className="col-span-3 truncate">{p.email}</div>
            <div className="col-span-2 truncate">{p.faculty_name ?? "-"}</div>
            <div className="col-span-2 truncate">{p.major_name ?? "-"}</div>
            <div className="col-span-2 truncate">{p.academic_rank ?? "-"}</div>
            <div className="col-span-2 truncate">{p.office_location ?? "-"}</div>
            <div className="col-span-1 flex justify-end gap-2">
              <button
                onClick={() => setEditingProfessor(p)}
                className="
                  p-2 rounded cursor-pointer
                  text-accent
                  transition-all duration-200
                  hover:scale-110
                  hover:drop-shadow-[0_0_6px_currentColor]
                "
                title="Edit professor"
              >
                <FiEdit2 size={16} />
              </button>
              <button
                onClick={() => handleDeleteProfessor(p.user_id)}
                className="
                  p-2 rounded cursor-pointer
                  text-red-500
                  transition-all duration-200
                  hover:scale-110
                  hover:drop-shadow-[0_0_6px_currentColor]
                "
                title="Delete professor"
              >
                <FiTrash2 size={16} />
              </button>
            </div>
          </div>
        ))
      ) : (
        <div className="text-center py-10 text-white/50 transition-opacity duration-500">
          No professors found.
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

