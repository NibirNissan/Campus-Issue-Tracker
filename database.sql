-- Campus Issue Tracker Database Schema
-- For use with XAMPP (MySQL)

CREATE DATABASE IF NOT EXISTS campus_issue_tracker;
USE campus_issue_tracker;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
   id INT AUTO_INCREMENT PRIMARY KEY,
   name VARCHAR(100) NOT NULL,
   email VARCHAR(100) UNIQUE NOT NULL,
   password VARCHAR(255) NOT NULL,
   role ENUM('student', 'admin') DEFAULT 'student',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Complaints Table
CREATE TABLE IF NOT EXISTS complaints (
   id INT AUTO_INCREMENT PRIMARY KEY,
   user_id INT NOT NULL,
   title VARCHAR(255) NOT NULL,
   department ENUM('Lab', 'Class', 'Hostel', 'Library', 'Other') NOT NULL,
   description TEXT NOT NULL,
   image VARCHAR(255),
   status ENUM('Pending', 'In Progress', 'Done') DEFAULT 'Pending',
   created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample Admin User (password is 'admin123')
-- Note: In a real app, use password_hash() in PHP.
-- This is just for initial setup.
INSERT IGNORE INTO users (name, email, password, role) 
VALUES ('System Admin', 'admin@campus.edu', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
