-- Step-by-step SQL setup for Workshop Planner
-- Run each section separately in phpMyAdmin to avoid timeouts

-- ===== STEP 1: Drop existing tables (if any) =====
-- Copy and paste this first, then click "Go"
DROP TABLE IF EXISTS workshops;
DROP TABLE IF EXISTS csrf_tokens;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS users;

-- ===== STEP 2: Create users table =====
-- Copy and paste this second, then click "Go"
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('TEACHER', 'ADMIN') NOT NULL DEFAULT 'TEACHER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_users_email (email),
    INDEX idx_users_role (role)
);

-- ===== STEP 3: Create workshops table =====
-- Copy and paste this third, then click "Go"
CREATE TABLE workshops (
    id CHAR(36) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    subject ENUM('DEV', 'UX', 'PO', 'RESEARCH', 'PORTFOLIO', 'MISC') NOT NULL,
    date DATE NOT NULL,
    teacher_id CHAR(36) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_workshops_date (date),
    INDEX idx_workshops_subject (subject),
    INDEX idx_workshops_teacher_id (teacher_id),
    INDEX idx_workshops_date_subject (date, subject)
);

-- ===== STEP 4: Create security tables =====
-- Copy and paste this fourth, then click "Go"
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

-- ===== STEP 5: Insert default users =====
-- Copy and paste this last, then click "Go"
INSERT INTO users (id, email, name, password, role) VALUES 
('550e8400-e29b-41d4-a716-446655440000', 'admin@fed.nl', 'Admin User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN'),
('550e8400-e29b-41d4-a716-446655440001', 'teacher@fed.nl', 'Teacher User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TEACHER');