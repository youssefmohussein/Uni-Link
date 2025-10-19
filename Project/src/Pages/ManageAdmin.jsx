import React, { useMemo, useState } from "react";
import { FiPlus, FiEdit, FiTrash2 } from "react-icons/fi";
import Card from "../components/Admin/Card";
import Modal from "../components/Admin/Modal";
import AddEditForm from "../components/Admin/AddEditForm";
import { UsersPerDeptChart, UsersPerYearChart } from "../components/Admin/AnalyticsCharts";

export default function ManageTAs() {
  const [tas, setTAs] = useState([
    { id: 1, name: "Ahmed Ali", email: "ahmed.ali@uni.edu", phone: "+201555555555", department: "Computer Science", year: 2024 },
    { id: 2, name: "Laila Omar", email: "laila.omar@uni.edu", phone: "+201666666666", department: "Software Engineering", year: 2025 },
    { id: 3, name: "Youssef Adel", email: "youssef.adel@uni.edu", phone: "+201777777777", department: "AI", year: 2023 },
  ]);
  const [loading, setLoading] = useState(false);
  const [query, setQuery] = useState("");
  const [filterDept, setFilterDept] = useState("All");
  const [filterYear, setFilterYear] = useState("All");
  const [isAddEditOpen, setIsAddEditOpen] = useState(false);
  const [editingTA, setEditingTA] = useState(null);
  const [isDeleteOpen, setIsDeleteOpen] = useState(false);
  const [deletingTA, setDeletingTA] = useState(null);
  const [page, setPage] = useState(1);
  const pageSize = 6;

  const departments = useMemo(() => ["All", ...new Set(tas.map((t) => t.department))], [tas]);
  const years = useMemo(() => ["All", ...new Set(tas.map((t) => t.year))], [tas]);

  const filtered = useMemo(() => {
    let list = [...tas];
    if (query.trim()) {
      const q = query.toLowerCase();
      list = list.filter((t) => {
        const searchableValues = [t.name, t.email, t.department, t.year, t.phone];
        return searchableValues.some((v) => String(v ?? "").toLowerCase().includes(q));
      });
    }
    if (filterDept !== "All") list = list.filter((t) => t.department === filterDept);
    if (filterYear !== "All") list = list.filter((t) => t.year === filterYear);
    return list;
  }, [tas, query, filterDept, filterYear]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
  const paginated = filtered.slice((page - 1) * pageSize, page * pageSize);

  const perDept = useMemo(() => {
    const map = {};
    tas.forEach((t) => (map[t.department] = (map[t.department] || 0) + 1));
    return Object.entries(map).map(([department, value]) => ({ department, value }));
  }, [tas]);

  const perYear = useMemo(() => {
    const map = {};
    tas.forEach((t) => (map[t.year] = (map[t.year] || 0) + 1));
    return Object.entries(map).map(([year, value]) => ({ year, value }));
  }, [tas]);

  function saveTA(payload) {
    setLoading(true);
    setTimeout(() => {
      setTAs((prev) => {
        if (payload.id) {
          return prev.map((t) => (t.id === payload.id ? { ...t, ...payload } : t));
        }
        const nextId = prev.length ? Math.max(...prev.map((t) => t.id)) + 1 : 1;
        return [
          { id: nextId, email: `${payload.name.split(" ")[0] || "ta"}@uni.edu`, phone: "+201000000000", ...payload },
          ...prev,
        ];
      });
      setIsAddEditOpen(false);
      setLoading(false);
    }, 300);
  }

  function confirmDelete(t) {
    setDeletingTA(t);
    setIsDeleteOpen(true);
  }

  function doDelete() {
    if (!deletingTA) return;
    setLoading(true);
    setTimeout(() => {
      setTAs((prev) => prev.filter((t) => t.id !== deletingTA.id));
      setIsDeleteOpen(false);
      setDeletingTA(null);
      setLoading(false);
    }, 250);
  }

  return (
    <main className="p-6 space-y-6">
      <header className="flex items-center justify-between mb-6">
        <h1 className="text-3xl font-bold text-accent">Admin</h1>
        <button
          onClick={() => {
            setEditingTA(null);
            setIsAddEditOpen(true);
          }}
          className="inline-flex items-center gap-2 bg-indigo-600 text-white px-4 py-2 rounded-2xl shadow hover:brightness-105"
        >
          <FiPlus /> Add Admin
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

        <section className="lg:col-span-3">
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
                {paginated.map((t) => (
                  <tr key={t.id} className="border-t hover:bg-white/50 transition">
                    <td className="py-3 px-2">{t.name}</td>
                    <td className="py-3 px-2">{t.email}</td>
                    <td className="py-3 px-2">{t.department}</td>
                    <td className="py-3 px-2">{t.year}</td>
                    <td className="py-3 px-2">{t.phone}</td>
                    <td className="py-3 px-2 flex gap-2">
                      <button
                        onClick={() => {
                          setEditingTA(t);
                          setIsAddEditOpen(true);
                        }}
                        className="text-indigo-600 hover:text-indigo-800"
                      >
                        <FiEdit />
                      </button>
                      <button
                        onClick={() => confirmDelete(t)}
                        className="text-red-600 hover:text-red-800"
                      >
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
        </section>

      {isAddEditOpen && (
        <Modal onClose={() => setIsAddEditOpen(false)}>
          <AddEditForm
            initialData={editingTA}
            onCancel={() => setIsAddEditOpen(false)}
            onSave={(data) => saveTA(editingTA ? { ...editingTA, ...data } : data)}
          />
        </Modal>
      )}

      {isDeleteOpen && (
        <Modal onClose={() => setIsDeleteOpen(false)}>
          <div className="space-y-4">
            <p>
              Are you sure you want to delete <strong>{deletingTA?.name}</strong>?
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


