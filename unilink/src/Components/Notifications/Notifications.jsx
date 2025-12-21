// Notifications Component
// Displays user notifications with real-time updates

import React, { useState, useEffect } from 'react';
import {
    getNotifications,
    getUnreadCount,
    markAsRead,
    markAllAsRead,
    deleteNotification
} from '../../services/notificationService';
import './Notifications.css';

const Notifications = ({ isOpen, onClose }) => {
    const [notifications, setNotifications] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [loading, setLoading] = useState(false);

    // Fetch notifications
    const fetchNotifications = async () => {
        try {
            setLoading(true);
            const data = await getNotifications(50, 0);
            if (data.status === 'success') {
                setNotifications(data.data.notifications || []);
            }
        } catch (error) {
            console.error('Error fetching notifications:', error);
        } finally {
            setLoading(false);
        }
    };

    // Fetch unread count
    const fetchUnreadCount = async () => {
        try {
            const data = await getUnreadCount();
            if (data.status === 'success') {
                setUnreadCount(data.data.unread_count || 0);
            }
        } catch (error) {
            console.error('Error fetching unread count:', error);
        }
    };

    // Mark notification as read
    const handleMarkAsRead = async (notificationId) => {
        try {
            await markAsRead(notificationId);
            setNotifications(prev =>
                prev.map(n =>
                    n.notification_id === notificationId
                        ? { ...n, is_read: true }
                        : n
                )
            );
            fetchUnreadCount();
        } catch (error) {
            console.error('Error marking as read:', error);
        }
    };

    // Mark all as read
    const handleMarkAllAsRead = async () => {
        try {
            await markAllAsRead();
            setNotifications(prev => prev.map(n => ({ ...n, is_read: true })));
            setUnreadCount(0);
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    };

    // Delete notification
    const handleDelete = async (notificationId) => {
        try {
            await deleteNotification(notificationId);
            setNotifications(prev => prev.filter(n => n.notification_id !== notificationId));
            fetchUnreadCount();
        } catch (error) {
            console.error('Error deleting notification:', error);
        }
    };

    // Initial fetch
    useEffect(() => {
        if (isOpen) {
            fetchNotifications();
            fetchUnreadCount();
        }
    }, [isOpen]);

    // Poll for new notifications every 30 seconds
    useEffect(() => {
        const interval = setInterval(() => {
            fetchUnreadCount();
        }, 30000);

        return () => clearInterval(interval);
    }, []);

    // Navigate to notification target
    const handleNotificationClick = (e, notification) => {
        e.stopPropagation();

        // Mark as read first
        if (!notification.is_read) {
            handleMarkAsRead(notification.notification_id);
        }

        // Navigate based on type
        if (notification.type === 'CHAT_MENTION' || notification.related_entity_type === 'CHAT_ROOM') {
            window.location.href = `/project-room/${notification.related_entity_id}`;
        }
    };

    if (!isOpen) return null;

    return (
        <div className="notifications-panel" onClick={e => e.stopPropagation()}>
            <div className="notifications-header">
                <h3>Notifications</h3>
                <div className="notifications-actions">
                    {unreadCount > 0 && (
                        <button onClick={handleMarkAllAsRead} className="mark-all-read">
                            Mark Read
                        </button>
                    )}
                    <button onClick={onClose} className="close-btn">
                        <i className="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>

            <div className="notifications-list custom-scrollbar">
                {loading && notifications.length === 0 ? (
                    <div className="loading">
                        <i className="fa-solid fa-circle-notch fa-spin mb-2 text-xl"></i>
                        <p>Updating...</p>
                    </div>
                ) : notifications.length === 0 ? (
                    <div className="empty-state">
                        <i className="fa-solid fa-bell-slash mb-3 text-3xl opacity-20"></i>
                        <p>No notifications yet</p>
                    </div>
                ) : (
                    notifications
                        .filter(n => n.message !== 'You were mentioned in a chat message')
                        .map(notification => (
                            <div
                                key={notification.notification_id}
                                className={`notification-item ${!notification.is_read ? 'unread' : ''}`}
                                onClick={(e) => handleNotificationClick(e, notification)}
                            >
                                <div className="notification-content">
                                    {/* Only show title if it's NOT the generic "You were mentioned" */}
                                    {notification.title !== 'You were mentioned' && (
                                        <div className="notification-title">{notification.title}</div>
                                    )}
                                    <div className="notification-message">{notification.message}</div>
                                    <div className="notification-time">
                                        <i className="fa-regular fa-clock mr-1"></i>
                                        {new Date(notification.created_at).toLocaleDateString()} at {new Date(notification.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                    </div>
                                </div>
                                <button
                                    className="delete-btn"
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        handleDelete(notification.notification_id);
                                    }}
                                    title="Delete Notification"
                                >
                                    <i className="fa-solid fa-trash-can text-xs"></i>
                                </button>
                            </div>
                        ))
                )}
            </div>
        </div>
    );
};

export default Notifications;
