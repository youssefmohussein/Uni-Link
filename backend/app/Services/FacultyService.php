<?php
namespace App\Services;

use App\Repositories\FacultyRepository;
use App\Repositories\MajorRepository;

/**
 * Faculty Service
 * 
 * Business logic for faculty and major management
 */
class FacultyService extends BaseService {
    private FacultyRepository $facultyRepo;
    private MajorRepository $majorRepo;
    
    public function __construct(FacultyRepository $facultyRepo, MajorRepository $majorRepo) {
        $this->facultyRepo = $facultyRepo;
        $this->majorRepo = $majorRepo;
    }
    
    /**
     * Get all faculties
     * 
     * @return array Array of faculties
     */
    public function getAllFaculties(): array {
        return $this->facultyRepo->getAllWithMajorCount();
    }
    
    /**
     * Get majors by faculty
     * 
     * @param int $facultyId Faculty ID
     * @return array Array of majors
     */
    public function getMajorsByFaculty(int $facultyId): array {
        return $this->majorRepo->findByFaculty($facultyId);
    }
    
    /**
     * Get all majors with faculty information
     * 
     * @return array Array of majors
     */
    public function getAllMajors(): array {
        return $this->majorRepo->getAllWithFaculty();
    }
    
    /**
     * Find faculty by name
     * 
     * @param string $name Faculty name
     * @return array|null Faculty data
     */
    public function findFacultyByName(string $name): ?array {
        return $this->facultyRepo->findByName($name);
    }
    
    /**
     * Find major by name
     * 
     * @param string $name Major name
     * @return array|null Major data
     */
    public function findMajorByName(string $name): ?array {
        return $this->majorRepo->findByName($name);
    }
}
