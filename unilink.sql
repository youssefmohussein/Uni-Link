-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2026 at 02:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `unilink`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`permissions`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `user_id`, `permissions`, `created_at`) VALUES
(1, 1, '[\"all\"]', '2026-04-27 17:32:55');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `priority` enum('LOW','MEDIUM','HIGH','URGENT') DEFAULT 'MEDIUM',
  `target_audience` enum('ALL','STUDENTS','PROFESSORS','FACULTY','MAJOR') DEFAULT 'ALL',
  `faculty_id` int(11) DEFAULT NULL,
  `major_id` int(11) DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_mentions`
--

CREATE TABLE `chat_mentions` (
  `mention_id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `message_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `message_type` enum('TEXT','FILE','IMAGE','VOICE','SYSTEM') DEFAULT 'TEXT',
  `file_path` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`message_id`, `room_id`, `sender_id`, `content`, `message_type`, `file_path`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'AB4ehc1HuqybyTiFYvfD3BjOo1b7DsazzkOfHlOVKo7Wuzqiv0Ol3aGGAhstI95gXXekDQt/wSyESpbYJGHe/A==', 'TEXT', NULL, '2026-04-29 23:46:08', '2026-04-29 23:46:08'),
(2, 1, 2, 'yKJAl+NtY6lfKfadT14YwCZZ2ED3qekhcdbIvX8EI4xG8nFDry5DQeEk0XHVvqh/MFJSHuTHfAa9afzQaKNXWQ==', 'VOICE', 'public/uploads/chat/chat_69f29883ddde0.webm', '2026-04-29 23:47:15', '2026-04-29 23:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `chat_rooms`
--

CREATE TABLE `chat_rooms` (
  `room_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `photo_url` varchar(500) DEFAULT NULL,
  `is_private` tinyint(1) DEFAULT 0,
  `faculty_id` int(11) DEFAULT NULL,
  `professor_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_rooms`
--

INSERT INTO `chat_rooms` (`room_id`, `name`, `description`, `password_hash`, `owner_id`, `photo_url`, `is_private`, `faculty_id`, `professor_id`, `created_at`, `updated_at`) VALUES
(1, '1111', NULL, '$2y$10$m59c72xRhD09.MNEgL/NkOREkFxerjHToLFFxxrV7qkZx/6s5Fbaa', 2, 'public/uploads/room_photos/69f2983bd3447_1777506363.png', 0, 3, 3, '2026-04-29 22:46:04', '2026-04-29 23:46:04'),
(2, 'Study Group', 'General study group', NULL, 201, NULL, 0, NULL, NULL, '2026-04-30 00:18:05', '2026-04-30 00:18:05');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `post_id`, `user_id`, `content`, `parent_comment_id`, `created_at`, `updated_at`) VALUES
(1, 2, 202, 'I am interested! DM me.', NULL, '2026-04-30 00:18:05', '2026-04-30 00:18:05'),
(2, 1, 201, 'Thank you Dr. Smith!', NULL, '2026-04-30 00:18:05', '2026-04-30 00:18:05');

--
-- Triggers `comments`
--
DELIMITER $$
CREATE TRIGGER `notify_author_on_comment` AFTER INSERT ON `comments` FOR EACH ROW BEGIN
  DECLARE post_author_id INT;
  
  SELECT author_id INTO post_author_id FROM posts WHERE post_id = NEW.post_id;
  
  IF post_author_id != NEW.user_id THEN
    INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `related_entity_type`, `related_entity_id`)
    VALUES (post_author_id, 'POST_COMMENT', 'New Comment', 'Someone commented on your post', 'POST', NEW.post_id);
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `cvs`
--

