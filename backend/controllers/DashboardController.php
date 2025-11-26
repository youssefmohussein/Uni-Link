<?php

require_once __DIR__ . '/../utils/DbConnection.php';

class DashboardController
{

    /**
     * Get aggregated dashboard statistics
     * Returns user counts, faculty distribution, and activity metrics
     */
    public static function getDashboardStats()
    {
        global $pdo;

        try {
            // Get user counts by role
            $stmt = $pdo->query("
                SELECT role, COUNT(*) as count 
                FROM Users 
                GROUP BY role
            ");
            $roleCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Initialize counts
            $stats = [
                'students' => 0,
                'professors' => 0,
                'admins' => 0,
                'totalUsers' => 0
            ];

            // Map role counts
            foreach ($roleCounts as $row) {
                $stats['totalUsers'] += $row['count'];

                switch ($row['role']) {
                    case 'Student':
                        $stats['students'] = (int) $row['count'];
                        break;
                    case 'Professor':
                        $stats['professors'] = (int) $row['count'];
                        break;
                    case 'Admin':
                        $stats['admins'] = (int) $row['count'];
                        break;
                }
            }

            // Get faculty-wise student distribution
            $stmt = $pdo->query("
                SELECT f.faculty_name, COUNT(u.user_id) as student_count
                FROM Faculty f
                LEFT JOIN Users u ON f.faculty_id = u.faculty_id AND u.role = 'Student'
                GROUP BY f.faculty_id, f.faculty_name
                ORDER BY student_count DESC
            ");
            $facultyDistribution = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get user status distribution (mock data for now - add status column later if needed)
            $userStatus = [
                'active' => (int) ($stats['totalUsers'] * 0.72),
                'idle' => (int) ($stats['totalUsers'] * 0.20),
                'suspended' => (int) ($stats['totalUsers'] * 0.08)
            ];

            // Get weekly activity (mock data - can be enhanced later with actual login tracking)
            $weeklyActivity = [
                ['day' => 'Mon', 'count' => (int) ($stats['totalUsers'] * 0.24)],
                ['day' => 'Tue', 'count' => (int) ($stats['totalUsers'] * 0.31)],
                ['day' => 'Wed', 'count' => (int) ($stats['totalUsers'] * 0.28)],
                ['day' => 'Thu', 'count' => (int) ($stats['totalUsers'] * 0.34)],
                ['day' => 'Fri', 'count' => (int) ($stats['totalUsers'] * 0.39)]
            ];

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'stats' => $stats,
                    'facultyDistribution' => $facultyDistribution,
                    'userStatus' => $userStatus,
                    'weeklyActivity' => $weeklyActivity
                ]
            ]);

        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
}
?>