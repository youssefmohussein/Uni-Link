// src/Pages/Admin/AdminDashboardPage.jsx
import React, { useState, useEffect } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import StatsCard from "../../Components/Admin_Components/StatsCard";
import ChartCard from "../../Components/Admin_Components/ChartCard";
// import ActivityTimeline from "../../Components/Admin_Components/ActivityTimeline";
import { FiUsers, FiUserCheck, FiShield, FiUser } from "react-icons/fi";
import { Line, Bar, Doughnut, Radar } from "react-chartjs-2";
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
import { getDashboardStats } from "../../../api/userHandler";

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
  const [loading, setLoading] = useState(true);
  const [stats, setStats] = useState([]);
  const [lineData, setLineData] = useState(null);
  const [barData, setBarData] = useState(null);
  const [doughnutData, setDoughnutData] = useState(null);
  const [radarData, setRadarData] = useState(null);

  const activities = [
    { time:"10:15 AM", description:"John Doe logged in." },
    { time:"09:55 AM", description:"Jane Smith added a new student." },
    { time:"09:30 AM", description:"System backup completed." },
    { time:"09:00 AM", description:"Prof. Alan updated course CS101." },
    { time:"08:45 AM", description:"New professor registered." },
  ];

  useEffect(() => {
    const fetchDashboardData = async () => {
      try {
        setLoading(true);
        const dashboardData = await getDashboardStats();
        
        const { counts, studentsPerFaculty, weeklyActiveUsers, userStatusDistribution, systemHealth } = dashboardData;

        // Set stats cards
        setStats([
          {
            label: "Students",
            value: counts.students,
            icon: FiUsers,
            color: "text-accent",
            sparklineData: {
              labels: weeklyActiveUsers.labels,
              datasets:[{ 
                data: weeklyActiveUsers.data.map((_, i) => Math.floor(counts.students * (0.6 + i * 0.05))), 
                borderColor:"#20B2AA", 
                backgroundColor:"rgba(32,178,170,0.2)" 
              }]
            }
          },
          {
            label: "Professors",
            value: counts.professors,
            icon: FiUserCheck,
            color: "text-accent",
            sparklineData: {
              labels: weeklyActiveUsers.labels,
              datasets:[{ 
                data: weeklyActiveUsers.data.map((_, i) => Math.floor(counts.professors * (0.7 + i * 0.03))), 
                borderColor:"#ffb547", 
                backgroundColor:"rgba(255,181,71,0.2)" 
              }]
            }
          },
          { 
            label: "Admins", 
            value: counts.admins, 
            icon: FiShield, 
            color: "text-accent" 
          },
          {
            label: "Total Users",
            value: counts.totalUsers,
            icon: FiUser,
            color: "text-accent",
            sparklineData: {
              labels: weeklyActiveUsers.labels,
              datasets:[{ 
                data: weeklyActiveUsers.data, 
                borderColor:"#008080", 
                backgroundColor:"rgba(0,128,128,0.2)" 
              }]
            }
          }
        ]);

        // Set line chart data (Weekly Active Users)
        setLineData({
          labels: weeklyActiveUsers.labels,
          datasets:[{ 
            label:"Active Users", 
            data: weeklyActiveUsers.data, 
            borderColor:"#20B2AA", 
            backgroundColor:"rgba(32,178,170,0.2)", 
            tension:0.4 
          }]
        });

        // Set bar chart data (Students per Faculty)
        const facultyColors = ["#008080","#20B2AA","#E0FFFF","#48D1CC","#00CED1","#5F9EA0","#4682B4","#87CEEB"];
        setBarData({
          labels: studentsPerFaculty.labels.length > 0 ? studentsPerFaculty.labels : ["No Data"],
          datasets:[{ 
            label:"Students", 
            data: studentsPerFaculty.data.length > 0 ? studentsPerFaculty.data : [0], 
            backgroundColor: studentsPerFaculty.labels.map((_, i) => facultyColors[i % facultyColors.length])
          }]
        });

        // Set doughnut chart data (User Status Distribution)
        setDoughnutData({ 
          labels: userStatusDistribution.labels, 
          datasets:[{ 
            data: userStatusDistribution.data, 
            backgroundColor:["#20B2AA","#ffb547","#555"] 
          }] 
        });

        // Set radar chart data (System Health)
        setRadarData({ 
          labels: systemHealth.labels, 
          datasets:[{ 
            label:"Health", 
            data: systemHealth.data, 
            backgroundColor:"rgba(32,178,170,0.3)", 
            borderColor:"#20B2AA", 
            borderWidth:2 
          }] 
        });

      } catch (err) {
        console.error("Failed to load dashboard data:", err);
      } finally {
        setLoading(false);
      }
    };

    fetchDashboardData();
  }, []);

  // -------------------- Render --------------------
  if (loading) {
    return (
      <div className="flex min-h-screen bg-bg text-main">
        <Sidebar />
        <div className="flex-1 p-10 flex items-center justify-center">
          <p className="text-gray-400">Loading dashboard...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="flex min-h-screen bg-bg text-main">
      <Sidebar />

      <div className="flex-1 p-10">
        <h1 className="text-4xl font-bold mb-8">Admin Dashboard</h1>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-10">
          {stats.map((s,i)=><StatsCard key={i} {...s} />)}
        </div>

        {/* Charts & Activity */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {lineData && <ChartCard title="Weekly Active Users"><Line data={lineData} /></ChartCard>}
          {barData && <ChartCard title="Students per Faculty"><Bar data={barData} /></ChartCard>}
          {doughnutData && <ChartCard title="User Status Distribution"><Doughnut data={doughnutData} /></ChartCard>}
        </div>

        {/* <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
          {radarData && <ChartCard title="System Health Score"><Radar data={radarData} /></ChartCard>}
          <ActivityTimeline activities={activities} />
        </div> */}
      </div>
    </div>
  );
}
