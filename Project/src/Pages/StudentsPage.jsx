import React, { useMemo, useState } from "react";
import Card from "../components/Card";

export default function StudentsPage() {
  const [students] = useState([
    { id: 1, name: "Ali Mohamed", email: "ali@example.com", phone: "+201111111111", department: "Computer Science", year: 2024, gpa: 3.8 },
    { id: 2, name: "Sara Ahmed", email: "sara@example.com", phone: "+201222222222", department: "Information Systems", year: 2023, gpa: 3.9 },
    { id: 3, name: "Omar Ehab", email: "omar@example.com", phone: "+201333333333", department: "Software Engineering", year: 2025, gpa: 3.7 },
    { id: 4, name: "Nour Hassan", email: "nour@example.com", phone: "+201444444444", department: "AI", year: 2024, gpa: 3.6 },
    { id: 5, name: "Ahmed Ali", email: "ahmed@example.com", phone: "+201555555555", department: "Computer Science", year: 2023, gpa: 3.5 },
    { id: 6, name: "Fatma Mohamed", email: "fatma@example.com", phone: "+201666666666", department: "Information Systems", year: 2024, gpa: 3.8 },
    { id: 7, name: "Youssef Hassan", email: "youssef@example.com", phone: "+201777777777", department: "Software Engineering", year: 2025, gpa: 3.9 },
    { id: 8, name: "Mariam Ali", email: "mariam@example.com", phone: "+201888888888", department: "AI", year: 2023, gpa: 3.7 },
  ]);

  const [query, setQuery] = useState("");
  const [filterDept, setFilterDept] = useState("All");
  const [filterYear, setFilterYear] = useState("All");
  const [page, setPage] = useState(1);
  const pageSize = 6;

  const departments = useMemo(() => ["Department", ...new Set(students.map((s) => s.department))], [students]);
  const years = useMemo(() => ["Year", ...new Set(students.map((s) => s.year))], [students]);

  const filtered = useMemo(() => {
    let list = [...students];
    if (query) {
      list = list.filter((student) =>
        student.name.toLowerCase().includes(query.toLowerCase()) ||
        student.email.toLowerCase().includes(query.toLowerCase())
      );
    }
    if (filterDept !== "All") {
      list = list.filter((student) => student.department === filterDept);
    }
    if (filterYear !== "All") {
      list = list.filter((student) => student.year.toString() === filterYear);
    }
    return list;
  }, [students, query, filterDept, filterYear]);

  const paginated = useMemo(() => {
    const start = (page - 1) * pageSize;
    return filtered.slice(start, start + pageSize);
  }, [filtered, page, pageSize]);

  const totalPages = Math.ceil(filtered.length / pageSize);

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold text-gray-900">Students</h1>
        <div className="text-sm text-gray-500">
          Total: {students.length} students
        </div>
      </div>

      {/* Filters & Charts */}
      <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <aside className="lg:col-span-1 space-y-4">
          <Card title="Filters">
            <div className="space-y-3">
              <input
                value={query}
                onChange={(e) => {
                  setQuery(e.target.value);
                  setPage(1);
                }}
                placeholder="Search students..."
                className="w-full px-3 py-2 rounded-lg border"
              />
              <select 
                value={filterDept}
                onChange={(e) => {
                  setFilterDept(e.target.value);
                  setPage(1);
                }}
                className="w-full px-3 py-2 rounded-lg border"
              >
                {departments.map((d) => (
                  <option key={d}>{d}</option>
                ))}
              </select>

              <select 
                value={filterYear}
                onChange={(e) => {
                  setFilterYear(e.target.value);
                  setPage(1);
                }}
                className="w-full px-3 py-2 rounded-lg border"
              >
                {years.map((y) => (
                  <option key={y}>{y}</option>
                ))}
              </select>

            </div>
          </Card>
        </aside>

        {/* Students Table */}
        <section className="lg:col-span-3">
          <Card title="Students List">
            <div className="overflow-x-auto">
              <table className="w-full">
                <thead>
                  <tr className="border-b">
                    <th className="text-left py-3 px-4 font-medium text-gray-600">Name</th>
                    <th className="text-left py-3 px-4 font-medium text-gray-600">Email</th>
                    <th className="text-left py-3 px-4 font-medium text-gray-600">Department</th>
                    <th className="text-left py-3 px-4 font-medium text-gray-600">Year</th>
                    <th className="text-left py-3 px-4 font-medium text-gray-600">GPA</th>
                  </tr>
                </thead>
                <tbody>
                  {paginated.map((student) => (
                    <tr key={student.id} className="border-b hover:bg-gray-50">
                      <td className="py-3 px-4">
                        <div className="font-medium text-gray-900">{student.name}</div>
                      </td>
                      <td className="py-3 px-4 text-gray-600">{student.email}</td>
                      <td className="py-3 px-4">
                        <span className="px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs">
                          {student.department}
                        </span>
                      </td>
                      <td className="py-3 px-4 text-gray-600">{student.year}</td>
                      <td className="py-3 px-4">
                        <span className={`px-2 py-1 rounded-full text-xs ${
                          student.gpa >= 3.7 ? 'bg-green-100 text-green-800' :
                          student.gpa >= 3.0 ? 'bg-yellow-100 text-yellow-800' :
                          'bg-red-100 text-red-800'
                        }`}>
                          {student.gpa}
                        </span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Pagination */}
            {totalPages > 1 && (
              <div className="flex justify-between items-center mt-4 px-4">
                <div className="text-sm text-gray-500">
                  Showing {((page - 1) * pageSize) + 1} to {Math.min(page * pageSize, filtered.length)} of {filtered.length} students
                </div>
                <div className="flex gap-2">
                  <button
                    onClick={() => setPage(p => Math.max(1, p - 1))}
                    disabled={page === 1}
                    className="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Previous
                  </button>
                  <span className="px-3 py-1 text-sm">
                    Page {page} of {totalPages}
                  </span>
                  <button
                    onClick={() => setPage(p => Math.min(totalPages, p + 1))}
                    disabled={page === totalPages}
                    className="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Next
                  </button>
                </div>
              </div>
            )}
          </Card>
        </section>
      </div>
    </div>
  );
}
