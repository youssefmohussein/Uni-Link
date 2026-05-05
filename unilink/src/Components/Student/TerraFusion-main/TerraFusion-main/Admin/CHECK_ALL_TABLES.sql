-- =====================================================
-- STEP-BY-STEP DATABASE STRUCTURE CHECK
-- Run each section ONE AT A TIME and tell me the results
-- =====================================================

-- STEP 1: Check users table structure
-- Copy this, paste in phpMyAdmin, click Go
DESCRIBE users;

-- Expected columns: user_id, username, password_hash, full_name, role, created_at
-- Tell me what you see!

-- =====================================================

-- STEP 2: Check menu_items table structure
-- Copy this, paste in phpMyAdmin, click Go
DESCRIBE menu_items;

-- Tell me what columns you see!

-- =====================================================

-- STEP 3: Check orders table structure
-- Copy this, paste in phpMyAdmin, click Go
DESCRIBE orders;

-- Tell me what columns you see!

-- =====================================================

-- STEP 4: Check order_details table structure
-- Copy this, paste in phpMyAdmin, click Go
DESCRIBE order_details;

-- Tell me what columns you see!

-- =====================================================

-- STEP 5: Check reservations table structure
-- Copy this, paste in phpMyAdmin, click Go
DESCRIBE reservations;

-- Tell me what columns you see!
-- IMPORTANT: Is the primary key 'id' or 'reservation_id'?

-- =====================================================
