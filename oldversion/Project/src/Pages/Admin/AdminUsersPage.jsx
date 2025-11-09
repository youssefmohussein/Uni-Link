import React, { useMemo, useState, useEffect } from "react"; 
import Card from "../../components/Admin/Card"; 
import Sidebar from "../../components/Admin/Sidebar"; 


function UserForm({ isOpen, onClose, onSubmit, initialData }) {
  const [formData, setFormData] = useState(
    initialData || {
      name: "",
      id: initialData?.id || "", 
      email: "",
      phone: "",
      department: "",
      year: "",
      role: "Student",
    }
  );

  const isEditing = !!initialData?.id; 
  const formTitle = isEditing ? "Edit User Details" : "Add New User";
  const submitButtonText = isEditing ? "Save Changes" : "Save User";
  const submitButtonClass = isEditing ? "bg-blue-600 hover:bg-blue-700" : "bg-blue-600 hover:bg-blue-700"; 

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prevData) => ({
      ...prevData,
      [name]: name === "year" ? parseInt(value, 10) || "" : value,
    }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!formData.name || !formData.email || !formData.department || !formData.role || (!isEditing && !formData.id)) { 
      alert("Please fill out all required fields (Name, ID, Email, Department, Role).");
      return;
    }
    onSubmit(formData); 
  };

  if (!isOpen) return null;

  return (
    // ... (Your UserForm JSX is unchanged, but now handles asynchronous onSubmit)
    <div className="fixed inset-0 bg-black bg-opacity-70 z-50 flex justify-center items-center p-4 backdrop-blur-sm">
      <Card className="w-full max-w-xl p-6 shadow-2xl rounded-lg transform transition-all duration-300 scale-95 animate-scaleIn">
        <h3 className="text-2xl font-bold text-accent mb-6">{formTitle}</h3>
        <form onSubmit={handleSubmit} className="space-y-5">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {/* Input: Name */}
            <label className="block">
              <span className="text-sm font-medium text-muted">Name:</span>
              <input 
                type="text" name="name" value={formData.name} onChange={handleChange} required 
                className="mt-1 w-full px-4 py-2 bg-panel-light rounded-md border border-panel-border focus:ring-accent focus:border-accent text-main placeholder-muted-light" placeholder="Full Name" 
              />
            </label>
            {/* Input: ID (Disabled if editing - primary key shouldn't change) */}
            <label className="block">
              <span className="text-sm font-medium text-muted">ID:</span>
              <input 
                type="text" name="id" value={formData.id} onChange={handleChange} required={!isEditing} disabled={isEditing} 
                className={`mt-1 w-full px-4 py-2 rounded-md border border-panel-border text-main placeholder-muted-light ${isEditing ? 'bg-gray-600/50 cursor-not-allowed' : 'bg-panel-light focus:ring-accent focus:border-accent'}`} placeholder="Unique ID" 
              />
            </label>
            {/* Input: Email */}
            <label className="block">
              <span className="text-sm font-medium text-muted">Email:</span>
              <input 
                type="email" name="email" value={formData.email} onChange={handleChange} required 
                className="mt-1 w-full px-4 py-2 bg-panel-light rounded-md border border-panel-border focus:ring-accent focus:border-accent text-main placeholder-muted-light" placeholder="user@example.com" 
              />
            </label>
            {/* Input: Phone */}
            <label className="block">
              <span className="text-sm font-medium text-muted">Phone:</span>
              <input 
                type="tel" name="phone" value={formData.phone} onChange={handleChange} 
                className="mt-1 w-full px-4 py-2 bg-panel-light rounded-md border border-panel-border focus:ring-accent focus:border-accent text-main placeholder-muted-light" placeholder="+201XXXXXXXXX" 
              />
            </label>
            {/* Input: Department */}
            <label className="block">
              <span className="text-sm font-medium text-muted">Department:</span>
              <input 
                type="text" name="department" value={formData.department} onChange={handleChange} required 
                className="mt-1 w-full px-4 py-2 bg-panel-light rounded-md border border-panel-border focus:ring-accent focus:border-accent text-main placeholder-muted-light" placeholder="e.g., Computer Science" 
              />
            </label>
            {/* Input: Year */}
            <label className="block">
              <span className="text-sm font-medium text-muted">Year:</span>
              <input 
                type="number" name="year" value={formData.year} onChange={handleChange} placeholder="e.g., 2025" 
                className="mt-1 w-full px-4 py-2 bg-panel-light rounded-md border border-panel-border focus:ring-accent focus:border-accent text-main placeholder-muted-light" 
              />
            </label>
          </div>
          {/* Select: Role */}
          <label className="block">
            <span className="text-sm font-medium text-muted">Role:</span>
            <select name="role" value={formData.role} onChange={handleChange} required className="mt-1 w-full px-4 py-2 bg-panel-light rounded-md border border-panel-border focus:ring-accent focus:border-accent text-main">
              <option value="Student">Student</option>
              <option value="TA">Teaching Assistant (TA)</option>
              <option value="Professor">Professor</option>
            </select>
          </label>

          {/* Buttons */}
          <div className="flex justify-end space-x-3 pt-6">
            <button
              type="button"
              onClick={onClose}
              className="px-6 py-2 border border-gray-600 text-muted rounded-lg hover:bg-gray-700 hover:text-white transition-all duration-200"
            >
              Cancel
            </button>
            <button
              type="submit"
              className={`px-6 py-2 text-white rounded-lg font-semibold transition-all duration-200 ${submitButtonClass}`}
            >
              {submitButtonText}
            </button>
          </div>
        </form>
      </Card>
    </div>
  );
}

