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
        // Get student record from user ID
        $student = $this->studentRepo->findOneBy('user_id', $userId);

        if ($student) {
            // Use user_id as studentId since they are the same in this schema
            $this->studentRepo->updatePoints($userId, $points);
            error_log("Gamification: Awarded $points points to User $userId for $actionType");
            return true;
        } else {
            // User might be a professor or admin, or student record missing
            error_log("Gamification Warning: Could not find student record for User $userId to award points.");
            return false;
        }
    }
}
