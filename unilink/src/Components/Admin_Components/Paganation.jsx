import React from "react";

export default function Pagination({ currentPage, totalPages, onPrev, onNext }) {
  if (totalPages <= 1) return null; // Hide if only 1 page

  return (
    <div className="flex justify-end items-center gap-4 mt-4 px-4">
      <button
        onClick={onPrev}
        disabled={currentPage === 1}
        className="px-3 py-1 rounded bg-accent hover:brightness-110 disabled:opacity-50 transition"
      >
        Prev
      </button>
      <span>
        Page {currentPage} of {totalPages}
      </span>
      <button
        onClick={onNext}
        disabled={currentPage === totalPages}
        className="px-3 py-1 rounded bg-accent hover:brightness-110 disabled:opacity-50 transition"
      >
        Next
      </button>
    </div>
  );
}
