import React, { useState, useMemo } from "react";
import { FiEdit, FiTrash2, FiPlus } from "react-icons/fi";
import Card from "./Card";
import Pagination from "./Paganation";

export default function MajorsTable({
  majors = [],
  facultyName = "",
  onAddMajor,
  onEditMajor,
  onDeleteMajor,
}) {
  const [query, setQuery] = useState("");
  const [currentPage, setCurrentPage] = useState(1);
  const rowsPerPage = 7;

  const filtered = useMemo(() => {
    if (!query.trim()) return majors;
    const q = query.toLowerCase();
    return majors.filter((m) => m.major_name?.toLowerCase().includes(q));
  }, [majors, query]);

  const totalPages = Math.ceil(filtered.length / rowsPerPage);
  const paginated = filtered.slice(
    (currentPage - 1) * rowsPerPage,
    currentPage * rowsPerPage
  );

  const handlePrev = () => setCurrentPage((p) => Math.max(p - 1, 1));
  const handleNext = () => setCurrentPage((p) => Math.min(p + 1, totalPages));

  return (
    <Card>
      {/* Header */}
      <div className="flex justify-between items-center mb-4 px-4">
        <h2 className="text-xl font-bold text-accent">Majors of {facultyName}</h2>
        <button
          onClick={onAddMajor}
          className="p-2 rounded-full cursor-pointer text-accent transition-all duration-200 hover:scale-110 hover:drop-shadow-[0_0_6px_currentColor] hover:bg-white/10"
          title="Add Major"
        >
          <FiPlus size={20} />
        </button>
      </div>

      {/* Search */}
      <input
        value={query}
        onChange={(e) => {
          setQuery(e.target.value);
          setCurrentPage(1);
        }}
        placeholder="Search majors..."
        className="w-full mb-4 px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
      />

      {/* Table Header */}
      <div className="grid grid-cols-12 gap-2 px-4 py-3 border-b border-white/10 items-center text-xs font-semibold uppercase text-accent mb-2">
        <div className="col-span-2">ID</div>
        <div className="col-span-7">Major Name</div>
        <div className="col-span-3 text-right">Actions</div>
      </div>

      {/* Table Rows */}
      {paginated.map((m) => (
        <div
          key={m.major_id}
          className="grid grid-cols-12 gap-2 px-4 py-2 border-b border-white/10 items-center"
        >
          <div className="col-span-2">{m.major_id}</div>
          <div className="col-span-7">{m.major_name}</div>
          <div className="col-span-3 text-right flex justify-end gap-2">
            <button
              onClick={() => onEditMajor(m)}
              className="p-2 rounded-full cursor-pointer text-accent transition-all duration-200 hover:scale-110 hover:drop-shadow-[0_0_6px_currentColor] hover:bg-white/10"
              title="Edit"
            >
              <FiEdit size={18} />
            </button>
            <button
              onClick={() => onDeleteMajor(m.major_id)}
              className="p-2 rounded-full cursor-pointer text-red-600 transition-all duration-200 hover:scale-110 hover:drop-shadow-[0_0_6px_currentColor] hover:bg-white/10"
              title="Delete"
            >
              <FiTrash2 size={18} />
            </button>
          </div>
        </div>
      ))}


      {/* Pagination */}
      <Pagination currentPage={currentPage} totalPages={totalPages} onPrev={handlePrev} onNext={handleNext} />
    </Card>
  );
}
