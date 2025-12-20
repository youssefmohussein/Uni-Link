import { apiRequest } from "./apiClient";

export const initRoomsDB = async () => {
    return await apiRequest("initRoomsDB", "GET");
};

export const createRoom = async (roomData) => {
    // roomData: { owner_id, name, description, password, photo_url }
    const res = await apiRequest("createRoom", "POST", roomData);
    if (res.status !== "success") throw new Error(res.message || "Failed to create room");
    return res;
};

export const getAllRooms = async () => {
    const res = await apiRequest("getAllRooms", "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch rooms");
    if (res.data && Array.isArray(res.data)) return res.data;
    if (res.data && res.data.data && Array.isArray(res.data.data)) return res.data.data;
    return Array.isArray(res) ? res : [];
};

export const getUserRooms = async () => {
    const res = await apiRequest("getUserRooms", "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch your rooms");
    if (res.data && Array.isArray(res.data)) return res.data;
    if (res.data && res.data.data && Array.isArray(res.data.data)) return res.data.data;
    return Array.isArray(res) ? res : [];
};

export const getRoomById = async (room_id) => {
    const res = await apiRequest(`getRoom?room_id=${room_id}`, "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch room details");
    return res.data;
};

export const sendMessage = async (msgData) => {
    // msgData: { room_id, user_id, content }
    const res = await apiRequest("sendMessage", "POST", msgData);
    if (res.status !== "success") throw new Error(res.message || "Failed to send message");
    return true;
};

export const getRoomMessages = async (room_id, after_id = 0) => {
    const res = await apiRequest(`getMessages?room_id=${room_id}&after_id=${after_id}`, "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch messages");
    return res.data;
};

export const updateRoom = async (roomData) => {
    // roomData: { room_id, room_name, description }
    const res = await apiRequest("updateRoom", "POST", roomData);
    if (res.status !== "success") throw new Error(res.message || "Failed to update room");
    return true;
};

export const deleteRoom = async (room_id) => {
    const res = await apiRequest("deleteRoom", "POST", { room_id });
    if (res.status !== "success") throw new Error(res.message || "Failed to delete room");
    return true;
};
export const joinRoom = async (roomId, password) => {
    const res = await apiRequest("joinRoom", "POST", { room_id: roomId, password });
    if (res.status !== "success") throw new Error(res.message || "Failed to join room");
    return res;
};

export const getRoomMembers = async (room_id) => {
    const res = await apiRequest(`getRoomMembers?room_id=${room_id}`, "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch room members");
    return res.data;
};
