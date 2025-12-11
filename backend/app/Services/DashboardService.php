<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\PostRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\StudentRepository;

/**
 * Dashboard Service
 * 
 * Business logic for dashboard statistics
 */
class DashboardService extends BaseService {
    private UserRepository $userRepo;
    private PostRepository $postRepo;
    private ProjectRepository $projectRepo;
    private StudentRepository $studentRepo;
    
    public function __construct(
        UserRepository $userRepo,
        PostRepository $postRepo,
        ProjectRepository $projectRepo,
        StudentRepository $studentRepo
    ) {
        $this->userRepo = $userRepo;
        $this->postRepo = $postRepo;
        $this->projectRepo = $projectRepo;
        $this->studentRepo = $studentRepo;
    }
    
    /**
     * Get dashboard statistics
     * 
     * @return array Dashboard stats
     */
    public function getStats(): array {
        return [
            'users' => [
                'total' => $this->userRepo->count(),
                'students' => $this->userRepo->count(['role' => 'Student']),
                'professors' => $this->userRepo->count(['role' => 'Professor']),
                'admins' => $this->userRepo->count(['role' => 'Admin'])
            ],
            'posts' => [
                'total' => $this->postRepo->count(),
                'recent' => $this->getRecentPostsCount()
            ],
            'projects' => [
                'total' => $this->projectRepo->count(),
                'pending' => $this->projectRepo->count(['status' => 'Pending']),
                'graded' => $this->projectRepo->count(['status' => 'Graded'])
            ],
            'topStudents' => $this->studentRepo->getTopByPoints(5)
        ];
    }
    
    /**
     * Get count of posts from last 7 days
     * 
     * @return int Recent posts count
     */
    private function getRecentPostsCount(): int {
        $sql = "SELECT COUNT(*) as count FROM Post WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $result = $this->postRepo->queryOne($sql);
        return (int)($result['count'] ?? 0);
    }
    
    /**
     * Get user-specific dashboard stats
     * 
     * @param int $userId User ID
     * @param string $role User role
     * @return array User dashboard stats
     */
    public function getUserStats(int $userId, string $role): array {
        $stats = [
            'posts' => $this->postRepo->count(['user_id' => $userId])
        ];
        
        if ($role === 'Student') {
            $stats['projects'] = $this->projectRepo->count(['student_id' => $userId]);
            $student = $this->studentRepo->find($userId);
            $stats['points'] = $student['points'] ?? 0;
            $stats['gpa'] = $student['gpa'] ?? 0;
        } elseif ($role === 'Professor') {
            $stats['supervisedProjects'] = $this->projectRepo->count(['supervisor_id' => $userId]);
        }
        
        return $stats;
    }
}
