import React, { useMemo, useState } from "react";
import { FiRefreshCw } from "react-icons/fi";
import AnimatedUserRow from "./AnimatedUserRow";
import Card from "./Card";
import Pagination from "../Admin_Components/Paganation"; // Import the pagination component

export default function ProfessorTable({
  professors = [],
  query,
  setQuery,
  setEditingProfessor,
  handleDeleteProfessor,
  onRefresh
}) {
  const [currentPage, setCurrentPage] = useState(1);
  const rowsPerPage = 7; // Limit for professors table

  // Filter professors based on search query
  const filtered = useMemo(() => {
    if (!query.trim()) return professors;
    const q = query.toLowerCase();
    return professors.filter(
      (p) =>
        p.username?.toLowerCase().includes(q) ||
        p.email?.toLowerCase().includes(q) ||
        p.faculty_name?.toLowerCase().includes(q) ||
        p.major_name?.toLowerCase().includes(q)
    );
  }, [professors, query]);

  // Pagination logic
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
        <h2 className="text-xl font-bold text-accent">Professors</h2>
        <button
          onClick={onRefresh}
          className="p-2 rounded-full hover:bg-white/10 transition"
          title="Refresh list"
        >
          <FiRefreshCw className="text-accent" size={20} />
        </button>
      </div>

      {/* Search */}
      <input
        value={query}
        onChange={(e) => {
          setQuery(e.target.value);
          setCurrentPage(1); // Reset page when searching
        }}
        placeholder="Search by username, email, faculty, or major..."
        className="w-full mb-4 px-3 py-2 rounded-custom border border-white/20 bg-panel text-main focus:ring-2 focus:ring-accent outline-none transition"
      />

      {/* Table Header */}
      <div className="grid grid-cols-12 gap-2 px-4 py-3 border-b border-white/10 text-xs font-semibold uppercase text-accent">
        <div className="col-span-3">Username</div>
        <div className="col-span-3">Email</div>
        <div className="col-span-3">Faculty</div>
        <div className="col-span-2">Major</div>
        <div className="col-span-1 text-right">Actions</div>
      </div>

      {/* Table Rows */}
      {paginated.map((p, index) => (
        <AnimatedUserRow
          key={p.user_id}
          u={p}
          index={index}
          setEditingUser={setEditingProfessor} // reused
          handleDeleteUser={handleDeleteProfessor} // reused
          showRole={false} // hide role
          isProfessor={true} // custom flag
        />
      ))}

      {/* Empty state */}
      {paginated.length === 0 && (
        <div className="text-center py-10 text-white/50">No professors found.</div>
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
