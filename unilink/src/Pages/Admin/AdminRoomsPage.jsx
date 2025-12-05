import React, { useState, useEffect } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import * as projectRoomHandler from "../../../api/projectRoomHandler";
import { motion } from "framer-motion";

const AdminRoomsPage = () => {
    const [rooms, setRooms] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [editingRoom, setEditingRoom] = useState(null);

    useEffect(() => {
        fetchRooms();
    }, []);

    const fetchRooms = async () => {
        try {
            setLoading(true);
            const data = await projectRoomHandler.getAllRooms();
            setRooms(data);
        } catch (err) {
            setError("Failed to fetch rooms");
        } finally {
            setLoading(false);
        }
    };

    const handleDelete = async (roomId) => {
        if (!window.confirm("Are you sure? This will delete the room and ALL messages inside it.")) return;
        try {
            await projectRoomHandler.deleteRoom(roomId);
            fetchRooms();
        } catch (err) {
            alert(err.message);
        }
    };

    const handleUpdate = async (e) => {
        e.preventDefault();
        try {
            await projectRoomHandler.updateRoom(editingRoom);
            setEditingRoom(null);
            fetchRooms();
        } catch (err) {
            alert(err.message);
        }
    };

    return (
        <div className="flex bg-dark min-h-screen text-white font-main">
            <Sidebar />

            <div className="flex-1 p-8">
                <h1 className="text-2xl font-bold mb-6">Manage Project Rooms</h1>

                {loading ? (
                    <div className="text-gray-400">Loading rooms...</div>
                ) : (
                    <div className="bg-[#121212] rounded-xl border border-white/10 overflow-hidden">
                        <table className="w-full text-left">
                            <thead className="bg-white/5 text-gray-400 text-sm uppercase">
                                <tr>
                                    <th className="p-4">ID</th>
                                    <th className="p-4">Room Name</th>
                                    <th className="p-4">Created By</th>
                                    <th className="p-4">Description</th>
                                    <th className="p-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-white/10">
                                {rooms.map(room => (
                                    <tr key={room.room_id} className="hover:bg-white/5 transition">
                                        <td className="p-4 text-gray-500">#{room.room_id}</td>
                                        <td className="p-4 font-bold text-white">{room.room_name}</td>
                                        <td className="p-4 text-gray-300">{room.creator_name}</td>
                                        <td className="p-4 text-gray-400 text-sm max-w-xs truncate">{room.description}</td>
                                        <td className="p-4 text-right space-x-2">
                                            <button
                                                onClick={() => setEditingRoom(room)}
                                                className="px-3 py-1 bg-blue-500/20 text-blue-400 rounded hover:bg-blue-500/30 transition"
                                            >
                                                Edit
                                            </button>
                                            <button
                                                onClick={() => handleDelete(room.room_id)}
                                                className="px-3 py-1 bg-red-500/20 text-red-400 rounded hover:bg-red-500/30 transition"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                                {rooms.length === 0 && (
                                    <tr>
                                        <td colSpan="5" className="p-8 text-center text-gray-500">No rooms found.</td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>

            {/* Edit Modal */}
            {editingRoom && (
                <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
                    <div className="bg-[#1a1a1a] border border-white/10 rounded-2xl p-6 w-full max-w-md">
                        <h2 className="text-2xl font-bold text-white mb-4">Edit Room</h2>
                        <form onSubmit={handleUpdate} className="space-y-4">
                            <div>
                                <label className="block text-gray-400 mb-1 text-sm">Room Name</label>
                                <input
                                    className="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white focus:outline-none focus:border-accent"
                                    value={editingRoom.room_name}
                                    onChange={e => setEditingRoom({ ...editingRoom, room_name: e.target.value })}
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-gray-400 mb-1 text-sm">Description</label>
                                <textarea
                                    className="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white focus:outline-none focus:border-accent h-24"
                                    value={editingRoom.description}
                                    onChange={e => setEditingRoom({ ...editingRoom, description: e.target.value })}
                                />
                            </div>
                            <div className="flex justify-end gap-3 mt-6">
                                <button type="button" onClick={() => setEditingRoom(null)} className="px-4 py-2 text-gray-400 hover:text-white transition">Cancel</button>
                                <button type="submit" className="px-6 py-2 bg-accent text-white rounded-lg font-bold hover:bg-accent/80 transition">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
};

export default AdminRoomsPage;
