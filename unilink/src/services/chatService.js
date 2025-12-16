// Chat API Service
// Handles all chat-related API calls

import { apiCall } from '../config/api';

/**
 * Send a message to a chat room
 * @param {number} roomId - Room ID
 * @param {string} content - Message content
 * @param {string} messageType - Message type (TEXT, FILE, IMAGE)
 * @param {string} filePath - Optional file path
 * @returns {Promise} Response with message data
 */
export const sendMessage = async (roomId, content, messageType = 'TEXT', filePath = null) => {
    const formData = new FormData();
    formData.append('room_id', roomId);
    formData.append('content', content);
    formData.append('message_type', messageType);
    if (filePath) {
        formData.append('file_path', filePath);
    }

    const response = await apiCall('/api/chat/send', {
        method: 'POST',
        headers: {}, // Remove Content-Type to let browser set it for FormData
        body: formData
    });
    return response.json();
};

/**
 * Get messages for a room
 * @param {number} roomId - Room ID
 * @param {number} limit - Number of messages to fetch
 * @param {number} offset - Offset for pagination
 * @returns {Promise} Messages data
 */
export const getRoomMessages = async (roomId, limit = 50, offset = 0) => {
    const response = await apiCall(`/api/chat/messages?room_id=${roomId}&limit=${limit}&offset=${offset}`);
    return response.json();
};

/**
 * Get message count for a room
 * @param {number} roomId - Room ID
 * @returns {Promise} Message count
 */
export const getMessageCount = async (roomId) => {
    const response = await apiCall(`/api/chat/message-count?room_id=${roomId}`);
    return response.json();
};

/**
 * Delete a message
 * @param {number} messageId - Message ID
 * @returns {Promise} Response
 */
export const deleteMessage = async (messageId) => {
    const response = await apiCall('/api/chat/messages/delete', {
        method: 'DELETE',
        body: JSON.stringify({ message_id: messageId })
    });
    return response.json();
};

export default {
    sendMessage,
    getRoomMessages,
    getMessageCount,
    deleteMessage
};
