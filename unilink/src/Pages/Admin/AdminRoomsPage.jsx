import React, { useState, useEffect } from "react";
import Sidebar from "../../Components/Admin_Components/Sidebar";
import * as projectRoomHandler from "../../../api/projectRoomHandler";
import { motion, AnimatePresence } from "framer-motion";
import { FiEdit2, FiTrash2, FiRefreshCw, FiPlus, FiSearch } from "react-icons/fi";
import Card from "../../Components/Admin_Components/Card";
import ConfirmationModal from "../../Components/Common/ConfirmationModal";

const RoomForm = ({ isOpen, onClose, onSubmit, initialData }) => {
    const [formData, setFormData] = useState({
        room_name: "",
        description: "",
        created_by: JSON.parse(localStorage.getItem('user'))?.id
    });

    useEffect(() => {
        if (initialData) {
            setFormData(initialData);
        } else {
            setFormData({
                room_name: "",
                description: "",
                created_by: JSON.parse(localStorage.getItem('user'))?.id
            });
        }
    }, [initialData, isOpen]);

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <motion.div
                initial={{ scale: 0.95, opacity: 0 }}
                animate={{ scale: 1, opacity: 1 }}
                className="bg-[#1a1a1a] border border-white/10 rounded-2xl p-6 w-full max-w-md shadow-2xl"
            >
                <h2 className="text-2xl font-bold text-white mb-4">
                    {initialData ? "Edit Room" : "Create New Room"}
                </h2>
                <form onSubmit={(e) => { e.preventDefault(); onSubmit(formData); }} className="space-y-4">
                    <div>
                        <label className="block text-gray-400 mb-1 text-sm">Room Name</label>
                        <input
                            className="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white focus:outline-none focus:border-accent transition-colors"
                            value={formData.room_name}
                            onChange={e => setFormData({ ...formData, room_name: e.target.value })}
                            required
                        />
                    </div>
                    <div>
                        <label className="block text-gray-400 mb-1 text-sm">Description</label>
                        <textarea
                            className="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-white focus:outline-none focus:border-accent h-24 transition-colors"
                            value={formData.description}
                            onChange={e => setFormData({ ...formData, description: e.target.value })}
                        />
                    </div>
                    <div className="flex justify-end gap-3 mt-6">
                        <button type="button" onClick={onClose} className="px-4 py-2 text-gray-400 hover:text-white transition">Cancel</button>
                        <button
                            type="submit"
                            className="px-6 py-2 rounded-lg font-medium text-white bg-accent hover:bg-accent/80 transition shadow-lg shadow-accent/20"
                        >
                            {initialData ? "Save Changes" : "Create Room"}
                        </button>
                    </div>
                </form>
            </motion.div>
        </div>
    );
};

