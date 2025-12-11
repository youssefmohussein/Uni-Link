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
        // Validate
        $errors = $this->validate($data, [
            'owner_id' => ['required'],
            'name' => ['required', 'min:3'],
            'password' => ['required', 'min:6']
        ]);
        
        if (!empty($errors)) {
            throw new \Exception('Validation failed: ' . json_encode($errors), 400);
        }
        
        // Hash password
        $data['password_hash'] = $this->hashPassword($data['password']);
        unset($data['password']);
        
        $data['created_at'] = date('Y-m-d H:i:s');
        
        // Create room
        $this->roomRepo->beginTransaction();
        
        try {
            $roomId = $this->roomRepo->create($data);
            
            // Add owner as admin
            $this->roomRepo->addMember($roomId, $data['owner_id'], 'Admin');
            
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
}
