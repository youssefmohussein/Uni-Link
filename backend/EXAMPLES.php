<?php
/**
 * OOP Backend Architecture - Usage Examples
 * 
 * This file demonstrates how to use the new OOP architecture
 * DO NOT run this file directly - it's for reference only
 */

// ============================================
// EXAMPLE 1: Using the Auth System
// ============================================

require_once __DIR__ . '/config/autoload.php';

use App\Controllers\AuthController;
use App\Services\AuthService;
use App\Repositories\UserRepository;

// Create dependencies (in production, use a DI container)
$userRepo = new UserRepository();
$authService = new AuthService($userRepo);
$authController = new AuthController($authService);

// Handle login request
// POST /api/auth/login
// $authController->login();

// ============================================
// EXAMPLE 2: Using Strategy Pattern for Post Interactions
// ============================================

use App\Strategies\PostInteraction\InteractionContext;

// Create context
$context = new InteractionContext();

// User wants to like a post
$likeStrategy = InteractionContext::createStrategy('like');
$context->setStrategy($likeStrategy);
$result = $context->executeInteraction($postId = 1, $userId = 123);

// User wants to save a post
$saveStrategy = InteractionContext::createStrategy('save');
$context->setStrategy($saveStrategy);
$result = $context->executeInteraction($postId = 1, $userId = 123);

// ============================================
// EXAMPLE 3: Using Strategy Pattern for Role-Based Access
// ============================================

use App\Strategies\RoleAccess\AdminAccessStrategy;
use App\Strategies\RoleAccess\StudentAccessStrategy;

// Check if admin can access resource
$adminStrategy = new AdminAccessStrategy();
$canAccess = $adminStrategy->canAccessResource($userId = 1, 'project_delete');
// Returns: true (admins can do anything)

// Check if student can edit a project
$studentStrategy = new StudentAccessStrategy();
$canAccess = $studentStrategy->canAccessResource(
    $userId = 123, 
    'project_edit',
    ['owner_id' => 123]  // Context: student owns this project
);
// Returns: true (student can edit own project)

$canAccess = $studentStrategy->canAccessResource(
    $userId = 123, 
    'project_edit',
    ['owner_id' => 456]  // Context: student doesn't own this project
);
// Returns: false (student cannot edit others' projects)

// ============================================
// EXAMPLE 4: Using Mediator Pattern for Notifications
// ============================================

use App\Mediators\NotificationMediator;

$notificationMediator = new NotificationMediator();

// When a post is commented
$notificationMediator->notify($this, 'post.commented', [
    'post_id' => 1,
    'post_author_id' => 100,
    'commenter_id' => 123,
    'commenter_name' => 'John Doe'
]);
// This automatically creates a notification for the post author

// When a project is reviewed
$notificationMediator->notify($this, 'project.reviewed', [
    'project_id' => 5,
    'project_title' => 'AI Chatbot',
    'student_id' => 123,
    'status' => 'Approved'
]);
// This automatically notifies the student

// ============================================
// EXAMPLE 5: Using Mediator Pattern for Project Rooms
// ============================================

use App\Mediators\ProjectRoomMediator;

$roomMediator = new ProjectRoomMediator($notificationMediator);

// When a chat message is sent
$roomMediator->notify($this, 'chat.message', [
    'room_id' => 10,
    'room_name' => 'AI Project Team',
    'sender_id' => 123,
    'sender_name' => 'John Doe'
]);
// This notifies all room members except the sender

// ============================================
// EXAMPLE 6: Using User Models with Inheritance
// ============================================

use App\Models\Student;
use App\Models\Professor;
use App\Models\Admin;

// Create a student
$studentData = [
    'user_id' => 123,
    'username' => 'john_doe',
    'email' => 'john@example.com',
    'password' => 'hashed_password',
    'role' => 'Student',
    'year' => 3,
    'gpa' => 3.5,
    'points' => 100
];

$student = new Student($studentData);

// Use student-specific methods
echo $student->getAcademicStanding(); // "Very Good"
$student->addPoints(50);
echo $student->getPoints(); // 150

// Polymorphism: toArray() works for all user types
$studentArray = $student->toArray();
// Returns array with both base user data and student-specific data

// Create a professor
$professorData = [
    'user_id' => 456,
    'username' => 'dr_smith',
    'email' => 'smith@example.com',
    'password' => 'hashed_password',
    'role' => 'Professor',
    'academic_rank' => 'Associate Professor',
    'office_location' => 'Building A, Room 301'
];

$professor = new Professor($professorData);

// Use professor-specific methods
echo $professor->isSenior(); // true
echo $professor->canGradeProjects(); // true

// ============================================
// EXAMPLE 7: Using Middlewares
// ============================================

use App\Middlewares\AuthMiddleware;
use App\Middlewares\RoleMiddleware;

// Protect a route - require authentication
AuthMiddleware::handle();

// Protect a route - require specific role
RoleMiddleware::requireRole('Admin');

// Protect a route - require one of multiple roles
RoleMiddleware::requireAnyRole(['Admin', 'Professor']);

// Check resource access with context
RoleMiddleware::checkResourceAccess('project_edit', [
    'owner_id' => 123
]);

// ============================================
// EXAMPLE 8: Complete Request Flow
// ============================================

/*
Request Flow Example: Student Likes a Post

1. Frontend sends POST request to /api/posts/1/interact
   Body: { "type": "like", "user_id": 123 }

2. Route handler calls PostInteractionController->interact()

3. Controller uses InteractionContext with Strategy Pattern:
   - Creates LikeStrategy
   - Executes the strategy
   
4. LikeStrategy interacts with database

5. Controller notifies NotificationMediator:
   - Mediator creates notification for post author
   
6. Response sent back to frontend

This demonstrates:
- Dependency Injection (Controller receives services)
- Strategy Pattern (Different interaction types)
- Mediator Pattern (Notification coordination)
- Separation of Concerns (Controller → Service → Repository)
*/

// ============================================
// EXAMPLE 9: Transaction Management
// ============================================

use App\Repositories\UserRepository;
use App\Repositories\StudentRepository;

$userRepo = new UserRepository();

try {
    $userRepo->beginTransaction();
    
    // Create user
    $userId = $userRepo->create([...]);
    
    // Create student record
    $studentRepo = new StudentRepository();
    $studentRepo->create([...]);
    
    $userRepo->commit();
    
} catch (\Exception $e) {
    $userRepo->rollback();
    throw $e;
}

// ============================================
// Key Benefits of This Architecture
// ============================================

/*
1. SOLID Principles:
   - Single Responsibility: Each class has one job
   - Open/Closed: Extend via strategies, not modification
   - Liskov Substitution: User subclasses are interchangeable
   - Interface Segregation: Specific interfaces for each need
   - Dependency Inversion: Depend on abstractions, not concretions

2. Design Patterns:
   - Strategy: Flexible behavior selection at runtime
   - Mediator: Decoupled component communication
   - Repository: Abstracted data access
   - Singleton: Single database connection
   - Factory: Strategy creation

3. Maintainability:
   - Easy to test (mock dependencies)
   - Easy to extend (add new strategies)
   - Easy to understand (clear separation)
   - Easy to modify (change one layer at a time)

4. Type Safety:
   - Type hints everywhere
   - Clear interfaces
   - Predictable behavior
*/
