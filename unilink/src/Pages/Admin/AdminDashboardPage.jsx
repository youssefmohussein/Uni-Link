import React from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import Card from "../../Components/Admin_Components/Card";
import { Pie } from "react-chartjs-2";
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  Title,
} from "chart.js";

ChartJS.register(ArcElement, Tooltip, Legend, Title);

export default function AdminDashboardPage() {
  // ---------------- STATIC DATA ----------------
  const users = [
    { id: 1, role: "Student", faculty_id: 1 },
    { id: 2, role: "Student", faculty_id: 2 },
    { id: 3, role: "Professor", faculty_id: 1 },
    { id: 4, role: "Admin", faculty_id: null },
    { id: 5, role: "Student", faculty_id: 3 },
    { id: 6, role: "Professor", faculty_id: 3 },
  ];

  const faculties = [
    { faculty_id: 1, faculty_name: "Engineering" },
    { faculty_id: 2, faculty_name: "Business" },
    { faculty_id: 3, faculty_name: "Computer Science" },
  ];

  const students = users.filter((u) => u.role === "Student");
  const professors = users.filter((u) => u.role === "Professor");
  const admins = users.filter((u) => u.role === "Admin");

  // ---------------- CHART DATA ----------------
  const usersPieData = {
    labels: ["Students", "Professors", "Admins"],
    datasets: [
      {
        data: [students.length, professors.length, admins.length],
        backgroundColor: ["#7C3AED", "#14B8A6", "#F59E0B"],
        borderColor: "#0f0f0f",
        borderWidth: 2,
        hoverOffset: 15,
      },
    ],
  };

  const facultyPieData = {
    labels: faculties.map((f) => f.faculty_name),
    datasets: [
      {
        data: faculties.map(
          (f) => students.filter((s) => s.faculty_id === f.faculty_id).length
        ),
        backgroundColor: ["#EC4899", "#3B82F6", "#22C55E"],
        borderColor: "#0f0f0f",
        borderWidth: 2,
        hoverOffset: 15,
      },
    ],
  };

  const chartOptions = {
    responsive: true,
    plugins: {
      legend: {
        labels: { color: "#e5e5e5", font: { size: 13 } },
      },
      title: {
        display: true,
        color: "#fff",
        font: { size: 18, weight: "bold" },
      },
    },
  };

  // Modern glassy card style
  const GlassCard =
    "backdrop-blur-xl bg-white/5 border border-white/10 rounded-2xl p-6 shadow-md hover:shadow-lg hover:bg-white/10 transition-all duration-300";

  // Number style
  const StatNumber = "text-4xl font-extrabold tracking-wide mt-3";

  return (
    <div className="flex min-h-screen bg-[#0c0c0c] text-white">
      <Sidebar />

      <div className="flex-1 p-10">
        {/* HEADER */}
        <div className="mb-10">
          <h1 className="text-4xl font-bold tracking-tight">
            Admin Dashboard
          </h1>
          <p className="text-gray-400 mt-2">
            Overview of system users and faculties
          </p>
        </div>

        {/* STAT CARDS */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
          <Card className={GlassCard}>
            <h2 className="text-lg text-gray-300">Total Users</h2>
            <p className={StatNumber}>{users.length}</p>
          </Card>

          <Card className={GlassCard}>
            <h2 className="text-lg text-gray-300">Students</h2>
            <p className={StatNumber}>{students.length}</p>
          </Card>

          <Card className={GlassCard}>
            <h2 className="text-lg text-gray-300">Professors</h2>
            <p className={StatNumber}>{professors.length}</p>
          </Card>

          <Card className={GlassCard}>
            <h2 className="text-lg text-gray-300">Admins</h2>
            <p className={StatNumber}>{admins.length}</p>
          </Card>
        </div>

        {/* CHARTS */}
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-10">
          {/* Users by Role */}
          <Card className={GlassCard}>
            <Pie
              data={usersPieData}
              options={{
                ...chartOptions,
                title: { ...chartOptions.title, text: "Users by Role" },
              }}
            />
          </Card>

          {/* Students per Faculty */}
          <Card className={GlassCard}>
            <Pie
              data={facultyPieData}
              options={{
                ...chartOptions,
                title: {
                  ...chartOptions.title,
                  text: "Students per Faculty",
                },
              }}
            />
          </Card>
        </div>
      </div>
    </div>
  );
}