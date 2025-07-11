<?php
// Application configuration
return [
    'name' => 'FED Workshop Planner',
    'version' => '1.0.0',
    'timezone' => 'UTC',
    'debug' => false,
    
    // JWT Configuration
    'jwt' => [
        'secret' => 'workshop-planner-secret-key-2024-production',
        'algorithm' => 'HS256',
        'expiration' => 7 * 24 * 60 * 60, // 7 days
        'issuer' => 'workshop-planner',
        'audience' => 'workshop-planner'
    ],
    
    // Security settings
    'security' => [
        'csrf_token_name' => 'csrf_token',
        'csrf_expiration' => 3600, // 1 hour
        'session_name' => 'workshop_session',
        'password_min_length' => 6,
        'max_login_attempts' => 5,
        'lockout_duration' => 900, // 15 minutes
    ],
    
    // Database settings (hardcoded - no longer using env)
    'database' => [
        'host' => '192.168.15.56',
        'port' => 3306,
        'database' => 'i888908_workshopplanner',
        'username' => 'i888908_workshopplanner',
        'password' => 'Cookies2022!',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    ],
    
    // Subject categories
    'subjects' => [
        'Dev' => 'Development',
        'UX' => 'UX Design', 
        'PO' => 'Professional Skills',
        'Research' => 'Research',
        'Portfolio' => 'Portfolio',
        'Misc' => 'Miscellaneous'
    ],
    
    // User roles
    'roles' => [
        'TEACHER' => 'teacher',
        'ADMIN' => 'admin'
    ]
];