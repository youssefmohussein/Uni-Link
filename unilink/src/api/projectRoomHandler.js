import { apiRequest } from "./apiClient";
import { API_BASE_URL } from "../../config/api";

export const createRoom = async (formData) => {
    // Note: formData should be FormData object
    // When sending FormData, DO NOT set Content-Type header manually
    const response = await fetch(`${API_BASE_URL}/createRoom`, {
        method: 'POST',
        credentials: 'include',
        body: formData
    });

    // Check if response is empty or invalid JSON
    const text = await response.text();
    let data;
    try {
        data = JSON.parse(text);
    } catch (e) {
        throw new Error(`Server returned invalid response: ${text.substring(0, 100)}...`);
    }

    if (!response.ok) throw new Error(data.message || 'Failed to create room');
    return data.data || data;
};

export const getAllRooms = async () => {
    const res = await apiRequest("getAllRooms", "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch rooms");
    // Handle both response formats
    if (res.data && Array.isArray(res.data)) return res.data;
    if (Array.isArray(res)) return res;
    return [];
};

export const getUserRooms = async () => {
    const res = await apiRequest("getUserRooms", "GET");
    if (res.status !== "success") throw new Error(res.message || "Failed to fetch your rooms");
    // Handle both response formats
    if (res.data && Array.isArray(res.data)) return res.data;
    if (Array.isArray(res)) return res;
    return [];
};

export const getRoom = async (roomId) => {
    const response = await fetch(`${API_BASE_URL}/getRoom?room_id=${roomId}`, {
        credentials: 'include'
    });
    const data = await response.json();
    if (!response.ok) throw new Error(data.message || 'Failed to fetch room');
    return data.data || data;
};

