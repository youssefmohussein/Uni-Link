import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import Header from "../../components/Posts/Header";
import * as projectRoomHandler from "../../../api/projectRoomHandler";

const CreateRoomModal = ({ onClose, onCreated, userId }) => {
    const [name, setName] = useState("");
    const [desc, setDesc] = useState("");
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    // ss
    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError(null);
        try {
            await projectRoomHandler.createRoom({
                room_name: name,
                description: desc,
                created_by: userId
            });
            onCreated();
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div className="bg-[#1a1a1a] border border-white/10 rounded-2xl p-6 w-full max-w-md">
                <h2 className="text-2xl font-bold text-white mb-4">Create Project Room</h2>
                {error && <div className="bg-red-500/20 text-red-200 p-3 rounded mb-4 text-sm">{error}</div>}

                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-gray-400 mb-1 text-sm">Room Name</label>
                        <input
                            className="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white focus:outline-none focus:border-accent"
                            value={name}
                            onChange={e => setName(e.target.value)}
                            required
                            placeholder="e.g. Final Year Project Team A"
                        />
                    </div>
                    <div>
                        <label className="block text-gray-400 mb-1 text-sm">Description</label>
                        <textarea
                            className="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white focus:outline-none focus:border-accent h-24"
                            value={desc}
                            onChange={e => setDesc(e.target.value)}
                            placeholder="Brief description of the project..."
                        />
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
    const [rooms, setRooms] = useState([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const navigate = useNavigate();

    // Get current user
    const user = JSON.parse(localStorage.getItem('user'));

    useEffect(() => {
        // Quick DB init check (fire and forget)
        projectRoomHandler.initRoomsDB().catch(() => { });
        fetchRooms();
    }, []);

    const fetchRooms = async () => {
        try {
            setLoading(true);
            const data = await projectRoomHandler.getAllRooms();
            setRooms(data);
        } catch (err) {
            console.error(err);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-main text-white font-main">
            <Header logoSize="large" />

            <div className="container mx-auto pt-24 px-4 md:px-8 max-w-7xl">
                <div className="flex justify-between items-center mb-8">
                    <div>
                        <h1 className="text-3xl font-bold mb-2">Project Collaboration Rooms</h1>
                        <p className="text-gray-400">Join a room to collaborate with your team</p>
                    </div>
                    <button
                        onClick={() => {
                            if (!user) {
                                alert("Please log in to create a room");
                                navigate('/login');
                                return;
                            }
                            setShowModal(true);
                        }}
                        className="px-6 py-3 bg-accent text-white rounded-xl font-bold hover:scale-105 transition shadow-lg shadow-accent/20 flex items-center gap-2"
                    >
                        <i className="fa-solid fa-plus"></i> Create Room
                    </button>
                </div>

                {loading ? (
                    <div className="text-center py-20 text-gray-500">Loading rooms...</div>
                ) : rooms.length === 0 ? (
                    <div className="text-center py-20 bg-white/5 rounded-3xl border border-white/10">
                        <div className="text-6xl mb-4">🚀</div>
                        <h3 className="text-2xl font-bold mb-2">No Rooms Yet</h3>
                        <p className="text-gray-400 mb-6">Be the first to create a project room!</p>
                        <button
                            onClick={() => setShowModal(true)}
                            className="px-6 py-2 bg-white/10 rounded-full hover:bg-white/20 transition"
                        >
                            Create Now
                        </button>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        {rooms.map(room => (
                            <div key={room.room_id} className="bg-[#121212] border border-white/5 rounded-2xl p-6 hover:border-accent/50 transition group relative overflow-hidden">
                                <div className="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                                    <i className="fa-solid fa-users text-8xl"></i>
                                </div>
                                <h3 className="text-xl font-bold mb-2 truncate pr-8">{room.room_name}</h3>
                                <p className="text-gray-400 text-sm mb-4 line-clamp-2 h-10">{room.description || "No description"}</p>

                                <div className="flex justify-between items-end mt-4">
                                    <div className="text-xs text-gray-500">
                                        Created by <span className="text-gray-300">{room.creator_name}</span>
                                    </div>
                                    <button
                                        onClick={() => navigate(`/project-room/${room.room_id}`)}
                                        className="px-4 py-2 bg-white/5 hover:bg-accent text-white rounded-lg transition text-sm font-semibold"
                                    >
                                        Join Room
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {showModal && (
                <CreateRoomModal
                    userId={user?.id}
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
