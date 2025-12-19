import React, { useState, useEffect, useRef, useMemo } from "react";
import { useNavigate } from "react-router-dom";
import Header from "../../Components/Posts/Header";
import { FiRefreshCw, FiPlus } from "react-icons/fi";
import * as projectRoomHandler from "../../../api/projectRoomHandler";
import * as facultyHandler from "../../../api/facultyandmajorHandler";
import * as professorHandler from "../../../api/professorHandler";
import { API_BASE_URL } from "../../../config/api";

const CreateRoomModal = ({ onClose, onCreated, userId }) => {
    const [name, setName] = useState("");
    const [desc, setDesc] = useState("");
    const [password, setPassword] = useState("");
    const [photoFile, setPhotoFile] = useState(null);
    const [previewUrl, setPreviewUrl] = useState(null);

    // Faculty and Professor States
    const [faculties, setFaculties] = useState([]);
    const [professors, setProfessors] = useState([]);
    const [facultyId, setFacultyId] = useState("");
    const [professorId, setProfessorId] = useState("");

    const [loading, setLoading] = useState(false);
    const [loadingFaculties, setLoadingFaculties] = useState(false);
    const [loadingProfessors, setLoadingProfessors] = useState(false);
    const [error, setError] = useState(null);

    // Fetch faculties on mount
    useEffect(() => {
        const loadFaculties = async () => {
            try {
                setLoadingFaculties(true);
                const data = await facultyHandler.getAllFaculties();
                setFaculties(data);
            } catch (err) {
                console.error("Failed to load faculties:", err);
                setError("Failed to load faculties. Please refresh the page.");
            } finally {
                setLoadingFaculties(false);
            }
        };
        loadFaculties();
    }, []);

    // Fetch professors when faculty is selected
    useEffect(() => {
        const loadProfessors = async () => {
            if (!facultyId) {
                setProfessors([]);
                setProfessorId("");
                return;
            }
            
            try {
                setLoadingProfessors(true);
                const data = await professorHandler.getProfessorsByFaculty(parseInt(facultyId));
                // Ensure data is always an array
                const professorsList = Array.isArray(data) ? data : (data?.data && Array.isArray(data.data) ? data.data : []);
                setProfessors(professorsList);
                // Reset professor selection when faculty changes
                setProfessorId("");
            } catch (err) {
                console.error("Failed to load professors:", err);
                setError("Failed to load professors for selected faculty.");
                setProfessors([]);
            } finally {
                setLoadingProfessors(false);
            }
        };
        loadProfessors();
    }, [facultyId]);

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setPhotoFile(file);
            setPreviewUrl(URL.createObjectURL(file));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);

        // Validate required fields
        if (!facultyId) {
            setError("Please select a faculty.");
            setLoading(false);
            return;
        }

        if (!professorId) {
            setError("Please select a professor.");
            setLoading(false);
            return;
        }

        try {
            const formData = new FormData();
            // Don't pass owner_id - let backend use session user ID
            // This ensures authentication is properly validated
            formData.append('name', name);
            if (desc) formData.append('description', desc);
            formData.append('password', password);
            formData.append('faculty_id', facultyId);
            formData.append('professor_id', professorId);

            if (photoFile) {
                formData.append('room_photo', photoFile);
            }

            // Debug: Log FormData contents
            console.log("FormData being sent:");
            for (let pair of formData.entries()) {
                console.log(pair[0], pair[1]);
            }

            await projectRoomHandler.createRoom(formData);
            onCreated();
        } catch (err) {
            console.error("Room creation error:", err);
            setError(err.message || "Failed to create room. Please try again.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div className="bg-[#1a1a1a] border border-white/10 rounded-2xl p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
                <h2 className="text-2xl font-bold text-white mb-4">Create Project Room</h2>
                {error && <div className="bg-red-500/20 text-red-200 p-3 rounded mb-4 text-sm">{error}</div>}

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-gray-400 mb-1 text-sm">
                            Room Name <span className="text-red-400">*</span>
                        </label>
                        <input
                            className="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white focus:outline-none focus:border-accent"
                            value={name}
                            onChange={e => setName(e.target.value)}
                            required
                            placeholder="e.g. Final Year Project Team A"
                        />
                    </div>

                    {/* Faculty and Professor Selection */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-gray-400 mb-1 text-sm">
                                Faculty <span className="text-red-400">*</span>
                            </label>
                            <select
                                className="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white focus:outline-none focus:border-accent disabled:opacity-50 disabled:cursor-not-allowed"
                                value={facultyId}
                                onChange={e => setFacultyId(e.target.value)}
                                required
                                disabled={loadingFaculties}
                            >
                                <option value="">{loadingFaculties ? "Loading..." : "Select Faculty"}</option>
                                {faculties.map(faculty => (
                                    <option key={faculty.faculty_id} value={faculty.faculty_id}>
                                        {faculty.name}
                                    </option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="block text-gray-400 mb-1 text-sm">
                                Professor <span className="text-red-400">*</span>
                            </label>
                            <select
                                className="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white focus:outline-none focus:border-accent disabled:opacity-50 disabled:cursor-not-allowed"
                                value={professorId}
                                onChange={e => setProfessorId(e.target.value)}
                                required
                                disabled={!facultyId || loadingProfessors}
                            >
                                <option value="">
                                    {!facultyId 
                                        ? "Select Faculty First" 
                                        : loadingProfessors 
                                        ? "Loading..." 
                                        : "Select Professor"}
                                </option>
                                {Array.isArray(professors) && professors.map(prof => (
                                    <option key={prof.professor_id} value={prof.professor_id}>
                                        {prof.username}
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div>
                        <label className="block text-gray-400 mb-1 text-sm">
                            Room Password <span className="text-red-400">*</span>
                        </label>
                        <input
                            type="password"
                            className="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white focus:outline-none focus:border-accent"
                            value={password}
                            onChange={e => setPassword(e.target.value)}
                            required
                            placeholder="Enter a secure password"
                            minLength={4}
                        />
                    </div>

                    <div>
                        <label className="block text-gray-400 mb-1 text-sm">Description</label>
                        <textarea
                            className="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white focus:outline-none focus:border-accent h-24 resize-none"
                            value={desc}
                            onChange={e => setDesc(e.target.value)}
                            placeholder="Brief description of the project..."
                        />
                    </div>

                    <div>
                        <label className="block text-gray-400 mb-1 text-sm">Room Photo</label>
                        <div className="flex items-center gap-4">
                            <div className="relative w-16 h-16 rounded-lg bg-white/5 border border-white/10 overflow-hidden flex-shrink-0">
                                {previewUrl ? (
                                    <img src={previewUrl} alt="Preview" className="w-full h-full object-cover" />
                                ) : (
                                    <div className="w-full h-full flex items-center justify-center text-gray-500">
                                        <i className="fa-solid fa-image text-xl"></i>
                                    </div>
                                )}
                            </div>
                            <input
                                type="file"
                                accept="image/*"
                                onChange={handleFileChange}
                                className="block w-full text-sm text-gray-400
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-white/10 file:text-white
                                  hover:file:bg-white/20
                                  cursor-pointer"
                            />
                        </div>
                    </div>

                    <div className="flex justify-end gap-3 mt-6">
                        <button type="button" onClick={onClose} className="px-4 py-2 text-gray-400 hover:text-white transition">Cancel</button>
                        <button
                            type="submit"
                            disabled={loading}
                            className="px-6 py-2 bg-accent text-white rounded-lg font-bold hover:bg-accent/80 transition disabled:opacity-50"
                        >
                            {loading ? "Creating..." : "Create Room"}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
};


const ProjectRoomsPage = () => {
    const [myRooms, setMyRooms] = useState([]);
    const [otherRooms, setOtherRooms] = useState([]);
    const [loading, setLoading] = useState(true);
    const [refreshing, setRefreshing] = useState(false);
    const [showModal, setShowModal] = useState(false);
    const [activeTab, setActiveTab] = useState("my"); // 'my' or 'other'
    const navigate = useNavigate();
    const hasFetched = useRef(false);

    // Get current user once
    const user = useMemo(() => JSON.parse(localStorage.getItem('user')), []);

    useEffect(() => {
        if (!user) {
            alert("Please log in to access Project Rooms");
            navigate('/login');
            return;
        }

        if (!hasFetched.current) {
            hasFetched.current = true;
            fetchRooms();
        }
    }, []);

    const fetchRooms = async (isRefresh = false) => {
        try {
            if (isRefresh) setRefreshing(true);
            else setLoading(true);

            // Fetch both lists in parallel
            const [userRoomsData, allRoomsData] = await Promise.all([
                projectRoomHandler.getUserRooms(),
                projectRoomHandler.getAllRooms()
            ]);

            setMyRooms(userRoomsData);

            // Filter all rooms to find ones I'm NOT in
            // Set of my room IDs for fast lookup
            const myRoomIds = new Set(userRoomsData.map(r => r.room_id));
            const others = allRoomsData.filter(r => !myRoomIds.has(r.room_id));
            setOtherRooms(others);

        } catch (err) {
            console.error(err);
        } finally {
            if (isRefresh) setRefreshing(false);
            else setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-main text-white font-main">
            <Header logoSize="large" hideShareButton={true} />

            <div className="container mx-auto pt-24 px-4 md:px-8 max-w-7xl">
                <div className="flex justify-between items-center mb-8">
                    <div>
                        <h1 className="text-3xl font-bold mb-2">Project Rooms</h1>
                        <p className="text-gray-400">Collaborate with peers and mentors</p>
                    </div>
                    <div className="flex gap-3">
                        <button
                            onClick={() => fetchRooms(true)}
                            disabled={refreshing}
                            className="
                                p-2 rounded-full cursor-pointer
                                text-[#58a6ff]
                                transition-all duration-200
                                hover:scale-110
                                hover:drop-shadow-[0_0_6px_currentColor]
                                hover:bg-white/10
                                disabled:opacity-50
                                disabled:cursor-not-allowed
                            "
                            title="Refresh Rooms"
                        >
                            <FiRefreshCw size={20} className={refreshing ? 'animate-spin' : ''} />
                        </button>
                        <button
                            onClick={() => {
                                if (!user) {
                                    alert("Please log in to create a room");
                                    navigate('/login');
                                    return;
                                }
                                setShowModal(true);
                            }}
                            className="
                                p-2 rounded-full cursor-pointer
                                text-[#58a6ff]
                                transition-all duration-200
                                hover:scale-110
                                hover:drop-shadow-[0_0_6px_currentColor]
                                hover:bg-white/10
                            "
                            title="Create Room"
                        >
                            <FiPlus size={20} />
                        </button>
                    </div>
                </div>

                {/* Tabs */}
                <div className="flex gap-6 border-b border-white/10 mb-8">
                    <button
                        onClick={() => setActiveTab("my")}
                        className={`pb-4 px-2 font-semibold transition relative ${activeTab === "my" ? "text-[#58a6ff]" : "text-gray-400 hover:text-white"
                            }`}
                    >
                        My Rooms ({myRooms.length})
                        {activeTab === "my" && (
                            <div className="absolute bottom-0 left-0 w-full h-0.5 bg-[#58a6ff] rounded-t-full"></div>
                        )}
                    </button>
                    <button
                        onClick={() => setActiveTab("other")}
                        className={`pb-4 px-2 font-semibold transition relative ${activeTab === "other" ? "text-[#58a6ff]" : "text-gray-400 hover:text-white"
                            }`}
                    >
                        Other Rooms ({otherRooms.length})
                        {activeTab === "other" && (
                            <div className="absolute bottom-0 left-0 w-full h-0.5 bg-[#58a6ff] rounded-t-full"></div>
                        )}
                    </button>
                </div>

                {loading ? (
                    <div className="text-center py-20 text-gray-500">
                        <i className="fa-solid fa-spinner fa-spin text-4xl mb-4"></i>
                        <p>Loading rooms...</p>
                    </div>
                ) : (
                    activeTab === "my" ? (
                        myRooms.length === 0 ? (
                            <div className="text-center py-20 bg-white/5 rounded-3xl border border-white/10">
                                <div className="text-6xl mb-4">📭</div>
                                <h3 className="text-2xl font-bold mb-2">No Rooms Found</h3>
                                <p className="text-gray-400 mb-6">You haven't joined any project rooms yet.</p>
                                <div className="flex gap-3 justify-center">
                                    <button
                                        onClick={() => setShowModal(true)}
                                        className="px-6 py-2 bg-[#58a6ff] text-white rounded-full hover:bg-[#3b82f6] transition hover:shadow-[0_0_15px_rgba(88,166,255,0.4)]"
                                    >
                                        <i className="fa-solid fa-plus mr-2"></i>
                                        Create Room
                                    </button>
                                </div>
                            </div>
                        ) : (
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {myRooms.map(room => (
                                    <div key={room.room_id} className="bg-[#121212] border border-white/5 rounded-2xl p-6 hover:border-accent/50 transition group relative overflow-hidden h-48 flex flex-col justify-end">
                                        {room.photo_url ? (
                                            <>
                                                <div
                                                    className="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110"
                                                    style={{ backgroundImage: `url(${API_BASE_URL}/${room.photo_url})` }}
                                                />
                                                <div className="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent" />
                                            </>
                                        ) : (
                                            <div className="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                                                <i className="fa-solid fa-users text-8xl"></i>
                                            </div>
                                        )}

                                        <div className="relative z-10">
                                            <h3 className="text-xl font-bold mb-1 truncate">{room.name}</h3>
                                            <p className="text-gray-300 text-sm mb-3 line-clamp-2">{room.description || "No description"}</p>

                                            <div className="flex justify-between items-center">
                                                <div className="text-xs text-gray-400">
                                                    By <span className="text-white">{room.creator_name}</span>
                                                </div>
                                                <button
                                                    onClick={() => navigate(`/project-room/${room.room_id}`)}
                                                    className="px-3 py-1.5 bg-white/10 hover:bg-accent text-white rounded-lg transition text-xs font-semibold backdrop-blur-sm"
                                                >
                                                    Enter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )
                    ) : (
                        otherRooms.length === 0 ? (
                            <div className="text-center py-20 bg-white/5 rounded-3xl border border-white/10">
                                <div className="text-6xl mb-4">🌍</div>
                                <h3 className="text-2xl font-bold mb-2">No Other Rooms</h3>
                                <p className="text-gray-400">There are no other active rooms to join right now.</p>
                            </div>
                        ) : (
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {otherRooms.map(room => (
                                    <div key={room.room_id} className="bg-[#121212]/50 border border-white/5 rounded-2xl p-6 hover:border-white/20 transition group relative overflow-hidden h-48 flex flex-col justify-end grayscale hover:grayscale-0">
                                        {room.photo_url ? (
                                            <>
                                                <div
                                                    className="absolute inset-0 bg-cover bg-center transition-transform duration-500 group-hover:scale-110 opacity-50"
                                                    style={{ backgroundImage: `url(${API_BASE_URL}/${room.photo_url})` }}
                                                />
                                                <div className="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent" />
                                            </>
                                        ) : (
                                            <div className="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                                                <i className="fa-solid fa-users text-8xl"></i>
                                            </div>
                                        )}

                                        <div className="relative z-10">
                                            <h3 className="text-xl font-bold mb-1 truncate">{room.name}</h3>
                                            <p className="text-gray-300 text-sm mb-3 line-clamp-2">{room.description || "No description"}</p>

                                            <div className="flex justify-between items-center">
                                                <div className="text-xs text-gray-400">
                                                    By <span className="text-white">{room.creator_name}</span>
                                                </div>
                                                <button
                                                    onClick={() => navigate(`/project-room/${room.room_id}`)}
                                                    className="px-3 py-1.5 border border-white/20 hover:bg-white hover:text-black text-white rounded-lg transition text-xs font-semibold backdrop-blur-sm"
                                                >
                                                    Join
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        )
                    )
                )}
            </div>

            {showModal && (
                <CreateRoomModal
                    userId={user?.id || user?.user_id}
                    onClose={() => setShowModal(false)}
                    onCreated={() => {
                        setShowModal(false);
                        fetchRooms();
                    }}
                />
            )}
        </div>
    );
};



export default ProjectRoomsPage;
