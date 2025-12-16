<?php
namespace App\Observers;

use App\Repositories\NotificationRepository;

/**
 * ProjectNotificationObserver
 * 
 * Observer Pattern Implementation
 * Handles project-related notifications (reviews, approvals, grades)
 * Part of the Notification System (UML Design)
 */
class ProjectNotificationObserver implements NotificationObserver {
    private NotificationRepository $notificationRepo;
    
    public function __construct(NotificationRepository $notificationRepo) {
        $this->notificationRepo = $notificationRepo;
    }
    
    /**
     * Handle project-related events and create notifications
     */
    public function update(string $eventType, array $payload): void {
        switch ($eventType) {
            case 'PROJECT_REVIEWED':
                $this->handleProjectReview($payload);
                break;
                
            case 'PROJECT_APPROVED':
                $this->handleProjectApproval($payload);
                break;
                
            case 'PROJECT_REJECTED':
                $this->handleProjectRejection($payload);
                break;
                
            case 'PROJECT_GRADED':
                $this->handleProjectGrade($payload);
                break;
        }
    }
    
    private function handleProjectReview(array $payload): void {
        $this->notificationRepo->create([
            'user_id' => $payload['student_id'],
            'type' => 'PROJECT_REVIEW',
            'title' => 'Project Reviewed',
            'message' => 'Your project has been reviewed by a professor',
            'related_entity_type' => 'PROJECT',
            'related_entity_id' => $payload['project_id']
        ]);
    }
    
    private function handleProjectApproval(array $payload): void {
        $message = 'Your project has been approved';
        if (isset($payload['score'])) {
            $message .= " with a score of {$payload['score']}";
        }
        
        $this->notificationRepo->create([
            'user_id' => $payload['student_id'],
            'type' => 'PROJECT_APPROVED',
            'title' => 'Project Approved',
            'message' => $message,
            'related_entity_type' => 'PROJECT',
            'related_entity_id' => $payload['project_id']
        ]);
    }
    
    private function handleProjectRejection(array $payload): void {
        $this->notificationRepo->create([
            'user_id' => $payload['student_id'],
            'type' => 'PROJECT_REJECTED',
            'title' => 'Project Rejected',
            'message' => 'Your project has been rejected. Please review the feedback.',
            'related_entity_type' => 'PROJECT',
            'related_entity_id' => $payload['project_id']
        ]);
    }
    
    private function handleProjectGrade(array $payload): void {
        $this->notificationRepo->create([
            'user_id' => $payload['student_id'],
            'type' => 'PROJECT_GRADED',
            'title' => 'Project Graded',
            'message' => "Your project has been graded: {$payload['grade']}",
            'related_entity_type' => 'PROJECT',
            'related_entity_id' => $payload['project_id']
        ]);
    }
}
