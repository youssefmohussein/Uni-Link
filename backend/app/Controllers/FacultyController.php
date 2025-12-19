<?php
namespace App\Controllers;

use App\Services\FacultyService;
use App\Repositories\MajorRepository;

/**
 * Faculty Controller
 * 
 * Handles faculty operations
 */
class FacultyController extends BaseController
{
    private FacultyService $facultyService;
    private MajorRepository $majorRepo;

    public function __construct(FacultyService $facultyService, MajorRepository $majorRepo)
    {
        $this->facultyService = $facultyService;
        $this->majorRepo = $majorRepo;
    }

    /**
     * Get all faculties
     */
    public function getAll(): void
    {
        header('X-UniLink-Debug: controller-is-running');
        try {
            $faculties = $this->facultyService->getAllFaculties();

            // Ensure keys are lowercase for frontend consistency
            $faculties = array_map(function ($f) {
                return array_change_key_case($f, CASE_LOWER);
            }, $faculties);

            $this->success($faculties);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Get faculty by ID
     */
    public function getById(): void
    {
        try {
            $id = isset($_GET['id']) ? (int) $_GET['id'] : null;

            if (!$id) {
                throw new \Exception('Faculty ID is required', 400);
            }

            $faculty = $this->facultyService->getFacultyById($id);

            if (!$faculty) {
                throw new \Exception('Faculty not found', 404);
            }

            // Ensure keys are lowercase for frontend consistency
            $faculty = array_change_key_case($faculty, CASE_LOWER);

            // Add debug info
            $faculty['_debug_id_received'] = $id;
            $faculty['_debug_timestamp'] = time();

            $this->success($faculty);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Create faculty
     */
    public function create(): void
    {
        try {
            $this->requireRole('Admin');

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['name']);

            // Create faculty using service
            $facultyId = $this->facultyService->createFaculty($data);

            $this->success([
                'faculty_id' => $facultyId
            ], 'Faculty created successfully');

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Update faculty
     */
    public function update(): void
    {
        try {
            $this->requireRole('Admin');

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['faculty_id']);

            $this->facultyService->updateFaculty($data);

            $this->success(null, 'Faculty updated successfully');

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Delete faculty
     */
    public function delete(): void
    {
        try {
            $this->requireRole('Admin');

            $data = $this->getJsonInput();
            $this->validateRequired($data, ['faculty_id']);

            $this->facultyService->deleteFaculty($data['faculty_id']);

            $this->success(null, 'Faculty deleted successfully');

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Get majors by faculty
     */
    public function getMajors(): void
    {
        try {
            $facultyId = isset($_GET['faculty_id']) ? (int) $_GET['faculty_id'] : null;

            if (!$facultyId) {
                throw new \Exception('Faculty ID is required', 400);
            }

            $majors = $this->facultyService->getMajorsByFaculty($facultyId);
            $this->success([
                'count' => count($majors),
                'data' => $majors
            ]);

        } catch (\Exception $e) {
            $code = is_numeric($e->getCode()) ? (int) $e->getCode() : 500;
            $this->error($e->getMessage(), $code ?: 500);
        }
    }

    /**
     * Seed faculty data (Temporary for deployment/fixes)
     */
    public function seedData(): void
    {
        try {
            $faculties = [
                'Faculty of Computer Science' => [
                    'description' => "Overview\n\nThis faculty focuses on how computers, software, and data are designed, built, and used to solve real-world problems. It blends logical thinking, mathematics, and creativity to create digital solutions.\n\nWhat You Study\n\nYou study programming languages (such as Python, Java, C++), algorithms, data structures, databases, operating systems, computer networks, artificial intelligence, cybersecurity, and software engineering. Advanced years may include machine learning, cloud computing, and mobile or web development.\n\nHow You Study\n\nLearning is project-based and practical. You spend time coding, debugging, building systems, and working in labs. Many courses include individual and team projects that simulate real industry problems.\n\nSkills You Gain\n\nProblem-solving and logical thinking\n\nCoding and software design\n\nData analysis and system optimization\n\nAttention to detail and independent learning\n\nCareer Paths\n\nSoftware Engineer, Web/Mobile Developer, Data Analyst, AI Engineer, Cybersecurity Specialist, Systems Analyst, Game Developer.\n\nBest For Students Who\n\nEnjoy technology, logic, problem-solving, and continuous learning. Comfortable spending long hours working on computers."
                ],
                'Faculty of Business Administration and International Business' => [
                    'description' => "Overview\n\nThis faculty prepares students to understand how organizations operate locally and globally. It focuses on decision-making, leadership, and managing resources efficiently in competitive markets.\n\nWhat You Study\n\nManagement, marketing, finance, accounting, economics, entrepreneurship, supply chain management, and international business laws. International tracks focus on global markets, trade, and cross-cultural management.\n\nHow You Study\n\nCase studies, group projects, presentations, simulations, and real business scenarios. Courses emphasize teamwork, analysis, and communication.\n\nSkills You Gain\n\nLeadership and strategic thinking\n\nFinancial and market analysis\n\nCommunication and negotiation\n\nDecision-making under pressure\n\nCareer Paths\n\nBusiness Manager, Marketing Specialist, Financial Analyst, HR Manager, Supply Chain Analyst, International Trade Officer, Entrepreneur.\n\nBest For Students Who\n\nEnjoy leadership, teamwork, communication, and understanding how businesses grow and compete."
                ],
                'Faculty of Al-Alsun (Languages)' => [
                    'description' => "Overview\n\nThis faculty specializes in mastering foreign languages and understanding cultures. It focuses on communication, translation, and global interaction.\n\nWhat You Study\n\nOne or more foreign languages in depth, translation techniques, linguistics, literature, cultural studies, and sometimes interpretation and media language.\n\nHow You Study\n\nLanguage practice, reading, writing, listening, speaking, and translation exercises. Continuous assessment through assignments, presentations, and exams.\n\nSkills You Gain\n\nAdvanced language proficiency\n\nTranslation and interpretation skills\n\nCultural awareness\n\nStrong communication and writing abilities\n\nCareer Paths\n\nTranslator, Interpreter, Language Instructor, Diplomat, International Relations Officer, Media Editor, Tourism Professional.\n\nBest For Students Who\n\nLove languages, reading, writing, and cross-cultural communication."
                ],
                'Faculty of Engineering Sciences & Arts' => [
                    'description' => "Overview\n\nThis faculty combines technical engineering knowledge with creativity and design. It focuses on building, designing, and improving physical and digital structures.\n\nWhat You Study\n\nEngineering mathematics, physics, mechanics, electronics, materials, architecture, interior design, industrial design, and applied arts depending on specialization.\n\nHow You Study\n\nHands-on labs, design studios, technical drawings, software tools, and real-world projects. Strong emphasis on precision and practical application.\n\nSkills You Gain\n\nAnalytical and technical problem-solving\n\nDesign and creativity\n\nProject planning and execution\n\nAttention to detail and teamwork\n\nCareer Paths\n\nEngineer, Architect, Interior Designer, Product Designer, Project Manager, Technical Consultant.\n\nBest For Students Who\n\nEnjoy math, physics, design, creativity, and turning ideas into real structures or products."
                ],
                'Faculty of Pharmacy' => [
                    'description' => "Overview\n\nThis faculty focuses on medicines and how they affect the human body. It combines science, healthcare, and patient safety.\n\nWhat You Study\n\nChemistry, biochemistry, pharmacology, pharmaceutical formulation, clinical pharmacy, drug interactions, and quality control.\n\nHow You Study\n\nLaboratory work, case studies, clinical training, and scientific research. Heavy focus on accuracy and responsibility.\n\nSkills You Gain\n\nScientific analysis and precision\n\nKnowledge of medicines and treatments\n\nPatient counseling skills\n\nResearch and safety awareness\n\nCareer Paths\n\nPharmacist, Clinical Pharmacist, Pharmaceutical Industry Specialist, Drug Researcher, Quality Control Analyst.\n\nBest For Students Who\n\nEnjoy chemistry, biology, healthcare, and working in medical or research environments."
                ],
                'Faculty of Oral and Dental Medicine' => [
                    'description' => "Overview\n\nThis faculty trains students to diagnose, treat, and prevent oral and dental diseases while improving patients’ health and appearance.\n\nWhat You Study\n\nAnatomy, oral biology, dental materials, pathology, orthodontics, prosthodontics, oral surgery, and preventive dentistry.\n\nHow You Study\n\nCombination of theoretical lectures, laboratory practice, and clinical training with real patients under supervision.\n\nSkills You Gain\n\nClinical and manual skills\n\nPatient care and communication\n\nPrecision and attention to detail\n\nMedical decision-making\n\nCareer Paths\n\nGeneral Dentist, Orthodontist, Oral Surgeon, Prosthodontist, Dental Clinic Owner.\n\nBest For Students Who\n\nAre patient, detail-oriented, comfortable working closely with people, and interested in healthcare."
                ],
                'Faculty of Mass Communication' => [
                    'description' => "Overview\n\nThis faculty focuses on how information is created, delivered, and consumed through media platforms.\n\nWhat You Study\n\nJournalism, advertising, public relations, digital media, broadcasting, media ethics, content creation, and audience analysis.\n\nHow You Study\n\nPractical projects, media production, writing, filming, editing, presentations, and internships.\n\nSkills You Gain\n\nPublic speaking and writing\n\nMedia production and storytelling\n\nCreativity and audience analysis\n\nStrategic communication\n\nCareer Paths\n\nJournalist, Content Creator, PR Specialist, Advertising Executive, Media Producer, Social Media Manager.\n\nBest For Students Who\n\nEnjoy creativity, storytelling, media, public influence, and fast-paced environments."
                ]
            ];

            $results = [];
            foreach ($faculties as $name => $data) {
                $faculty = $this->facultyService->findFacultyByName($name);
                $facultyId = null;
                if ($faculty) {
                    $this->facultyService->updateFaculty([
                        'faculty_id' => $faculty['faculty_id'],
                        'description' => $data['description']
                    ]);
                    $facultyId = $faculty['faculty_id'];
                    $results[] = "Updated: $name";
                } else {
                    $facultyId = $this->facultyService->createFaculty([
                        'name' => $name,
                        'description' => $data['description']
                    ]);
                    $results[] = "Created: $name";
                }

                // Add sample majors if they don't exist
                $existingMajors = $this->majorRepo->findByFaculty($facultyId);
                if (empty($existingMajors)) {
                    $sampleMajors = [];
                    if (strpos($name, 'Computer Science') !== false) {
                        $sampleMajors = ['Artificial Intelligence', 'Cybersecurity', 'Software Engineering', 'Data Science'];
                    } elseif (strpos($name, 'Business Administration') !== false) {
                        $sampleMajors = ['Marketing', 'Finance', 'International Business', 'HR Management'];
                    } elseif (strpos($name, 'Al-Alsun') !== false) {
                        $sampleMajors = ['English Translation', 'German Studies', 'Chinese Language', 'Spanish Literature'];
                    } elseif (strpos($name, 'Engineering') !== false) {
                        $sampleMajors = ['Architecture', 'Civil Engineering', 'Mechatronics', 'Electrical Engineering'];
                    } elseif (strpos($name, 'Pharmacy') !== false) {
                        $sampleMajors = ['Clinical Pharmacy', 'Pharmacology', 'Pharmaceutical Chemistry'];
                    } elseif (strpos($name, 'Dental') !== false) {
                        $sampleMajors = ['Orthodontics', 'Oral Surgery', 'General Dentistry'];
                    } elseif (strpos($name, 'Mass Communication') !== false) {
                        $sampleMajors = ['Journalism', 'Advertising', 'Digital Media', 'Public Relations'];
                    }

                    foreach ($sampleMajors as $majorName) {
                        $this->majorRepo->create(['faculty_id' => $facultyId, 'name' => $majorName]);
                        $results[] = "  Added Major: $majorName";
                    }
                }
            }

            $this->success(['results' => $results], 'Data seeded successfully');

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
