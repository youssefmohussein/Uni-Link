<?php
namespace App\Controllers;

use App\Repositories\ChatRepository;
use App\Repositories\ProjectRoomRepository;
use App\Repositories\UserRepository;
use App\Services\NotificationService;
use App\Handlers\{ValidationHandler, PermissionHandler, MentionHandler, PersistenceHandler};
use App\Middlewares\AuthMiddleware;
use App\Utils\ResponseHandler;

/**
 * ChatController
 * 
 * Handles chat room and messaging API endpoints
 * Uses Chain of Responsibility pattern for message processing
 */
class ChatController extends BaseController
{
    private ChatRepository $chatRepo;
    private ProjectRoomRepository $roomRepo;
    private UserRepository $userRepo;
    private NotificationService $notificationService;

    public function __construct(
        ChatRepository $chatRepo,
        ProjectRoomRepository $roomRepo,
        UserRepository $userRepo,
        NotificationService $notificationService
    ) {
        $this->chatRepo = $chatRepo;
        $this->roomRepo = $roomRepo;
        $this->userRepo = $userRepo;
        $this->notificationService = $notificationService;
    }

    /**
     * Send a message (uses Chain of Responsibility)
     * POST /api/chat/send
     */
    public function sendMessage(): void
    {
        AuthMiddleware::handle();
        $userId = AuthMiddleware::getCurrentUserId();

        $data = $this->getJsonInput();

        // Prepare message data
        $messageData = [
            'room_id' => $data['room_id'] ?? null,
            'sender_id' => $userId,
            'content' => $data['content'] ?? null,
            'message_type' => $data['message_type'] ?? 'TEXT',
            'file_path' => $data['file_path'] ?? null
        ];

        // Build the Chain of Responsibility
        $validation = new ValidationHandler();
        $permission = new PermissionHandler($this->roomRepo);
        $mention = new MentionHandler($this->userRepo, $this->notificationService);
        $persistence = new PersistenceHandler($this->chatRepo, $this->notificationService);

        $validation->setNext($permission)
            ->setNext($mention)
            ->setNext($persistence);

        // Process message through the chain
        $result = $validation->handle($messageData);

        if (isset($result['error'])) {
            ResponseHandler::error($result['error'], 400);
        } else {
            ResponseHandler::success($result);
        }
    }

    /**
     * Get messages for a room
     * GET /api/chat/rooms/:id/messages
     */
    public function getRoomMessages(): void
    {
        AuthMiddleware::handle();
        $userId = AuthMiddleware::getCurrentUserId();

        $roomId = $_GET['room_id'] ?? $_GET['id'] ?? null;

        if (!$roomId) {
            ResponseHandler::error('Room ID is required', 400);
            return;
        }

        // Verify user is a member of the room
        if (!$this->roomRepo->isMember($roomId, $userId)) {
            ResponseHandler::error('You are not a member of this room', 403);
            return;
        }

        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 50;
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

        $messages = $this->chatRepo->getRoomMessages($roomId, $limit, $offset);

        ResponseHandler::success($messages);
    }

    /**
     * Get message count for a room
     * GET /api/chat/rooms/:id/message-count
     */
    public function getMessageCount(): void
    {
        AuthMiddleware::handle();
        $userId = AuthMiddleware::getCurrentUserId();

        $roomId = $_GET['room_id'] ?? $_GET['id'] ?? null;

        if (!$roomId) {
            ResponseHandler::error('Room ID is required', 400);
            return;
        }

        // Verify user is a member of the room
        if (!$this->roomRepo->isMember($roomId, $userId)) {
            ResponseHandler::error('You are not a member of this room', 403);
            return;
        }

        $count = $this->chatRepo->getRoomMessageCount($roomId);

        ResponseHandler::success($count);
    }

    /**
     * Delete a message
     * DELETE /api/chat/messages/:id
     */
    public function deleteMessage(): void
    {
        AuthMiddleware::handle();
        $userId = AuthMiddleware::getCurrentUserId();

        $data = $this->getJsonInput();
        $messageId = $data['message_id'] ?? $_GET['id'] ?? null;

        if (!$messageId) {
            ResponseHandler::error('Message ID is required', 400);
            return;
        }

        // Get message to verify ownership
        $message = $this->chatRepo->find($messageId);
        if (!$message) {
            ResponseHandler::error('Message not found', 404);
            return;
        }

        // Only sender or room admin can delete
        if ($message['sender_id'] != $userId) {
            // Check if user is room admin
            $isAdmin = $this->roomRepo->isUserRoomAdmin($message['room_id'], $userId);
            if (!$isAdmin) {
                ResponseHandler::error('You do not have permission to delete this message', 403);
                return;
            }
        }

        $success = $this->chatRepo->deleteMessage($messageId);

        if ($success) {
            ResponseHandler::success(['message' => 'Message deleted successfully']);
        } else {
            ResponseHandler::error('Failed to delete message', 500);
        }
    }

    /**
     * Upload a file for chat
     * POST /api/chat/upload
     */
    public function uploadFile(): void
    {
        AuthMiddleware::handle();

        if (!isset($_FILES['file'])) {
            ResponseHandler::error('No file uploaded', 400);
            return;
        }

        $file = $_FILES['file'];
        $uploadDir = __DIR__ . '/../../uploads/chat/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = 'chat_' . uniqid() . '.' . $extension;
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            ResponseHandler::success([
                'file_path' => 'uploads/chat/' . $fileName,
                'file_name' => $file['name'],
                'file_type' => $file['type']
            ]);
        } else {
            ResponseHandler::error('Failed to move uploaded file', 500);
        }
    }
}