const AdminRoomsPage = () => {
    const [rooms, setRooms] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [editingRoom, setEditingRoom] = useState(null);
    const [isAdding, setIsAdding] = useState(false);
    const [searchTerm, setSearchTerm] = useState("");
    const [deleteModal, setDeleteModal] = useState({ isOpen: false, id: null });

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

    const handleDelete = async () => {
        try {
            await projectRoomHandler.deleteRoom(deleteModal.id);
            fetchRooms();
        } catch (err) {
            alert(err.message);
        }
    };

    const openDeleteModal = (roomId) => {
        setDeleteModal({ isOpen: true, id: roomId });
    };

    const handleSave = async (data) => {
        try {
            if (editingRoom) {
                await projectRoomHandler.updateRoom(data);
                setEditingRoom(null);
            } else {
                await projectRoomHandler.createRoom(data);
                setIsAdding(false);
            }
            fetchRooms();
        } catch (err) {
            alert(err.message);
        }
    };

    const filteredRooms = rooms.filter(room => {
        if (!searchTerm) return true;
        const lowerTerm = searchTerm.toLowerCase();
        return (
            room.room_name?.toLowerCase().includes(lowerTerm) ||
            room.description?.toLowerCase().includes(lowerTerm) ||
            String(room.room_id).toLowerCase().includes(lowerTerm)
        );
    });

    return (
        <div className="flex bg-bg min-h-screen text-main font-main">
            <Sidebar />
            <motion.div
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                className="flex-1 p-6 overflow-y-auto"
            >
                <div className="flex justify-between items-center mb-6">
                    <div>
                        <h1 className="text-3xl font-bold text-white mb-2">Project Rooms</h1>
                        <p className="text-gray-400">Manage collaboration rooms</p>
                    </div>
                </div>

                <Card>
                    <div className="flex items-center justify-between mb-4 px-4">
                        <h2 className="text-xl font-bold text-accent">Rooms List</h2>
                        <div className="flex gap-2">
                            <button
                                onClick={fetchRooms}
                                className="p-2 rounded-full cursor-pointer text-accent transition-all duration-200
                                           hover:scale-110 hover:drop-shadow-[0_0_6px_currentColor] hover:bg-white/10"
                                title="Refresh"
                            >
                                <FiRefreshCw size={20} />
                            </button>
                            <button
                                onClick={() => { setEditingRoom(null); setIsAdding(true); }}
                                className="p-2 rounded-full cursor-pointer text-accent transition-all duration-200 hover:scale-110 hover:drop-shadow-[0_0_6px_currentColor] hover:bg-white/10"
                                title="Add Room"
                            >
                                <FiPlus size={24} />
                            </button>
                        </div>
                    </div>

                    <div className="px-4 mb-4">
                        <div className="relative w-full">
                            <input
                                type="text"
                                placeholder="Search by room name, id, or description..."
                                value={searchTerm}
                                onChange={(e) => setSearchTerm(e.target.value)}
                                className="w-full px-3 py-2 pl-10 rounded-custom border border-white/20 bg-panel text-main
                                           focus:ring-2 focus:ring-accent outline-none transition"
                            />
                            <FiSearch className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" size={16} />
                        </div>
                    </div>

                    <div className="grid grid-cols-12 gap-4 px-4 py-3 border-b border-white/10 text-xs font-semibold uppercase text-accent">
                        <div className="col-span-1">ID</div>
                        <div className="col-span-3">Name</div>
                        <div className="col-span-6">Description</div>
                        <div className="col-span-2 text-right">Actions</div>
                    </div>

                    <div className="max-h-[600px] overflow-y-auto">
                        {loading ? (
                            <div className="text-center py-8 text-white/50">Loading...</div>
                        ) : (
                            <AnimatePresence>
                                {filteredRooms.map(room => (
                                    <motion.div
                                        key={room.room_id}
                                        initial={{ opacity: 0 }}
                                        animate={{ opacity: 1 }}
                                        exit={{ opacity: 0 }}
                                        className="grid grid-cols-12 gap-4 px-4 py-3 border-b border-white/10 items-center hover:bg-white/5 transition text-sm text-white/80"
                                    >
                                        <div className="col-span-1">#{room.room_id}</div>
                                        <div className="col-span-3 font-medium truncate" title={room.room_name}>{room.room_name}</div>
                                        <div className="col-span-6 truncate" title={room.description}>{room.description}</div>

                                        <div className="col-span-2 flex justify-end gap-2">
                                            <button
                                                onClick={() => setEditingRoom(room)}
                                                className="p-2 rounded cursor-pointer text-accent transition-all duration-200 hover:scale-110 hover:drop-shadow-[0_0_6px_currentColor]"
                                                title="Edit Room"
                                            >
                                                <FiEdit2 size={16} />
                                            </button>
                                            <button
                                                onClick={() => openDeleteModal(room.room_id)}
                                                className="p-2 rounded cursor-pointer text-red-500 transition-all duration-200 hover:scale-110 hover:drop-shadow-[0_0_6px_currentColor]"
                                                title="Delete Room"
                                            >
                                                <FiTrash2 size={16} />
                                            </button>
                                        </div>
                                    </motion.div>
                                ))}
                            </AnimatePresence>
                        )}
                        {!loading && filteredRooms.length === 0 && (
                            <div className="text-center py-10 text-white/50">
                                No rooms found.
                            </div>
                        )}
                    </div>
                </Card>
            </motion.div>

            {/* Reusable Form Modal */}
            <RoomForm
                isOpen={isAdding || !!editingRoom}
                onClose={() => { setIsAdding(false); setEditingRoom(null); }}
                onSubmit={handleSave}
                initialData={editingRoom}
            />

            <ConfirmationModal
                isOpen={deleteModal.isOpen}
                onClose={() => setDeleteModal({ isOpen: false, id: null })}
                onConfirm={handleDelete}
                title="Delete Room"
                message="Are you sure you want to delete this room? This will assume deleting all messages and files inside it."
                confirmText="Delete Room"
            />
        </div>
    );
};

export default AdminRoomsPage;
