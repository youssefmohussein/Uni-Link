-- =====================================================
-- COPY AND PASTE THIS ENTIRE SCRIPT INTO PHPMYADMIN
-- Database: terra_fusion_db
-- Table: users
-- =====================================================

-- Step 1: Check if your users table exists and what columns it has
DESCRIBE users;

-- Step 2: If the table has 'password' column instead of 'password_hash', 
-- run this to rename it:
-- ALTER TABLE users CHANGE COLUMN `password` `password_hash` VARCHAR(255) NOT NULL;

-- Step 3: Clean up any existing test users (optional)
-- DELETE FROM users WHERE username IN ('manager', 'chef', 'tablemanager', 'waiter');

-- Step 4: Insert test users
-- All passwords are: "password123"

INSERT INTO users (username, password_hash, role_id) VALUES 
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4)
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role_id = 4;

INSERT INTO users (username, password_hash, role_id) VALUES 
('chef', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3)
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role_id = 3;

INSERT INTO users (username, password_hash, role_id) VALUES 
('tablemanager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2)
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role_id = 2;

INSERT INTO users (username, password_hash, role_id) VALUES 
('waiter', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1)
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    role_id = 1;

-- Step 5: Verify the users were created
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
    LEFT(password_hash, 20) as password_preview
FROM users 
WHERE username IN ('manager', 'chef', 'tablemanager', 'waiter')
ORDER BY role_id DESC;
