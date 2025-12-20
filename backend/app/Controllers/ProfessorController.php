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

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
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
}
