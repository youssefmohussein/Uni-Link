<?php
namespace App\Services;

use App\Repositories\SubjectRepository;

/**
 * Subject Service
 * 
 * Business logic for subject management
 */
class SubjectService extends BaseService {
    private SubjectRepository $subjectRepo;
    
    public function __construct(SubjectRepository $subjectRepo) {
        $this->subjectRepo = $subjectRepo;
    }
    
    /**
     * Get student registered subjects
     * 
     * @param int $userId Student User ID
     * @return array List of subjects
     */
    public function getStudentSubjects(int $userId): array {
        // STATIC SUBJECTS FOR TESTING (Requested by User)
        return [
            [
                'subject_id' => 1,
                'name' => 'Software Engineering',
                'code' => 'CS301',
                'faculty_id' => 1
            ],
            [
                'subject_id' => 2,
                'name' => 'Database Systems',
                'code' => 'CS302',
                'faculty_id' => 1
            ],
            [
                'subject_id' => 3,
                'name' => 'Web Development',
                'code' => 'CS303',
                'faculty_id' => 1
            ],
            [
                'subject_id' => 4,
                'name' => 'Mobile Applications',
                'code' => 'CS304',
                'faculty_id' => 1
            ],
            [
                'subject_id' => 5,
                'name' => 'Artificial Intelligence',
                'code' => 'CS305',
                'faculty_id' => 1
            ]
        ];
        // Original logic:
        // return $this->subjectRepo->findStudentSubjects($userId);
    }
    
    /**
     * Get faculty subjects
     * 
     * @param int $facultyId
     * @return array
     */
    public function getFacultySubjects(int $facultyId): array {
        return $this->subjectRepo->findByFaculty($facultyId);
    }

    /**
     * Register student to subject (Helper if needed)
     */
    public function registerStudentToSubject(int $userId, int $subjectId): bool {
        return $this->subjectRepo->registerStudent($userId, $subjectId);
    }
}
