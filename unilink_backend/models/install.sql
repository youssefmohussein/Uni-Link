-- CREATE DATABASE IF NOT EXISTS unilink_Db;
-- USE unilink_Db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100),
  email VARCHAR(100),
  password VARCHAR(255)
);
INSERT INTO users (username, email, password) VALUES
('youssef', 'youssef@example.com', '123456'),
('admin', 'admin@example.com', 'adminpass'),
('mohameddd', 'admisssn@example.com', '12345');
