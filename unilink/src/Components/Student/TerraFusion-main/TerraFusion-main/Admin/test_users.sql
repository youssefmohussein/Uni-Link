-- Terra Fusion Admin Dashboard - Test Users
-- Database: terra_fusion_db
-- 
-- This script creates test users for each role level to test RBAC functionality
-- All passwords are hashed using PHP's password_hash() function
-- Default password for all test users: "password123"
--
-- IMPORTANT: Column name is 'password_hash' NOT 'password'
--
-- Usage:
-- 1. Open phpMyAdmin or MySQL command line
-- 2. Select the 'terra_fusion_db' database
-- 3. Run this script
-- 4. Login with any of the credentials below

-- Clean up existing test users (optional - uncomment if needed)
-- DELETE FROM users WHERE username IN ('manager', 'chef', 'tablemanager', 'waiter');

-- 1. Manager (role_id: 4) - Full Access
-- Username: manager
-- Password: password123
-- Access: All pages (Dashboard, Orders, Menu, Reservations, Staff Management)
INSERT INTO users (username, password_hash, role_id) VALUES 
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 4);

-- 2. Chef Boss (role_id: 3) - Menu + Orders
-- Username: chef
-- Password: password123
-- Access: Dashboard, Orders, Menu
INSERT INTO users (username, password_hash, role_id) VALUES 
('chef', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3);

-- 3. Table Manager (role_id: 2) - Reservations + Orders
-- Username: tablemanager
-- Password: password123
-- Access: Dashboard, Orders, Reservations
INSERT INTO users (username, password_hash, role_id) VALUES 
('tablemanager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2);

-- 4. Waiter (role_id: 1) - Orders Only
-- Username: waiter
-- Password: password123
-- Access: Dashboard, Orders
INSERT INTO users (username, password_hash, role_id) VALUES 
('waiter', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- Verify the users were created
SELECT id, username, role_id, 
    CASE role_id
        WHEN 4 THEN 'Manager'
        WHEN 3 THEN 'Chef Boss'
        WHEN 2 THEN 'Table Manager'
        WHEN 1 THEN 'Waiter'
        ELSE 'Unknown'
    END as role_name
FROM users 
WHERE username IN ('manager', 'chef', 'tablemanager', 'waiter')
ORDER BY role_id DESC;
