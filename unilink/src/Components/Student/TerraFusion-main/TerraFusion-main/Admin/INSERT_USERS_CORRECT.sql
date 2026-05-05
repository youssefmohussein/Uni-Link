-- =====================================================
-- CORRECT SQL FOR YOUR DATABASE STRUCTURE
-- Copy and paste this into phpMyAdmin SQL tab
-- =====================================================

-- Your table structure:
-- user_id (PRIMARY KEY)
-- username
-- password_hash
-- full_name
-- role (ENUM: 'Manager', 'Chef Boss', 'Table Manager', 'Waiter')
-- created_at

-- Clean up existing test users (optional - uncomment if needed)
-- DELETE FROM users WHERE username IN ('manager', 'chef', 'tablemanager', 'waiter');

-- Insert test users with CORRECT column names
-- All passwords are: "password123"

-- 1. Manager (Full Access)
INSERT INTO users (username, password_hash, full_name, role) VALUES 
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Manager', 'Manager')
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    full_name = 'System Manager',
    role = 'Manager';

-- 2. Chef Boss (Menu + Orders)
INSERT INTO users (username, password_hash, full_name, role) VALUES 
('chef', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Head Chef', 'Chef Boss')
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    full_name = 'Head Chef',
    role = 'Chef Boss';

-- 3. Table Manager (Reservations + Orders)
INSERT INTO users (username, password_hash, full_name, role) VALUES 
('tablemanager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Table Manager', 'Table Manager')
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    full_name = 'Table Manager',
    role = 'Table Manager';

-- 4. Waiter (Orders Only)
INSERT INTO users (username, password_hash, full_name, role) VALUES 
('waiter', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Waiter', 'Waiter')
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    full_name = 'Staff Waiter',
    role = 'Waiter';

-- Verify the users were created
SELECT 
    user_id, 
    username, 
    full_name,
    role,
    LEFT(password_hash, 30) as password_preview,
    created_at
FROM users 
WHERE username IN ('manager', 'chef', 'tablemanager', 'waiter')
ORDER BY 
    CASE role
        WHEN 'Manager' THEN 1
        WHEN 'Chef Boss' THEN 2
        WHEN 'Table Manager' THEN 3
        WHEN 'Waiter' THEN 4
    END;
