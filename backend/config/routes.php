<?php
/**
 * API Routes Configuration
 * 
 * All API routes for the application
 */

return [
    // ============================================
    // Authentication Routes
    // ============================================
    'POST /api/auth/login' => ['AuthController', 'login'],
    'POST /api/auth/logout' => ['AuthController', 'logout'],
    'GET /api/auth/me' => ['AuthController', 'getCurrentUser'],
    
    // ============================================
    // User Routes (Legacy compatibility)
    // ============================================
    'POST /api/user' => ['UserController', 'create'],
    'GET /api/user' => ['UserController', 'getAll'],
    'GET /api/user/profile' => ['UserController', 'getProfile'],
    'PUT /api/user' => ['UserController', 'update'],
    'DELETE /api/user' => ['UserController', 'delete'],
    
    // ============================================
    // Student Routes (Legacy compatibility)
    // ============================================
    'GET /api/students' => ['StudentController', 'getAll'],
    'PUT /api/student' => ['StudentController', 'update'],
    
    // ============================================
    // Professor Routes (Legacy compatibility)
    // ============================================
    'GET /api/professors' => ['ProfessorController', 'getAll'],
    
    // ============================================
    // Admin Routes (Legacy compatibility)
    // ============================================
    'GET /api/admins' => ['AdminController', 'getAll'],
    'PUT /api/admin' => ['AdminController', 'update'],
    
    // ============================================
    // Post Routes (Legacy compatibility)
    // ============================================
    'GET /api/posts' => ['PostController', 'getAll'],
    'POST /api/posts' => ['PostController', 'create'],
    'GET /api/posts/search' => ['PostController', 'search'],
    'GET /api/posts/user' => ['PostController', 'getUserPosts'],
    'PUT /api/posts' => ['PostController', 'update'],
    'DELETE /api/posts' => ['PostController', 'delete'],
    
    // ============================================
    // Post Interaction Routes (Legacy compatibility)
    // ============================================
    'POST /api/post-interactions' => ['PostInteractionController', 'add'],
    'POST /api/post-interactions/get' => ['PostInteractionController', 'getByPost'],
    'POST /api/post-interactions/user-reaction' => ['PostInteractionController', 'getUserReaction'],
    'POST /api/post-interactions/counts' => ['PostInteractionController', 'getReactionCounts'],
    'DELETE /api/post-interactions' => ['PostInteractionController', 'delete'],
    
    // ============================================
    // Comment Routes (Legacy compatibility)
    // ============================================
    'POST /api/comments' => ['CommentController', 'create'],
    'POST /api/comments/get' => ['CommentController', 'getByPost'],
    
    // ============================================
    // Project Routes (Legacy compatibility)
    // ============================================
    'GET /api/projects' => ['ProjectController', 'getAll'],
    'POST /api/projects/upload' => ['ProjectController', 'upload'],
    'POST /api/projects' => ['ProjectController', 'create'],
    'GET /api/projects/user' => ['ProjectController', 'getUserProjects'],
    'PUT /api/projects' => ['ProjectController', 'update'],
    'DELETE /api/projects' => ['ProjectController', 'delete'],
    'POST /api/projects/grade' => ['ProjectController', 'addGrade'],
    
    // ============================================
    // Project Room Routes (Legacy compatibility)
    // ============================================
    'GET /api/project-rooms' => ['ProjectRoomController', 'getAll'],
    'POST /api/project-rooms' => ['ProjectRoomController', 'create'],
    'GET /api/project-rooms/user' => ['ProjectRoomController', 'getUserRooms'],
    'GET /api/project-rooms/room' => ['ProjectRoomController', 'getRoom'],
    
    // ============================================
    // CV Routes (Legacy compatibility)
    // ============================================
    'POST /api/cv/upload' => ['CvController', 'upload'],
    'GET /api/cv/download' => ['CvController', 'download'],
    
    // ============================================
    // Skill Routes (Legacy compatibility)
    // ============================================
    'GET /api/skills' => ['SkillController', 'getAll'],
    'GET /api/skill-categories' => ['SkillCategoryController', 'getAll'],
    'GET /api/user-skills' => ['UserSkillController', 'getUserSkills'],
    'POST /api/user-skills' => ['UserSkillController', 'add'],
    'DELETE /api/user-skills' => ['UserSkillController', 'delete'],
    
    // ============================================
    // Faculty & Major Routes (Legacy compatibility)
    // ============================================
    'GET /api/faculties' => ['FacultyController', 'getAll'],
    'GET /api/majors' => ['MajorController', 'getAll'],
    
    // ============================================
    // Announcement Routes (Legacy compatibility)
    // ============================================
    'GET /api/announcements' => ['AnnouncementController', 'getAll'],
    'POST /api/announcements' => ['AnnouncementController', 'create'],
    
    // ============================================
    // Dashboard Routes (Legacy compatibility)
    // ============================================
    'GET /api/dashboard/stats' => ['DashboardController', 'getStats'],
    
    // ============================================
    // Saved Posts Routes (Legacy compatibility)
    // ============================================
    'POST /api/saved-posts' => ['SavedPostController', 'save'],
    'GET /api/saved-posts' => ['SavedPostController', 'getUserSaved'],
    'DELETE /api/saved-posts' => ['SavedPostController', 'unsave'],
];