CREATE TABLE `cvs` (
  `cv_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `size` bigint(20) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `faculty_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`faculty_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Faculty of Engineering', 'Engineering and Technology', '2026-04-27 17:35:34', '2026-04-27 17:35:34'),
(2, 'Faculty of Science', 'Natural Sciences', '2026-04-27 17:35:34', '2026-04-27 17:35:34'),
(3, 'Faculty of Commerce', 'Business and Finance', '2026-04-27 17:35:34', '2026-04-27 17:35:34');

-- --------------------------------------------------------

--
-- Table structure for table `majors`
--

CREATE TABLE `majors` (
  `major_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `majors`
--

INSERT INTO `majors` (`major_id`, `faculty_id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'Computer Engineering', NULL, '2026-04-27 17:35:34', '2026-04-27 17:35:34'),
(2, 1, 'Electrical Engineering', NULL, '2026-04-27 17:35:34', '2026-04-27 17:35:34'),
(3, 2, 'Computer Science', NULL, '2026-04-27 17:35:34', '2026-04-27 17:35:34'),
(4, 2, 'Physics', NULL, '2026-04-27 17:35:34', '2026-04-27 17:35:34'),
(5, 3, 'e commerce', NULL, '2026-04-30 00:14:25', '2026-04-30 00:14:25'),
(6, 3, 'Artificial Intelligence', 'Study of AI and machine learning', '2026-04-30 00:18:05', '2026-04-30 00:18:05');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('POST_LIKE','POST_COMMENT','POST_SHARE','PROJECT_REVIEW','PROJECT_APPROVED','PROJECT_REJECTED','PROJECT_GRADED','CHAT_MENTION','CHAT_MESSAGE','SYSTEM') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `related_entity_type` enum('POST','COMMENT','PROJECT','CHAT_MESSAGE','USER') DEFAULT NULL,
  `related_entity_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `type`, `title`, `message`, `related_entity_type`, `related_entity_id`, `is_read`, `created_at`) VALUES
(1, 2, 'POST_COMMENT', 'New Comment', 'Someone commented on your post', 'POST', 2, 0, '2026-04-30 00:18:05'),
(2, 2, 'POST_COMMENT', 'New Comment', 'Someone commented on your post', 'POST', 1, 0, '2026-04-30 00:18:05');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `category` varchar(100) DEFAULT 'General',
  `visibility` enum('PUBLIC','FACULTY','MAJOR','PRIVATE') DEFAULT 'PUBLIC',
  `status` enum('DRAFT','PUBLISHED','ARCHIVED','MODERATED') DEFAULT 'PUBLISHED',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`post_id`, `author_id`, `content`, `category`, `visibility`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, ',,', 'General', 'PUBLIC', 'PUBLISHED', '2026-04-29 22:39:17', '2026-04-29 23:39:17'),
(2, 2, 'kk', 'Announcements', 'PUBLIC', 'PUBLISHED', '2026-04-29 22:39:44', '2026-04-29 23:39:44'),
(3, 204, 'Looking for study group for Marketing 101.', 'Study Group', 'MAJOR', 'PUBLISHED', '2026-04-30 00:18:05', '2026-04-30 00:18:05');

-- --------------------------------------------------------

--
-- Table structure for table `post_interactions`
--

