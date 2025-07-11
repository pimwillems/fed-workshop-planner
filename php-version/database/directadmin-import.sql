-- Workshop Planner Database Setup for DirectAdmin Import
-- Database: i888908_workshopplanner
-- Upload this file via DirectAdmin MySQL Management

-- Use the correct database
USE i888908_workshopplanner;

-- Remove existing tables if they exist
DROP TABLE IF EXISTS workshops;
DROP TABLE IF EXISTS csrf_tokens;
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS users;

-- Create users table
CREATE TABLE users (
  id CHAR(36) NOT NULL PRIMARY KEY,
  email VARCHAR(255) NOT NULL UNIQUE,
  name VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('TEACHER', 'ADMIN') NOT NULL DEFAULT 'TEACHER',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create workshops table
CREATE TABLE workshops (
  id CHAR(36) NOT NULL PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT NOT NULL,
  subject ENUM('DEV', 'UX', 'PO', 'RESEARCH', 'PORTFOLIO', 'MISC') NOT NULL,
  date DATE NOT NULL,
  teacher_id CHAR(36) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create login attempts table for security
CREATE TABLE login_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  ip_address VARCHAR(45) NOT NULL,
  attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  success BOOLEAN DEFAULT FALSE
);

-- Create CSRF tokens table for security
CREATE TABLE csrf_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  token VARCHAR(255) NOT NULL UNIQUE,
  user_id CHAR(36) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  expires_at TIMESTAMP NOT NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users (id, email, name, password, role) VALUES 
('550e8400-e29b-41d4-a716-446655440000', 'admin@fed.nl', 'Admin User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN');

-- Insert default teacher user (password: admin123)
INSERT INTO users (id, email, name, password, role) VALUES 
('550e8400-e29b-41d4-a716-446655440001', 'teacher@fed.nl', 'Teacher User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TEACHER');

-- Add indexes for better performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role ON users(role);
CREATE INDEX idx_workshops_date ON workshops(date);
CREATE INDEX idx_workshops_subject ON workshops(subject);
CREATE INDEX idx_workshops_teacher_id ON workshops(teacher_id);
CREATE INDEX idx_login_attempts_email ON login_attempts(email);
CREATE INDEX idx_login_attempts_ip ON login_attempts(ip_address);
CREATE INDEX idx_csrf_tokens_token ON csrf_tokens(token);
CREATE INDEX idx_csrf_tokens_expires ON csrf_tokens(expires_at);

-- Add foreign key constraint
ALTER TABLE workshops ADD CONSTRAINT fk_workshops_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE;
ALTER TABLE csrf_tokens ADD CONSTRAINT fk_csrf_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

-- Verify tables were created
SELECT 'Database setup completed successfully' AS status;
SELECT COUNT(*) as user_count FROM users;
SELECT table_name FROM information_schema.tables WHERE table_schema = 'i888908_workshopplanner';