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
            $data['owner_id'] = $this->getCurrentUserId();
            
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
            
            // This would need implementation in service
            $this->success(null);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
