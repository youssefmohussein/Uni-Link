// src/Pages/Admin/AdminDashboardPage.jsx
import React, { useState, useEffect } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import StatsCard from "../../Components/Admin_Components/StatsCard";
import ChartCard from "../../Components/Admin_Components/ChartCard";
import { FiUsers, FiUserCheck, FiShield, FiUser } from "react-icons/fi";
import { Line, Bar, Doughnut } from "react-chartjs-2";
import {
  Chart as ChartJS,
  ArcElement,
  CategoryScale,
  LinearScale,
  BarElement,
  RadialLinearScale,
  PointElement,
  LineElement,
  Tooltip,
  Legend
} from "chart.js";
import { apiRequest } from "../../../api/apiClient";

ChartJS.register(
  ArcElement,
  CategoryScale,
  LinearScale,
  BarElement,
  RadialLinearScale,
  PointElement,
  LineElement,
  Tooltip,
  Legend
);

export default function AdminDashboardPage() {
  const [dashboardData, setDashboardData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  // Fetch dashboard data from backend
  useEffect(() => {
    const fetchDashboardStats = async () => {
      try {
        setLoading(true);
        const result = await apiRequest("/api/dashboard/stats", "GET");

        if (result.status === 'success') {
          setDashboardData(result.data);
          setError(null);
        } else {
          throw new Error(result.message || 'Failed to fetch dashboard data');
        }
      } catch (err) {
        console.error('Error fetching dashboard stats:', err);
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardStats();

    // Refresh data every 30 seconds
    const interval = setInterval(fetchDashboardStats, 30000);
    return () => clearInterval(interval);
  }, []);

  // Loading state
  if (loading && !dashboardData) {
    return (
      <div className="flex min-h-screen bg-bg text-main">
        <Sidebar />
        <div className="flex-1 p-10 flex items-center justify-center">
          <div className="text-2xl" style={{ color: 'var(--admin-accent)' }}>Loading dashboard...</div>
        </div>
      </div>
    );
  }

  // Error state
  if (error && !dashboardData) {
    return (
      <div className="flex min-h-screen bg-bg text-main">
        <Sidebar />
        <div className="flex-1 p-10 flex items-center justify-center">
          <div className="text-center">
            <div className="text-2xl text-red-500 mb-4">Error loading dashboard</div>
            <div className="text-gray-400">{error}</div>
            <button
              onClick={() => window.location.reload()}
              className="mt-4 px-6 py-2 text-bg rounded-lg hover:opacity-80"
              style={{ backgroundColor: 'var(--admin-accent)' }}
            >
              Retry
            </button>
          </div>
        </div>
      </div>
    );
  }

  // Prepare stats cards data
  // Prepare stats cards data
  const stats = dashboardData ? [
    {
      label: "Students",
      value: dashboardData.stats.students,
      icon: FiUsers,
      color: "admin-accent",
      path: "/admin/students",
      sparklineData: {
        labels: dashboardData.weeklyActivity.map(d => d.day),
        datasets: [{
          data: dashboardData.weeklyActivity.map(d => Math.floor(d.count * 0.85)),
          borderColor: "#20B2AA",
          backgroundColor: "rgba(32,178,170,0.2)"
        }]
      }
    },
    {
      label: "Professors",
      value: dashboardData.stats.professors,
      icon: FiUserCheck,
      color: "admin-accent",
      path: "/admin/manage-professors",
      sparklineData: {
        labels: dashboardData.weeklyActivity.map(d => d.day),
        datasets: [{
          data: dashboardData.weeklyActivity.map(d => Math.floor(d.count * 0.1)),
          borderColor: "#ffb547",
          backgroundColor: "rgba(255,181,71,0.2)"
        }]
      }
    },
    {
      label: "Admins",
      value: dashboardData.stats.admins,
      icon: FiShield,
      color: "admin-accent",
      path: "/admin/admin"
    },
    {
      label: "Total Users",
      value: dashboardData.stats.totalUsers,
      icon: FiUser,
      color: "admin-accent",
      path: "/admin/users",
      sparklineData: {
        labels: dashboardData.weeklyActivity.map(d => d.day),
        datasets: [{
          data: dashboardData.weeklyActivity.map(d => d.count),
          borderColor: "#008080",
          backgroundColor: "rgba(0,128,128,0.2)"
        }]
      }
    }
  ] : [];

  // Prepare chart data
  const lineData = dashboardData ? {
    labels: dashboardData.weeklyActivity.map(d => d.day),
    datasets: [{
      label: "Active Users",
      data: dashboardData.weeklyActivity.map(d => d.count),
      borderColor: "#20B2AA",
      backgroundColor: "rgba(32,178,170,0.2)",
      tension: 0.4
    }]
  } : { labels: [], datasets: [] };

  const barData = dashboardData ? {
    labels: dashboardData.facultyDistribution.map(f => f.faculty_name),
    datasets: [{
      label: "Students",
      data: dashboardData.facultyDistribution.map(f => f.student_count),
      backgroundColor: ["#008080", "#20B2AA", "#E0FFFF", "#66B2B2", "#4D9999"]
    }]
  } : { labels: [], datasets: [] };

  const doughnutData = dashboardData ? {
    labels: ["Active", "Idle", "Suspended"],
    datasets: [{
      data: [
        dashboardData.userStatus.active,
        dashboardData.userStatus.idle,
        dashboardData.userStatus.suspended
      ],
      backgroundColor: ["#20B2AA", "#ffb547", "#555"]
    }]
  } : { labels: [], datasets: [] };

  // -------------------- Render --------------------
  return (
    <div className="flex min-h-screen bg-bg text-main">
      <Sidebar />

      <div className="flex-1 p-10">
        <div className="flex justify-between items-center mb-8">
          <h1 className="text-4xl font-bold">Admin Dashboard</h1>
          {loading && <div className="text-sm animate-pulse" style={{ color: 'var(--admin-accent)' }}>Updating...</div>}
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">
          {stats.map((s, i) => <StatsCard key={i} {...s} />)}
        </div>

        {/* Charts */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <ChartCard title="Weekly Active Users"><Line data={lineData} /></ChartCard>
          <ChartCard title="Students per Faculty" path="/admin/university"><Bar data={barData} /></ChartCard>
          <ChartCard title="User Status Distribution"><Doughnut data={doughnutData} /></ChartCard>
        </div>
      </div>
    </div>
  );
}
