import React from "react";
import GlassSurface from "../Login_Components/LiquidGlass/GlassSurface";
import { FiAlertTriangle, FiTrash2, FiX } from "react-icons/fi";

const DeleteRoomModal = ({ isOpen, onClose, onConfirm, roomName, loading }) => {
    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
            {/* Backdrop */}
            <div
                className="absolute inset-0 bg-black/60 backdrop-blur-sm transition-opacity duration-300"
                onClick={onClose}
            />

            {/* Modal Content */}
            <div className="relative w-full max-w-[450px] transform transition-all duration-300 scale-100 opacity-100">
                <GlassSurface
                    width="100%"
                    height="auto"
                    className="p-0 overflow-hidden border border-white/10 shadow-2xl"
                >
                    <div className="relative">
                        {/* Close Icon (Top Right) */}
                        <button
                            onClick={onClose}
                            className="absolute top-4 right-4 p-1 text-gray-500 hover:text-white transition-colors z-20"
                        >
                            <FiX size={20} />
                        </button>

                        {/* Content */}
                        <div className="p-8 pt-10">
                            <div className="w-16 h-16 rounded-full bg-red-500/10 flex items-center justify-center border border-red-500/20 mx-auto mb-6">
                                <FiAlertTriangle className="text-red-500 text-3xl" />
                            </div>
                            <h3 className="text-2xl font-bold text-white mb-3 text-center">Delete Room?</h3>
                            <div className="text-gray-400 text-center mb-6 space-y-2">
                                <p>Are you sure you want to delete <span className="text-white font-semibold block mt-1">"{roomName}"</span>?</p>
                                <p className="text-red-400/90 text-sm font-medium pt-2 border-t border-white/5">
                                    <FiAlertTriangle className="inline-block mr-1.5 -mt-0.5" />
                                    This action is permanent and cannot be undone.
                                </p>
                            </div>

                            <div className="flex flex-col gap-3">
                                <button
                                    onClick={onConfirm}
                                    disabled={loading}
                                    className="w-full py-3 bg-red-500 hover:bg-red-600 active:scale-95 disabled:opacity-50 disabled:scale-100 text-white font-bold rounded-xl transition-all duration-200 flex items-center justify-center gap-2 shadow-lg shadow-red-500/20"
                                >
                                    {loading ? (
                                        <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                                    ) : (
                                        <>
                                            <FiTrash2 />
                                            Delete Room
                                        </>
                                    )}
                                </button>
                                <button
                                    onClick={onClose}
                                    disabled={loading}
                                    className="w-full py-3 bg-white/5 hover:bg-white/10 text-white font-semibold rounded-xl transition-all duration-200 border border-white/10"
                                >
                                    Cancel
                                </button>
                            </div>
                        </div>

                    </div>
                </GlassSurface>
            </div>
        </div>
    );
};

export default DeleteRoomModal;
