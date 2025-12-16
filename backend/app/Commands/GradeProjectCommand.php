<?php
namespace App\Commands;

use App\Repositories\ProjectRepository;
use App\Services\NotificationService;

/**
 * GradeProjectCommand
 * 
 * Command Pattern Implementation
 * Grades a project and notifies the student
 * Part of the Project Domain (UML Design)
 */
class GradeProjectCommand implements ProjectCommand {
    private ProjectRepository $projectRepo;
    private NotificationService $notificationService;
    private int $projectId;
    private int $professorId;
    private float $grade;
    private ?string $comment;
    
    public function __construct(
        ProjectRepository $projectRepo,
        NotificationService $notificationService,
        int $projectId,
        int $professorId,
        float $grade,
        ?string $comment = null
    ) {
        $this->projectRepo = $projectRepo;
        $this->notificationService = $notificationService;
        $this->projectId = $projectId;
        $this->professorId = $professorId;
        $this->grade = $grade;
        $this->comment = $comment;
    }
    
    public function execute(): bool {
        try {
            // Get project details
            $project = $this->projectRepo->findById($this->projectId);
            if (!$project) {
                return false;
            }
            
            // Update project grade
            $this->projectRepo->update($this->projectId, [
                'grade' => $this->grade,
                'supervisor_id' => $this->professorId
            ]);
            
            // Create or update review record
            $this->projectRepo->createReview([
                'project_id' => $this->projectId,
                'professor_id' => $this->professorId,
                'status' => $project['status'] ?? 'PENDING',
                'score' => $this->grade,
                'comment' => $this->comment
            ]);
            
            // Notify observers (triggers notification creation)
            $this->notificationService->notifyAll('PROJECT_GRADED', [
                'project_id' => $this->projectId,
                'student_id' => $project['student_id'],
                'professor_id' => $this->professorId,
                'grade' => $this->grade
            ]);
            
            return true;
        } catch (\Exception $e) {
            error_log("GradeProjectCommand failed: " . $e->getMessage());
            return false;
        }
    }
}
