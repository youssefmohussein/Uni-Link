<?php
namespace App\Commands;

use App\Repositories\ProjectRepository;
use App\Services\NotificationService;

/**
 * RejectProjectCommand
 * 
 * Command Pattern Implementation
 * Rejects a project and notifies the student
 * Part of the Project Domain (UML Design)
 */
class RejectProjectCommand implements ProjectCommand {
    private ProjectRepository $projectRepo;
    private NotificationService $notificationService;
    private int $projectId;
    private int $professorId;
    private ?string $comment;
    
    public function __construct(
        ProjectRepository $projectRepo,
        NotificationService $notificationService,
        int $projectId,
        int $professorId,
        ?string $comment = null
    ) {
        $this->projectRepo = $projectRepo;
        $this->notificationService = $notificationService;
        $this->projectId = $projectId;
        $this->professorId = $professorId;
        $this->comment = $comment;
    }
    
    public function execute(): bool {
        try {
            // Get project details
            $project = $this->projectRepo->findById($this->projectId);
            if (!$project) {
                return false;
            }
            
            // Update project status
            $this->projectRepo->update($this->projectId, [
                'status' => 'REJECTED',
                'supervisor_id' => $this->professorId
            ]);
            
            // Create review record
            $this->projectRepo->createReview([
                'project_id' => $this->projectId,
                'professor_id' => $this->professorId,
                'status' => 'REJECTED',
                'comment' => $this->comment
            ]);
            
            // Notify observers (triggers notification creation)
            $this->notificationService->notifyAll('PROJECT_REJECTED', [
                'project_id' => $this->projectId,
                'student_id' => $project['student_id'],
                'professor_id' => $this->professorId
            ]);
            
            return true;
        } catch (\Exception $e) {
            error_log("RejectProjectCommand failed: " . $e->getMessage());
            return false;
        }
    }
}
