import React, { useMemo, useState } from "react";
import { FiPlus, FiEdit, FiTrash2 } from "react-icons/fi";
import Card from "../components/Card";
import Modal from "../components/Modal";
import AddEditForm from "../components/AddEditForm";

export default function ManageProfessors() {
  const [professors, setProfessors] = useState([
    { id: 1, name: "Dr. Ahmed Samir", email: "ahmed.samir@uni.edu", phone: "+201111111111", department: "Computer Science", year: 2015 },
    { id: 2, name: "Dr. Mona Youssef", email: "mona.youssef@uni.edu", phone: "+201222222222", department: "Software Engineering", year: 2018 },
    { id: 3, name: "Dr. Karim Nabil", email: "karim.nabil@uni.edu", phone: "+201333333333", department: "AI", year: 2012 },
    { id: 4, name: "Dr. Sara Fathy", email: "sara.fathy@uni.edu", phone: "+201444444444", department: "Information Systems", year: 2020 },
  ]);
  const [loading, setLoading] = useState(false);
  const [query, setQuery] = useState("");
  const [filterDept, setFilterDept] = useState("All");
  const [filterYear, setFilterYear] = useState("All");
  const [isAddEditOpen, setIsAddEditOpen] = useState(false);
  const [editingProfessor, setEditingProfessor] = useState(null);
  const [isDeleteOpen, setIsDeleteOpen] = useState(false);
  const [deletingProfessor, setDeletingProfessor] = useState(null);
  const [page, setPage] = useState(1);
  const pageSize = 6;

  // Use "All" for departments and normalize years to strings
  const departments = useMemo(
    () => ["All", ...Array.from(new Set(professors.map((p) => p.department)))],
    [professors]
  );
  const years = useMemo(
    () => ["All", ...Array.from(new Set(professors.map((p) => String(p.year))))],
    [professors]
  );

  const filtered = useMemo(() => {
    let list = [...professors];

    if (query.trim()) {
      const q = query.toLowerCase();
      list = list.filter((p) => {
        const searchableValues = [p.name, p.email, p.department, p.year, p.phone];
        return searchableValues.some((v) => String(v ?? "").toLowerCase().includes(q));
      });
    }

    if (filterDept !== "All") {
      list = list.filter((p) => p.department === filterDept);
    }

    // Compare as strings because filterYear comes from select (string)
    if (filterYear !== "All") {
      list = list.filter((p) => String(p.year) === filterYear);
    }

    return list;
  }, [professors, query, filterDept, filterYear]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
  const paginated = filtered.slice((page - 1) * pageSize, page * pageSize);

  function saveProfessor(payload) {
    setLoading(true);
    setTimeout(() => {
      setProfessors((prev) => {
        if (payload.id) {
          return prev.map((p) => (p.id === payload.id ? { ...p, ...payload } : p));
        }
        const nextId = prev.length ? Math.max(...prev.map((p) => p.id)) + 1 : 1;
        return [
          { id: nextId, email: `${payload.name.split(" ")[0] || "prof"}@uni.edu`, phone: "+201000000000", ...payload },
          ...prev,
        ];
      });
      setIsAddEditOpen(false);
      setLoading(false);
    }, 300);
  }

  function confirmDelete(p) {
    setDeletingProfessor(p);
    setIsDeleteOpen(true);
  }

  function doDelete() {
    if (!deletingProfessor) return;
    setLoading(true);
    setTimeout(() => {
      setProfessors((prev) => prev.filter((p) => p.id !== deletingProfessor.id));
      setIsDeleteOpen(false);
      setDeletingProfessor(null);
      setLoading(false);
    }, 250);
  }

  return (
    <main className="p-6 space-y-6">
      <header className="flex items-center justify-between mb-6">
        <h1 className="text-3xl font-bold text-accent">Professor Management</h1>
        <button
          onClick={() => {
            setEditingProfessor(null);
            setIsAddEditOpen(true);
          }}
          className="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-2xl shadow hover:brightness-105"
        >
          <FiPlus /> Add Professor
        </button>
      </header>

      <Card title="Filters" className="mb-6">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 justify-center items-center ">
          <input
            value={query}
            onChange={(e) => {
              setQuery(e.target.value);
              setPage(1);
            }}
            placeholder="Search..."
            className="w-full px-3 py-2 rounded-lg border "
          />
          <select
            value={filterDept}
            onChange={(e) => {
              setFilterDept(e.target.value);
              setPage(1);
            }}
            className="w-full px-3 py-2 rounded-lg border text-white bg-panel"
          >
            {departments.map((d) => (
              <option key={d} value={d}>
                {d}
              </option>
            ))}
          </select>
          <select
            value={filterYear}
            onChange={(e) => {
              setFilterYear(e.target.value);
              setPage(1);
            }}
            className="w-full px-3 py-2 rounded-lg border text-white bg-panel"
          >
            {years.map((y) => (
              <option key={y} value={y}>
                {y}
              </option>
            ))}
          </select>
        </div>
      </Card>

      <Card>
        <table className="min-w-full text-sm">
          <thead>
            <tr className="text-left text-gray-500">
              <th className="py-3 px-2">Name</th>
              <th className="py-3 px-2">Email</th>
              <th className="py-3 px-2">Department</th>
              <th className="py-3 px-2">Year</th>
              <th className="py-3 px-2">Phone</th>
              <th className="py-3 px-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            {paginated.map((p) => (
              <tr key={p.id} className="border-t hover:bg-white/50 transition">
                <td className="py-3 px-2">{p.name}</td>
                <td className="py-3 px-2">{p.email}</td>
                <td className="py-3 px-2">{p.department}</td>
                <td className="py-3 px-2">{p.year}</td>
                <td className="py-3 px-2">{p.phone}</td>
                <td className="py-3 px-2 flex gap-2">
                  <button
                    onClick={() => {
                      setEditingProfessor(p);
                      setIsAddEditOpen(true);
                    }}
                    className="text-indigo-600 hover:text-indigo-800"
                  >
                    <FiEdit />
                  </button>
                  <button onClick={() => confirmDelete(p)} className="text-red-600 hover:text-red-800">
                    <FiTrash2 />
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>

        <div className="flex justify-between items-center mt-4 text-sm">
          <button
            disabled={page === 1}
            onClick={() => setPage(page - 1)}
            className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
          >
            Prev
          </button>
          <div>
            Page {page} / {totalPages}
          </div>
          <button
            disabled={page === totalPages}
            onClick={() => setPage(page + 1)}
            className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
          >
            Next
          </button>
        </div>
      </Card>

      {isAddEditOpen && (
        <Modal onClose={() => setIsAddEditOpen(false)}>
          <AddEditForm
            initialData={editingProfessor}
            onCancel={() => setIsAddEditOpen(false)}
            onSave={(data) => saveProfessor(editingProfessor ? { ...editingProfessor, ...data } : data)}
          />
        </Modal>
      )}

      {isDeleteOpen && (
        <Modal onClose={() => setIsDeleteOpen(false)}>
          <div className="space-y-4">
            <p>
              Are you sure you want to delete <strong>{deletingProfessor?.name}</strong>?
            </p>
            <div className="flex justify-end gap-3">
              <button onClick={() => setIsDeleteOpen(false)} className="px-4 py-2 bg-gray-200 rounded">
                Cancel
              </button>
              <button onClick={doDelete} className="px-4 py-2 bg-red-600 text-white rounded">
                Delete
              </button>
            </div>
          </div>
        </Modal>
      )}
    </main>
  );
}
