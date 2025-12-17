<?php
namespace App\Facades;

use App\Repositories\UserRepository;
use App\Repositories\PostRepository;
use App\Repositories\ProjectRepository;
use App\Repositories\SkillRepository;
use App\Repositories\CvRepository;

/**
 * ProfileFacade
 * 
 * Facade Pattern Implementation
 * Aggregates user profile data from multiple sources
 * Part of the Profile System (UML Design)
 */
class ProfileFacade {
    private UserRepository $userRepo;
    private PostRepository $postRepo;
    private ProjectRepository $projectRepo;
    private SkillRepository $skillRepo;
    private CvRepository $cvRepo;
    
    public function __construct(
        UserRepository $userRepo,
        PostRepository $postRepo,
        ProjectRepository $projectRepo,
        SkillRepository $skillRepo,
        CvRepository $cvRepo
    ) {
        $this->userRepo = $userRepo;
        $this->postRepo = $postRepo;
        $this->projectRepo = $projectRepo;
        $this->skillRepo = $skillRepo;
        $this->cvRepo = $cvRepo;
    }
    
    /**
     * Get complete user profile with all related data
     * 
     * @param int $userId User ID
     * @return array Complete profile data
     */
    public function getFullProfile(int $userId): array {
        // Get basic user info
        $user = $this->userRepo->find($userId);
        if (!$user) {
            return [];
        }
        
        // Remove sensitive data
        unset($user['password'], $user['password_hash']);
        
        // Get user skills
        $skills = $this->skillRepo->findUserSkills($userId);
        
        // Get user projects
        $projects = $this->projectRepo->findByUser($userId);
        
        // Get recent posts (limit to 10)
        $posts = $this->postRepo->findByUser($userId, 10);
        
        // Get CV information
        $cv = $this->cvRepo->findByUser($userId);
        
        // Aggregate all data
        return [
            'user' => $user,
            'skills' => $skills,
            'projects' => $projects,
            'recent_posts' => $posts,
            'cv' => $cv,
            'stats' => [
                'total_skills' => count($skills),
                'total_projects' => count($projects),
                'total_posts' => $this->postRepo->countByUser($userId)
            ]
        ];
    }
    
    /**
     * Get public profile (limited information)
     * 
     * @param int $userId User ID
     * @return array Public profile data
     */
    public function getPublicProfile(int $userId): array {
        $fullProfile = $this->getFullProfile($userId);
        
        if (empty($fullProfile)) {
            return [];
        }
        
        // Return only public information
        return [
            'user' => [
                'user_id' => $fullProfile['user']['user_id'] ?? null,
                'username' => $fullProfile['user']['username'] ?? null,
                'bio' => $fullProfile['user']['bio'] ?? null,
                'profile_picture' => $fullProfile['user']['profile_picture'] ?? null,
                'faculty_id' => $fullProfile['user']['faculty_id'] ?? null,
                'major_id' => $fullProfile['user']['major_id'] ?? null
            ],
            'skills' => $fullProfile['skills'],
            'projects' => array_map(function($project) {
                // Remove file paths from public view
                unset($project['file_path']);
                return $project;
            }, $fullProfile['projects']),
            'stats' => $fullProfile['stats']
        ];
    }
}