CREATE TABLE `post_interactions` (
  `interaction_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('LIKE','LOVE','CELEBRATE','SAVE','SHARE') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `post_interactions`
--
DELIMITER $$
CREATE TRIGGER `notify_author_on_interaction` AFTER INSERT ON `post_interactions` FOR EACH ROW BEGIN
  DECLARE post_author_id INT;
  DECLARE interaction_type_text VARCHAR(50);
  
  SELECT author_id INTO post_author_id FROM posts WHERE post_id = NEW.post_id;
  
  IF post_author_id != NEW.user_id AND NEW.type IN ('LIKE', 'LOVE', 'CELEBRATE') THEN
    SET interaction_type_text = LOWER(NEW.type);
    INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `related_entity_type`, `related_entity_id`)
    VALUES (post_author_id, 'POST_LIKE', 'Post Reaction', CONCAT('Someone reacted to your post with ', interaction_type_text), 'POST', NEW.post_id);
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `post_media`
--

CREATE TABLE `post_media` (
  `media_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `type` enum('IMAGE','VIDEO','FILE') NOT NULL,
  `path` varchar(500) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `size` bigint(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `post_media`
--

INSERT INTO `post_media` (`media_id`, `post_id`, `type`, `path`, `filename`, `mime_type`, `size`, `created_at`) VALUES
(1, 2, 'IMAGE', 'public/uploads/media/media_69f296c08165e.png', NULL, NULL, NULL, '2026-04-29 23:39:44');

-- --------------------------------------------------------

--
-- Table structure for table `professors`
--

CREATE TABLE `professors` (
  `professor_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `academic_rank` varchar(100) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `office_location` varchar(255) DEFAULT NULL,
  `office_hours` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `professors`
--

INSERT INTO `professors` (`professor_id`, `user_id`, `academic_rank`, `department`, `office_location`, `office_hours`, `created_at`) VALUES
(1, 3, 'Associate Professor', 'Computer Science', 'Building A, Room 302', NULL, '2026-04-27 17:36:02'),
(2, 101, 'Full Professor', 'Software Engineering', 'Room 404', NULL, '2026-04-30 00:18:05'),
(3, 102, 'Associate Professor', 'Marketing', 'Room 200', NULL, '2026-04-30 00:18:05');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') DEFAULT 'PENDING',
  `grade` decimal(5,2) DEFAULT NULL,
  `supervisor_id` int(11) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `student_id`, `title`, `description`, `file_path`, `filename`, `status`, `grade`, `supervisor_id`, `submitted_at`, `updated_at`) VALUES
(1, 201, 'Uni-Link Web App', 'A university collaboration platform built with React and PHP.', NULL, NULL, 'APPROVED', NULL, 101, '2026-04-30 00:18:05', '2026-04-30 00:18:05'),
(2, 2, 'eee', 'eee', 'F:\\GG store\\Uni-Link\\backend\\app\\Controllers/../../public/uploads/projects/69f29da4c3d61_1777507748.pdf', NULL, 'APPROVED', 85.50, NULL, '2026-04-29 23:09:08', '2026-04-30 00:10:05');

--
-- Triggers `projects`
--
DELIMITER $$
CREATE TRIGGER `increment_student_points_on_approval` AFTER UPDATE ON `projects` FOR EACH ROW BEGIN
  IF NEW.status = 'APPROVED' AND OLD.status != 'APPROVED' THEN
    UPDATE `students` 
    SET `points` = `points` + 10 
    WHERE `user_id` = NEW.student_id;
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `project_reviews`
--

CREATE TABLE `project_reviews` (
  `review_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `professor_id` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `status` enum('PENDING','APPROVED','REJECTED') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Triggers `project_reviews`
--
DELIMITER $$
CREATE TRIGGER `notify_student_on_project_review` AFTER INSERT ON `project_reviews` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `project_skills`
--

CREATE TABLE `project_skills` (
  `project_skill_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_members`
--

CREATE TABLE `room_members` (
  `membership_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('ADMIN','MODERATOR','MEMBER') DEFAULT 'MEMBER',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `room_members`
--

INSERT INTO `room_members` (`membership_id`, `room_id`, `user_id`, `role`, `joined_at`) VALUES
(1, 1, 2, 'ADMIN', '2026-04-29 23:46:04'),
(2, 1, 101, 'ADMIN', '2026-04-30 00:18:05'),
(3, 1, 201, 'MEMBER', '2026-04-30 00:18:05'),
(4, 1, 202, 'MEMBER', '2026-04-30 00:18:05'),
(5, 2, 201, 'ADMIN', '2026-04-30 00:18:05'),
(6, 2, 203, 'MEMBER', '2026-04-30 00:18:05'),
(7, 2, 204, 'MEMBER', '2026-04-30 00:18:05');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `skill_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`skill_id`, `category_id`, `name`, `description`, `created_at`) VALUES
(1, 1, 'JavaScript', 'JavaScript programming language', '2026-04-27 17:31:08'),
(2, 1, 'Python', 'Python programming language', '2026-04-27 17:31:08'),
(3, 1, 'Java', 'Java programming language', '2026-04-27 17:31:08'),
(4, 1, 'C++', 'C++ programming language', '2026-04-27 17:31:08'),
(5, 1, 'PHP', 'PHP programming language', '2026-04-27 17:31:08'),
(6, 2, 'React', 'React JavaScript library', '2026-04-27 17:31:08'),
(7, 2, 'Vue.js', 'Vue.js JavaScript framework', '2026-04-27 17:31:08'),
(8, 2, 'Node.js', 'Node.js runtime environment', '2026-04-27 17:31:08'),
(9, 2, 'Laravel', 'Laravel PHP framework', '2026-04-27 17:31:08'),
(10, 3, 'React Native', 'React Native mobile framework', '2026-04-27 17:31:08'),
(11, 3, 'Flutter', 'Flutter mobile framework', '2026-04-27 17:31:08'),
(12, 3, 'Swift', 'Swift for iOS development', '2026-04-27 17:31:08'),
(13, 4, 'MySQL', 'MySQL database', '2026-04-27 17:31:08'),
(14, 4, 'PostgreSQL', 'PostgreSQL database', '2026-04-27 17:31:08'),
(15, 4, 'MongoDB', 'MongoDB NoSQL database', '2026-04-27 17:31:08'),
(16, 5, 'Docker', 'Docker containerization', '2026-04-27 17:31:08'),
(17, 5, 'Kubernetes', 'Kubernetes orchestration', '2026-04-27 17:31:08'),
(18, 5, 'Git', 'Git version control', '2026-04-27 17:31:08'),
(19, 6, 'Figma', 'Figma design tool', '2026-04-27 17:31:08'),
(20, 6, 'Adobe XD', 'Adobe XD design tool', '2026-04-27 17:31:08'),
(21, 7, 'Communication', 'Effective communication skills', '2026-04-27 17:31:08'),
(22, 7, 'Teamwork', 'Team collaboration skills', '2026-04-27 17:31:08'),
(23, 7, 'Leadership', 'Leadership and management skills', '2026-04-27 17:31:08'),
(24, 9, 'bb', NULL, '2026-04-30 00:04:37'),
(25, 10, 'ss', NULL, '2026-04-30 00:08:03');

-- --------------------------------------------------------

--
-- Table structure for table `skill_categories`
--

CREATE TABLE `skill_categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `skill_categories`
--

INSERT INTO `skill_categories` (`category_id`, `name`, `description`, `icon`, `created_at`) VALUES
(1, 'Programming Languages', 'Programming and scripting languages', 'code', '2026-04-27 17:31:08'),
(2, 'Web Development', 'Frontend and backend web technologies', 'web', '2026-04-27 17:31:08'),
(3, 'Mobile Development', 'Mobile app development frameworks', 'mobile', '2026-04-27 17:31:08'),
(4, 'Database', 'Database management systems', 'database', '2026-04-27 17:31:08'),
(5, 'DevOps', 'Development operations and CI/CD', 'cloud', '2026-04-27 17:31:08'),
(6, 'Design', 'UI/UX and graphic design tools', 'palette', '2026-04-27 17:31:08'),
(7, 'Soft Skills', 'Communication and collaboration skills', 'people', '2026-04-27 17:31:08'),
(8, 'dd', NULL, NULL, '2026-04-29 23:58:28'),
(9, 'vbv', NULL, NULL, '2026-04-30 00:04:37'),
(10, 'ss', NULL, NULL, '2026-04-30 00:08:03');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `year` int(11) DEFAULT NULL,
  `gpa` decimal(3,2) DEFAULT NULL,
  `points` int(11) DEFAULT 0,
  `enrollment_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `year`, `gpa`, `points`, `enrollment_date`, `created_at`) VALUES
(1, 2, 3, 3.80, 80, '2026-04-27', '2026-04-27 17:36:02'),
(2, 4, 1, 0.00, 0, '2026-04-30', '2026-04-30 00:15:04'),
(3, 201, 3, 3.80, 150, NULL, '2026-04-30 00:18:05'),
(4, 202, 4, 3.90, 200, NULL, '2026-04-30 00:18:05'),
(5, 203, 2, 3.20, 50, NULL, '2026-04-30 00:18:05'),
(6, 204, 1, 0.00, 10, NULL, '2026-04-30 00:18:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('ADMIN','PROFESSOR','STUDENT') NOT NULL,
  `faculty_id` int(11) DEFAULT NULL,
  `major_id` int(11) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `role`, `faculty_id`, `major_id`, `profile_picture`, `bio`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@unilink.com', '$2y$10$Mt5Kh9EHuo.axRfjVoBB0uSC9i/8pc.QWzzufPQ69cZAOwoLo36IC', 'ADMIN', NULL, NULL, 'uploads/defaults/admin.png', 'System Administrator', '2026-04-27 17:32:55', '2026-04-27 17:32:55'),
(2, 'student', 'student@unilink.com', '$2y$10$cziD2CoWMQ3Ir0C11dZvSOw/SGnrQzKJkAzS6xDJvDyIoJ4HSaFvm', 'STUDENT', 3, 1, 'uploads/defaults/student.png', 'Computer Science Student', '2026-04-27 17:36:02', '2026-04-27 17:36:02'),
(3, 'professor', 'prof@unilink.com', '$2y$10$ggCo/Nd51qrV9dfn4A9rxuiKOV3rOmTggFhkuQd0v2budVG572TTG', 'PROFESSOR', 3, 1, 'uploads/defaults/professor.png', 'Professor of Computer Science', '2026-04-27 17:36:02', '2026-04-27 17:36:02'),
(4, 'joe', 'youssefpls9@gmail.com', '$2y$10$6Y2Q2EBxIvjP.wK8IAM8yO5HqROYJqAX0tXh6oURsBiVqZGJKxut.', 'STUDENT', 3, 5, '', '', '2026-04-30 00:15:04', '2026-04-30 00:15:04'),
(101, 'dr_smith', 'smith@unilink.edu', '$2y$10$gzyrPjMMMpqdw6Mqr/5R9ur3T7Ia5r1Fytr0jQ0xsPBNddLv.yhlW', 'PROFESSOR', 3, 5, NULL, 'Professor of Software Engineering', '2026-04-30 00:18:05', '2026-04-30 00:18:05'),
(102, 'dr_jones', 'jones@unilink.edu', '$2y$10$gzyrPjMMMpqdw6Mqr/5R9ur3T7Ia5r1Fytr0jQ0xsPBNddLv.yhlW', 'PROFESSOR', 2, 4, NULL, 'Professor of Marketing', '2026-04-30 00:18:05', '2026-04-30 00:18:05'),
(201, 'alice', 'alice@unilink.edu', '$2y$10$gzyrPjMMMpqdw6Mqr/5R9ur3T7Ia5r1Fytr0jQ0xsPBNddLv.yhlW', 'STUDENT', 3, 5, NULL, 'Passionate about coding', '2026-04-30 00:18:05', '2026-04-30 00:18:05'),
(202, 'bob', 'bob@unilink.edu', '$2y$10$gzyrPjMMMpqdw6Mqr/5R9ur3T7Ia5r1Fytr0jQ0xsPBNddLv.yhlW', 'STUDENT', 3, 6, NULL, 'AI enthusiast', '2026-04-30 00:18:05', '2026-04-30 00:18:05'),
(203, 'charlie', 'charlie@unilink.edu', '$2y$10$gzyrPjMMMpqdw6Mqr/5R9ur3T7Ia5r1Fytr0jQ0xsPBNddLv.yhlW', 'STUDENT', 1, 1, NULL, 'Mechanical eng student', '2026-04-30 00:18:05', '2026-04-30 00:18:05'),
(204, 'diana', 'diana@unilink.edu', '$2y$10$gzyrPjMMMpqdw6Mqr/5R9ur3T7Ia5r1Fytr0jQ0xsPBNddLv.yhlW', 'STUDENT', 2, 4, NULL, 'Marketing major', '2026-04-30 00:18:05', '2026-04-30 00:18:05');

-- --------------------------------------------------------

--
-- Table structure for table `user_skills`
--

CREATE TABLE `user_skills` (
  `user_skill_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skill_id` int(11) NOT NULL,
  `proficiency_level` enum('BEGINNER','INTERMEDIATE','ADVANCED','EXPERT') DEFAULT 'INTERMEDIATE',
  `years_of_experience` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_skills`
--

INSERT INTO `user_skills` (`user_skill_id`, `user_id`, `skill_id`, `proficiency_level`, `years_of_experience`, `created_at`) VALUES
(1, 2, 25, 'ADVANCED', NULL, '2026-04-30 00:08:03'),
(2, 201, 1, 'ADVANCED', NULL, '2026-04-30 00:18:05'),
(3, 201, 6, 'INTERMEDIATE', NULL, '2026-04-30 00:18:05'),
(4, 202, 2, 'EXPERT', NULL, '2026-04-30 00:18:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_admin_user` (`user_id`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `major_id` (`major_id`),
  ADD KEY `idx_announcement_admin` (`admin_id`),
  ADD KEY `idx_announcement_priority` (`priority`),
  ADD KEY `idx_announcement_audience` (`target_audience`),
  ADD KEY `idx_announcement_created` (`created_at`);

--
-- Indexes for table `chat_mentions`
--
ALTER TABLE `chat_mentions`
  ADD PRIMARY KEY (`mention_id`),
  ADD UNIQUE KEY `unique_message_mention` (`message_id`,`user_id`),
  ADD KEY `idx_mention_message` (`message_id`),
  ADD KEY `idx_mention_user` (`user_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `idx_message_room` (`room_id`),
  ADD KEY `idx_message_sender` (`sender_id`),
  ADD KEY `idx_message_created` (`created_at`);

--
-- Indexes for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `idx_room_owner` (`owner_id`),
  ADD KEY `idx_room_name` (`name`),
  ADD KEY `idx_room_created` (`created_at`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `idx_comment_post` (`post_id`),
  ADD KEY `idx_comment_user` (`user_id`),
  ADD KEY `idx_comment_parent` (`parent_comment_id`),
  ADD KEY `idx_comment_created` (`created_at`);

--
-- Indexes for table `cvs`
--
ALTER TABLE `cvs`
  ADD PRIMARY KEY (`cv_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_cv_user` (`user_id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`faculty_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_faculty_name` (`name`);

--
-- Indexes for table `majors`
--
ALTER TABLE `majors`
  ADD PRIMARY KEY (`major_id`),
  ADD UNIQUE KEY `unique_major_per_faculty` (`faculty_id`,`name`),
  ADD KEY `idx_major_faculty` (`faculty_id`),
  ADD KEY `idx_major_name` (`name`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_notification_user` (`user_id`),
  ADD KEY `idx_notification_type` (`type`),
  ADD KEY `idx_notification_read` (`is_read`),
  ADD KEY `idx_notification_created` (`created_at`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `idx_post_author` (`author_id`),
  ADD KEY `idx_post_status` (`status`),
  ADD KEY `idx_post_visibility` (`visibility`),
  ADD KEY `idx_post_created` (`created_at`);

--
-- Indexes for table `post_interactions`
--
ALTER TABLE `post_interactions`
  ADD PRIMARY KEY (`interaction_id`),
  ADD UNIQUE KEY `unique_user_post_interaction` (`post_id`,`user_id`,`type`),
  ADD KEY `idx_interaction_post` (`post_id`),
  ADD KEY `idx_interaction_user` (`user_id`),
  ADD KEY `idx_interaction_type` (`type`);

--
-- Indexes for table `post_media`
--
ALTER TABLE `post_media`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `idx_media_post` (`post_id`),
  ADD KEY `idx_media_type` (`type`);

--
-- Indexes for table `professors`
--
ALTER TABLE `professors`
  ADD PRIMARY KEY (`professor_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_professor_user` (`user_id`),
  ADD KEY `idx_professor_department` (`department`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `idx_project_student` (`student_id`),
  ADD KEY `idx_project_supervisor` (`supervisor_id`),
  ADD KEY `idx_project_status` (`status`),
  ADD KEY `idx_project_submitted` (`submitted_at`);

--
-- Indexes for table `project_reviews`
--
ALTER TABLE `project_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `idx_review_project` (`project_id`),
  ADD KEY `idx_review_professor` (`professor_id`),
  ADD KEY `idx_review_status` (`status`);

--
-- Indexes for table `project_skills`
--
ALTER TABLE `project_skills`
  ADD PRIMARY KEY (`project_skill_id`),
  ADD UNIQUE KEY `unique_project_skill` (`project_id`,`skill_id`),
  ADD KEY `idx_project_skill_project` (`project_id`),
  ADD KEY `idx_project_skill_skill` (`skill_id`);

--
-- Indexes for table `room_members`
--
ALTER TABLE `room_members`
  ADD PRIMARY KEY (`membership_id`),
  ADD UNIQUE KEY `unique_room_member` (`room_id`,`user_id`),
  ADD KEY `idx_member_room` (`room_id`),
  ADD KEY `idx_member_user` (`user_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`skill_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_skill_name` (`name`),
  ADD KEY `idx_skill_category` (`category_id`);

--
-- Indexes for table `skill_categories`
--
ALTER TABLE `skill_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_skill_category_name` (`name`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_student_user` (`user_id`),
  ADD KEY `idx_student_year` (`year`),
  ADD KEY `idx_student_points` (`points`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_email` (`email`),
  ADD KEY `idx_user_role` (`role`),
  ADD KEY `idx_user_faculty` (`faculty_id`),
  ADD KEY `idx_user_major` (`major_id`);

--
-- Indexes for table `user_skills`
--
ALTER TABLE `user_skills`
  ADD PRIMARY KEY (`user_skill_id`),
  ADD UNIQUE KEY `unique_user_skill` (`user_id`,`skill_id`),
  ADD KEY `idx_user_skill_user` (`user_id`),
  ADD KEY `idx_user_skill_skill` (`skill_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_mentions`
--
ALTER TABLE `chat_mentions`
  MODIFY `mention_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `cvs`
--
ALTER TABLE `cvs`
  MODIFY `cv_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `majors`
--
ALTER TABLE `majors`
  MODIFY `major_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `post_interactions`
--
ALTER TABLE `post_interactions`
  MODIFY `interaction_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_media`
--
ALTER TABLE `post_media`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `professors`
--
ALTER TABLE `professors`
  MODIFY `professor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_reviews`
--
ALTER TABLE `project_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_skills`
--
ALTER TABLE `project_skills`
  MODIFY `project_skill_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_members`
--
ALTER TABLE `room_members`
  MODIFY `membership_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `skill_categories`
--
ALTER TABLE `skill_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- AUTO_INCREMENT for table `user_skills`
--
ALTER TABLE `user_skills`
  MODIFY `user_skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admins`
--
ALTER TABLE `admins`
  ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `announcements_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `announcements_ibfk_3` FOREIGN KEY (`major_id`) REFERENCES `majors` (`major_id`) ON DELETE SET NULL;

--
-- Constraints for table `chat_mentions`
--
ALTER TABLE `chat_mentions`
  ADD CONSTRAINT `chat_mentions_ibfk_1` FOREIGN KEY (`message_id`) REFERENCES `chat_messages` (`message_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_mentions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `chat_rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  ADD CONSTRAINT `chat_rooms_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE;

--
-- Constraints for table `cvs`
--
ALTER TABLE `cvs`
  ADD CONSTRAINT `cvs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `majors`
--
ALTER TABLE `majors`
  ADD CONSTRAINT `majors_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `post_interactions`
--
ALTER TABLE `post_interactions`
  ADD CONSTRAINT `post_interactions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `post_interactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `post_media`
--
ALTER TABLE `post_media`
  ADD CONSTRAINT `post_media_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE;

--
-- Constraints for table `professors`
--
ALTER TABLE `professors`
  ADD CONSTRAINT `professors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `project_reviews`
--
ALTER TABLE `project_reviews`
  ADD CONSTRAINT `project_reviews_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_reviews_ibfk_2` FOREIGN KEY (`professor_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `project_skills`
--
ALTER TABLE `project_skills`
  ADD CONSTRAINT `project_skills_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `projects` (`project_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_skills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`skill_id`) ON DELETE CASCADE;

--
-- Constraints for table `room_members`
--
ALTER TABLE `room_members`
  ADD CONSTRAINT `room_members_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `chat_rooms` (`room_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `skill_categories` (`category_id`) ON DELETE SET NULL;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`major_id`) REFERENCES `majors` (`major_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_skills`
--
ALTER TABLE `user_skills`
  ADD CONSTRAINT `user_skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_skills_ibfk_2` FOREIGN KEY (`skill_id`) REFERENCES `skills` (`skill_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
