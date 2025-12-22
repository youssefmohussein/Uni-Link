<?php
namespace App\Controllers;

use App\Services\UserService;

use App\Repositories\ProfessorRepository;

/**
 * Professor Controller
 * 
 * Handles professor-specific operations
 */
class ProfessorController extends BaseController
{
    private UserService $userService;
    private ProfessorRepository $professorRepo;
    private \App\Services\DashboardService $dashboardService;

    public function __construct(UserService $userService, \App\Services\DashboardService $dashboardService)
    {
        $this->userService = $userService;
        $this->dashboardService = $dashboardService;
        $this->professorRepo = new ProfessorRepository();
    }

    /**
     * Get all professors
     * GET /api/professors
     */
    public function getAll(): void
    {
        try {
            $this->requireAuth();

            // Use repository to get joined data (User + Professor details)
            $professors = $this->professorRepo->getAllWithUserInfo();

            // Force role to uppercase
            $professors = array_map(function ($prof) {
                if (isset($prof['role'])) {
                    $prof['role'] = strtoupper($prof['role']);
                }
                return $prof;
            }, $professors);

            $this->success([
                'count' => count($professors),
                'data' => $professors
            ]);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Get professors by faculty
     * GET /api/professors/by-faculty?faculty_id=1
     */
    public function getByFaculty(): void
    {
        try {
            // $this->requireAuth();

            $facultyId = isset($_GET['faculty_id']) ? (int) $_GET['faculty_id'] : null;

            if (!$facultyId) {
                throw new \Exception('Faculty ID is required', 400);
            }

            $professors = $this->professorRepo->findByFaculty($facultyId);

            $this->success([
                'count' => count($professors),
                'data' => $professors
            ]);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Update professor
     * PUT /api/professors
     */
    public function update(): void
    {
        try {
            $this->requireRole('Admin');

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['user_id']); // Admin form sends user_id/professor_id

            // 1. Update User basic info
            // (UserService handles validation and User table update)
            $userId = (int) $data['user_id'];
            $this->userService->updateUser($userId, $data);

            // 2. Update Professor specific details
            // Check if we have a professor record for this user
            // We might need to find professor_id by user_id if not provided
            $professorId = $data['professor_id'] ?? null;

            if (!$professorId) {
                // Try to find by user_id
                // Assuming ProfessorRepository has findByUserId or use query
                // For now, let's assume one-to-one mapping in DB (user_id is unique in Professors)
                // Or we can get it from the full object
                $prof = $this->professorRepo->findOneBy('user_id', $userId);
                if ($prof) {
                    $professorId = $prof['professor_id'];
                }
            }

            if ($professorId) {
                // Filter fields that belong to Professor table
                $professorFields = ['academic_rank', 'department', 'office_location', 'office_hours'];
                $updateData = array_intersect_key($data, array_flip($professorFields));

                if (!empty($updateData)) {
                    $this->professorRepo->update($professorId, $updateData);
                }
            } else {
                // If professor record missing but role is Professor, create it?
                // Logic for consistency
                $professorFields = ['academic_rank', 'department', 'office_location', 'office_hours'];
                $insertData = array_intersect_key($data, array_flip($professorFields));
                $insertData['user_id'] = $userId;
                $this->professorRepo->create($insertData);
            }

            $this->success(null, 'Professor updated successfully');

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Get professor by ID
     * GET /getProfessorById/3
     */
    public function getById(): void
    {
        try {
            // Parse ID from URL path (e.g. /getProfessorById/3)
            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            if (preg_match('#/getProfessorById/(\d+)#', $path, $matches)) {
                $professorId = (int)$matches[1];
            } else {
                // Fallback for query param ?id=3 (legacy support)
                $professorId = isset($_GET['id']) ? (int)$_GET['id'] : null;
            }

            if (!$professorId) {
                throw new \Exception('Professor ID is required', 400);
            }

            // Since there is no professor_id column, the ID provided IS the user_id
            $professor = $this->professorRepo->getWithUserInfo($professorId);
            
            if (!$professor) {
                throw new \Exception('Professor not found', 404);
            }

            $this->success($professor);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Get dashboard statistics for professors
     * GET /getDashboardStats
     */
    public function getDashboardStats(): void
    {
        try {
            $this->requireAuth();
            $userId = $this->getCurrentUserId();

            // Use DashboardService for global stats
            $globalStats = $this->dashboardService->getStats();
            
            // Get supervised projects count using user_id
            $supervisedProjectsCount = $this->professorRepo->getSupervisedProjectsCount($userId);
            
            // Combine stats
            // We want to return the structure expected by frontend (global stats)
            // But we can also add professor specific stats if needed
            $stats = $globalStats;
            
            // Override or add professor specific counts if the frontend expects them mixed
            // Based on ProfessorPage.jsx: 
            // stats.stats.students, stats.stats.totalUsers are used.
            
            $this->success($stats);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }
}
