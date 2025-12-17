<?php
namespace App\Services;

use App\Repositories\ProjectRoomRepository;

/**
 * Project Room Service
 * 
 * Business logic for project room management
 */
class ProjectRoomService extends BaseService {
    private ProjectRoomRepository $roomRepo;
    
    public function __construct(ProjectRoomRepository $roomRepo) {
        $this->roomRepo = $roomRepo;
    }
    
    /**
     * Create room
     * 
     * @param array $data Room data
     * @return array Created room
     */
    public function createRoom(array $data): array {
        // Handle room_name as name (for compatibility)
        if (isset($data['room_name']) && !isset($data['name'])) {
            $data['name'] = $data['room_name'];
        }
        
        // Validate
        $errors = $this->validate($data, [
            'owner_id' => ['required'],
            'name' => ['required', 'min:3']
        ]);
        
        if (!empty($errors)) {
            throw new \Exception('Validation failed: ' . json_encode($errors), 400);
        }
        
        // Generate password if not provided (for admin-created rooms)
        if (!isset($data['password']) || empty($data['password'])) {
            $data['password'] = bin2hex(random_bytes(16)); // Generate random password
        }
        
        // Hash password
        $data['password_hash'] = $this->hashPassword($data['password']);
        unset($data['password']);
        unset($data['room_name']); // Remove room_name as we use 'name'
        
        $data['created_at'] = date('Y-m-d H:i:s');
        
        // Create room
        $this->roomRepo->beginTransaction();
        
        try {
            $roomId = $this->roomRepo->create($data);
            
            // Add owner as admin (use created_by if owner_id not set)
            $ownerId = $data['owner_id'] ?? $data['created_by'] ?? null;
            if ($ownerId) {
                $this->roomRepo->addMember($roomId, $ownerId, 'Admin');
            }
            
            $this->roomRepo->commit();
            
            return $this->roomRepo->find($roomId);
            
        } catch (\Exception $e) {
            $this->roomRepo->rollback();
            throw $e;
        }
    }
    
    /**
     * Join room
     * 
     * @param int $roomId Room ID
     * @param int $userId User ID
     * @param string $password Room password
     * @return bool Success
     */
    public function joinRoom(int $roomId, int $userId, string $password): bool {
        // Check if already member
        if ($this->roomRepo->isMember($roomId, $userId)) {
            throw new \Exception('Already a member of this room', 400);
        }
        
        // Verify password
        if (!$this->roomRepo->verifyPassword($roomId, $password)) {
            throw new \Exception('Incorrect password', 401);
        }
        
        // Add member
        $this->roomRepo->addMember($roomId, $userId, 'Member');
        
        return true;
    }
    
    /**
     * Get all rooms
     * 
     * @return array Array of all rooms
     */
    public function getAllRooms(): array {
        return $this->roomRepo->findAll(null, 0, 'created_at DESC');
    }
    
    /**
     * Update room
     * 
     * @param int $roomId Room ID
     * @param array $data Updated data
     * @return array Updated room
     */
    public function updateRoom(int $roomId, array $data): array {
        if (!$this->roomRepo->exists($roomId)) {
            throw new \Exception('Room not found', 404);
        }
        
        // Sanitize allowed fields
        $allowedFields = ['name', 'room_name', 'description', 'photo_url'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $updateData[$field] = $data[$field];
            }
        }
        
        // Handle room_name as name (for compatibility)
        if (isset($data['room_name']) && !isset($updateData['name'])) {
            $updateData['name'] = $data['room_name'];
        }
        
        if (!empty($updateData)) {
            $this->roomRepo->update($roomId, $updateData);
        }
        
        return $this->roomRepo->find($roomId);
    }
    
    /**
     * Delete room
     * 
     * @param int $roomId Room ID
     * @return bool Success status
     */
    public function deleteRoom(int $roomId): bool {
        if (!$this->roomRepo->exists($roomId)) {
            throw new \Exception('Room not found', 404);
        }
        
        return $this->transaction(function() use ($roomId) {
            // Delete memberships first (cascade)
            $this->roomRepo->deleteMemberships($roomId);
            
            // Delete room
            return $this->roomRepo->delete($roomId);
        }, $this->roomRepo);
    }
    
    /**
     * Get room by ID
     * 
     * @param int $roomId Room ID
     * @return array Room data
     */
    public function getRoom(int $roomId): array {
        $room = $this->roomRepo->find($roomId);
        
        if (!$room) {
            throw new \Exception('Room not found', 404);
        }
        
        return $room;
    }
}
