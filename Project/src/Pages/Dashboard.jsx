import React, { useMemo, useState } from "react";
import Card from "../components/Admin/Card";
import { UsersPerDeptChart, UsersPerYearChart } from "../components/Admin/AnalyticsCharts";

export default function Dashboard() {
  // Sample data - in a real app, this would come from an API
  const [users] = useState([
    { id: 1, name: "Ali Mohamed", email: "ali@example.com", phone: "+201111111111", department: "Computer Science", year: 2024 },
    { id: 2, name: "Sara Ahmed", email: "sara@example.com", phone: "+201222222222", department: "Information Systems", year: 2023 },
    { id: 3, name: "Omar Ehab", email: "omar@example.com", phone: "+201333333333", department: "Software Engineering", year: 2025 },
    { id: 4, name: "Nour Hassan", email: "nour@example.com", phone: "+201444444444", department: "AI", year: 2024 },
    { id: 5, name: "Ahmed Ali", email: "ahmed@example.com", phone: "+201555555555", department: "Computer Science", year: 2023 },
    { id: 6, name: "Fatma Mohamed", email: "fatma@example.com", phone: "+201666666666", department: "Information Systems", year: 2024 },
  ]);

  const [professors] = useState([
    { id: 1, name: "Dr. Mohamed Hassan", department: "Computer Science", courses: 3 },
    { id: 2, name: "Dr. Sara Ahmed", department: "Information Systems", courses: 2 },
    { id: 3, name: "Dr. Omar Ehab", department: "Software Engineering", courses: 4 },
    { id: 4, name: "Dr. Nour Hassan", department: "AI", courses: 2 },
  ]);

  const [tas] = useState([
    { id: 1, name: "Ahmed Ali", department: "Computer Science", courses: 2 },
    { id: 2, name: "Fatma Mohamed", department: "Information Systems", courses: 1 },
    { id: 3, name: "Youssef Hassan", department: "Software Engineering", courses: 3 },
  ]);

  // Calculate analytics data
  const usersPerDept = useMemo(() => {
    const deptCount = users.reduce((acc, user) => {
      acc[user.department] = (acc[user.department] || 0) + 1;
      return acc;
    }, {});
    return Object.entries(deptCount).map(([department, value]) => ({ department, value }));
  }, [users]);

  const usersPerYear = useMemo(() => {
    const yearCount = users.reduce((acc, user) => {
      acc[user.year] = (acc[user.year] || 0) + 1;
      return acc;
    }, {});
    return Object.entries(yearCount).map(([year, value]) => ({ year, value }));
  }, [users]);

  const professorsPerDept = useMemo(() => {
    const deptCount = professors.reduce((acc, prof) => {
      acc[prof.department] = (acc[prof.department] || 0) + 1;
      return acc;
    }, {});
    return Object.entries(deptCount).map(([department, value]) => ({ department, value }));
  }, [professors]);

  const tasPerDept = useMemo(() => {
    const deptCount = tas.reduce((acc, ta) => {
      acc[ta.department] = (acc[ta.department] || 0) + 1;
      return acc;
    }, {});
    return Object.entries(deptCount).map(([department, value]) => ({ department, value }));
  }, [tas]);

  return (
    <div className="p-6 space-y-6">
      {/* Header */}
      <div className="flex justify-between items-center">
        <h1 className="text-3xl font-bold text-accent">Dashboard</h1>
        <div className="text-sm text-gray-500">
          Last updated: {new Date().toLocaleDateString()}
        </div>
      </div>

      {/* Stats Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <Card title="Total Users">
          <div className="text-3xl font-bold text-indigo-600">{users.length}</div>
          <div className="text-sm text-gray-500">Active users</div>
        </Card>
        <Card title="Professors">
          <div className="text-3xl font-bold text-emerald-600">{professors.length}</div>
          <div className="text-sm text-gray-500">Teaching staff</div>
        </Card>
        <Card title="Teaching Assistants">
          <div className="text-3xl font-bold text-purple-600">{tas.length}</div>
          <div className="text-sm text-gray-500">Support staff</div>
        </Card>
        <Card title="Departments">
          <div className="text-3xl font-bold text-orange-600">
            {new Set([...users, ...professors, ...tas].map(u => u.department)).size}
          </div>
          <div className="text-sm text-gray-500">Active departments</div>
        </Card>
      </div>

      {/* Charts Section */}
      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Users Analytics */}
        <div className="space-y-6">
          <Card title="Users per Department">
            <UsersPerDeptChart data={usersPerDept} />
          </Card>
          <Card title="Users per Year">
            <UsersPerYearChart data={usersPerYear} />
          </Card>
        </div>

        {/* Staff Analytics */}
        <div className="space-y-6">
          <Card title="Professors per Department">
            <UsersPerDeptChart data={professorsPerDept} />
          </Card>
          <Card title="TAs per Department">
            <UsersPerDeptChart data={tasPerDept} />
          </Card>
        </div>
      </div>

      {/* Recent Activity */}
      <Card title="Recent Activity">
        <div className="space-y-3">
          <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
            <div className="w-2 h-2 bg-green-500 rounded-full"></div>
            <div className="flex-1">
              <div className="text-sm font-medium">New user registered</div>
              <div className="text-xs text-gray-500">Ali Mohamed joined Computer Science</div>
            </div>
            <div className="text-xs text-gray-400">2 hours ago</div>
          </div>
          <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
            <div className="w-2 h-2 bg-blue-500 rounded-full"></div>
            <div className="flex-1">
              <div className="text-sm font-medium">Professor assigned</div>
              <div className="text-xs text-gray-500">Dr. Sara Ahmed assigned to Information Systems</div>
            </div>
            <div className="text-xs text-gray-400">4 hours ago</div>
          </div>
          <div className="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
            <div className="w-2 h-2 bg-purple-500 rounded-full"></div>
            <div className="flex-1">
              <div className="text-sm font-medium">TA updated</div>
              <div className="text-xs text-gray-500">Youssef Hassan courses updated</div>
            </div>
            <div className="text-xs text-gray-400">6 hours ago</div>
          </div>
        </div>
      </Card>
    </div>
  );
}
