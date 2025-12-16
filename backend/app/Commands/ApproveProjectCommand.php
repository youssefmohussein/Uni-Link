<?php
namespace App\Commands;

use App\Repositories\ProjectRepository;
use App\Services\NotificationService;

/**
 * ApproveProjectCommand
 * 
 * Command Pattern Implementation
 * Approves a project and notifies the student
 * Part of the Project Domain (UML Design)
 */
class ApproveProjectCommand implements ProjectCommand {
    private ProjectRepository $projectRepo;
    private NotificationService $notificationService;
    private int $projectId;
    private int $professorId;
    private ?float $score;
    private ?string $comment;
    
    public function __construct(
        ProjectRepository $projectRepo,
        NotificationService $notificationService,
        int $projectId,
        int $professorId,
        ?float $score = null,
        ?string $comment = null
    ) {
        $this->projectRepo = $projectRepo;
        $this->notificationService = $notificationService;
        $this->projectId = $projectId;
        $this->professorId = $professorId;
        $this->score = $score;
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
                'status' => 'APPROVED',
                'supervisor_id' => $this->professorId
            ]);
            
            // Create review record
            $this->projectRepo->createReview([
                'project_id' => $this->projectId,
                'professor_id' => $this->professorId,
                'status' => 'APPROVED',
                'score' => $this->score,
                'comment' => $this->comment
            ]);
            
            // Notify observers (triggers notification creation)
            $this->notificationService->notifyAll('PROJECT_APPROVED', [
                'project_id' => $this->projectId,
                'student_id' => $project['student_id'],
                'professor_id' => $this->professorId,
                'score' => $this->score
            ]);
            
            return true;
        } catch (\Exception $e) {
            error_log("ApproveProjectCommand failed: " . $e->getMessage());
            return false;
        }
    }
}
