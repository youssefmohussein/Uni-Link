import React, { useState } from "react";

/* Project Rooms Section for Professor with current data without any database connection */
function ProjectRoomsSection() {
  const [searchTerm, setSearchTerm] = useState("");
  const [filterStatus, setFilterStatus] = useState("all");
  const [rooms, setRooms] = useState([
    {
      id: 1,
      name: "E-Commerce Development Room",
      description: "Collaborative space for building modern e-commerce solutions",
      technology: "React + Node.js",
      capacity: 6,
      currentMembers: 4,
      status: "active",
      createdDate: "2024-01-15",
      deadline: "2024-03-15",
      members: [
        { name: "Ahmed Mohamed", role: "Team Lead", status: "active" },
        { name: "Fatima Ali", role: "Frontend Developer", status: "active" },
        { name: "Omar Hassan", role: "Backend Developer", status: "active" },
        { name: "Mariam Ahmed", role: "UI/UX Designer", status: "active" }
      ],
      progress: 65,
      milestones: [
        { title: "Project Setup", completed: true, date: "2024-01-20" },
        { title: "Database Design", completed: true, date: "2024-01-25" },
        { title: "API Development", completed: false, date: "2024-02-10" },
        { title: "Frontend Implementation", completed: false, date: "2024-02-20" },
        { title: "Testing & Deployment", completed: false, date: "2024-03-15" }
      ]
    },
    {
      id: 2,
      name: "Mobile App Development Lab",
      description: "Cross-platform mobile app development using React Native",
      technology: "React Native + Firebase",
      capacity: 4,
      currentMembers: 3,
      status: "active",
      createdDate: "2024-01-10",
      deadline: "2024-04-01",
      members: [
        { name: "Youssef Omar", role: "Mobile Developer", status: "active" },
        { name: "Nour Hassan", role: "Backend Developer", status: "active" },
        { name: "Layla Mohamed", role: "Designer", status: "active" }
      ],
      progress: 40,
      milestones: [
        { title: "Project Planning", completed: true, date: "2024-01-15" },
        { title: "UI/UX Design", completed: true, date: "2024-01-25" },
        { title: "Core Features", completed: false, date: "2024-02-15" },
        { title: "Testing & Optimization", completed: false, date: "2024-03-20" },
        { title: "App Store Submission", completed: false, date: "2024-04-01" }
      ]
    },
    {
      id: 3,
      name: "AI/ML Research Group",
      description: "Exploring machine learning applications in web development",
      technology: "Python + TensorFlow",
      capacity: 5,
      currentMembers: 2,
      status: "planning",
      createdDate: "2024-01-20",
      deadline: "2024-05-01",
      members: [
        { name: "Ahmed Ibrahim", role: "ML Engineer", status: "active" },
        { name: "Sara Mohamed", role: "Data Scientist", status: "active" }
      ],
      progress: 15,
      milestones: [
        { title: "Research Proposal", completed: false, date: "2024-02-01" },
        { title: "Data Collection", completed: false, date: "2024-02-15" },
        { title: "Model Development", completed: false, date: "2024-03-15" },
        { title: "Integration", completed: false, date: "2024-04-15" },
        { title: "Documentation", completed: false, date: "2024-05-01" }
      ]
    }
  ]);

  const [selectedRoom, setSelectedRoom] = useState(null);
  const [roomModal, setRoomModal] = useState(false);
  const [createRoomModal, setCreateRoomModal] = useState(false);

  const filteredRooms = rooms.filter(room => {
    const matchesSearch = room.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         room.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
                         room.technology.toLowerCase().includes(searchTerm.toLowerCase());
    const matchesFilter = filterStatus === "all" || room.status === filterStatus;
    return matchesSearch && matchesFilter;
  });

  const getStatusColor = (status) => {
    switch (status) {
      case "active": return "text-green-400 bg-green-400/20";
      case "planning": return "text-yellow-400 bg-yellow-400/20";
      case "completed": return "text-blue-400 bg-blue-400/20";
      case "paused": return "text-red-400 bg-red-400/20";
      default: return "text-muted bg-main/20";
    }
  };

  const handleCreateRoom = (roomData) => {
    const newRoom = {
      id: rooms.length + 1,
      ...roomData,
      currentMembers: 0,
      members: [],
      progress: 0,
      milestones: [],
      createdDate: new Date().toISOString().split('T')[0]
    };
    setRooms([...rooms, newRoom]);
    setCreateRoomModal(false);
  };

  return (
    <div>
      <div className="flex justify-between items-center mb-6">
        <h2 className="text-xl font-semibold">🏠 Project Rooms</h2>
        <div className="flex gap-4">
          <button
            onClick={() => setCreateRoomModal(true)}
            className="bg-accent text-white px-4 py-2 rounded-custom hover:opacity-80"
          >
            + Create Room
          </button>
          <input
            type="text"
            placeholder="Search rooms..."
            className="px-4 py-2 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            value={searchTerm}
            onChange={(e) => setSearchTerm(e.target.value)}
          />
          <select
            value={filterStatus}
            onChange={(e) => setFilterStatus(e.target.value)}
            className="px-4 py-2 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
          >
            <option value="all">All Status</option>
            <option value="active">Active</option>
            <option value="planning">Planning</option>
            <option value="completed">Completed</option>
            <option value="paused">Paused</option>
          </select>
        </div>
      </div>

      {/* Rooms Grid */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {filteredRooms.map((room) => (
          <div key={room.id} className="border border-muted/30 rounded-custom p-4 hover:border-accent/50 transition">
            <div className="flex justify-between items-start mb-3">
              <div>
                <h3 className="font-semibold text-main">{room.name}</h3>
                <p className="text-sm text-muted">{room.technology}</p>
              </div>
              <span className={`px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(room.status)}`}>
                {room.status.charAt(0).toUpperCase() + room.status.slice(1)}
              </span>
            </div>
            
            <p className="text-muted text-sm mb-3">{room.description}</p>
            
            <div className="mb-4">
              <div className="flex justify-between items-center mb-2">
                <span className="text-sm text-muted">Progress</span>
                <span className="text-sm font-medium text-accent">{room.progress}%</span>
              </div>
              <div className="w-full bg-main/20 rounded-full h-2">
                <div 
                  className="bg-accent h-2 rounded-full transition-all duration-300"
                  style={{ width: `${room.progress}%` }}
                ></div>
              </div>
            </div>
            
            <div className="flex justify-between items-center mb-4 text-sm text-muted">
              <span>Members: {room.currentMembers}/{room.capacity}</span>
              <span>Deadline: {room.deadline}</span>
            </div>
            
            <button
              onClick={() => {
                setSelectedRoom(room);
                setRoomModal(true);
              }}
              className="w-full bg-accent text-white py-2 rounded-custom hover:opacity-80 transition"
            >
              View Details
            </button>
          </div>
        ))}
      </div>

      {/* Room Details Modal */}
      {roomModal && selectedRoom && (
        <RoomDetailsModal
          room={selectedRoom}
          onClose={() => setRoomModal(false)}
        />
      )}

      {/* Create Room Modal */}
      {createRoomModal && (
        <CreateRoomModal
          onClose={() => setCreateRoomModal(false)}
          onSubmit={handleCreateRoom}
        />
      )}
    </div>
  );
}

function RoomDetailsModal({ room, onClose }) {
  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div className="bg-panel p-8 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div className="flex justify-between items-center mb-6">
          <h3 className="text-xl font-semibold">{room.name}</h3>
          <button
            onClick={onClose}
            className="text-muted hover:text-main text-2xl"
          >
            ×
          </button>
        </div>
        
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
          {/* Room Info */}
          <div>
            <h4 className="font-semibold mb-4">Room Information</h4>
            <div className="space-y-3 text-sm">
              <div>
                <span className="text-muted">Description:</span>
                <p className="text-main mt-1">{room.description}</p>
              </div>
              <div>
                <span className="text-muted">Technology:</span>
                <p className="text-main mt-1">{room.technology}</p>
              </div>
              <div>
                <span className="text-muted">Created:</span>
                <p className="text-main mt-1">{room.createdDate}</p>
              </div>
              <div>
                <span className="text-muted">Deadline:</span>
                <p className="text-main mt-1">{room.deadline}</p>
              </div>
              <div>
                <span className="text-muted">Members:</span>
                <p className="text-main mt-1">{room.currentMembers}/{room.capacity}</p>
              </div>
            </div>
          </div>
          
          {/* Progress & Members */}
          <div>
            <h4 className="font-semibold mb-4">Team Members</h4>
            <div className="space-y-2 mb-6">
              {room.members.map((member, index) => (
                <div key={index} className="flex justify-between items-center p-2 bg-main/10 rounded">
                  <div>
                    <p className="text-main text-sm font-medium">{member.name}</p>
                    <p className="text-muted text-xs">{member.role}</p>
                  </div>
                  <span className={`px-2 py-1 rounded text-xs ${
                    member.status === 'active' ? 'bg-green-400/20 text-green-400' : 'bg-muted/20 text-muted'
                  }`}>
                    {member.status}
                  </span>
                </div>
              ))}
            </div>
          </div>
        </div>
        
        {/* Milestones */}
        <div className="mt-8">
          <h4 className="font-semibold mb-4">Project Milestones</h4>
          <div className="space-y-3">
            {room.milestones.map((milestone, index) => (
              <div key={index} className="flex items-center gap-3 p-3 bg-main/10 rounded">
                <div className={`w-4 h-4 rounded-full ${
                  milestone.completed ? 'bg-green-400' : 'bg-muted/50'
                }`}></div>
                <div className="flex-1">
                  <p className="text-main text-sm font-medium">{milestone.title}</p>
                  <p className="text-muted text-xs">Due: {milestone.date}</p>
                </div>
                {milestone.completed && (
                  <span className="text-green-400 text-sm">✓ Completed</span>
                )}
              </div>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}

function CreateRoomModal({ onClose, onSubmit }) {
  const [formData, setFormData] = useState({
    name: "",
    description: "",
    technology: "",
    capacity: 4,
    deadline: ""
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    onSubmit(formData);
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
  };

  return (
    <div className="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
      <div className="bg-panel p-8 rounded-2xl shadow-2xl w-full max-w-2xl">
        <h3 className="text-xl font-semibold mb-6">Create New Project Room</h3>
        
        <form onSubmit={handleSubmit} className="space-y-4">
          <input
            type="text"
            name="name"
            placeholder="Room Name"
            value={formData.name}
            onChange={handleChange}
            className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            required
          />
          
          <textarea
            name="description"
            placeholder="Room Description"
            value={formData.description}
            onChange={handleChange}
            className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            rows="3"
            required
          />
          
          <input
            type="text"
            name="technology"
            placeholder="Technology Stack"
            value={formData.technology}
            onChange={handleChange}
            className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
            required
          />
          
          <div className="grid grid-cols-2 gap-4">
            <input
              type="number"
              name="capacity"
              placeholder="Team Capacity"
              value={formData.capacity}
              onChange={handleChange}
              min="2"
              max="10"
              className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
              required
            />
            <input
              type="date"
              name="deadline"
              value={formData.deadline}
              onChange={handleChange}
              className="w-full p-3 rounded-custom bg-main/5 border border-muted focus:ring-2 focus:ring-accent focus:outline-none"
              required
            />
          </div>
          
          <div className="flex justify-end gap-3">
            <button
              type="button"
              onClick={onClose}
              className="px-5 py-2 rounded-custom bg-muted/20 text-muted hover:bg-muted/30 transition"
            >
              Cancel
            </button>
            <button
              type="submit"
              className="px-5 py-2 rounded-custom bg-accent text-white hover:bg-accent/90 transition"
            >
              Create Room
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default ProjectRoomsSection;






