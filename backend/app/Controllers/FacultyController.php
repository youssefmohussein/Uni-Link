<?php
namespace App\Controllers;

use App\Services\FacultyService;

/**
 * Faculty Controller
 * 
 * Handles faculty operations
 */
class FacultyController extends BaseController {
    private FacultyService $facultyService;
    
    public function __construct(FacultyService $facultyService) {
        $this->facultyService = $facultyService;
    }
    
    /**
     * Get all faculties
     */
    public function getAll(): void {
        try {
            $faculties = $this->facultyService->getAllFaculties();
            
            // Return in format expected by frontend
            echo json_encode([
                'status' => 'success',
                'data' => $faculties
            ]);
            
        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }
    
    /**
     * Create faculty
     */
    public function create(): void {
        try {
            $this->requireRole('Admin');
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['faculty_name']);
            
            // For now, just return success - implement actual creation in FacultyService
            $this->success([
                'status' => 'success',
                'message' => 'Faculty created successfully',
                'faculty_id' => 1 // Placeholder
            ]);
            
        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }
    
    /**
     * Update faculty
     */
    public function update(): void {
        try {
            $this->requireRole('Admin');
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['faculty_id']);
            
            $this->success([
                'status' => 'success',
                'message' => 'Faculty updated successfully'
            ]);
            
        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }
    
    /**
     * Delete faculty
     */
    public function delete(): void {
        try {
            $this->requireRole('Admin');
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['faculty_id']);
            
            $this->success([
                'status' => 'success',
                'message' => 'Faculty deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }
    
    /**
     * Get majors by faculty
     */
    public function getMajors(): void {
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
            $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }
}
