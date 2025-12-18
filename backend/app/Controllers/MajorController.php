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
    
    /**
     * Create major
     */
    public function create(): void {
        try {
            $this->requireRole('Admin');
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['name', 'faculty_id']);
            
            // Create major using service
            $majorId = $this->facultyService->createMajor($data);
            
            $this->success([
                'status' => 'success',
                'message' => 'Major created successfully',
                'major_id' => $majorId
            ]);
            
        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }
    
    /**
     * Update major
     */
    public function update(): void {
        try {
            $this->requireRole('Admin');
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['major_id']);
            
            $this->facultyService->updateMajor($data);
            
            $this->success([
                'status' => 'success',
                'message' => 'Major updated successfully'
            ]);
            
        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }
    
    /**
     * Delete major
     */
    public function delete(): void {
        try {
            $this->requireRole('Admin');
            
            $data = $this->getJsonInput();
            $this->validateRequired($data, ['major_id']);
            
            $this->facultyService->deleteMajor($data['major_id']);
            
            $this->success([
                'status' => 'success',
                'message' => 'Major deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int)$e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }
}
