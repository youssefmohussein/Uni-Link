import React, { useState, useEffect } from "react";

export default function AddEditForm({ initialData, onSave, onCancel }) {
  const [form, setForm] = useState({
    name: "",
    department: "",
    year: "",
  });

  useEffect(() => {
    if (initialData) setForm(initialData);
  }, [initialData]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setForm((prev) => ({ ...prev, [name]: value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    if (!form.name || !form.department || !form.year) {
      alert("Please fill in all fields");
      return;
    }
    onSave(form);
  };

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      <h2 className="text-xl font-semibold text-gray-800 mb-2">
        {initialData ? "Edit User" : "Add New User"}
      </h2>

      <div>
        <label className="block text-sm text-gray-600 mb-1">Name</label>
        <input
          type="text"
          name="name"
          value={form.name}
          onChange={handleChange}
          className="w-full border rounded-lg p-2 outline-none focus:ring focus:ring-blue-200"
        />
      </div>

      <div>
        <label className="block text-sm text-gray-600 mb-1">Department</label>
        <input
          type="text"
          name="department"
          value={form.department}
          onChange={handleChange}
          className="w-full border rounded-lg p-2 outline-none focus:ring focus:ring-blue-200"
        />
      </div>

      <div>
        <label className="block text-sm text-gray-600 mb-1">Year</label>
        <input
          type="number"
          name="year"
          value={form.year}
          onChange={handleChange}
          className="w-full border rounded-lg p-2 outline-none focus:ring focus:ring-blue-200"
        />
      </div>

      <div className="flex justify-end gap-2 pt-3">
        <button
          type="button"
          onClick={onCancel}
          className="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400"
        >
          Cancel
        </button>
        <button
          type="submit"
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
          Save
        </button>
      </div>
    </form>
  );
}

