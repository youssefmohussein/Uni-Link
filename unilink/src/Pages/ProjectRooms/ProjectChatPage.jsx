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
    const [selectedFile, setSelectedFile] = useState(null);
    const [filePreview, setFilePreview] = useState(null);
    const [isRecording, setIsRecording] = useState(false);
    const [audioBlob, setAudioBlob] = useState(null);
    const [recordingDuration, setRecordingDuration] = useState(0);
    const [showMentionList, setShowMentionList] = useState(false);
    const [mentionSearch, setMentionSearch] = useState("");
    const [mentionIndex, setMentionIndex] = useState(0);

    const fileInputRef = useRef(null);
    const messagesEndRef = useRef(null);
    const mediaRecorderRef = useRef(null);
    const recordingIntervalRef = useRef(null);
    const inputRef = useRef(null);
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

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setSelectedFile(file);
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onloadend = () => setFilePreview(reader.result);
                reader.readAsDataURL(file);
            } else {
                setFilePreview(null);
            }
        }
    };

    const handleDeleteMessage = async (messageId) => {
        if (!window.confirm("Are you sure you want to delete this message?")) return;

        try {
            console.log("Deleting message:", messageId);
            await projectRoomHandler.removeChatMessage(messageId);
            setMessages(prev => prev.filter(m => m.message_id !== messageId));
            toast.success("Message deleted");
        } catch (err) {
            toast.error(err.message);
        }
    };

    const startRecording = async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            const mediaRecorder = new MediaRecorder(stream);
            mediaRecorderRef.current = mediaRecorder;
            const chunks = [];

            mediaRecorder.ondataavailable = (e) => {
                if (e.data.size > 0) {
                    chunks.push(e.data);
                }
            };

            mediaRecorder.onstop = () => {
                const blob = new Blob(chunks, { type: 'audio/webm' });
                setAudioBlob(blob);
                stream.getTracks().forEach(track => track.stop());
            };

            mediaRecorder.start();
            setIsRecording(true);
            setRecordingDuration(0);

            // Start duration timer
            recordingIntervalRef.current = setInterval(() => {
                setRecordingDuration(prev => prev + 1);
            }, 1000);
        } catch (err) {
            toast.error("Failed to access microphone: " + err.message);
        }
    };

    const stopRecording = () => {
        if (mediaRecorderRef.current && isRecording) {
            mediaRecorderRef.current.stop();
            setIsRecording(false);
            if (recordingIntervalRef.current) {
                clearInterval(recordingIntervalRef.current);
            }
        }
    };

    const handleInputChange = (e) => {
        const value = e.target.value;
        const cursorPosition = e.target.selectionStart;
        setNewMessage(value);

        // Check for mention trigger (@)
        const lastPart = value.slice(0, cursorPosition).split(/\s/).pop();
        if (lastPart.startsWith('@')) {
            const query = lastPart.slice(1).toLowerCase();
            setMentionSearch(query);
            setShowMentionList(true);
            setMentionIndex(0);
        } else {
            setShowMentionList(false);
        }
    };

    const insertMention = (username) => {
        const cursorPosition = inputRef.current.selectionStart;
        const prefix = newMessage.slice(0, cursorPosition);
        const suffix = newMessage.slice(cursorPosition);

        const lastAtIndex = prefix.lastIndexOf('@');
        const updatedPrefix = prefix.slice(0, lastAtIndex) + '@' + username + ' ';

        setNewMessage(updatedPrefix + suffix);
        setShowMentionList(false);

        // Focus back on input
        setTimeout(() => {
            inputRef.current.focus();
            const newPos = updatedPrefix.length;
            inputRef.current.setSelectionRange(newPos, newPos);
        }, 0);
    };

    const filteredMembers = members.filter(m =>
        m.user_id !== (user.id || user.user_id) &&
        m.username.toLowerCase().includes(mentionSearch)
    );

    const handleKeyDown = (e) => {
        if (showMentionList && filteredMembers.length > 0) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                setMentionIndex(prev => (prev + 1) % filteredMembers.length);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                setMentionIndex(prev => (prev - 1 + filteredMembers.length) % filteredMembers.length);
            } else if (e.key === 'Enter' || e.key === 'Tab') {
                e.preventDefault();
                insertMention(filteredMembers[mentionIndex].username);
            } else if (e.key === 'Escape') {
                setShowMentionList(false);
            }
        }
    };

    const cancelRecording = () => {
        if (mediaRecorderRef.current && isRecording) {
            mediaRecorderRef.current.stop();
            setIsRecording(false);
            setAudioBlob(null);
            setRecordingDuration(0);
            if (recordingIntervalRef.current) {
                clearInterval(recordingIntervalRef.current);
            }
        }
    };

    const handleSend = async (e) => {
        e.preventDefault();
        if (!newMessage.trim() && !selectedFile && !audioBlob) return;

        setSending(true);
        try {
            let fileData = null;

            // Handle voice message
            if (audioBlob) {
                const voiceFile = new File([audioBlob], `voice-${Date.now()}.webm`, { type: 'audio/webm' });
                fileData = await projectRoomHandler.uploadChatFile(voiceFile);
                await projectRoomHandler.sendMessage({
                    room_id: roomId,
                    sender_id: user.id || user.user_id,
                    content: newMessage || "Voice message",
                    message_type: 'VOICE',
                    file_path: fileData.file_path
                });
                setAudioBlob(null);
                setRecordingDuration(0);
            }
            // Handle file attachment
            else if (selectedFile) {
                fileData = await projectRoomHandler.uploadChatFile(selectedFile);
                await projectRoomHandler.sendMessage({
                    room_id: roomId,
                    sender_id: user.id || user.user_id,
                    content: newMessage,
                    message_type: selectedFile.type.startsWith('image/') ? 'IMAGE' : 'FILE',
                    file_path: fileData.file_path
                });
                setSelectedFile(null);
                setFilePreview(null);
            }
            // Handle text message
            else {
                await projectRoomHandler.sendMessage({
                    room_id: roomId,
                    sender_id: user.id || user.user_id,
                    content: newMessage,
                    message_type: 'TEXT',
                    file_path: null
                });
            }

            setNewMessage("");
            fetchMessages(); // Immediate refresh
        } catch (err) {
            toast.error("Failed to send: " + err.message);
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

                                            {msg.message_type === 'IMAGE' && msg.file_path && (
                                                <div className="mb-2 max-w-sm rounded-lg overflow-hidden border border-white/10">
                                                    <img
                                                        src={`${API_BASE_URL}/${msg.file_path}`}
                                                        alt="Attachment"
                                                        className="w-full h-auto cursor-pointer hover:opacity-90 transition"
                                                        onClick={() => window.open(`${API_BASE_URL}/${msg.file_path}`, '_blank')}
                                                    />
                                                </div>
                                            )}

                                            {msg.message_type === 'VOICE' && msg.file_path && (
                                                <div className="mb-2">
                                                    <audio controls className="w-full max-w-xs">
                                                        <source src={`${API_BASE_URL}/${msg.file_path}`} type="audio/webm" />
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                </div>
                                            )}

                                            {msg.message_type === 'FILE' && msg.file_path && (
                                                <div className="mb-2">
                                                    <a
                                                        href={`${API_BASE_URL}/${msg.file_path}`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className={`flex items-center gap-3 p-3 rounded-xl border transition ${isMe ? 'bg-white/10 border-white/10 hover:bg-white/20' : 'bg-black/20 border-white/5 hover:bg-black/30'}`}
                                                    >
                                                        <div className={`w-10 h-10 rounded-lg flex items-center justify-center ${isMe ? 'bg-white/10' : 'bg-accent/20'}`}>
                                                            <i className={`fa-solid ${msg.file_path.endsWith('.zip') ? 'fa-file-zipper' : (msg.file_path.endsWith('.doc') || msg.file_path.endsWith('.docx')) ? 'fa-file-word' : 'fa-file-lines'} text-lg ${isMe ? 'text-white' : 'text-accent'}`}></i>
                                                        </div>
                                                        <div className="flex-grow min-w-0">
                                                            <p className="text-xs font-medium truncate">Attachment</p>
                                                            <p className="text-[10px] opacity-60">Click to Download</p>
                                                        </div>
                                                        <i className="fa-solid fa-download text-xs opacity-40"></i>
                                                    </a>
                                                </div>
                                            )}

                                            {msg.content && <p className="whitespace-pre-wrap break-words text-sm leading-relaxed">{msg.content}</p>}
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
                    <div className="flex flex-col gap-2 mb-4">
                        {selectedFile && (
                            <div className="bg-[#121212] rounded-xl border border-white/10 p-3 flex items-center justify-between animate-slide-in-right">
                                <div className="flex items-center gap-3 overflow-hidden">
                                    {filePreview ? (
                                        <img src={filePreview} alt="Preview" className="w-12 h-12 rounded object-cover border border-white/10" />
                                    ) : (
                                        <div className="w-12 h-12 rounded bg-white/5 flex items-center justify-center border border-white/10">
                                            <i className="fa-solid fa-file-lines text-accent text-xl"></i>
                                        </div>
                                    )}
                                    <div className="overflow-hidden">
                                        <p className="text-sm font-medium truncate">{selectedFile.name}</p>
                                        <p className="text-[10px] text-gray-500">{(selectedFile.size / 1024).toFixed(1)} KB</p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => { setSelectedFile(null); setFilePreview(null); }}
                                    className="p-2 text-gray-400 hover:text-red-500 transition"
                                >
                                    <i className="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        )}

                        {audioBlob && (
                            <div className="bg-[#121212] rounded-xl border border-white/10 p-3 flex items-center justify-between animate-slide-in-right">
                                <div className="flex items-center gap-3">
                                    <div className="w-12 h-12 rounded bg-red-500/20 flex items-center justify-center">
                                        <i className="fa-solid fa-microphone text-red-500 text-xl"></i>
                                    </div>
                                    <div>
                                        <p className="text-sm font-medium">Voice Message</p>
                                        <p className="text-[10px] text-gray-500">{recordingDuration}s</p>
                                    </div>
                                </div>
                                <button
                                    onClick={cancelRecording}
                                    className="p-2 text-gray-400 hover:text-red-500 transition"
                                >
                                    <i className="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        )}

                        <form onSubmit={handleSend} className="bg-[#121212] rounded-2xl border border-white/10 p-2 flex items-center gap-2 shadow-xl">
                            <input
                                type="file"
                                ref={fileInputRef}
                                onChange={handleFileChange}
                                className="hidden"
                            />
                            <div className="flex gap-1">
                                <button
                                    type="button"
                                    onClick={() => fileInputRef.current.click()}
                                    className="p-4 text-gray-400 hover:text-white transition w-12 h-14 flex items-center justify-center"
                                    disabled={sending}
                                    title="Attach File"
                                >
                                    <i className="fa-solid fa-paperclip text-lg"></i>
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setNewMessage(prev => (prev ? prev + " " : "") + "@unilink ")}
                                    className="p-4 text-accent hover:text-accent/80 transition w-12 h-14 flex items-center justify-center relative group"
                                    disabled={sending || isRecording}
                                    title="Ask AI (@unilink)"
                                >
                                    <i className="fa-solid fa-robot text-xl"></i>
                                    <span className="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-accent text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">
                                        Ask AI
                                    </span>
                                </button>
                                <button
                                    type="button"
                                    onClick={isRecording ? stopRecording : startRecording}
                                    className={`p-4 transition w-12 h-14 flex items-center justify-center relative group ${isRecording
                                        ? 'text-red-500 hover:text-red-400'
                                        : 'text-gray-400 hover:text-white'
                                        }`}
                                    disabled={sending}
                                    title={isRecording ? "Stop Recording" : "Voice Message"}
                                >
                                    <i className={`fa-solid fa-microphone text-lg ${isRecording ? 'animate-pulse' : ''}`}></i>
                                    <span className="absolute -top-8 left-1/2 transform -translate-x-1/2 bg-accent text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition whitespace-nowrap pointer-events-none">
                                        {isRecording ? 'Stop' : 'Voice'}
                                    </span>
                                </button>
                            </div>
                            <div className="flex-grow relative">
                                {showMentionList && filteredMembers.length > 0 && (
                                    <div className="absolute bottom-full left-0 mb-2 w-64 bg-[#1a1a1a] border border-white/10 rounded-xl shadow-2xl overflow-hidden animate-slide-in-up z-[60]">
                                        <div className="p-2 border-b border-white/5 bg-white/5 text-[10px] uppercase tracking-widest text-gray-500 font-bold">
                                            Mention Member
                                        </div>
                                        <div className="max-h-48 overflow-y-auto custom-scrollbar">
                                            {filteredMembers.map((member, idx) => (
                                                <div
                                                    key={member.user_id}
                                                    onClick={() => insertMention(member.username)}
                                                    className={`flex items-center gap-3 p-3 cursor-pointer transition ${mentionIndex === idx ? 'bg-accent text-white' : 'hover:bg-white/5 text-gray-300'}`}
                                                >
                                                    <div className="w-8 h-8 rounded-full bg-gradient-to-br from-white/10 to-white/5 flex items-center justify-center text-xs font-bold overflow-hidden border border-white/10">
                                                        {member.profile_image || member.profile_picture ? (
                                                            <img src={`${API_BASE_URL}/${member.profile_image || member.profile_picture}`} alt="" className="w-full h-full object-cover" />
                                                        ) : (
                                                            member.username?.charAt(0).toUpperCase()
                                                        )}
                                                    </div>
                                                    <span className="text-sm font-medium">{member.username}</span>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}
                                <input
                                    ref={inputRef}
                                    className="w-full bg-transparent text-white placeholder-gray-500 px-2 py-4 focus:outline-none text-sm"
                                    placeholder="Type your message..."
                                    value={newMessage}
                                    onChange={handleInputChange}
                                    onKeyDown={handleKeyDown}
                                    disabled={sending || isRecording}
                                />
                            </div>
                            <button
                                type="submit"
                                disabled={(!newMessage.trim() && !selectedFile && !audioBlob) || sending}
                                className="p-4 bg-accent text-white rounded-xl hover:bg-accent/80 transition disabled:opacity-50 disabled:cursor-not-allowed w-14 h-14 flex items-center justify-center shadow-lg shadow-accent/20"
                            >
                                {sending ? <i className="fa-solid fa-spinner fa-spin"></i> : <i className="fa-solid fa-paper-plane text-lg"></i>}
                            </button>
                        </form>
                    </div>
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
                .animate-slide-in-up {
                    animation: slideUp 0.2s ease-out;
                }
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideUp {
                    from { transform: translateY(10px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
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
