import React, { useMemo, useState } from "react";
import Card from "../../components/Admin/Card";

export default function AdminUsersPage() {
  const [users] = useState([
    { id: 1, name: "Ali Mohamed", email: "ali@example.com", phone: "+201111111111", department: "Computer Science", year: 2024, role: "Student" },
    { id: 2, name: "Sara Ahmed", email: "sara@example.com", phone: "+201222222222", department: "Information Systems", year: 2023, role: "Student" },
    { id: 3, name: "Omar Ehab", email: "omar@example.com", phone: "+201333333333", department: "Software Engineering", year: 2025, role: "Student" },
    { id: 4, name: "Nour Hassan", email: "nour@example.com", phone: "+201444444444", department: "AI", year: 2024, role: "Student" },
    { id: 5, name: "Dr. Mohamed Hassan", email: "mohamed@university.edu", phone: "+201555555555", department: "Computer Science", year: 2020, role: "Professor" },
    { id: 6, name: "Dr. Sara Ahmed", email: "sara.prof@university.edu", phone: "+201666666666", department: "Information Systems", year: 2019, role: "Professor" },
    { id: 7, name: "Ahmed Ali", email: "ahmed.ta@university.edu", phone: "+201777777777", department: "Software Engineering", year: 2022, role: "TA" },
    { id: 8, name: "Fatma Mohamed", email: "fatma.ta@university.edu", phone: "+201888888888", department: "AI", year: 2021, role: "TA" },
  ]);

  const [query, setQuery] = useState("");
  const [filterDept, setFilterDept] = useState("All");
  const [filterYear, setFilterYear] = useState("All");
  const [filterRole, setFilterRole] = useState("All");
  const [page, setPage] = useState(1);
  const pageSize = 6;

  const departments = useMemo(() => ["All", ...new Set(users.map((u) => u.department))], [users]);
  const years = useMemo(() => ["All", ...new Set(users.map((u) => u.year))], [users]);
  const roles = useMemo(() => ["All", ...new Set(users.map((u) => u.role))], [users]);

  const filtered = useMemo(() => {
    let list = [...users];
    if (query.trim()) {
      const q = query.toLowerCase();
      list = list.filter((u) =>
        [u.name, u.email, u.department, u.year, u.phone, u.role]
          .some((v) => String(v ?? "").toLowerCase().includes(q))
      );
    }
    if (filterDept !== "All") list = list.filter((u) => u.department === filterDept);
    if (filterYear !== "All") list = list.filter((u) => u.year === filterYear);
    if (filterRole !== "All") list = list.filter((u) => u.role === filterRole);
    return list;
  }, [users, query, filterDept, filterYear, filterRole]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
  const paginated = filtered.slice((page - 1) * pageSize, page * pageSize);

  const usersPerDept = useMemo(() => {
    const map = {};
    users.forEach((u) => (map[u.department] = (map[u.department] || 0) + 1));
    return Object.entries(map).map(([department, value]) => ({ department, value }));
  }, [users]);

  const usersPerRole = useMemo(() => {
    const map = {};
    users.forEach((u) => (map[u.role] = (map[u.role] || 0) + 1));
    return Object.entries(map).map(([role, value]) => ({ role, value }));
  }, [users]);

  return (
    <div className="p-6 space-y-6 bg-main text-main min-h-screen font-main">
      {/* Header */}
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold text-accent">Users</h1>
        <div className="text-sm text-muted">Total: {users.length} users</div>
      </div>

      {/* Filters & Charts */}
      <Card title="Filters" className="mb-6">
        <div className="grid grid-cols-1 md:grid-cols-3 gap-4 justify-center items-center ">
          <input
            value={query}
            onChange={(e) => {
              setQuery(e.target.value);
              setPage(1);
            }}
            placeholder="Search..."
            className="w-full px-3 py-2 rounded-lg border"
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

        {/* User Table */}
        <section className="lg:col-span-3">
          <Card title="Users List">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b border-white/10 text-muted">
                    <th className="text-left py-3 px-4 font-medium">Name</th>
                    <th className="text-left py-3 px-4 font-medium">Email</th>
                    <th className="text-left py-3 px-4 font-medium">Role</th>
                    <th className="text-left py-3 px-4 font-medium">Department</th>
                    <th className="text-left py-3 px-4 font-medium">Year</th>
                    <th className="text-left py-3 px-4 font-medium">Phone</th>
                  </tr>
                </thead>
                <tbody>
                  {paginated.map((u) => (
                    <tr key={u.id} className="border-b border-white/10 hover:bg-panel/80 transition">
                      <td className="py-3 px-4 font-medium text-main">{u.name}</td>
                      <td className="py-3 px-4 text-muted">{u.email}</td>
                      <td className="py-3 px-4">
                        <span className="px-2 py-1 rounded-full text-xs bg-accent/20 text-accent">
                          {u.role}
                        </span>
                      </td>
                      <td className="py-3 px-4 text-muted">{u.department}</td>
                      <td className="py-3 px-4 text-muted">{u.year}</td>
                      <td className="py-3 px-4 text-muted">{u.phone}</td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Pagination */}
            <div className="flex justify-between items-center mt-4 px-4 text-muted">
              <div className="text-sm">
                Showing {((page - 1) * pageSize) + 1} to {Math.min(page * pageSize, filtered.length)} of {filtered.length} users
              </div>
              <div className="flex gap-2">
                <button
                  onClick={() => setPage(p => Math.max(1, p - 1))}
                  disabled={page === 1}
                  className="px-3 py-1 border border-white/10 rounded-custom hover:bg-accent/10 disabled:opacity-50"
                >
                  Previous
                </button>
                <span className="px-3 py-1 text-sm text-accent">
                  Page {page} of {totalPages}
                </span>
                <button
                  onClick={() => setPage(p => Math.min(totalPages, p + 1))}
                  disabled={page === totalPages}
                  className="px-3 py-1 border border-white/10 rounded-custom hover:bg-accent/10 disabled:opacity-50"
                >
                  Next
                </button>
              </div>
            </div>
          </Card>
        </section>
      </div>
  );
}