const API_ENDPOINT = 'http://localhost/backend/index.php/addUser'; // <--- ⚠️ CHECK AND CHANGE THIS URL

export default function AdminUsersPage() {
  const [users, setUsers] = useState([]);
  const [editingUser, setEditingUser] = useState(null); 
  const [isAdding, setIsAdding] = useState(false);
  const [isLoading, setIsLoading] = useState(true); // 3. Added loading state

 
  const closeForm = () => {
    setEditingUser(null);
    setIsAdding(false);
  };


  // CREATE Operation
  const handleAddUser = async (newUser) => {
    try {
      const response = await fetch(API_ENDPOINT, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(newUser), 
      });

      if (!response.ok) {
        // Attempt to read error message from server if available
        const errorData = await response.json().catch(() => ({ message: 'Unknown server error.' }));
        throw new Error(`Failed to add user. ${errorData.message}`);
      }

      // The server should return the newly created user object (including its permanent ID)
      const addedUser = await response.json(); 
      
      setUsers(prevUsers => [...prevUsers, addedUser]);
      closeForm();
      alert("✅ User added successfully!");
    } catch (error) {
      console.error("Error adding user:", error);
      alert(`❌ Failed to add user: ${error.message}`);
    }
  };

  // UPDATE Operation
  const handleEditUser = async (updatedUser) => {
    try {
      // Assuming your API uses the ID in the URL for updates (e.g., /api/users/123)
      const response = await fetch(`${API_ENDPOINT}/${updatedUser.id}`, {
        method: 'PUT', // Use PUT or PATCH for updates
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(updatedUser),
      });

      if (!response.ok) {
        const errorData = await response.json().catch(() => ({ message: 'Unknown server error.' }));
        throw new Error(`Failed to edit user. ${errorData.message}`);
      }

      // Update the local state with the edited user data
      setUsers(prevUsers => 
        prevUsers.map(u => (u.id === updatedUser.id ? updatedUser : u))
      );
      closeForm();
      alert("✅ Changes saved successfully!");
    } catch (error) {
      console.error("Error editing user:", error);
      alert(`❌ Failed to save changes: ${error.message}`);
    }
  };

  // DELETE Operation
  const handleDeleteUser = async (userId) => {
    if (window.confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
      try {
        const response = await fetch(`${API_ENDPOINT}/${userId}`, {
          method: 'DELETE',
        });

        if (!response.ok) {
          throw new Error(`Failed to delete user. Server status: ${response.status}`);
        }

        // Filter the user out of the local state upon successful deletion from DB
        setUsers(prevUsers => prevUsers.filter(u => u.id !== userId));
        alert("✅ User deleted successfully!");
        
      } catch (error) {
        console.error("Error deleting user:", error);
        alert(`❌ Failed to delete user: ${error.message}`);
      }
    }
  };
  
  // The rest of your state management and helper functions (openEditModal, closeForm)
  const openEditModal = (user) => {
    setEditingUser(user); // Sets the user data to be loaded into the form
    setIsAdding(false);
  };
  
  // --- Filtering and Pagination State (No changes needed here) ---
  const [query, setQuery] = useState("");
  const [filterDept, setFilterDept] = useState("All");
  const [filterYear, setFilterYear] = useState("All");
  const [filterRole, setFilterRole] = useState("All");
  const [page, setPage] = useState(1);
  const pageSize = 6;

  const departments = useMemo(() => ["All", ...new Set(users.map((u) => u.department))], [users]);
  // Convert year to string for consistent filtering against string 'All'
  const years = useMemo(() => ["All", ...new Set(users.map((u) => String(u.year)))], [users]); 
  const roles = useMemo(() => ["All", ...new Set(users.map((u) => u.role))], [users]);

  const filtered = useMemo(() => {
    let list = [...users];
    if (query.trim()) {
      const q = query.toLowerCase();
      list = list.filter((u) =>
        [u.name, u.email, u.department, u.year, u.phone, u.role]
          .some((v) => String(v ?? "").toLowerCase().includes(q))
      );
    }
    if (filterDept !== "All") list = list.filter((u) => u.department === filterDept);
    // Use loose equality (==) or ensure both sides are strings for comparison with filterYear
    if (filterYear !== "All") list = list.filter((u) => String(u.year) === filterYear); 
    if (filterRole !== "All") list = list.filter((u) => u.role === filterRole);
    return list;
  }, [users, query, filterDept, filterYear, filterRole]);

  const totalPages = Math.max(1, Math.ceil(filtered.length / pageSize));
  const paginated = filtered.slice((page - 1) * pageSize, page * pageSize);

  return (
    <div className="flex min-h-screen bg-main text-main font-main">
    
      <Sidebar />
      
      {/* Renders the inline form when adding (isAdding=true) or editing (editingUser is set) */}
      <UserForm 
        isOpen={isAdding || !!editingUser} 
        onClose={closeForm} 
        initialData={editingUser} // Passes the user data for pre-filling
        onSubmit={editingUser ? handleEditUser : handleAddUser} // Uses the correct async function
      />
      
      <main className="flex-1 p-6 space-y-6 overflow-auto">
        {/* Main Dashboard Header */}
        <div className="flex justify-between items-center bg-panel rounded-lg p-4 shadow-md mb-6">
          <h1 className="text-3xl font-extrabold text-accent">Users Dashboard</h1>
          <span className="text-lg font-medium text-muted">Total: {users.length} users</span>
        </div>
        
        
          <>
            {/* Filters Card */}
            <Card title="Filters" className="mb-6 bg-panel p-6 shadow-md rounded-lg">
              <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {/* Search Input */}
                <input
                  value={query}
                  onChange={(e) => { setQuery(e.target.value); setPage(1); }}
                  placeholder="Search users..."
                  className="w-full px-4 py-2 rounded-md border border-panel-border bg-panel-light text-main focus:ring-accent focus:border-accent placeholder-muted-light"
                />
                {/* Department Filter */}
                <select
                  value={filterDept}
                  onChange={(e) => { setFilterDept(e.target.value); setPage(1); }}
                  className="w-full px-4 py-2 rounded-md border border-panel-border bg-panel-light text-main focus:ring-accent focus:border-accent"
                >
                  {departments.map((d) => (<option key={d} value={d}>{d}</option>))}
                </select>
                {/* Year Filter */}
                <select
                  value={filterYear}
                  onChange={(e) => { setFilterYear(e.target.value); setPage(1); }}
                  className="w-full px-4 py-2 rounded-md border border-panel-border bg-panel-light text-main focus:ring-accent focus:border-accent"
                >
                  {years.map((y) => (<option key={y} value={y}>{y}</option>))}
                </select>
                {/* Role Filter */}
                <select
                  value={filterRole}
                  onChange={(e) => { setFilterRole(e.target.value); setPage(1); }}
                  className="w-full px-4 py-2 rounded-md border border-panel-border bg-panel-light text-main focus:ring-accent focus:border-accent"
                >
                  {roles.map((r) => (<option key={r} value={r}>{r}</option>))}
                </select>
              </div>
            </Card>

            {/* Users List Table */}
            <section className="lg:col-span-3">
              <div className="bg-panel p-6 shadow-md rounded-lg">
                {/* Header with Relocated "Add User" Button (Far right) */}
                <div className="flex justify-between items-center mb-4 pb-4 border-b border-panel-border">
                    <h2 className="text-xl font-bold text-main">Users List</h2>
                    <button
                        onClick={() => setIsAdding(true)}
                        className="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold shadow-md hover:bg-blue-700 transition-colors duration-200 flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" className="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fillRule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clipRule="evenodd" /></svg>
                        Add User
                    </button>
                </div>

                <div className="overflow-x-auto">
                  {/* Conditional rendering for empty state */}
                  {paginated.length === 0 ? (
                    <p className="text-center text-muted py-10">
                      No users found. Try adjusting your filters or click **Add User** to start.
                    </p>
                  ) : (
                    <table className="min-w-full divide-y divide-panel-border">
                      <thead>
                        <tr className="bg-panel-light">
                          <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-muted">Name</th>
                          <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-muted">Email</th>
                          <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-muted">Role</th>
                          <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-muted">Department</th>
                          <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-muted">Year</th>
                          <th className="text-left py-3 px-4 text-xs font-semibold uppercase tracking-wider text-muted">Actions</th>
                        </tr>
                      </thead>
                      <tbody className="divide-y divide-panel-border">
                        {paginated.map((u) => (
                          <tr key={u.id} className="hover:bg-panel-light transition-colors duration-150">
                            <td className="py-3 px-4 font-medium text-main">{u.name}</td>
                            <td className="py-3 px-4 text-muted">{u.email}</td>
                            <td className="py-3 px-4">
                              <span className="px-3 py-1 rounded-full text-xs font-medium bg-blue-500/20 text-blue-300">
                                {u.role}
                              </span>
                            </td>
                            <td className="py-3 px-4 text-muted">{u.department}</td>
                            <td className="py-3 px-4 text-muted">{u.year}</td>
                            
                            {/* --- Actions Column --- */}
                            <td className="py-3 px-4 space-x-2 whitespace-nowrap">
                              {/* Edit Button */}
                              <button
                                  onClick={() => openEditModal(u)}
                                  className="p-1 rounded-full text-yellow-500 hover:bg-yellow-500/20 transition-colors group"
                                  title="Edit User"
                              >
                                  <i className="fa-solid fa-pen-to-square text-lg"></i>
                              </button>
                              
                              {/* Delete Button */}
                              <button
                                  onClick={() => handleDeleteUser(u.id)}
                                  className="p-1 rounded-full text-red-500 hover:bg-red-500/20 transition-colors group"
                                  title="Delete User"
                              >
                                  <i className="fa-solid fa-trash text-lg"></i>
                              </button>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  )}
                </div>

                {/* Pagination */}
                {filtered.length > 0 && (
                  <div className="flex justify-between items-center mt-6 px-4 text-muted text-sm">
                    <div className="text-muted">
                      Showing {((page - 1) * pageSize) + 1} to {Math.min(page * pageSize, filtered.length)} of {filtered.length} users
                    </div>
                    <div className="flex gap-2">
                      <button onClick={() => setPage(p => Math.max(1, p - 1))} disabled={page === 1} className="px-4 py-2 border border-panel-border rounded-md text-main hover:bg-panel-light disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">Previous</button>
                      <span className="px-4 py-2 text-main bg-panel-light rounded-md border border-panel-border">Page {page} of {totalPages}</span>
                      <button onClick={() => setPage(p => Math.min(totalPages, p + 1))} disabled={page === totalPages} className="px-4 py-2 border border-panel-border rounded-md text-main hover:bg-panel-light disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">Next</button>
                    </div>
                  </div>
                )}
              </div>
            </section>
          </>
        )
      </main>
    </div>
  );
}