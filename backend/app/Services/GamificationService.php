<?php
namespace App\Services;

use App\Repositories\StudentRepository;

/**
 * Gamification Service
 * 
 * Handles awarding points and gamification logic
 */
class GamificationService extends BaseService
{
    private StudentRepository $studentRepo;

    // Point Values
    const POINTS_POST_CREATE = 10;
    const POINTS_COMMENT_CREATE = 5;
    const POINTS_LIKE_RECEIVED = 2;

    public function __construct(StudentRepository $studentRepo)
    {
        $this->studentRepo = $studentRepo;
    }

    /**
     * Award points to a user for an action
     * 
     * @param int $userId The user to award points to
     * @param int $points The amount of points to award
     * @param string $actionType Description of the action (for logging/debugging)
     * @return bool Success status
     */
    public function awardPoints(int $userId, int $points, string $actionType): bool
    {
        // Get student ID from user ID
        // Note: We need to find the student record associated with this user
        // The studentRepo updatePoints method expects a student_id, but usually we work with user_id
        // Let's modify this to handle finding the student_id first.
        
        // This query is needed because points are on the student table, not users table
        $db = $this->studentRepo->getDb();
        $stmt = $db->prepare("SELECT student_id FROM students WHERE user_id = ?");
        $stmt->execute([$userId]);
        $student = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($student) {
            $studentId = $student['student_id'];
            $this->studentRepo->updatePoints($studentId, $points);
            error_log("Gamification: Awarded $points points to User $userId (Student $studentId) for $actionType");
            return true;
        } else {
            // User might be a professor or admin, or student record missing
            error_log("Gamification Warning: Could not find student record for User $userId to award points.");
            return false;
        }
    }
}
