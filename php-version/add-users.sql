-- SQL script to add FED Workshop Planner users
-- Password for all users: "password123"
-- Admins: dschol, pwillems
-- Teachers: mputman, gsegers, aeyck, agroeneweg, svanoers, lderkx

-- First, remove any existing duplicate UUIDs
DELETE FROM users WHERE id IN (
    '550e8400-e29b-41d4-a716-446655440001',
    '550e8400-e29b-41d4-a716-446655440002',
    '550e8400-e29b-41d4-a716-446655440003',
    '550e8400-e29b-41d4-a716-446655440004',
    '550e8400-e29b-41d4-a716-446655440005',
    '550e8400-e29b-41d4-a716-446655440006',
    '550e8400-e29b-41d4-a716-446655440007',
    '550e8400-e29b-41d4-a716-446655440008',
    'a1b2c3d4-e5f6-4789-a012-345678901234',
    'b2c3d4e5-f6g7-4890-b123-456789012345',
    'c3d4e5f6-g7h8-4901-c234-567890123456',
    'd4e5f6g7-h8i9-4012-d345-678901234567',
    'e5f6g7h8-i9j0-4123-e456-789012345678',
    'f6g7h8i9-j0k1-4234-f567-890123456789',
    'g7h8i9j0-k1l2-4345-g678-901234567890',
    'h8i9j0k1-l2m3-4456-h789-012345678901'
);

-- Insert users with bcrypt hashed passwords (cost=12)
INSERT INTO users (id, name, email, password, role, created_at, updated_at) VALUES
-- Admins
('a1b2c3d4-e5f6-4789-a012-345678901234', 'D. Schol', 'dschol@fontys.nl', '$2y$12$LQv3c1yqBwLFaAODJeJCuOrM4rCb9rZ2gL5oZdmFqJnfVMJwVyYAm', 'ADMIN', NOW(), NOW()),
('b2c3d4e5-f6g7-4890-b123-456789012345', 'P. Willems', 'pwillems@fontys.nl', '$2y$12$LQv3c1yqBwLFaAODJeJCuOrM4rCb9rZ2gL5oZdmFqJnfVMJwVyYAm', 'ADMIN', NOW(), NOW()),

-- Teachers
('c3d4e5f6-g7h8-4901-c234-567890123456', 'M. Putman', 'mputman@fontys.nl', '$2y$12$LQv3c1yqBwLFaAODJeJCuOrM4rCb9rZ2gL5oZdmFqJnfVMJwVyYAm', 'TEACHER', NOW(), NOW()),
('d4e5f6g7-h8i9-4012-d345-678901234567', 'G. Segers', 'gsegers@fontys.nl', '$2y$12$LQv3c1yqBwLFaAODJeJCuOrM4rCb9rZ2gL5oZdmFqJnfVMJwVyYAm', 'TEACHER', NOW(), NOW()),
('e5f6g7h8-i9j0-4123-e456-789012345678', 'A. Eyck', 'aeyck@fontys.nl', '$2y$12$LQv3c1yqBwLFaAODJeJCuOrM4rCb9rZ2gL5oZdmFqJnfVMJwVyYAm', 'TEACHER', NOW(), NOW()),
('f6g7h8i9-j0k1-4234-f567-890123456789', 'A. Groeneweg', 'agroeneweg@fontys.nl', '$2y$12$LQv3c1yqBwLFaAODJeJCuOrM4rCb9rZ2gL5oZdmFqJnfVMJwVyYAm', 'TEACHER', NOW(), NOW()),
('g7h8i9j0-k1l2-4345-g678-901234567890', 'S. van Oers', 'svanoers@fontys.nl', '$2y$12$LQv3c1yqBwLFaAODJeJCuOrM4rCb9rZ2gL5oZdmFqJnfVMJwVyYAm', 'TEACHER', NOW(), NOW()),
('h8i9j0k1-l2m3-4456-h789-012345678901', 'L. Derkx', 'lderkx@fontys.nl', '$2y$12$LQv3c1yqBwLFaAODJeJCuOrM4rCb9rZ2gL5oZdmFqJnfVMJwVyYAm', 'TEACHER', NOW(), NOW());

-- Verify the inserts
SELECT id, name, email, role, created_at FROM users ORDER BY role DESC, name;