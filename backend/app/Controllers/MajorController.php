<?php
namespace App\Controllers;

use App\Services\FacultyService;

/**
 * Major Controller
 * 
 * Handles major operations
 */
class MajorController extends BaseController {
    private FacultyService $facultyService;
    
    public function __construct(FacultyService $facultyService) {
        $this->facultyService = $facultyService;
    }
    
    /**
     * Get all majors
     */
    public function getAll(): void {
        try {
            $majors = $this->facultyService->getAllMajors();
            $this->success([
                'count' => count($majors),
                'data' => $majors
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
    
    /**
     * Get majors by faculty
     */
    public function getByFaculty(): void {
        try {
            $facultyId = isset($_GET['faculty_id']) ? (int)$_GET['faculty_id'] : null;
            
            if (!$facultyId) {
                throw new \Exception('Faculty ID is required', 400);
            }
            
            $majors = $this->facultyService->getMajorsByFaculty($facultyId);
            $this->success([
                'count' => count($majors),
                'data' => $majors
            ]);
            
        } catch (\Exception $e) {
            $this->error($e->getMessage(), $e->getCode() ?: 400);
        }
    }
}
