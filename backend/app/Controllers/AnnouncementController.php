<?php
namespace App\Controllers;

use App\Services\AnnouncementService;

/**
 * Announcement Controller
 * 
 * Handles announcement operations
 */
class AnnouncementController extends BaseController {
    private AnnouncementService $announcementService;
    
    public function __construct(AnnouncementService $announcementService) {
        $this->announcementService = $announcementService;
    }
    
    /**
     * Create announcement
     */
    public function create(): void {
        try {
            $this->requireRole('Admin');
            
            $data = $this->getJsonInput();
            $data['author_id'] = $this->getCurrentUserId();
            
            $announcement = $this->announcementService->createAnnouncement($data);
            $this->success($announcement, 'Announcement created successfully', 201);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get all announcements
     */
    public function getAll(): void {
        try {
            $this->requireAuth();
            
            $user = $this->getCurrentUser();
            $announcements = $this->announcementService->getByRole($user['role']);
            
            $this->success([
                'count' => count($announcements),
                'data' => $announcements
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Delete announcement
     */
    public function delete(): void {
        try {
            $this->requireRole('Admin');
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['announcement_id']);
            
            $this->announcementService->deleteAnnouncement((int)$data['announcement_id']);
            $this->success(null, 'Announcement deleted successfully');
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
