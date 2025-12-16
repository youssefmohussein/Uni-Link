// Notification API Service
// Handles all notification-related API calls

import { apiCall } from '../config/api';

/**
 * Get all notifications for the current user
 * @param {number} limit - Number of notifications to fetch
 * @param {number} offset - Offset for pagination
 * @returns {Promise} Notifications data
 */
export const getNotifications = async (limit = 50, offset = 0) => {
    const response = await apiCall(`/api/notifications?limit=${limit}&offset=${offset}`);
    return response.json();
};

/**
 * Get unread notifications
 * @returns {Promise} Unread notifications
 */
export const getUnreadNotifications = async () => {
    const response = await apiCall('/api/notifications/unread');
    return response.json();
};

/**
 * Get unread notification count
 * @returns {Promise} Unread count
 */
export const getUnreadCount = async () => {
    const response = await apiCall('/api/notifications/unread-count');
    return response.json();
};

/**
 * Mark notification as read
 * @param {number} notificationId - ID of the notification
 * @returns {Promise} Response
 */
export const markAsRead = async (notificationId) => {
    const response = await apiCall('/api/notifications/mark-as-read', {
        method: 'PUT',
        body: JSON.stringify({ notification_id: notificationId })
    });
    return response.json();
};

/**
 * Mark all notifications as read
 * @returns {Promise} Response
 */
export const markAllAsRead = async () => {
    const response = await apiCall('/api/notifications/mark-all-read', {
        method: 'PUT'
    });
    return response.json();
};

/**
 * Delete a notification
 * @param {number} notificationId - ID of the notification
 * @returns {Promise} Response
 */
export const deleteNotification = async (notificationId) => {
    const response = await apiCall('/api/notifications/delete', {
        method: 'DELETE',
        body: JSON.stringify({ notification_id: notificationId })
    });
    return response.json();
};

/**
 * Get notifications by type
 * @param {string} type - Notification type (POST_LIKE, PROJECT_APPROVED, etc.)
 * @returns {Promise} Notifications of specific type
 */
export const getNotificationsByType = async (type) => {
    const response = await apiCall(`/api/notifications/by-type?type=${type}`);
    return response.json();
};

export default {
    getNotifications,
    getUnreadNotifications,
    getUnreadCount,
    markAsRead,
    markAllAsRead,
    deleteNotification,
    getNotificationsByType
};
