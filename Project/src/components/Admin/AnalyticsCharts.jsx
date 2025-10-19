import React from "react";

export function UsersPerDeptChart({ data = [] }) {
  return (
    <div className="space-y-2">
      {data.length === 0 && (
        <div className="h-10 flex items-center justify-center text-gray-400 border border-dashed rounded-lg">
          No data
        </div>
      )}
      {data.map(({ department, value }) => (
        <div key={department} className="w-full">
          <div className="flex justify-between text-sm text-gray-600 mb-1">
            <span>{department}</span>
            <span>{value}</span>
          </div>
          <div className="w-full bg-gray-100 rounded h-2">
            <div
              className="bg-indigo-500 h-2 rounded"
              style={{ width: `${Math.min(100, value * 10)}%` }}
            />
          </div>
        </div>
      ))}
    </div>
  );
}

export function UsersPerYearChart({ data = [] }) {
  return (
    <div className="space-y-2">
      {data.length === 0 && (
        <div className="h-10 flex items-center justify-center text-gray-400 border border-dashed rounded-lg">
          No data
        </div>
      )}
      {data.map(({ year, value }) => (
        <div key={year} className="w-full">
          <div className="flex justify-between text-sm text-gray-600 mb-1">
            <span>{year}</span>
            <span>{value}</span>
          </div>
          <div className="w-full bg-gray-100 rounded h-2">
            <div
              className="bg-emerald-500 h-2 rounded"
              style={{ width: `${Math.min(100, value * 10)}%` }}
            />
          </div>
        </div>
      ))}
    </div>
  );
}
