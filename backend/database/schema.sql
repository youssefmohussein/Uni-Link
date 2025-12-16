-- ======================================================
-- UNIVERSITY COLLABORATION PLATFORM - DATABASE SCHEMA
-- ======================================================
-- Based on UML Design with Design Patterns
-- Created: 2025-12-16
-- ======================================================

-- Drop existing tables (in reverse order of dependencies)
DROP TABLE IF EXISTS `chat_mentions`;
DROP TABLE IF EXISTS `chat_messages`;
DROP TABLE IF EXISTS `room_members`;
DROP TABLE IF EXISTS `chat_rooms`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `project_reviews`;
DROP TABLE IF EXISTS `project_skills`;
DROP TABLE IF EXISTS `projects`;
DROP TABLE IF EXISTS `user_skills`;
DROP TABLE IF EXISTS `skills`;
DROP TABLE IF EXISTS `skill_categories`;
DROP TABLE IF EXISTS `cvs`;
DROP TABLE IF EXISTS `post_interactions`;
DROP TABLE IF EXISTS `post_media`;
DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `posts`;
DROP TABLE IF EXISTS `announcements`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `professors`;
DROP TABLE IF EXISTS `admins`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `majors`;
DROP TABLE IF EXISTS `faculties`;

-- ======================================================
-- CORE REFERENCE TABLES
-- ======================================================

