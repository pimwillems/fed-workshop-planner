-- Manual MySQL Setup for Workshop Planner
-- Run this in phpMyAdmin if the web wizard fails

-- First, make sure you're using the correct database
-- USE your_database_name;

-- Drop tables if they exist (to start fresh)
DROP TABLE IF EXISTS workshops;
DROP TABLE IF EXISTS csrf_tokens;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('TEACHER', 'ADMIN') NOT NULL DEFAULT 'TEACHER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Indexes for performance
    INDEX idx_users_email (email),
    INDEX idx_users_role (role)
);

-- Create workshops table  
CREATE TABLE workshops (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    subject ENUM('DEV', 'UX', 'PO', 'RESEARCH', 'PORTFOLIO', 'MISC') NOT NULL,
    date DATE NOT NULL,
    teacher_id CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraint
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_workshops_date (date),
    INDEX idx_workshops_subject (subject),
    INDEX idx_workshops_teacher_id (teacher_id),
    INDEX idx_workshops_date_subject (date, subject)
);

-- Create login_attempts table for security
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN DEFAULT FALSE,
    
    INDEX idx_login_attempts_email (email),
    INDEX idx_login_attempts_ip (ip_address),
    INDEX idx_login_attempts_time (attempted_at)
);

-- Create csrf_tokens table for security
CREATE TABLE csrf_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(255) NOT NULL UNIQUE,
    user_id CHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_csrf_tokens_token (token),
    INDEX idx_csrf_tokens_expires (expires_at)
);

-- Generate UUIDs for default users (MySQL function)
SET @admin_id = UUID();
SET @teacher_id = UUID();

-- Insert default admin user (password: admin123)
INSERT INTO users (id, email, name, password, role) VALUES 
(@admin_id, 'admin@fed.nl', 'Admin User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN'),
(@teacher_id, 'teacher@fed.nl', 'Teacher User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TEACHER');

-- Verify tables were created
SELECT 'Tables created successfully' as status;
SELECT table_name FROM information_schema.tables WHERE table_schema = DATABASE();

-- Verify users were inserted
SELECT id, email, name, role FROM users;