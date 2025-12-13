<?php
namespace App\Services;

use App\Repositories\AnnouncementRepository;

/**
 * Announcement Service
 * 
 * Business logic for announcement management
 */
class AnnouncementService extends BaseService {
    private AnnouncementRepository $announcementRepo;
    
    public function __construct(AnnouncementRepository $announcementRepo) {
        $this->announcementRepo = $announcementRepo;
    }
    
    /**
     * Create announcement
     * 
     * @param array $data Announcement data
     * @return array Created announcement
     */
    public function createAnnouncement(array $data): array {
        // Validate
        $errors = $this->validate($data, [
            'author_id' => ['required', 'numeric'],
            'title' => ['required', 'min:3'],
            'content' => ['required'],
            'target_role' => ['required', 'in:All,Student,Professor,Admin']
        ]);
        
        if (!empty($errors)) {
            throw new \Exception($this->formatValidationErrors($errors), 400);
        }
        
        // Sanitize
        $sanitized = $this->sanitizeData($data, ['title', 'content']);
        
        $announcementId = $this->announcementRepo->create([
            'author_id' => (int)$data['author_id'],
            'title' => $sanitized['title'],
            'content' => $sanitized['content'],
            'target_role' => $data['target_role'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        return $this->announcementRepo->getWithAuthor($announcementId);
    }
    
    /**
     * Get recent announcements
     * 
     * @param int $limit Number of announcements
     * @return array Array of announcements
     */
    public function getRecentAnnouncements(int $limit = 10): array {
        return $this->announcementRepo->findRecent($limit);
    }
    
    /**
     * Get announcements by role
     * 
     * @param string $role User role
     * @return array Array of announcements
     */
    public function getByRole(string $role): array {
        return $this->announcementRepo->findByRole($role);
    }
    
    /**
     * Delete announcement
     * 
     * @param int $announcementId Announcement ID
     * @return bool Success status
     */
    public function deleteAnnouncement(int $announcementId): bool {
        if (!$this->announcementRepo->exists($announcementId)) {
            throw new \Exception('Announcement not found', 404);
        }
        
        return $this->announcementRepo->delete($announcementId);
    }
}
