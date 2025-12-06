import React, { useState, useEffect, useRef } from "react";
import { useParams, useNavigate } from "react-router-dom";
import Header from "../../Components/Posts/Header";
import * as projectRoomHandler from "../../../api/projectRoomHandler";

const ProjectChatPage = () => {
    const { id: roomId } = useParams();
    const navigate = useNavigate();
    const [room, setRoom] = useState(null);
    const [messages, setMessages] = useState([]);
    const [newMessage, setNewMessage] = useState("");
    const [loading, setLoading] = useState(true);
    const [sending, setSending] = useState(false);

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
            // Optimally, we could store the last message ID and only fetch new ones
            // For MVP, just fetching all (or last N) is fine. 
            // The API supports after_id, but handling state sync is tricky with simple polling if we miss updates.
            // Let's just fetch all for now or the last 50.
            const data = await projectRoomHandler.getRoomMessages(roomId);
            setMessages(data);
            if (loading) setLoading(false);
        } catch (err) {
            console.error("Failed to fetch messages:", err);
        }
    };

    const handleSend = async (e) => {
        e.preventDefault();
        if (!newMessage.trim()) return;

        setSending(true);
        try {
            await projectRoomHandler.sendMessage({
                room_id: roomId,
                sender_id: user.id,
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

    if (loading && !room) return <div className="min-h-screen bg-black text-white flex items-center justify-center">Loading Room...</div>;

    return (
        <div className="min-h-screen bg-main text-white font-main flex flex-col">
            <Header logoSize="small" hideShareButton={true} />

            <div className="pt-20 flex-grow container mx-auto px-4 md:px-8 max-w-6xl flex flex-col h-[calc(100vh-20px)]">
                {/* Chat Header */}
                <div className="flex items-center justify-between py-4 border-b border-white/10 mb-4">
                    <div>
                        <h1 className="text-2xl font-bold flex items-center gap-2">
                            <span className="text-accent">#{room?.name}</span>
                        </h1>
                        <p className="text-gray-400 text-sm">{room?.description}</p>
                    </div>
                    <button
                        onClick={() => navigate('/project-rooms')}
                        className="text-gray-400 hover:text-white transition"
                    >
                        <i className="fa-solid fa-arrow-left"></i> Back to Rooms
                    </button>
                </div>

                {/* Messages Area */}
                <div className="flex-grow bg-[#121212] rounded-2xl border border-white/5 overflow-y-auto p-4 space-y-4 mb-4 custom-scrollbar relative">
                    {messages.length === 0 && (
                        <div className="absolute inset-0 flex items-center justify-center text-gray-600">
                            No messages yet. Start the conversation!
                        </div>
                    )}

                    {messages.map((msg) => {
                        const isMe = msg.sender_id === user.id;
                        return (
                            <div key={msg.message_id} className={`flex ${isMe ? 'justify-end' : 'justify-start'}`}>
                                <div className={`max-w-[80%] md:max-w-[60%] flex gap-3 ${isMe ? 'flex-row-reverse' : ''}`}>
                                    {/* Avatar */}
                                    <img
                                        src={`https://ui-avatars.com/api/?name=${msg.username}&background=random`}
                                        className="w-8 h-8 rounded-full bg-gray-700 object-cover flex-shrink-0 mt-1"
                                        alt={msg.username}
                                    />

                                    {/* Bubble */}
                                    <div className={`p-3 rounded-2xl ${isMe
                                        ? 'bg-accent text-white rounded-tr-sm'
                                        : 'bg-white/10 text-gray-100 rounded-tl-sm'
                                        }`}>
                                        {!isMe && <div className="text-xs text-accent mb-1 font-bold">{msg.username}</div>}
                                        <p className="whitespace-pre-wrap break-words text-sm leading-relaxed">{msg.content}</p>
                                        <div className={`text-[10px] mt-1 ${isMe ? 'text-white/60' : 'text-gray-500'} text-right`}>
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
                <form onSubmit={handleSend} className="bg-[#121212] rounded-xl border border-white/10 p-2 flex items-center gap-2 mb-4">
                    <input
                        className="flex-grow bg-transparent text-white placeholder-gray-500 px-4 py-3 focus:outline-none"
                        placeholder="Type your message..."
                        value={newMessage}
                        onChange={e => setNewMessage(e.target.value)}
                        disabled={sending}
                    />
                    <button
                        type="submit"
                        disabled={!newMessage.trim() || sending}
                        className="p-3 bg-accent text-white rounded-lg hover:bg-accent/80 transition disabled:opacity-50 disabled:cursor-not-allowed w-12 h-12 flex items-center justify-center"
                    >
                        {sending ? <i className="fa-solid fa-spinner fa-spin"></i> : <i className="fa-solid fa-paper-plane"></i>}
                    </button>
                </form>
            </div>
        </div>
    );
};

export default ProjectChatPage;
