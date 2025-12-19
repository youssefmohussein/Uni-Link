<?php
/**
 * Update Faculty Descriptions
 * Run this script to update the descriptions of faculties in the database.
 */

require_once __DIR__ . '/../config/autoload.php';
$container = require_once __DIR__ . '/../config/services.php';

use App\Repositories\FacultyRepository;
use App\Repositories\MajorRepository;

$facultyRepo = $container->get('FacultyRepository');
$majorRepo = $container->get('MajorRepository');

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

echo "Updating faculty descriptions...\n";
echo "Total faculties to process: " . count($faculties) . "\n";

foreach ($faculties as $name => $data) {
    try {
        $faculty = $facultyRepo->findByName($name);
        $facultyId = null;
        if ($faculty) {
            $facultyRepo->update($faculty['faculty_id'], ['description' => $data['description']]);
            echo "Updated: $name\n";
            $facultyId = $faculty['faculty_id'];
        } else {
            // If not found, create it
            $facultyId = $facultyRepo->create([
                'name' => $name,
                'description' => $data['description']
            ]);
            echo "Created: $name\n";
        }

        // Add some sample majors if they don't exist
        $existingMajors = $majorRepo->findByFaculty($facultyId);
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
                $majorRepo->create(['faculty_id' => $facultyId, 'name' => $majorName]);
                echo "  Added Major: $majorName\n";
            }
        }
    } catch (\Exception $e) {
        echo "Error updating $name: " . $e->getMessage() . "\n";
    }
}

echo "Done!\n";
