<?php
namespace App\Controllers;

use App\Repositories\ProjectRepository;

/**
 * Grading Controller
 * 
 * Handles project grading operations for professors
 */
class GradingController extends BaseController {
    private ProjectRepository $projectRepo;

    public function __construct() {
        parent::__construct();
        $this->projectRepo = new ProjectRepository();
    }

    /**
     * Get all projects for grading
     * GET /api/grading/projects?faculty_id=1&status=all
     */
    public function getProjects(): void {
        try {
            $facultyId = isset($_GET['faculty_id']) ? (int)$_GET['faculty_id'] : null;
            $status = $_GET['status'] ?? 'all';

            // Validate status
            if (!in_array($status, ['all', 'graded', 'not_graded'])) {
                throw new \Exception('Invalid status filter', 400);
            }

            $projects = $this->projectRepo->getProjectsWithGradingStatus($facultyId, $status);

            $this->success($projects);
        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }

    /**
     * Grade a project
     * POST /api/grading/grade
     * Body: { project_id, grade, comments, status }
     */
    public function gradeProject(): void {
        try {
            // Require authentication
            $this->requireAuth();

            $data = $this->getRequestBody();

            // Validate required fields
            if (!isset($data['project_id']) || !isset($data['grade'])) {
                throw new \Exception('Project ID and grade are required', 400);
            }

            $projectId = (int)$data['project_id'];
            $grade = (float)$data['grade'];
            $comments = $data['comments'] ?? null;
            $status = $data['status'] ?? 'APPROVED'; // Default to APPROVED when grading

            // Validate grade range
            if ($grade < 0 || $grade > 100) {
                throw new \Exception('Grade must be between 0 and 100', 400);
            }

            // Validate status
            if (!in_array($status, ['PENDING', 'APPROVED', 'REJECTED'])) {
                throw new \Exception('Invalid status. Must be PENDING, APPROVED, or REJECTED', 400);
            }

            // Update project grade
            $success = $this->projectRepo->updateGrade($projectId, $grade);

            if (!$success) {
                throw new \Exception('Failed to update grade', 500);
            }

            // Update project status
            $this->projectRepo->updateProjectStatus($projectId, $status);

            // If comments provided, add a review
            if ($comments) {
                $userId = $this->getCurrentUserId();
                $this->projectRepo->addReview([
                    'project_id' => $projectId,
                    'professor_id' => $userId,
                    'comment' => $comments,
                    'score' => $grade,
                    'status' => $status
                ]);
            }

            $this->success([
                'message' => 'Project graded successfully',
                'project_id' => $projectId,
                'grade' => $grade,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 400);
        }
    }
}
