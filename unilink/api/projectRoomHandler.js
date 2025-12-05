import { apiRequest } from "./apiClient";

export const initRoomsDB = async () => {
    return await apiRequest("index.php/initRoomsDB", "GET");
};

export const createRoom = async (roomData) => {
    // roomData: { room_name, description, created_by }
    const res = await apiRequest("index.php/createRoom", "POST", roomData);
    if (res.status !== "success") throw new Error(res.message || "Failed to create room");
    return res;
};

export const getAllRooms = async () => {
    const res = await apiRequest("index.php/getAllRooms", "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch rooms");
    return res.data;
};

export const getRoomById = async (room_id) => {
    const res = await apiRequest(`index.php/getRoomById/${room_id}`, "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch room details");
    return res.data;
};

export const sendMessage = async (msgData) => {
    // msgData: { room_id, user_id, content }
    const res = await apiRequest("index.php/sendMessage", "POST", msgData);
    if (res.status !== "success") throw new Error(res.message || "Failed to send message");
    return true;
};

export const getRoomMessages = async (room_id, after_id = 0) => {
    const res = await apiRequest(`index.php/getRoomMessages/${room_id}?after_id=${after_id}`, "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch messages");
    return res.data;
};

export const updateRoom = async (roomData) => {
    // roomData: { room_id, room_name, description }
    const res = await apiRequest("index.php/updateRoom", "POST", roomData);
    if (res.status !== "success") throw new Error(res.message || "Failed to update room");
    return true;
};

export const deleteRoom = async (room_id) => {
    const res = await apiRequest("index.php/deleteRoom", "POST", { room_id });
    if (res.status !== "success") throw new Error(res.message || "Failed to delete room");
    return true;
};
