<?php
namespace App\Controllers;

use App\Services\ProjectRoomService;

/**
 * Project Room Controller
 * 
 * Handles project room operations
 */
class ProjectRoomController extends BaseController
{
    private ProjectRoomService $roomService;

    public function __construct(ProjectRoomService $roomService)
    {
        $this->roomService = $roomService;
    }

    /**
     * Create room
     */
    public function create(): void
    {
        try {
            $this->requireAuth();

            // Handle both JSON and Multipart/Form-Data
            $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
            if (strpos($contentType, 'application/json') !== false) {
                $data = $this->getJsonInput();
            } else {
                $data = $_POST;
            }

            // Handle File Upload
            if (isset($_FILES['room_photo']) && $_FILES['room_photo']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/room_photos';
                try {
                    $photoPath = $this->handleFileUpload('room_photo', $uploadDir, ['image/jpeg', 'image/png', 'image/webp']);
                    $data['photo_url'] = $photoPath;
                } catch (\Exception $e) {
                    // Log error but continue (optional photo)
                    error_log("Photo upload failed: " . $e->getMessage());
                }
            }

            // Security: Always use the authenticated user ID as the owner_id
            $currentUserId = $this->getCurrentUserId();

            // Log for debugging
            if ($currentUserId) {
                $data['owner_id'] = $currentUserId;
            } else {
                // This shouldn't happen due to requireAuth, but if it does, 
                // we want to catch it early rather than failing with SQL error
                throw new \Exception('No valid user session found for room creation', 401);
            }

            // Handle optional fields that might be empty strings from forms
            // Convert empty strings to NULL to avoid foreign key issues
            if (isset($data['faculty_id']) && $data['faculty_id'] === '') {
                $data['faculty_id'] = null;
            }
            if (isset($data['professor_id']) && $data['professor_id'] === '') {
                $data['professor_id'] = null;
            }

            // Filter only allowed fields for chat_rooms table to avoid SQL errors
            $allowedFields = [
                'name',
                'description',
                'password_hash',
                'owner_id',
                'photo_url',
                'is_private',
                'password',
                'room_name',
                'faculty_id',
                'professor_id'
            ];
            $insertData = array_intersect_key($data, array_flip($allowedFields));

            $room = $this->roomService->createRoom($insertData);
            $this->success($room, 'Room created successfully', 201);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }


    /**
     * Get all rooms
     */
    public function getAll(): void
    {
        try {
            $this->requireAuth();

            $rooms = $this->roomService->getAllRooms();
            $this->success($rooms);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Get user rooms
     */
    public function getUserRooms(): void
    {
        try {
            $this->requireAuth();

            $userId = $this->getCurrentUserId();
            $rooms = $this->roomService->getUserRooms($userId);

            $this->success($rooms);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Get room details
     */
    public function getRoom(): void
    {
        try {
            $this->requireAuth();

            $roomId = isset($_GET['room_id']) ? (int) $_GET['room_id'] : null;

            if (!$roomId) {
                throw new \Exception('Room ID is required', 400);
            }

            $room = $this->roomService->getRoom($roomId);
            $this->success($room);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Update room
     */
    public function update(): void
    {
        try {
            $this->requireAuth();

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['room_id']);

            $room = $this->roomService->updateRoom((int) $data['room_id'], $data);
            $this->success($room, 'Room updated successfully');

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Delete room
     */
    public function delete(): void
    {
        try {
            $this->requireAuth();

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['room_id']);

            $this->roomService->deleteRoom((int) $data['room_id']);
            $this->success(null, 'Room deleted successfully');

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }
}
