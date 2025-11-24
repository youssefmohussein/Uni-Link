// src/Components/Admin_Components/StatsCard.jsx
import React from "react";
import { motion } from "framer-motion";
import CountUp from "react-countup";
import { Line } from "react-chartjs-2";

export default function StatsCard({ label, value, icon: Icon, color, sparklineData }) {
  return (
    <motion.div
      className="bg-panel rounded-custom p-5 border border-border shadow-md cursor-pointer hover:shadow-xl transition-smooth"
      whileHover={{ y: -5, scale: 1.02 }}
    >
      <div className="flex items-center justify-between mb-3">
        <div>
          <p className="text-muted text-sm">{label}</p>
          <h2 className="text-2xl font-bold">
            <CountUp end={value} duration={1.5} separator="," />
          </h2>
        </div>
        <Icon className={`text-3xl ${color}`} />
      </div>
      {sparklineData && (
        <div className="mt-3">
          <Line
            data={sparklineData}
            options={{
              responsive: true,
              plugins: { legend: { display: false }, tooltip: { enabled: false } },
              elements: { line: { borderWidth: 2, tension: 0.4 } },
              scales: { x: { display: false }, y: { display: false } },
            }}
          />
        </div>
      )}
    </motion.div>
  );
}
