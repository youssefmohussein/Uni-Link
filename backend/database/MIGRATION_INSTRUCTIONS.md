# Database Migration Instructions

## ✅ Option 1: Run via Browser (Easiest)

I've created a web-accessible migration script for you.

### Steps:

1. **Access the migration script**:
   ```
   http://localhost/backend/database/web-migrate.php
   ```

2. **You should see JSON output** with:
   - Tables created
   - Triggers created
   - Data seeded
   - List of all tables

3. **IMPORTANT**: After successful migration, **DELETE** the `web-migrate.php` file for security!

---

## 🔧 Option 2: Import SQL Directly via phpMyAdmin

If you have phpMyAdmin installed (comes with XAMPP/WAMP):

1. **Open phpMyAdmin**: `http://localhost/phpmyadmin`

2. **Select your database** (or create one if needed):
   - Click "New" to create database
   - Name it `unilink` (or whatever you prefer)
   - Collation: `utf8mb4_unicode_ci`

3. **Import the SQL file**:
   - Click on your database name
   - Click "Import" tab
   - Click "Choose File"
   - Navigate to: `c:\Develope Tools\Github\Uni-Link\backend\database\schema.sql`
   - Click "Go"

4. **Verify**:
   - You should see 23 tables in the left sidebar
   - Click "Triggers" tab to see 5 triggers

---

## 🗄️ Option 3: Direct MySQL Command (If MySQL is in PATH)

```bash
# Navigate to backend directory
cd "c:\Develope Tools\Github\Uni-Link\backend"

# Import SQL file
mysql -u root -p unilink < database/schema.sql
```

---

## 📋 What Should Happen

After successful migration, you should have:

### Tables (23 total):
- `users`, `admins`, `professors`, `students`
- `posts`, `comments`, `post_media`, `post_interactions`
- `projects`, `project_reviews`, `project_skills`
- `chat_rooms`, `room_members`, `chat_messages`, `chat_mentions`
- `notifications`
- `cvs`, `skills`, `skill_categories`, `user_skills`
- `faculties`, `majors`, `announcements`

### Triggers (5 total):
- `increment_student_points_on_approval`
- `notify_student_on_project_review`
- `notify_author_on_comment`
- `notify_author_on_interaction`
- `notify_user_on_mention`

### Seeded Data:
- 7 skill categories
- 23 skills

---

## ✅ Verify Migration Success

Run this in phpMyAdmin SQL tab or MySQL command line:

```sql
-- Check tables
SHOW TABLES;

-- Check triggers
SHOW TRIGGERS;

-- Check seeded data
SELECT COUNT(*) as skill_count FROM skills;
SELECT COUNT(*) as category_count FROM skill_categories;

-- Verify a table structure
DESCRIBE users;
DESCRIBE notifications;
```

Expected results:
- 23 tables
- 5 triggers
- 23 skills
- 7 skill categories

---

## 🚨 Troubleshooting

### Error: "Access denied"
- Check your database credentials in `backend/.env`
- Make sure MySQL is running

### Error: "Table already exists"
- Drop existing tables first, or
- Use the rollback feature in `migrate.php`

### Error: "Unknown database"
- Create the database first in phpMyAdmin
- Or run: `CREATE DATABASE unilink CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`

---

## 🎯 Recommended: Use Option 1 (Browser)

Since you have a web server running, the easiest way is:

**Go to**: `http://localhost/backend/database/web-migrate.php`

This will show you exactly what happened during migration!
