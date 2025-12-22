<?php
namespace App\Controllers;

use App\Repositories\NotificationRepository;
use App\Middlewares\AuthMiddleware;
use App\Utils\ResponseHandler;

/**
 * NotificationController
 * 
 * Handles notification API endpoints
 * Works with Observer Pattern for notification creation
 */
class NotificationController extends BaseController
{
    private NotificationRepository $notificationRepo;

    public function __construct(NotificationRepository $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * Get user's notifications
     * GET /api/notifications
     */
    public function getNotifications(): void
    {
        AuthMiddleware::handle();
        $userId = AuthMiddleware::getCurrentUserId();

        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

        // Temporary fix for duplicates
        $this->notificationRepo->fixDuplicateMentions();

        $notifications = $this->notificationRepo->getUserNotifications($userId, $limit, $offset);

        ResponseHandler::success([
            'notifications' => $notifications,
            'count' => count($notifications)
        ]);
    }

    /**
     * Get unread notification count
     * GET /api/notifications/unread-count
     */
    public function getUnreadCount(): void
    {
        AuthMiddleware::handle();
        $userId = AuthMiddleware::getCurrentUserId();

        $count = $this->notificationRepo->getUnreadCount($userId);

        ResponseHandler::success([
            'unread_count' => $count
        ]);
    }

    /**
     * Get unread notifications
     * GET /api/notifications/unread
     */
    public function getUnread(): void
    {
        AuthMiddleware::handle();
        $userId = AuthMiddleware::getCurrentUserId();

        $notifications = $this->notificationRepo->getUnreadNotifications($userId);

        ResponseHandler::success([
            'notifications' => $notifications,
            'count' => count($notifications)
        ]);
    }

    /**
     * Mark notification as read
     * PUT /api/notifications/:id/read
     */
    public function markAsRead(): void
    {
        AuthMiddleware::handle();
        $userId = AuthMiddleware::getCurrentUserId();

        $notificationId = $_POST['notification_id'] ?? $_GET['id'] ?? null;

        if (!$notificationId) {
            ResponseHandler::error('Notification ID is required', 400);
            return;
        }

        // Verify notification belongs to user
        $notification = $this->notificationRepo->find($notificationId);
        if (!$notification || $notification['user_id'] != $userId) {
            ResponseHandler::error('Notification not found', 404);
            return;
        }

        $success = $this->notificationRepo->markAsRead($notificationId);

        if ($success) {
            ResponseHandler::success(['message' => 'Notification marked as read']);
        } else {
            ResponseHandler::error('Failed to mark notification as read', 500);
        }
    }

    /**
     * Mark all notifications as read
     * PUT /api/notifications/mark-all-read
     */
    public function markAllAsRead(): void
    {
        AuthMiddleware::handle();
        $userId = AuthMiddleware::getCurrentUserId();

        $success = $this->notificationRepo->markAllAsRead($userId);

        if ($success) {
            ResponseHandler::success(['message' => 'All notifications marked as read']);
        } else {
            ResponseHandler::error('Failed to mark notifications as read', 500);
        }
    }

    /**
     * Delete notification
     * DELETE /api/notifications/:id
     */
    public function deleteNotification(): void
    {
        AuthMiddleware::handle();
        $userId = AuthMiddleware::getCurrentUserId();

        $notificationId = $_POST['notification_id'] ?? $_GET['id'] ?? null;

        if (!$notificationId) {
            ResponseHandler::error('Notification ID is required', 400);
            return;
        }

        // Verify notification belongs to user
        $notification = $this->notificationRepo->find($notificationId);
        if (!$notification || $notification['user_id'] != $userId) {
            ResponseHandler::error('Notification not found', 404);
            return;
        }

        $success = $this->notificationRepo->delete($notificationId);

        if ($success) {
            ResponseHandler::success(['message' => 'Notification deleted']);
        } else {
            ResponseHandler::error('Failed to delete notification', 500);
        }
    }

    /**
     * Debug: Check notifications and mentions
     */
    public function debug(): void
    {
        try {
            $db = \App\Utils\Database::getInstance()->getConnection();

            // Get recent notifications
            $stmt = $db->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10");
            $notifications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get recent mentions
            $stmt = $db->query("SELECT * FROM chat_mentions ORDER BY created_at DESC LIMIT 10");
            $mentions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            ResponseHandler::success([
                'notifications' => $notifications,
                'notifications_count' => count($notifications),
                'mentions' => $mentions,
                'mentions_count' => count($mentions),
                'message' => count($notifications) === 0
                    ? 'No notifications found - notifications are NOT being created from mentions'
                    : 'Found notifications in database'
            ]);

        } catch (\Exception $e) {
            ResponseHandler::error($e->getMessage(), 500);
        }
    }

    /**
            $db = \App\Utils\Database::getInstance()->getConnection();

            // Get recent notifications
            $stmt = $db->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10");
            $notifications = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Get recent mentions
            $stmt = $db->query("SELECT * FROM chat_mentions ORDER BY created_at DESC LIMIT 10");
            $mentions = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            $this->success([
                'notifications' => $notifications,
                'notifications_count' => count($notifications),
                'mentions' => $mentions,
                'mentions_count' => count($mentions),
                'message' => count($notifications) === 0
                    ? 'No notifications found - notifications are NOT being created from mentions'
                    : 'Found notifications in database'
            ]);

        } catch (\Exception $e) {
            $this->error($e->getMessage(), 500);
        }
    }

    /**
     * Get notifications by type
     * GET /api/notifications/by-type/:type
     */
    public function getByType(): void
    {
        AuthMiddleware::handle();
        $userId = AuthMiddleware::getCurrentUserId();

        $type = $_GET['type'] ?? null;

        if (!$type) {
            ResponseHandler::error('Notification type is required', 400);
            return;
        }

        $notifications = $this->notificationRepo->getNotificationsByType($userId, $type);

        ResponseHandler::success([
            'notifications' => $notifications,
            'type' => $type,
            'count' => count($notifications)
        ]);
    }
}
