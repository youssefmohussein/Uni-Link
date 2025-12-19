<?php
namespace App\Controllers;

use App\Services\ProjectRoomService;

/**
 * Project Room Controller
 * 
 * Handles project room operations
 */
class ProjectRoomController extends BaseController {
    private ProjectRoomService $roomService;
    
    public function __construct(ProjectRoomService $roomService) {
        $this->roomService = $roomService;
    }
    
    /**
     * Create room
     */
    public function create(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            // Use created_by if provided, otherwise use current user
            if (!isset($data['owner_id']) && !isset($data['created_by'])) {
                $data['owner_id'] = $this->getCurrentUserId();
            } elseif (isset($data['created_by'])) {
                $data['owner_id'] = $data['created_by'];
            }
            
            $room = $this->roomService->createRoom($data);
            $this->success($room, 'Room created successfully', 201);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get all rooms
     */
    public function getAll(): void {
        try {
            $this->requireAuth();
            
            $rooms = $this->roomService->getAllRooms();
            $this->success([
                'count' => count($rooms),
                'data' => $rooms
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get user rooms
     */
    public function getUserRooms(): void {
        try {
            $this->requireAuth();
            
            $userId = $this->getCurrentUserId();
            // This would need implementation in service
            
            $this->success([
                'count' => 0,
                'data' => []
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get room details
     */
    public function getRoom(): void {
        try {
            $this->requireAuth();
            
            $roomId = isset($_GET['room_id']) ? (int)$_GET['room_id'] : null;
            
            if (!$roomId) {
                throw new \Exception('Room ID is required', 400);
            }
            
            $room = $this->roomService->getRoom($roomId);
            $this->success($room);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Update room
     */
    public function update(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['room_id']);
            
            $room = $this->roomService->updateRoom((int)$data['room_id'], $data);
            $this->success($room, 'Room updated successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Delete room
     */
    public function delete(): void {
        try {
            $this->requireAuth();
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['room_id']);
            
            $this->roomService->deleteRoom((int)$data['room_id']);
            $this->success(null, 'Room deleted successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