CREATE TABLE `faculties` (
  `faculty_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_faculty_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `majors` (
  `major_id` INT AUTO_INCREMENT PRIMARY KEY,
  `faculty_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`faculty_id`) ON DELETE CASCADE,
  INDEX `idx_major_faculty` (`faculty_id`),
  INDEX `idx_major_name` (`name`),
  UNIQUE KEY `unique_major_per_faculty` (`faculty_id`, `name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- USER DOMAIN (INHERITANCE HIERARCHY)
-- ======================================================

CREATE TABLE `users` (
  `user_id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('ADMIN', 'PROFESSOR', 'STUDENT') NOT NULL,
  `faculty_id` INT,
  `major_id` INT,
  `profile_picture` VARCHAR(255),
  `bio` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`faculty_id`) ON DELETE SET NULL,
  FOREIGN KEY (`major_id`) REFERENCES `majors`(`major_id`) ON DELETE SET NULL,
  INDEX `idx_user_email` (`email`),
  INDEX `idx_user_role` (`role`),
  INDEX `idx_user_faculty` (`faculty_id`),
  INDEX `idx_user_major` (`major_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admins` (
  `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `permissions` JSON,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  INDEX `idx_admin_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `professors` (
  `professor_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `academic_rank` VARCHAR(100),
  `department` VARCHAR(255),
  `office_location` VARCHAR(255),
  `office_hours` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  INDEX `idx_professor_user` (`user_id`),
  INDEX `idx_professor_department` (`department`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `students` (
  `student_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `year` INT,
  `gpa` DECIMAL(3, 2),
  `points` INT DEFAULT 0,
  `enrollment_date` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  INDEX `idx_student_user` (`user_id`),
  INDEX `idx_student_year` (`year`),
  INDEX `idx_student_points` (`points`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- POSTS DOMAIN
-- ======================================================

CREATE TABLE `posts` (
  `post_id` INT AUTO_INCREMENT PRIMARY KEY,
  `author_id` INT NOT NULL,
  `content` TEXT NOT NULL,
  `visibility` ENUM('PUBLIC', 'FACULTY', 'MAJOR', 'PRIVATE') DEFAULT 'PUBLIC',
  `status` ENUM('DRAFT', 'PUBLISHED', 'ARCHIVED', 'MODERATED') DEFAULT 'PUBLISHED',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`author_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  INDEX `idx_post_author` (`author_id`),
  INDEX `idx_post_status` (`status`),
  INDEX `idx_post_visibility` (`visibility`),
  INDEX `idx_post_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comments` (
  `comment_id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `content` TEXT NOT NULL,
  `parent_comment_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`post_id`) REFERENCES `posts`(`post_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`parent_comment_id`) REFERENCES `comments`(`comment_id`) ON DELETE CASCADE,
  INDEX `idx_comment_post` (`post_id`),
  INDEX `idx_comment_user` (`user_id`),
  INDEX `idx_comment_parent` (`parent_comment_id`),
  INDEX `idx_comment_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `post_media` (
  `media_id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `type` ENUM('IMAGE', 'VIDEO', 'FILE') NOT NULL,
  `path` VARCHAR(500) NOT NULL,
  `filename` VARCHAR(255),
  `mime_type` VARCHAR(100),
  `size` BIGINT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`post_id`) REFERENCES `posts`(`post_id`) ON DELETE CASCADE,
  INDEX `idx_media_post` (`post_id`),
  INDEX `idx_media_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `post_interactions` (
  `interaction_id` INT AUTO_INCREMENT PRIMARY KEY,
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `type` ENUM('LIKE', 'LOVE', 'CELEBRATE', 'SAVE', 'SHARE') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`post_id`) REFERENCES `posts`(`post_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_post_interaction` (`post_id`, `user_id`, `type`),
  INDEX `idx_interaction_post` (`post_id`),
  INDEX `idx_interaction_user` (`user_id`),
  INDEX `idx_interaction_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- PROFILE DOMAIN
-- ======================================================

CREATE TABLE `cvs` (
  `cv_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL UNIQUE,
  `file_path` VARCHAR(500) NOT NULL,
  `filename` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(100),
  `size` BIGINT,
  `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  INDEX `idx_cv_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `skill_categories` (
  `category_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `icon` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_skill_category_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `skills` (
  `skill_id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `skill_categories`(`category_id`) ON DELETE SET NULL,
  INDEX `idx_skill_name` (`name`),
  INDEX `idx_skill_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `user_skills` (
  `user_skill_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `skill_id` INT NOT NULL,
  `proficiency_level` ENUM('BEGINNER', 'INTERMEDIATE', 'ADVANCED', 'EXPERT') DEFAULT 'INTERMEDIATE',
  `years_of_experience` INT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`skill_id`) REFERENCES `skills`(`skill_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_skill` (`user_id`, `skill_id`),
  INDEX `idx_user_skill_user` (`user_id`),
  INDEX `idx_user_skill_skill` (`skill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- PROJECT DOMAIN
-- ======================================================

CREATE TABLE `projects` (
  `project_id` INT AUTO_INCREMENT PRIMARY KEY,
  `student_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `file_path` VARCHAR(500),
  `filename` VARCHAR(255),
  `status` ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
  `grade` DECIMAL(5, 2),
  `supervisor_id` INT,
  `submitted_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`student_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`supervisor_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL,
  INDEX `idx_project_student` (`student_id`),
  INDEX `idx_project_supervisor` (`supervisor_id`),
  INDEX `idx_project_status` (`status`),
  INDEX `idx_project_submitted` (`submitted_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `project_skills` (
  `project_skill_id` INT AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT NOT NULL,
  `skill_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`project_id`) ON DELETE CASCADE,
  FOREIGN KEY (`skill_id`) REFERENCES `skills`(`skill_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_project_skill` (`project_id`, `skill_id`),
  INDEX `idx_project_skill_project` (`project_id`),
  INDEX `idx_project_skill_skill` (`skill_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `project_reviews` (
  `review_id` INT AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT NOT NULL,
  `professor_id` INT NOT NULL,
  `comment` TEXT,
  `score` DECIMAL(5, 2),
  `status` ENUM('PENDING', 'APPROVED', 'REJECTED') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`project_id`) ON DELETE CASCADE,
  FOREIGN KEY (`professor_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  INDEX `idx_review_project` (`project_id`),
  INDEX `idx_review_professor` (`professor_id`),
  INDEX `idx_review_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- CHAT ROOM DOMAIN
-- ======================================================

CREATE TABLE `chat_rooms` (
  `room_id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `password_hash` VARCHAR(255),
  `owner_id` INT NOT NULL,
  `photo_url` VARCHAR(500),
  `is_private` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`owner_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  INDEX `idx_room_owner` (`owner_id`),
  INDEX `idx_room_name` (`name`),
  INDEX `idx_room_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `room_members` (
  `membership_id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `role` ENUM('ADMIN', 'MODERATOR', 'MEMBER') DEFAULT 'MEMBER',
  `joined_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`room_id`) REFERENCES `chat_rooms`(`room_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_room_member` (`room_id`, `user_id`),
  INDEX `idx_member_room` (`room_id`),
  INDEX `idx_member_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chat_messages` (
  `message_id` INT AUTO_INCREMENT PRIMARY KEY,
  `room_id` INT NOT NULL,
  `sender_id` INT NOT NULL,
  `content` TEXT NOT NULL,
  `message_type` ENUM('TEXT', 'FILE', 'IMAGE', 'SYSTEM') DEFAULT 'TEXT',
  `file_path` VARCHAR(500),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`room_id`) REFERENCES `chat_rooms`(`room_id`) ON DELETE CASCADE,
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  INDEX `idx_message_room` (`room_id`),
  INDEX `idx_message_sender` (`sender_id`),
  INDEX `idx_message_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `chat_mentions` (
  `mention_id` INT AUTO_INCREMENT PRIMARY KEY,
  `message_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`message_id`) REFERENCES `chat_messages`(`message_id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_message_mention` (`message_id`, `user_id`),
  INDEX `idx_mention_message` (`message_id`),
  INDEX `idx_mention_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- NOTIFICATION DOMAIN (OBSERVER PATTERN)
-- ======================================================

CREATE TABLE `notifications` (
  `notification_id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `type` ENUM('POST_LIKE', 'POST_COMMENT', 'POST_SHARE', 'PROJECT_REVIEW', 'PROJECT_APPROVED', 'PROJECT_REJECTED', 'PROJECT_GRADED', 'CHAT_MENTION', 'CHAT_MESSAGE', 'SYSTEM') NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `related_entity_type` ENUM('POST', 'COMMENT', 'PROJECT', 'CHAT_MESSAGE', 'USER') DEFAULT NULL,
  `related_entity_id` INT DEFAULT NULL,
  `is_read` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  INDEX `idx_notification_user` (`user_id`),
  INDEX `idx_notification_type` (`type`),
  INDEX `idx_notification_read` (`is_read`),
  INDEX `idx_notification_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- ADMIN DOMAIN
-- ======================================================

CREATE TABLE `announcements` (
  `announcement_id` INT AUTO_INCREMENT PRIMARY KEY,
  `admin_id` INT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `priority` ENUM('LOW', 'MEDIUM', 'HIGH', 'URGENT') DEFAULT 'MEDIUM',
  `target_audience` ENUM('ALL', 'STUDENTS', 'PROFESSORS', 'FACULTY', 'MAJOR') DEFAULT 'ALL',
  `faculty_id` INT,
  `major_id` INT,
  `expires_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`admin_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`faculty_id`) REFERENCES `faculties`(`faculty_id`) ON DELETE SET NULL,
  FOREIGN KEY (`major_id`) REFERENCES `majors`(`major_id`) ON DELETE SET NULL,
  INDEX `idx_announcement_admin` (`admin_id`),
  INDEX `idx_announcement_priority` (`priority`),
  INDEX `idx_announcement_audience` (`target_audience`),
  INDEX `idx_announcement_created` (`created_at` DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ======================================================
-- TRIGGERS FOR AUTOMATED ACTIONS
-- ======================================================

-- Trigger: Auto-increment student points on project approval
DELIMITER $$
CREATE TRIGGER `increment_student_points_on_approval`
AFTER UPDATE ON `projects`
FOR EACH ROW
BEGIN
  IF NEW.status = 'APPROVED' AND OLD.status != 'APPROVED' THEN
    UPDATE `students` 
    SET `points` = `points` + 10 
    WHERE `user_id` = NEW.student_id;
  END IF;
END$$
DELIMITER ;

-- Trigger: Create notification on project review
DELIMITER $$
CREATE TRIGGER `notify_student_on_project_review`
AFTER INSERT ON `project_reviews`
FOR EACH ROW
BEGIN
  DECLARE student_user_id INT;
  DECLARE notification_title VARCHAR(255);
  DECLARE notification_message TEXT;
  
  SELECT student_id INTO student_user_id FROM projects WHERE project_id = NEW.project_id;
  
  IF NEW.status = 'APPROVED' THEN
    SET notification_title = 'Project Approved';
    SET notification_message = CONCAT('Your project has been approved with a score of ', NEW.score);
  ELSEIF NEW.status = 'REJECTED' THEN
    SET notification_title = 'Project Rejected';
    SET notification_message = 'Your project has been rejected. Please review the feedback.';
  ELSE
    SET notification_title = 'Project Reviewed';
    SET notification_message = 'Your project has been reviewed.';
  END IF;
  
  INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `related_entity_type`, `related_entity_id`)
  VALUES (student_user_id, 'PROJECT_REVIEW', notification_title, notification_message, 'PROJECT', NEW.project_id);
END$$
DELIMITER ;

-- Trigger: Create notification on post comment
DELIMITER $$
CREATE TRIGGER `notify_author_on_comment`
AFTER INSERT ON `comments`
FOR EACH ROW
BEGIN
  DECLARE post_author_id INT;
  
  SELECT author_id INTO post_author_id FROM posts WHERE post_id = NEW.post_id;
  
  IF post_author_id != NEW.user_id THEN
    INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `related_entity_type`, `related_entity_id`)
    VALUES (post_author_id, 'POST_COMMENT', 'New Comment', 'Someone commented on your post', 'POST', NEW.post_id);
  END IF;
END$$
DELIMITER ;

-- Trigger: Create notification on post interaction (like, love, etc.)
DELIMITER $$
CREATE TRIGGER `notify_author_on_interaction`
AFTER INSERT ON `post_interactions`
FOR EACH ROW
BEGIN
  DECLARE post_author_id INT;
  DECLARE interaction_type_text VARCHAR(50);
  
  SELECT author_id INTO post_author_id FROM posts WHERE post_id = NEW.post_id;
  
  IF post_author_id != NEW.user_id AND NEW.type IN ('LIKE', 'LOVE', 'CELEBRATE') THEN
    SET interaction_type_text = LOWER(NEW.type);
    INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `related_entity_type`, `related_entity_id`)
    VALUES (post_author_id, 'POST_LIKE', 'Post Reaction', CONCAT('Someone reacted to your post with ', interaction_type_text), 'POST', NEW.post_id);
  END IF;
END$$
DELIMITER ;

-- Trigger: Create notification on chat mention
DELIMITER $$
CREATE TRIGGER `notify_user_on_mention`
AFTER INSERT ON `chat_mentions`
FOR EACH ROW
BEGIN
  DECLARE room_id_val INT;
  
  SELECT room_id INTO room_id_val FROM chat_messages WHERE message_id = NEW.message_id;
  
  INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `related_entity_type`, `related_entity_id`)
  VALUES (NEW.user_id, 'CHAT_MENTION', 'You were mentioned', 'You were mentioned in a chat message', 'CHAT_MESSAGE', NEW.message_id);
END$$
DELIMITER ;

-- ======================================================
-- INITIAL DATA SEEDING
-- ======================================================

-- Insert default skill categories
INSERT INTO `skill_categories` (`name`, `description`, `icon`) VALUES
('Programming Languages', 'Programming and scripting languages', 'code'),
('Web Development', 'Frontend and backend web technologies', 'web'),
('Mobile Development', 'Mobile app development frameworks', 'mobile'),
('Database', 'Database management systems', 'database'),
('DevOps', 'Development operations and CI/CD', 'cloud'),
('Design', 'UI/UX and graphic design tools', 'palette'),
('Soft Skills', 'Communication and collaboration skills', 'people');

-- Insert common skills
INSERT INTO `skills` (`category_id`, `name`, `description`) VALUES
(1, 'JavaScript', 'JavaScript programming language'),
(1, 'Python', 'Python programming language'),
(1, 'Java', 'Java programming language'),
(1, 'C++', 'C++ programming language'),
(1, 'PHP', 'PHP programming language'),
(2, 'React', 'React JavaScript library'),
(2, 'Vue.js', 'Vue.js JavaScript framework'),
(2, 'Node.js', 'Node.js runtime environment'),
(2, 'Laravel', 'Laravel PHP framework'),
(3, 'React Native', 'React Native mobile framework'),
(3, 'Flutter', 'Flutter mobile framework'),
(3, 'Swift', 'Swift for iOS development'),
(4, 'MySQL', 'MySQL database'),
(4, 'PostgreSQL', 'PostgreSQL database'),
(4, 'MongoDB', 'MongoDB NoSQL database'),
(5, 'Docker', 'Docker containerization'),
(5, 'Kubernetes', 'Kubernetes orchestration'),
(5, 'Git', 'Git version control'),
(6, 'Figma', 'Figma design tool'),
(6, 'Adobe XD', 'Adobe XD design tool'),
(7, 'Communication', 'Effective communication skills'),
(7, 'Teamwork', 'Team collaboration skills'),
(7, 'Leadership', 'Leadership and management skills');

-- ======================================================
-- END OF SCHEMA
-- ======================================================
