-- MySQL Database Schema for Workshop Planner
-- Created by converting from PostgreSQL Prisma schema

-- Create database (run manually)
-- CREATE DATABASE workshop_planner CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE workshop_planner;

-- Create users table
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
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
    id CHAR(36) PRIMARY KEY DEFAULT (UUID()),
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

-- Insert default admin user (password: admin123)
INSERT INTO users (id, email, name, password, role) VALUES 
(UUID(), 'admin@fed.nl', 'Admin User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN'),
(UUID(), 'teacher@fed.nl', 'Teacher User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TEACHER');

-- Create procedure to clean up expired tokens
DELIMITER //
CREATE PROCEDURE CleanupExpiredTokens()
BEGIN
    DELETE FROM csrf_tokens WHERE expires_at < NOW();
    DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 DAY);
END //
DELIMITER ;

-- Create event to automatically clean up expired tokens (optional)
-- SET GLOBAL event_scheduler = ON;
-- CREATE EVENT ev_cleanup_tokens
-- ON SCHEDULE EVERY 1 HOUR
-- DO CALL CleanupExpiredTokens();