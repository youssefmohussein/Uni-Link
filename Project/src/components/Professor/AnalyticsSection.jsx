import React, { useState } from "react";
function AnalyticsSection() {
  const [selectedPeriod, setSelectedPeriod] = useState("month");
  const [selectedMetric, setSelectedMetric] = useState("technology");

  const technologyData = [
    { name: "React", count: 45, percentage: 35, trend: "+12%" },
    { name: "Node.js", count: 38, percentage: 30, trend: "+8%" },
    { name: "Python", count: 32, percentage: 25, trend: "+15%" },
    { name: "Vue.js", count: 28, percentage: 22, trend: "+5%" },
    { name: "MongoDB", count: 25, percentage: 20, trend: "+10%" },
    { name: "TypeScript", count: 22, percentage: 17, trend: "+18%" },
    { name: "Firebase", count: 18, percentage: 14, trend: "+7%" },
    { name: "Docker", count: 15, percentage: 12, trend: "+20%" }
  ];

  const projectStats = [
    { metric: "Total Projects", value: "127", change: "+8%", trend: "up" },
    { metric: "Active Students", value: "89", change: "+12%", trend: "up" },
    { metric: "Completed Projects", value: "94", change: "+15%", trend: "up" },
    { metric: "Average Rating", value: "4.2", change: "+0.3", trend: "up" }
  ];

  const studentActivity = [
    { month: "Jan", projects: 12, students: 45, submissions: 18 },
    { month: "Feb", projects: 18, students: 52, submissions: 24 },
    { month: "Mar", projects: 22, students: 58, submissions: 31 },
    { month: "Apr", projects: 25, students: 63, submissions: 28 },
    { month: "May", projects: 28, students: 67, submissions: 35 },
    { month: "Jun", projects: 32, students: 72, submissions: 41 }
  ];

  const topPerformers = [
    { name: "Ahmed Mohamed", projects: 5, avgRating: 4.8, technologies: ["React", "Node.js", "MongoDB"] },
    { name: "Fatima Ali", projects: 4, avgRating: 4.6, technologies: ["Vue.js", "Python", "Firebase"] },
    { name: "Omar Hassan", projects: 4, avgRating: 4.5, technologies: ["React", "TypeScript", "Docker"] },
    { name: "Mariam Ahmed", projects: 3, avgRating: 4.7, technologies: ["Python", "D3.js", "Flask"] },
    { name: "Youssef Omar", projects: 3, avgRating: 4.4, technologies: ["React Native", "Firebase", "JavaScript"] }
  ];

  const getTrendColor = (trend) => {
    return trend === "up" ? "text-green-400" : "text-red-400";
  };

  const getTrendIcon = (trend) => {
    return trend === "up" ? "↗" : "↘";
  };

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-xl font-semibold">📊 Analytics Dashboard</h2>
        <div className="flex gap-4">
          <select
            value={selectedPeriod}
            onChange={(e) => setSelectedPeriod(e.target.value)}
            className="px-4 py-2 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
          >
            <option value="week">Last Week</option>
            <option value="month">Last Month</option>
            <option value="quarter">Last Quarter</option>
            <option value="year">Last Year</option>
          </select>
        </div>
      </div>

  
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {projectStats.map((stat, index) => (
          <div key={index} className="bg-main/10 rounded-custom p-6">
            <div className="flex justify-between items-start mb-2">
              <h3 className="text-sm font-medium text-muted">{stat.metric}</h3>
              <span className={`text-sm font-medium ${getTrendColor(stat.trend)}`}>
                {getTrendIcon(stat.trend)} {stat.change}
              </span>
            </div>
            <p className="text-2xl font-bold text-main">{stat.value}</p>
          </div>
        ))}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <div className="bg-main/10 rounded-custom p-6">
          <h3 className="text-lg font-semibold mb-6">Technology Usage</h3>
          <div className="space-y-4">
            {technologyData.map((tech, index) => (
              <div key={index} className="flex items-center justify-between">
                <div className="flex-1">
                  <div className="flex justify-between items-center mb-1">
                    <span className="text-main font-medium">{tech.name}</span>
                    <div className="flex items-center gap-2">
                      <span className="text-sm text-muted">{tech.count} students</span>
                      <span className={`text-xs font-medium ${getTrendColor("up")}`}>
                        {tech.trend}
                      </span>
                    </div>
                  </div>
                  <div className="w-full bg-main/20 rounded-full h-2">
                    <div 
                      className="bg-accent h-2 rounded-full transition-all duration-300"
                      style={{ width: `${tech.percentage}%` }}
                    ></div>
                  </div>
                  <span className="text-xs text-muted mt-1 block">{tech.percentage}% of students</span>
                </div>
              </div>
            ))}
          </div>
        </div>

      
        <div className="bg-main/10 rounded-custom p-6">
          <h3 className="text-lg font-semibold mb-6">Top Performing Students</h3>
          <div className="space-y-4">
            {topPerformers.map((student, index) => (
              <div key={index} className="flex items-center justify-between p-3 bg-panel rounded-custom">
                <div className="flex-1">
                  <div className="flex justify-between items-center mb-2">
                    <span className="font-medium text-main">{student.name}</span>
                    <span className="text-sm text-muted">{student.projects} projects</span>
                  </div>
                  <div className="flex items-center gap-2 mb-2">
                    <span className="text-xs text-muted">Avg Rating:</span>
                    <div className="flex">
                      {[...Array(5)].map((_, i) => (
                        <span key={i} className={`text-xs ${i < Math.floor(student.avgRating) ? 'text-yellow-400' : 'text-muted'}`}>
                          ★
                        </span>
                      ))}
                    </div>
                    <span className="text-xs text-muted">{student.avgRating}</span>
                  </div>
                  <div className="flex flex-wrap gap-1">
                    {student.technologies.map((tech, techIndex) => (
                      <span key={techIndex} className="bg-accent/20 text-accent px-2 py-1 rounded text-xs">
                        {tech}
                      </span>
                    ))}
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>

     
      <div className="mt-8 bg-main/10 rounded-custom p-6">
        <h3 className="text-lg font-semibold mb-6">Student Activity Trends</h3>
        <div className="grid grid-cols-6 gap-4">
          {studentActivity.map((month, index) => (
            <div key={index} className="text-center">
              <div className="mb-2">
                <div className="flex flex-col items-center space-y-1">
                  <div 
                    className="w-4 bg-accent rounded"
                    style={{ height: `${(month.projects / 35) * 60}px` }}
                    title={`${month.projects} projects`}
                  ></div>
                  <div 
                    className="w-4 bg-green-400 rounded"
                    style={{ height: `${(month.students / 75) * 60}px` }}
                    title={`${month.students} students`}
                  ></div>
                  <div 
                    className="w-4 bg-yellow-400 rounded"
                    style={{ height: `${(month.submissions / 45) * 60}px` }}
                    title={`${month.submissions} submissions`}
                  ></div>
                </div>
              </div>
              <span className="text-xs text-muted">{month.month}</span>
            </div>
          ))}
        </div>
        <div className="flex justify-center gap-6 mt-4 text-sm">
          <div className="flex items-center gap-2">
            <div className="w-3 h-3 bg-accent rounded"></div>
            <span className="text-muted">Projects</span>
          </div>
          <div className="flex items-center gap-2">
            <div className="w-3 h-3 bg-green-400 rounded"></div>
            <span className="text-muted">Students</span>
          </div>
          <div className="flex items-center gap-2">
            <div className="w-3 h-3 bg-yellow-400 rounded"></div>
            <span className="text-muted">Submissions</span>
          </div>
        </div>
      </div>

      
      <div className="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
        <div className="bg-main/10 rounded-custom p-6">
          <h3 className="text-lg font-semibold mb-4">Project Categories</h3>
          <div className="space-y-3">
            {[
              { category: "Web Applications", count: 45, percentage: 35 },
              { category: "Mobile Apps", count: 28, percentage: 22 },
              { category: "Data Analysis", count: 22, percentage: 17 },
              { category: "AI/ML Projects", count: 18, percentage: 14 },
              { category: "DevOps Tools", count: 14, percentage: 11 }
            ].map((item, index) => (
              <div key={index} className="flex items-center justify-between">
                <span className="text-main">{item.category}</span>
                <div className="flex items-center gap-2">
                  <div className="w-20 bg-main/20 rounded-full h-2">
                    <div 
                      className="bg-accent h-2 rounded-full"
                      style={{ width: `${item.percentage}%` }}
                    ></div>
                  </div>
                  <span className="text-sm text-muted w-8">{item.count}</span>
                </div>
              </div>
            ))}
          </div>
        </div>

        
        <div className="bg-main/10 rounded-custom p-6">
          <h3 className="text-lg font-semibold mb-4">Recent Activity</h3>
          <div className="space-y-3">
            {[
              { action: "Project submitted", user: "Ahmed Mohamed", time: "2 hours ago", type: "submission" },
              { action: "Question asked", user: "Fatima Ali", time: "4 hours ago", type: "question" },
              { action: "Project approved", user: "Omar Hassan", time: "6 hours ago", type: "approval" },
              { action: "New room created", user: "Mariam Ahmed", time: "1 day ago", type: "room" },
              { action: "Post published", user: "Dr. Sarah Johnson", time: "2 days ago", type: "post" }
            ].map((activity, index) => (
              <div key={index} className="flex items-center gap-3 p-2 bg-panel rounded">
                <div className={`w-2 h-2 rounded-full ${
                  activity.type === 'submission' ? 'bg-blue-400' :
                  activity.type === 'question' ? 'bg-yellow-400' :
                  activity.type === 'approval' ? 'bg-green-400' :
                  activity.type === 'room' ? 'bg-purple-400' : 'bg-accent'
                }`}></div>
                <div className="flex-1">
                  <p className="text-sm text-main">{activity.action}</p>
                  <p className="text-xs text-muted">by {activity.user}</p>
                </div>
                <span className="text-xs text-muted">{activity.time}</span>
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

export default AnalyticsSection;






