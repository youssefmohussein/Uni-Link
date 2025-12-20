import React, { useState, useEffect, useRef } from "react";
import { useParams, useNavigate } from "react-router-dom";
import Header from "../../Components/Posts/Header";
import * as projectRoomHandler from "../../../api/projectRoomHandler";
import { API_BASE_URL } from "../../../config/api";
import { toast } from "react-hot-toast";

const ProjectChatPage = () => {
    const { id: roomId } = useParams();
    const navigate = useNavigate();
    const [room, setRoom] = useState(null);
    const [messages, setMessages] = useState([]);
    const [members, setMembers] = useState([]);
    const [newMessage, setNewMessage] = useState("");
    const [loading, setLoading] = useState(true);
    const [sending, setSending] = useState(false);
    const [showInfo, setShowInfo] = useState(false);

    const messagesEndRef = useRef(null);
    const user = JSON.parse(localStorage.getItem('user'));
    const pollingInterval = useRef(null);

    useEffect(() => {
        if (!user) {
            alert("Please log in to join the chat");
            navigate('/login');
            return;
        }
        fetchRoomDetails();
        fetchMessages();
        fetchMembers();

        // Poll for new messages every 3 seconds
        pollingInterval.current = setInterval(fetchMessages, 3000);

        return () => {
            if (pollingInterval.current) clearInterval(pollingInterval.current);
        };
    }, [roomId]);

    useEffect(() => {
        scrollToBottom();
    }, [messages]);

    const scrollToBottom = () => {
        messagesEndRef.current?.scrollIntoView({ behavior: "smooth" });
    };

    const fetchRoomDetails = async () => {
        try {
            const data = await projectRoomHandler.getRoomById(roomId);
            setRoom(data);
        } catch (err) {
            console.error("Failed to load room:", err);
            navigate('/project-rooms');
        }
    };

    const fetchMessages = async () => {
        try {
            const data = await projectRoomHandler.getRoomMessages(roomId);
            setMessages(data);
            if (loading) setLoading(false);
        } catch (err) {
            console.error("Failed to fetch messages:", err);
        }
    };

    const fetchMembers = async () => {
        try {
            const data = await projectRoomHandler.getRoomMembers(roomId);
            setMembers(data);
        } catch (err) {
            console.error("Failed to fetch members:", err);
        }
    };

    const handleDeleteRoom = async () => {
        if (!window.confirm("Are you sure you want to delete this room? This action cannot be undone.")) return;

        try {
            await projectRoomHandler.deleteRoom(roomId);
            toast.success("Room deleted successfully");
            navigate('/project-rooms');
        } catch (err) {
            toast.error("Failed to delete room: " + err.message);
        }
    };

    const handleSend = async (e) => {
        e.preventDefault();
        if (!newMessage.trim()) return;

        setSending(true);
        try {
            await projectRoomHandler.sendMessage({
                room_id: roomId,
                sender_id: user.id || user.user_id,
                content: newMessage
            });
            setNewMessage("");
            fetchMessages(); // Immediate refresh
        } catch (err) {
            alert("Failed to send: " + err.message);
        } finally {
            setSending(false);
        }
    };

    if (loading && !room) return <div className="min-h-screen bg-black text-white flex items-center justify-center">Loading Project...</div>;

    return (
        <div className="min-h-screen bg-main text-white font-main flex flex-col h-screen overflow-hidden">
            <Header logoSize="small" hideShareButton={true} />

            <div className="pt-20 flex-grow container mx-auto px-4 md:px-8 max-w-7xl flex gap-6 h-[calc(100vh-80px)] overflow-hidden pb-4">
                {/* Main Chat Content */}
                <div className={`flex flex-col h-full transition-all duration-300 ${showInfo ? 'w-full lg:w-2/3' : 'w-full'}`}>
                    {/* Chat Header */}
                    <div className="flex items-center justify-between py-4 border-b border-white/10 mb-4 bg-main/50 backdrop-blur-sm sticky top-0 z-10">
                        <div className="flex items-center gap-4">
                            <button
                                onClick={() => navigate('/project-rooms')}
                                className="p-2 text-gray-400 hover:text-white transition bg-white/5 rounded-full"
                                title="Back to Projects"
                            >
                                <i className="fa-solid fa-arrow-left"></i>
                            </button>
                            <div>
                                <h1 className="text-xl font-bold text-white">
                                    {room?.name}
                                </h1>
                                <button
                                    onClick={() => setShowInfo(!showInfo)}
                                    className="text-accent text-xs hover:underline mt-0.5"
                                >
                                    {members.length} members • View info
                                </button>
                            </div>
                        </div>
                        <button
                            onClick={() => setShowInfo(!showInfo)}
                            className={`p-3 rounded-full transition ${showInfo ? 'bg-accent text-white' : 'bg-white/5 text-gray-400 hover:text-white'}`}
                            title="Group Info"
                        >
                            <i className="fa-solid fa-circle-info text-xl"></i>
                        </button>
                    </div>

                    {/* Messages Area */}
                    <div className="flex-grow bg-[#121212] rounded-2xl border border-white/5 overflow-y-auto p-6 space-y-6 mb-4 custom-scrollbar relative">
                        {messages.length === 0 && (
                            <div className="absolute inset-0 flex flex-col items-center justify-center text-gray-600">
                                <div className="text-5xl mb-4 text-white/5">💬</div>
                                <p className="text-sm">No messages yet. Start the conversation!</p>
                            </div>
                        )}

                        {messages.map((msg, index) => {
                            const isMe = msg.sender_id === (user.id || user.user_id);
                            const nextMsg = messages[index + 1];
                            const isSameAuthorNext = nextMsg && nextMsg.sender_id === msg.sender_id;

                            return (
                                <div key={msg.message_id} className={`flex flex-col ${isMe ? 'items-end' : 'items-start'} ${isSameAuthorNext ? 'mb-1' : 'mb-4'}`}>
                                    <div className={`max-w-[85%] md:max-w-[70%] flex gap-3 ${isMe ? 'flex-row-reverse' : ''}`}>
                                        {/* Avatar - only show if first message in group */}
                                        {!isMe ? (
                                            <div className="w-8 h-8 rounded-full bg-gradient-to-br from-accent/50 to-purple-500/50 flex items-center justify-center text-[10px] font-bold text-white flex-shrink-0 mt-1 shadow-lg overflow-hidden">
                                                {msg.profile_image || msg.profile_picture ? (
                                                    <img src={`${API_BASE_URL}/${msg.profile_image || msg.profile_picture}`} alt={msg.username} className="w-full h-full object-cover" />
                                                ) : (
                                                    msg.username?.charAt(0).toUpperCase()
                                                )}
                                            </div>
                                        ) : null}

                                        {/* Bubble */}
                                        <div className={`relative px-4 py-2.5 rounded-2xl ${isMe
                                            ? 'bg-accent text-white rounded-tr-sm shadow-[0_4px_15px_rgba(88,166,255,0.2)]'
                                            : 'bg-white/10 text-gray-100 rounded-tl-sm border border-white/5'
                                            }`}>
                                            {!isMe && <div className="text-[11px] text-accent mb-1 font-bold">{msg.username}</div>}
                                            <p className="whitespace-pre-wrap break-words text-sm leading-relaxed">{msg.content}</p>
                                            <div className={`text-[9px] mt-1.5 ${isMe ? 'text-white/60' : 'text-gray-500'} text-right font-medium`}>
                                                {new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                        <div ref={messagesEndRef} />
                    </div>

                    {/* Input Area */}
                    <form onSubmit={handleSend} className="bg-[#121212] rounded-2xl border border-white/10 p-2 flex items-center gap-2 mb-4 shadow-xl">
                        <input
                            className="flex-grow bg-transparent text-white placeholder-gray-500 px-5 py-4 focus:outline-none text-sm"
                            placeholder="Type your message..."
                            value={newMessage}
                            onChange={e => setNewMessage(e.target.value)}
                            disabled={sending}
                        />
                        <button
                            type="submit"
                            disabled={!newMessage.trim() || sending}
                            className="p-4 bg-accent text-white rounded-xl hover:bg-accent/80 transition disabled:opacity-50 disabled:cursor-not-allowed w-14 h-14 flex items-center justify-center shadow-lg shadow-accent/20"
                        >
                            {sending ? <i className="fa-solid fa-spinner fa-spin"></i> : <i className="fa-solid fa-paper-plane text-lg"></i>}
                        </button>
                    </form>
                </div>

                {/* Group Info Sidebar */}
                {showInfo && (
                    <div className="w-full lg:w-1/3 h-full bg-[#121212] rounded-2xl border border-white/5 flex flex-col overflow-hidden animate-slide-in-right">
                        <div className="p-6 border-b border-white/10 flex justify-between items-center bg-white/5">
                            <h2 className="text-lg font-bold">Group Info</h2>
                            <button onClick={() => setShowInfo(false)} className="text-gray-400 hover:text-white">
                                <i className="fa-solid fa-xmark"></i>
                            </button>
                        </div>

                        <div className="flex-grow overflow-y-auto p-6 custom-scrollbar space-y-8">
                            {/* Description */}
                            <div>
                                <h3 className="text-xs uppercase tracking-widest text-gray-500 font-bold mb-3 flex items-center gap-2">
                                    <i className="fa-solid fa-align-left text-accent"></i> Description
                                </h3>
                                <div className="bg-white/5 p-4 rounded-xl border border-white/5">
                                    <p className="text-gray-300 text-sm leading-relaxed whitespace-pre-line">
                                        {room?.description || "No description provided for this project."}
                                    </p>
                                </div>
                            </div>

                            {/* Members */}
                            <div>
                                <div className="flex items-center justify-between mb-4">
                                    <h3 className="text-xs uppercase tracking-widest text-gray-500 font-bold flex items-center gap-2">
                                        <i className="fa-solid fa-users text-accent"></i> Members
                                    </h3>
                                    <span className="text-xs bg-accent/20 text-accent px-2 py-0.5 rounded-full font-bold">
                                        {members.length}
                                    </span>
                                </div>
                                <div className="space-y-3">
                                    {members.map((member) => (
                                        <div key={member.user_id} className="flex items-center gap-3 p-2 rounded-xl hover:bg-white/5 transition border border-transparent hover:border-white/5 group">
                                            <div className="w-10 h-10 rounded-full bg-gradient-to-br from-accent/30 to-purple-500/30 flex items-center justify-center text-xs font-bold text-white shadow-md overflow-hidden flex-shrink-0">
                                                {member.profile_image || member.profile_picture ? (
                                                    <img src={`${API_BASE_URL}/${member.profile_image || member.profile_picture}`} alt={member.username} className="w-full h-full object-cover" />
                                                ) : (
                                                    member.username?.charAt(0).toUpperCase()
                                                )}
                                            </div>
                                            <div className="flex-grow min-w-0">
                                                <div className="text-sm font-semibold truncate group-hover:text-accent transition-colors">{member.username}</div>
                                                <div className="text-[10px] text-gray-500 flex items-center gap-1.5 mt-0.5">
                                                    <span className={`w-1.5 h-1.5 rounded-full ${member.role === 'ADMIN' ? 'bg-accent' : 'bg-gray-600'}`}></span>
                                                    {member.role}
                                                </div>
                                            </div>
                                            {member.role === 'ADMIN' && (
                                                <i className="fa-solid fa-crown text-[10px] text-accent" title="Room Admin"></i>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Danger Zone */}
                            {parseInt(room?.owner_id) === parseInt(user?.id || user?.user_id) && (
                                <div className="pt-6 border-t border-white/5">
                                    <h3 className="text-xs uppercase tracking-widest text-red-500 font-bold mb-3 flex items-center gap-2">
                                        <i className="fa-solid fa-triangle-exclamation"></i> Danger Zone
                                    </h3>
                                    <button
                                        onClick={handleDeleteRoom}
                                        className="w-full flex items-center justify-center gap-2 py-3 px-4 bg-red-500/10 hover:bg-red-500/20 text-red-500 rounded-xl transition border border-red-500/20 group font-bold text-sm"
                                    >
                                        <i className="fa-solid fa-trash-can group-hover:shake"></i>
                                        Delete Room
                                    </button>
                                    <p className="text-[10px] text-gray-500 mt-2 text-center">
                                        Once you delete a room, there is no going back. Please be certain.
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </div>

            <style jsx>{`
                .animate-slide-in-right {
                    animation: slideIn 0.3s ease-out;
                }
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                .shake {
                    animation: shake 0.5s ease-in-out infinite;
                }
                @keyframes shake {
                    0%, 100% { transform: rotate(0deg); }
                    25% { transform: rotate(-5deg); }
                    75% { transform: rotate(5deg); }
                }
                .custom-scrollbar::-webkit-scrollbar {
                    width: 4px;
                }
                .custom-scrollbar::-webkit-scrollbar-track {
                    background: transparent;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb {
                    background: rgba(88, 166, 255, 0.2);
                    border-radius: 10px;
                }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                    background: rgba(88, 166, 255, 0.4);
                }
            `}</style>
        </div>
    );
};

export default ProjectChatPage;
