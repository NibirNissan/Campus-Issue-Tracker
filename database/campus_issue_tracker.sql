-- ============================================================
-- Campus Issue Tracker - Database Schema
-- Technology: MySQL (for XAMPP environment)
-- ============================================================

-- Create the database
CREATE DATABASE IF NOT EXISTS campus_issue_tracker
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_general_ci;

USE campus_issue_tracker;

-- ============================================================
-- 1. Users Table
--    Stores student and admin accounts.
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student', 'admin') NOT NULL DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 2. Complaints Table
--    Stores all complaints submitted by students.
-- ============================================================
CREATE TABLE IF NOT EXISTS complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    department ENUM('Lab', 'Class', 'Hostel', 'Library', 'Other') NOT NULL,
    description TEXT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    status ENUM('Pending', 'In Progress', 'Done') NOT NULL DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 3. Complaint Timeline Table
--    Tracks every status change for transparency and history.
-- ============================================================
CREATE TABLE IF NOT EXISTS complaint_timeline (
    id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT NOT NULL,
    changed_by INT NOT NULL,
    old_status ENUM('Pending', 'In Progress', 'Done') DEFAULT NULL,
    new_status ENUM('Pending', 'In Progress', 'Done') NOT NULL,
    remarks TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- 4. Seed Data - Default Admin Account
--    Password: admin123 (hashed with PHP password_hash)
-- ============================================================
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@campus.com', '$2y$10$m4Tlu3.TuFo4RHeoZ1VOmu/G4Sxcagv/PVzdF5Gtqi5Alx1MuCYbW', 'admin');

-- ============================================================
-- End of Schema
-- ============================================================
