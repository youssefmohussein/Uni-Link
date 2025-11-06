-- Final UniLink SQL (phpMyAdmin-friendly)

CREATE DATABASE IF NOT EXISTS unilink_db;
USE unilink_db;

-- Faculties & Majors
CREATE TABLE IF NOT EXISTS Faculty (
    faculty_id INT AUTO_INCREMENT PRIMARY KEY,
    faculty_name VARCHAR(150) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Major (
    major_id INT AUTO_INCREMENT PRIMARY KEY,
    major_name VARCHAR(200) NOT NULL,
    faculty_id INT NOT NULL,
    FOREIGN KEY (faculty_id) REFERENCES Faculty(faculty_id) ON DELETE CASCADE
);

-- Users (abstract)
CREATE TABLE IF NOT EXISTS Users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255),
    bio TEXT,
    job_title VARCHAR(100),
    role ENUM('Student', 'Professor', 'Admin') NOT NULL,
    faculty_id INT,
    major_id INT,
    FOREIGN KEY (faculty_id) REFERENCES Faculty(faculty_id),
    FOREIGN KEY (major_id) REFERENCES Major(major_id)
);
EIGN KEY (major_id) REFERENCES Major(major_id)
);

-- Student table
CREATE TABLE IF NOT EXISTS Student (
    student_id INT PRIMARY KEY,
    year INT,
    gpa FLOAT,
    FOREIGN KEY (student_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Professor table
CREATE TABLE IF NOT EXISTS Professor (
    professor_id INT PRIMARY KEY,
    department VARCHAR(100),
    specialization VARCHAR(150),
    FOREIGN KEY (professor_id) REFERENCES Users(user_id) ON DELETE CASCADE
);

-- Admin table
CREATE TABLE IF NOT EXISTS Admin (
    admin_id INT PRIMARY KEY,
    privilege_level ENUM('Super', 'Standard', 'Limited') DEFAULT 'Standard',
    created_by_admin_id INT,
    FOREIGN KEY (admin_id) REFERENCES Users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_admin_id) REFERENCES Users(user_id)
);

-- Insert static faculties
INSERT IGNORE INTO Faculty (faculty_name) VALUES
('Faculty of Al-Alsun'),
('Faculty of Business Administration and International Trade'),
('Faculty of Computer Science'),
('Faculty of Engineering Sciences & Arts'),
('Faculty of Pharmacy'),
('Faculty of Oral and Dental Medicine'),
('Faculty of Mass Communication');

-- Insert static majors (IDs assume insert order above)
-- Al-Alsun (1)
INSERT IGNORE INTO Major (major_name, faculty_id) VALUES
('Teaching English as a Foreign Language Program (TEFL/TT)', 1);

-- Business (2)
INSERT IGNORE INTO Major (major_name, faculty_id) VALUES
('Accounting', 2),
('Economics & International Trade', 2),
('Entrepreneurship', 2),
('Finance', 2),
('Human Resource Management', 2),
('Marketing Management & Communication', 2),
('Supply Chain Management', 2),
('Business Intelligence', 2);

-- Computer Science (3)
INSERT IGNORE INTO Major (major_name, faculty_id) VALUES
('Computer Science (CS)', 3),
('Data and Information Systems (IS)', 3),
('Software Engineering (SE)', 3);

-- Engineering Sciences & Arts (4)
INSERT IGNORE INTO Major (major_name, faculty_id) VALUES
('Communication Systems', 4),
('Networks', 4),
('Smart Systems', 4),
('Computational Architecture', 4),
('Conservation of Architecture', 4),
('Environmental & Sustainable Architecture', 4),
('Interior Design', 4),
('Landscape Architecture', 4),
('Real Estate Management', 4);

-- Pharmacy (5)
INSERT IGNORE INTO Major (major_name, faculty_id) VALUES
('Pharm D', 5),
('Pharm-D Clinical', 5);

-- Mass Communication (7)
INSERT IGNORE INTO Major (major_name, faculty_id) VALUES
('Audio-Visual Production (AVP)', 7),
('Integrated Marketing Communication (IMC)', 7),
('News Production (NP)', 7);
