-- =====================================================
-- STEP 1: INSERT TEST USERS
-- Copy this entire section and paste into phpMyAdmin
-- =====================================================

-- Insert Manager
INSERT INTO users (username, password_hash, full_name, role) VALUES 
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Manager', 'Manager')
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    full_name = 'System Manager',
    role = 'Manager';

-- Insert Chef Boss
INSERT INTO users (username, password_hash, full_name, role) VALUES 
('chef', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Head Chef', 'Chef Boss')
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    full_name = 'Head Chef',
    role = 'Chef Boss';

-- Insert Table Manager
INSERT INTO users (username, password_hash, full_name, role) VALUES 
('tablemanager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Table Manager', 'Table Manager')
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    full_name = 'Table Manager',
    role = 'Table Manager';

-- Insert Waiter
INSERT INTO users (username, password_hash, full_name, role) VALUES 
('waiter', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Staff Waiter', 'Waiter')
ON DUPLICATE KEY UPDATE 
    password_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    full_name = 'Staff Waiter',
    role = 'Waiter';

-- =====================================================
-- VERIFY: Check that users were created
-- =====================================================
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

-- =====================================================
-- LOGIN CREDENTIALS
-- =====================================================
-- Username: manager      | Password: password123 | Role: Manager
-- Username: chef         | Password: password123 | Role: Chef Boss
-- Username: tablemanager | Password: password123 | Role: Table Manager
-- Username: waiter       | Password: password123 | Role: Waiter
--
-- Login URL: http://localhost/Admin/public
-- =====================================================
