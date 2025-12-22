<?php
namespace App\Services;

use App\Repositories\FacultyRepository;
use App\Repositories\MajorRepository;

/**
 * Faculty Service
 * 
 * Business logic for faculty and major management
 */
class FacultyService extends BaseService
{
    private FacultyRepository $facultyRepo;
    private MajorRepository $majorRepo;

    public function __construct(FacultyRepository $facultyRepo, MajorRepository $majorRepo)
    {
        $this->facultyRepo = $facultyRepo;
        $this->majorRepo = $majorRepo;
    }

    /**
     * Get all faculties
     * 
     * @return array Array of faculties
     */
    public function getAllFaculties(): array
    {
        return $this->facultyRepo->getAllWithDetails();
    }

    /**
     * Get faculty by ID
     * 
     * @param int $id Faculty ID
     * @return array|null Faculty data
     */
    public function getFacultyById(int $id): ?array
    {
        return $this->facultyRepo->findByIdWithDetails($id);
    }

    /**
     * Get majors by faculty
     * 
     * @param int $facultyId Faculty ID
     * @return array Array of majors
     */
    public function getMajorsByFaculty(int $facultyId): array
    {
        return $this->majorRepo->findByFaculty($facultyId);
    }

    /**
     * Get all majors with faculty information
     * 
     * @return array Array of majors
     */
    public function getAllMajors(): array
    {
        return $this->majorRepo->getAllWithFaculty();
    }

    /**
     * Find faculty by name
     * 
     * @param string $name Faculty name
     * @return array|null Faculty data
     */
    public function findFacultyByName(string $name): ?array
    {
        return $this->facultyRepo->findByName($name);
    }

    /**
     * Find major by name
     * 
     * @param string $name Major name
     * @return array|null Major data
     */
    public function findMajorByName(string $name): ?array
    {
        return $this->majorRepo->findByName($name);
    }

    /**
     * Create a new faculty
     * 
     * @param array $data Faculty data
     * @return int Created faculty ID
     */
    public function createFaculty(array $data): int
    {
        // Check if faculty already exists
        $existing = $this->facultyRepo->findByName($data['name']);
        if ($existing) {
            throw new \Exception('Faculty with this name already exists', 409);
        }

        return $this->facultyRepo->create($data);
    }

    /**
     * Update a faculty
     * 
     * @param array $data Faculty data with faculty_id
     * @return bool Success status
     */
    public function updateFaculty(array $data): bool
    {
        if (!isset($data['faculty_id'])) {
            throw new \Exception('Faculty ID is required', 400);
        }

        // If name is being changed, check for duplicates
        if (isset($data['name'])) {
            $existing = $this->facultyRepo->findByName($data['name']);
            if ($existing && $existing['faculty_id'] != $data['faculty_id']) {
                throw new \Exception('Faculty with this name already exists', 409);
            }
        }

        return $this->facultyRepo->update($data['faculty_id'], $data);
    }

    /**
     * Delete a faculty
     * 
     * @param int $facultyId Faculty ID
     * @return bool Success status
     */
    public function deleteFaculty(int $facultyId): bool
    {
        // Check if faculty has associated majors and delete them (Cascade)
        $majors = $this->majorRepo->findByFaculty($facultyId);
        if (!empty($majors)) {
            foreach ($majors as $major) {
                $this->deleteMajor($major['major_id']);
            }
        }

        // Unlink users from this faculty (set faculty_id to NULL)
        // We use raw query here as we don't have userRepo injected, but we have database access via BaseRepository
        // Actually BaseService doesn't expose database directly, but we can use Database singleton or inject UserRepo.
        // For simplicity and robustness, we'll try to use Database singleton.
        $db = \App\Utils\Database::getInstance()->getConnection();
        $db->prepare("UPDATE users SET faculty_id = NULL WHERE faculty_id = ?")->execute([$facultyId]);

        return $this->facultyRepo->delete($facultyId);
    }

    /**
     * Create a new major
     * 
     * @param array $data Major data
     * @return int Created major ID
     */
    public function createMajor(array $data): int
    {
        // Check if major already exists in this faculty
        $existing = $this->majorRepo->findByName($data['name']);
        if ($existing && $existing['faculty_id'] == $data['faculty_id']) {
            throw new \Exception('Major with this name already exists in this faculty', 409);
        }

        return $this->majorRepo->create($data);
    }

    /**
     * Update a major
     * 
     * @param array $data Major data with major_id
     * @return bool Success status
     */
    public function updateMajor(array $data): bool
    {
        if (!isset($data['major_id'])) {
            throw new \Exception('Major ID is required', 400);
        }

        // If name is being changed, check for duplicates in the same faculty
        if (isset($data['name']) && isset($data['faculty_id'])) {
            $existing = $this->majorRepo->findByName($data['name']);
            if ($existing && $existing['major_id'] != $data['major_id'] && $existing['faculty_id'] == $data['faculty_id']) {
                throw new \Exception('Major with this name already exists in this faculty', 409);
            }
        }

        return $this->majorRepo->update($data['major_id'], $data);
    }

    /**
     * Delete a major
     * 
     * @param int $majorId Major ID
     * @return bool Success status
     */
    public function deleteMajor(int $majorId): bool
    {
        // Unlink users from this major
        $db = \App\Utils\Database::getInstance()->getConnection();
        $db->prepare("UPDATE users SET major_id = NULL WHERE major_id = ?")->execute([$majorId]);

        return $this->majorRepo->delete($majorId);
    }

    /**
     * Get faculty by name with all details
     * 
     * @param string $name Faculty name
     * @return array|null Faculty data or null
     */
    public function getFacultyByName(string $name): ?array
    {
        return $this->facultyRepo->findByNameWithDetails($name);
    }

    /**
     * Get faculty by fuzzy name with all details
     * 
     * @param string $name Part of faculty name
     * @return array|null Faculty data or null
     */
    public function getFacultyByNameFuzzy(string $name): ?array
    {
        return $this->facultyRepo->findByNameFuzzy($name);
    }
}
