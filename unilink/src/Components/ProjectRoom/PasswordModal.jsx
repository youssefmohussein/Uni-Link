import React, { useState } from "react";
import { motion, AnimatePresence } from "framer-motion";

const PasswordModal = ({ room, isOpen, onClose, onConfirm }) => {
    const [password, setPassword] = useState("");
    const [error, setError] = useState("");
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError("");
        setLoading(true);
        try {
            await onConfirm(password);
            setPassword("");
        } catch (err) {
            setError(err.message || "Wrong password, try again");
        } finally {
            setLoading(false);
        }
    };

    if (!isOpen) return null;

    return (
        <AnimatePresence>
            <div className="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-[100] p-4">
                <motion.div
                    initial={{ scale: 0.9, opacity: 0, y: 20 }}
                    animate={{ scale: 1, opacity: 1, y: 0 }}
                    exit={{ scale: 0.9, opacity: 0, y: 20 }}
                    className="bg-[#1a1a1a] border border-white/10 rounded-3xl p-8 w-full max-w-md shadow-2xl relative overflow-hidden"
                >
                    {/* Background Glow */}
                    <div className="absolute -top-24 -right-24 w-48 h-48 bg-accent/10 blur-[80px] rounded-full" />

                    <div className="relative z-10">
                        <div className="w-16 h-16 bg-accent/20 rounded-2xl flex items-center justify-center mb-6 mx-auto">
                            <i className="fa-solid fa-lock text-accent text-2xl"></i>
                        </div>

                        <h2 className="text-2xl font-bold text-white mb-2 text-center">
                            Private Room
                        </h2>
                        <p className="text-gray-400 mb-8 text-center text-sm">
                            Please enter the password to join <span className="text-accent font-semibold">"{room?.name || room?.room_name}"</span>
                        </p>

                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="relative">
                                <input
                                    type="password"
                                    placeholder="Enter Room Password"
                                    className="w-full bg-white/5 border border-white/10 rounded-xl p-4 text-white focus:outline-none focus:border-accent transition-all placeholder:text-gray-600"
                                    value={password}
                                    onChange={(e) => setPassword(e.target.value)}
                                    autoFocus
                                    required
                                />
                                {error && (
                                    <motion.p
                                        initial={{ opacity: 0, x: -10 }}
                                        animate={{ opacity: 1, x: 0 }}
                                        className="text-red-500 text-xs mt-2 ml-1"
                                    >
                                        <i className="fa-solid fa-circle-exclamation mr-1"></i>
                                        {error}
                                    </motion.p>
                                )}
                            </div>

                            <div className="flex flex-col gap-3 pt-4">
                                <button
                                    type="submit"
                                    disabled={loading}
                                    className="w-full py-4 bg-accent text-white rounded-xl font-bold hover:bg-accent/80 transition-all shadow-lg shadow-accent/20 flex items-center justify-center gap-2 group disabled:opacity-50"
                                >
                                    {loading ? (
                                        <i className="fa-solid fa-spinner fa-spin"></i>
                                    ) : (
                                        <>
                                            Join Room
                                            <i className="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                                        </>
                                    )}
                                </button>
                                <button
                                    type="button"
                                    onClick={onClose}
                                    className="w-full py-4 bg-white/5 text-gray-400 rounded-xl font-semibold hover:bg-white/10 hover:text-white transition-all"
                                >
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </motion.div>
            </div>
        </AnimatePresence>
    );
};

export default PasswordModal;
