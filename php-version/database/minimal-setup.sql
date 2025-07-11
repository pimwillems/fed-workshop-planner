-- Minimal setup for Workshop Planner (single execution)
-- This version should work better with phpMyAdmin

-- Clean start
DROP TABLE IF EXISTS workshops;
DROP TABLE IF EXISTS csrf_tokens; 
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('TEACHER', 'ADMIN') DEFAULT 'TEACHER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Workshops table
CREATE TABLE workshops (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    subject ENUM('DEV', 'UX', 'PO', 'RESEARCH', 'PORTFOLIO', 'MISC') NOT NULL,
    date DATE NOT NULL,
    teacher_id CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Security tables
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success BOOLEAN DEFAULT FALSE
);

CREATE TABLE csrf_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(255) NOT NULL UNIQUE,
    user_id CHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL
);

-- Default users (password: admin123)
INSERT INTO users (id, email, name, password, role) VALUES 
('550e8400-e29b-41d4-a716-446655440000', 'admin@fed.nl', 'Admin User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN'),
('550e8400-e29b-41d4-a716-446655440001', 'teacher@fed.nl', 'Teacher User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TEACHER');