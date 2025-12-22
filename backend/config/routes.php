<?php
/**
 * API Routes Configuration
 * 
 * All API routes for the application
 */

return [
    // ============================================
    // Health Check Routes
    // ============================================
    'GET /health' => ['HealthController', 'check'],
    'GET /api/DEBUG_ROUTE_999' => ['HealthController', 'check'],

    // ============================================
    // Authentication Routes
    // ============================================
    'POST /api/auth/login' => ['AuthController', 'login'],
    'POST /api/auth/logout' => ['AuthController', 'logout'],
    'GET /api/auth/me' => ['AuthController', 'getCurrentUser'],

    // Legacy authentication routes (for backward compatibility)
    'POST /login' => ['AuthController', 'login'],
    'POST /logout' => ['AuthController', 'logout'],
    'GET /check-session' => ['AuthController', 'getCurrentUser'],
    'GET /getUsers' => ['UserController', 'getAll'],
    'GET /getUserProfile' => ['UserController', 'getProfile'],

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

    // Legacy student routes (for frontend compatibility)
    'GET /getStudents' => ['StudentController', 'getAll'],

    // ============================================
    // Professor Routes (Legacy compatibility)
    // ============================================
    'GET /api/professors' => ['ProfessorController', 'getAll'],
    'GET /api/professors/by-faculty' => ['ProfessorController', 'getByFaculty'],
    'PUT /api/professors' => ['ProfessorController', 'update'],

    // Legacy professor routes
    'GET /getProfessorById/\d+' => ['ProfessorController', 'getById'],
    'GET /getDashboardStats' => ['ProfessorController', 'getDashboardStats'],

    // ============================================
    // Admin Routes (Legacy compatibility)
    // ============================================
    'GET /api/admins' => ['AdminController', 'getAll'],
    'PUT /api/admin' => ['AdminController', 'update'],

    // Legacy admin routes (for frontend compatibility)
    'GET /getAllAdmins' => ['AdminController', 'getAll'],
    'POST /updateAdmin' => ['AdminController', 'update'],

    // ============================================
    // Post Routes (Legacy compatibility)
    // ============================================
    'GET /api/posts' => ['PostController', 'getAll'],
    'POST /api/posts' => ['PostController', 'create'],
    'GET /api/posts/search' => ['PostController', 'search'],
    'GET /api/posts/user' => ['PostController', 'getUserPosts'],
    'PUT /api/posts' => ['PostController', 'update'],
    'DELETE /api/posts' => ['PostController', 'delete'],

    // Legacy post routes (without /api prefix)
    'GET /getAllPosts' => ['PostController', 'getAll'],
    'POST /addPost' => ['PostController', 'create'],
    'POST /createPost' => ['PostController', 'create'],
    'POST /updatePost' => ['PostController', 'update'],
    'POST /deletePost' => ['PostController', 'delete'],
    'POST /searchPosts' => ['PostController', 'search'],
    'GET /getUserPosts' => ['PostController', 'getUserPosts'],
    'POST /uploadMedia' => ['PostController', 'uploadMedia'],
    'POST /uploadPostMedia' => ['PostController', 'uploadMedia'],

    // ============================================
    // Post Interaction Routes (Legacy compatibility)
    // ============================================
    'POST /api/post-interactions' => ['PostInteractionController', 'add'],
    'POST /api/post-interactions/get' => ['PostInteractionController', 'getByPost'],
    'POST /api/post-interactions/user-reaction' => ['PostInteractionController', 'getUserReaction'],
    'POST /api/post-interactions/counts' => ['PostInteractionController', 'getReactionCounts'],
    'DELETE /api/post-interactions' => ['PostInteractionController', 'delete'],

    // Legacy interaction routes (for frontend)
    'POST /addInteraction' => ['PostInteractionController', 'add'],
    'POST /getInteractionsByPost' => ['PostInteractionController', 'getByPost'],
    'POST /getUserReaction' => ['PostInteractionController', 'getUserReaction'],
    'POST /getReactionCounts' => ['PostInteractionController', 'getReactionCounts'],
    'POST /deleteInteraction' => ['PostInteractionController', 'delete'],

    // ============================================
    // Comment Routes (Legacy compatibility)
    // ============================================
    'POST /api/comments' => ['CommentController', 'create'],
    'GET /api/comments' => ['CommentController', 'getByPost'],

    // Legacy comment routes
    'POST /addComment' => ['CommentController', 'create'],
    'GET /getComments' => ['CommentController', 'getByPost'],
    'POST /updateComment' => ['CommentController', 'update'],
    'POST /deleteComment' => ['CommentController', 'delete'],

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

    // Project Routes (New OOP endpoints)
    'POST /uploadProject' => ['ProjectController', 'uploadProject'],
    'GET /getUserProjects' => ['ProjectController', 'getUserProjects'],
    'POST /deleteProject' => ['ProjectController', 'deleteProject'],
    'POST /updateProject' => ['ProjectController', 'updateProject'],
    'POST /api/projects/approve' => ['ProjectController', 'approveProject'],
    'POST /api/projects/reject' => ['ProjectController', 'rejectProject'],

    // ============================================
    // Grading Routes
    // ============================================
    'GET /api/grading/projects' => ['GradingController', 'getProjects'],
    'POST /api/grading/grade' => ['GradingController', 'gradeProject'],

    // ============================================
    // Project Room Routes (Legacy compatibility)
    // ============================================
    'GET /api/project-rooms' => ['ProjectRoomController', 'getAll'],
    'POST /api/project-rooms' => ['ProjectRoomController', 'create'],
    'GET /api/project-rooms/user' => ['ProjectRoomController', 'getUserRooms'],
    'GET /api/project-rooms/room' => ['ProjectRoomController', 'getRoom'],
    'PUT /api/project-rooms' => ['ProjectRoomController', 'update'],
    'DELETE /api/project-rooms' => ['ProjectRoomController', 'delete'],
    // Legacy project room routes (for frontend compatibility)
    'GET /getAllRooms' => ['ProjectRoomController', 'getAll'],
    'GET /getUserRooms' => ['ProjectRoomController', 'getUserRooms'],
    'POST /createRoom' => ['ProjectRoomController', 'create'],
    'GET /getRoom' => ['ProjectRoomController', 'getRoom'],
    'POST /updateRoom' => ['ProjectRoomController', 'update'],
    'POST /deleteRoom' => ['ProjectRoomController', 'delete'],
    'POST /joinRoom' => ['ProjectRoomController', 'join'],
    'POST /api/project-rooms/join' => ['ProjectRoomController', 'join'],
    'POST /sendMessage' => ['ChatController', 'sendMessage'],
    'GET /getMessages' => ['ChatController', 'getRoomMessages'],
    'POST /deleteMessage' => ['ChatController', 'deleteMessage'],
    'GET /getRoomMembers' => ['ProjectRoomController', 'getRoomMembers'],

    // ============================================
    // CV Routes (Legacy compatibility)
    // ============================================
    // ============================================
    // CV Routes
    // ============================================
    'POST /api/cv/upload' => ['CvController', 'upload'],
    'GET /api/cv/download' => ['CvController', 'download'],
    'GET /getCV' => ['CvController', 'getCV'], // New endpoint for metadata
    'GET /downloadCV' => ['CvController', 'download'], // Added missing legacy route
    'POST /deleteCV' => ['CvController', 'delete'],

    // ============================================
    // Skill Routes
    // ============================================
    'GET /api/skills' => ['SkillController', 'getAll'],
    'GET /api/skill-categories' => ['SkillCategoryController', 'getAll'],
    'POST /addSkillCategory' => ['SkillCategoryController', 'create'], // Added
    'POST /addSkill' => ['SkillController', 'create'], // Added

    // User Skills
    'GET /api/user-skills' => ['UserSkillController', 'getUserSkills'],
    'POST /api/user-skills' => ['UserSkillController', 'add'],
    'DELETE /api/user-skills' => ['UserSkillController', 'delete'],

    // Legacy support for skills
    'POST /addUserSkills' => ['UserSkillController', 'add'],
    'POST /removeUserSkill' => ['UserSkillController', 'delete'],

    // ============================================
    // Faculty & Major Routes (Legacy compatibility)
    // ============================================
    'GET /api/faculties' => ['FacultyController', 'getAll'],
    'GET /api/faculty' => ['FacultyController', 'getById'],
    'GET /api/majors' => ['MajorController', 'getAll'],
    'GET /api/faculties/majors' => ['FacultyController', 'getMajors'],
    'GET /api/faculties/seed' => ['FacultyController', 'seedData'],

    // Legacy faculty/major routes
    'GET /getAllFaculties' => ['FacultyController', 'getAll'],
    'GET /getFaculty' => ['FacultyController', 'getById'],
    'GET /seedFaculties' => ['FacultyController', 'seedData'],
    'GET /getAllMajors' => ['MajorController', 'getAll'],
    'POST /addFaculty' => ['FacultyController', 'create'],
    'POST /updateFaculty' => ['FacultyController', 'update'],
    'POST /deleteFaculty' => ['FacultyController', 'delete'],
    'POST /addMajor' => ['MajorController', 'create'],
    'POST /updateMajor' => ['MajorController', 'update'],
    'POST /deleteMajor' => ['MajorController', 'delete'],

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

    // ============================================
    // Profile Routes (Aggregated Data)
    // ============================================
    'GET /api/profile/full' => ['ProfileController', 'getFullProfile'],
    'GET /api/profile/public' => ['ProfileController', 'getPublicProfile'],

    // ============================================
    // Notification Routes (NEW)
    // ============================================    // Notifications
    'GET /api/notifications' => ['NotificationController', 'getNotifications'],
    'GET /api/notifications/unread' => ['NotificationController', 'getUnread'],
    'GET /api/notifications/unread-count' => ['NotificationController', 'getUnreadCount'],
    'GET /api/notifications/debug' => ['NotificationController', 'debug'],
    'PUT /api/notifications/mark-as-read' => ['NotificationController', 'markAsRead'],
    'PUT /api/notifications/mark-all-read' => ['NotificationController', 'markAllAsRead'],
    'DELETE /api/notifications/delete' => ['NotificationController', 'deleteNotification'],
    'GET /api/notifications/by-type' => ['NotificationController', 'getByType'],

    // ============================================
    // Chat Routes (NEW - Chain of Responsibility)
    // ============================================
    'POST /api/chat/send' => ['ChatController', 'sendMessage'],
    'GET /api/chat/messages' => ['ChatController', 'getRoomMessages'],
    'GET /api/chat/message-count' => ['ChatController', 'getMessageCount'],
    'POST /api/chat/messages/delete' => ['ChatController', 'deleteMessage'],
    'POST /api/chat/upload' => ['ChatController', 'uploadFile'],
    'POST /uploadChatFile' => ['ChatController', 'uploadFile'],

    // Counting Routes
    'GET /api/posts/category-counts' => ['PostController', 'getCategoryCounts'],
    'GET /getCategoryCounts' => ['PostController', 'getCategoryCounts'],
    'GET /api/chat/rooms/total-count' => ['ProjectRoomController', 'getRoomCount'],
    'GET /getRoomCount' => ['ProjectRoomController', 'getRoomCount'],
];
