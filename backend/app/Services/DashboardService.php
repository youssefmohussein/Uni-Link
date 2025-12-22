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
class DashboardService extends BaseService
{
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
     * @param int|null $facultyId Optional faculty ID to filter stats
     * @return array Dashboard statistics
     */
    public function getStats(?int $facultyId = null): array
    {
        // Get total counts
        $totalUsers = count($this->userRepo->findAll());
        $students = count($this->userRepo->findByRole('Student'));
        $professors = count($this->userRepo->findByRole('Professor'));
        $admins = count($this->userRepo->findByRole('Admin'));

        return [
            'stats' => [
                'totalUsers' => $totalUsers,
                'students' => $students,
                'professors' => $professors,
                'admins' => $admins,
                'activeProjects' => 12 // Placeholder
            ],
            'weeklyActivity' => $this->getWeeklyActivity(),
            'topSkills' => $this->getTopSkills($facultyId),
            'projectGradeDistribution' => $this->getProjectGradeDistribution($facultyId),
            'facultyDistribution' => $this->getFacultyDistribution($facultyId),
            'majorDistribution' => $this->getMajorDistribution($facultyId),
            'userStatus' => [
                'active' => $totalUsers - 5, // Placeholder - would need actual status tracking
                'idle' => 3,
                'suspended' => 2
            ]
        ];
    }

    /**
     * Get weekly activity data
     * 
     * @return array Weekly activity
     */
    private function getWeeklyActivity(): array
    {
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $activity = [];

        // Get user registrations per day for last 7 days
        $results = $this->userRepo->getWeeklyActivity();
        $activityMap = [];
        foreach ($results as $row) {
            $dayName = date('D', strtotime($row['date']));
            $activityMap[$dayName] = (int) $row['count'];
        }

        // Fill in missing days with 0
        foreach ($days as $day) {
            $activity[] = [
                'day' => $day,
                'count' => $activityMap[$day] ?? 0
            ];
        }

        return $activity;
    }

    /**
     * Get top skills in faculty
     * 
     * @param int|null $facultyId Optional faculty ID
     * @return array Top skills
     */
    private function getTopSkills(?int $facultyId = null): array
    {
        $results = $this->projectRepo->getTopSkillsByFaculty($facultyId);
        return array_map(function ($row) {
            return [
                'name' => $row['skill_name'],
                'count' => (int) $row['project_count']
            ];
        }, $results);
    }

    /**
     * Get faculty distribution
     * 
     * @param int|null $facultyId Optional faculty ID
     * @return array Faculty distribution
     */
    private function getFacultyDistribution(?int $facultyId = null): array
    {
        $results = $this->userRepo->getFacultyDistribution($facultyId);
        return array_map(function ($row) {
            return [
                'faculty_name' => $row['faculty_name'] ?? 'Unknown',
                'student_count' => (int) ($row['student_count'] ?? 0)
            ];
        }, $results);
    }

    /**
     * Get major distribution
     * 
     * @param int|null $facultyId Optional faculty ID
     * @return array Major distribution
     */
    private function getMajorDistribution(?int $facultyId = null): array
    {
        $results = $this->userRepo->getStudentDistributionByMajor($facultyId);
        return array_map(function ($row) {
            return [
                'faculty_name' => $row['faculty_name'] ?? 'Unknown',
                'major_name' => $row['major_name'] ?? 'Unknown',
                'student_count' => (int) ($row['student_count'] ?? 0)
            ];
        }, $results);
    }

    /**
     * Get count of posts from last 7 days
     * 
     * @return int Recent posts count
     */
    private function getRecentPostsCount(): int
    {
        return $this->postRepo->getRecentCount();
    }

    /**
     * Get project grade distribution
     * 
     * @param int|null $facultyId Optional faculty ID
     * @return array Grade distribution
     */
    private function getProjectGradeDistribution(?int $facultyId = null): array
    {
        $results = $this->projectRepo->getProjectGradeDistribution($facultyId);
        return array_map(function ($row) {
            return [
                'grade_range' => $row['grade_range'] ?? 'Unknown',
                'project_count' => (int) ($row['project_count'] ?? 0)
            ];
        }, $results);
    }

    /**
     * Get user-specific dashboard stats
     * 
     * @param int $userId User ID
     * @param string $role User role
     * @return array User dashboard stats
     */
    public function getUserStats(int $userId, string $role): array
    {
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
