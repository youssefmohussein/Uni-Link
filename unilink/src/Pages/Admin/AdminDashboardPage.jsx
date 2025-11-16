// src/Pages/Admin/AdminDashboardPage.jsx
import React from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import StatsCard from "../../Components/Admin_Components/StatsCard";
import ChartCard from "../../Components/Admin_Components/ChartCard";
import ActivityTimeline from "../../Components/Admin_Components/ActivityTimeline";
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

  // -------------------- Static Data --------------------
  const stats = [
    {
      label: "Students",
      value: 1240,
      icon: FiUsers,
      color: "text-accent",
      sparklineData: {
        labels: ["Mon","Tue","Wed","Thu","Fri"],
        datasets:[{ data:[200,250,180,300,400], borderColor:"#20B2AA", backgroundColor:"rgba(32,178,170,0.2)" }]
      }
    },
    {
      label: "Professors",
      value: 85,
      icon: FiUserCheck,
      color: "text-accent",
      sparklineData: {
        labels: ["Mon","Tue","Wed","Thu","Fri"],
        datasets:[{ data:[10,15,12,14,18], borderColor:"#ffb547", backgroundColor:"rgba(255,181,71,0.2)" }]
      }
    },
    { label: "Admins", value: 12, icon: FiShield, color: "text-accent" },
    {
      label: "Total Users",
      value: 1337,
      icon: FiUser,
      color: "text-accent",
      sparklineData: {
        labels:["Mon","Tue","Wed","Thu","Fri"],
        datasets:[{ data:[220,280,190,320,418], borderColor:"#008080", backgroundColor:"rgba(0,128,128,0.2)" }]
      }
    }
  ];

  const activities = [
    { time:"10:15 AM", description:"John Doe logged in." },
    { time:"09:55 AM", description:"Jane Smith added a new student." },
    { time:"09:30 AM", description:"System backup completed." },
    { time:"09:00 AM", description:"Prof. Alan updated course CS101." },
    { time:"08:45 AM", description:"New professor registered." },
  ];

  const lineData = {
    labels:["Mon","Tue","Wed","Thu","Fri"],
    datasets:[{ label:"Active Users", data:[320,410,380,460,520], borderColor:"#20B2AA", backgroundColor:"rgba(32,178,170,0.2)", tension:0.4 }]
  };

  const barData = {
    labels:["Engineering","Business","CS"],
    datasets:[{ label:"Students", data:[450,380,410], backgroundColor:["#008080","#20B2AA","#E0FFFF"] }]
  };

  const doughnutData = { labels:["Active","Idle","Suspended"], datasets:[{ data:[72,20,8], backgroundColor:["#20B2AA","#ffb547","#555"] }] };

  const radarData = { labels:["System","Users","Security","Performance","Stability"], datasets:[{ label:"Health", data:[90,75,88,92,86], backgroundColor:"rgba(32,178,170,0.3)", borderColor:"#20B2AA", borderWidth:2 }] };

  // -------------------- Render --------------------
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
          <ChartCard title="Weekly Active Users"><Line data={lineData} /></ChartCard>
          <ChartCard title="Students per Faculty"><Bar data={barData} /></ChartCard>
          <ChartCard title="User Status Distribution"><Doughnut data={doughnutData} /></ChartCard>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
          <ChartCard title="System Health Score"><Radar data={radarData} /></ChartCard>
          <ActivityTimeline activities={activities} />
        </div>
      </div>
    </div>
  );
}
