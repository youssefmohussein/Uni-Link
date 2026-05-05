-- =====================================================
-- Terra Fusion Admin Dashboard - Database Schema
-- Users Table with Complete Structure
-- =====================================================

-- Create users table if it doesn't exist
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `role_id` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Role Mapping Reference:
-- role_id | Role Name
-- --------|-------------
--    4    | Manager
--    3    | Chef Boss
--    2    | Table Manager
--    1    | Waiter
-- =====================================================

-- Clean up existing test users (optional - uncomment if needed)
-- DELETE FROM users WHERE username IN ('manager', 'chef', 'tablemanager', 'waiter');

-- Insert test users
-- All passwords are: "password123"
-- Password hash generated with: password_hash('password123', PASSWORD_DEFAULT)

-- 1. Manager (role_id: 4) - Full Access
INSERT INTO users (username, password_hash, role_id) VALUES 
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4);

-- 2. Chef Boss (role_id: 3) - Menu + Orders
INSERT INTO users (username, password_hash, role_id) VALUES 
('chef', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3);

-- 3. Table Manager (role_id: 2) - Reservations + Orders
INSERT INTO users (username, password_hash, role_id) VALUES 
('tablemanager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2);

-- 4. Waiter (role_id: 1) - Orders Only
INSERT INTO users (username, password_hash, role_id) VALUES 
('waiter', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Verify the users were created successfully
SELECT 
    id, 
    username, 
    role_id,
    CASE role_id
        WHEN 4 THEN 'Manager'
        WHEN 3 THEN 'Chef Boss'
        WHEN 2 THEN 'Table Manager'
        WHEN 1 THEN 'Waiter'
        ELSE 'Unknown'
    END as role_name,
    created_at
FROM users 
WHERE username IN ('manager', 'chef', 'tablemanager', 'waiter')
ORDER BY role_id DESC;
