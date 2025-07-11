<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Database Setup</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 2rem auto; padding: 2rem; }
        .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .btn { background: #007bff; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        pre { background: #f8f9fa; padding: 1rem; border-radius: 6px; overflow-x: auto; font-size: 0.9rem; }
        .step { margin: 2rem 0; padding: 1.5rem; border: 1px solid #dee2e6; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>⚡ Quick Database Setup</h1>
    
    <?php
    // Load configuration
    $envPath = __DIR__ . '/../.env';
    $config = [];
    
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && $line[0] !== '#') {
                list($key, $value) = explode('=', $line, 2);
                $config[trim($key)] = trim($value);
            }
        }
    }
    
    if (isset($_GET['action']) && $_GET['action'] === 'setup') {
        echo '<div class="info"><h2>🔧 Setting up database...</h2></div>';
        
        try {
            $dsn = "mysql:host={$config['DB_HOST']};port=" . ($config['DB_PORT'] ?? 3306) . ";dbname={$config['DB_NAME']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Execute setup commands one by one
            $commands = [
                "DROP TABLE IF EXISTS workshops",
                "DROP TABLE IF EXISTS csrf_tokens", 
                "DROP TABLE IF EXISTS login_attempts",
                "DROP TABLE IF EXISTS users",
                
                "CREATE TABLE users (
                    id CHAR(36) PRIMARY KEY,
                    email VARCHAR(255) UNIQUE NOT NULL,
                    name VARCHAR(255) NOT NULL,
                    password VARCHAR(255) NOT NULL,
                    role ENUM('TEACHER', 'ADMIN') DEFAULT 'TEACHER',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )",
                
                "CREATE TABLE workshops (
                    id CHAR(36) PRIMARY KEY,
                    title VARCHAR(255) NOT NULL,
                    description TEXT NOT NULL,
                    subject ENUM('DEV', 'UX', 'PO', 'RESEARCH', 'PORTFOLIO', 'MISC') NOT NULL,
                    date DATE NOT NULL,
                    teacher_id CHAR(36) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                )",
                
                "CREATE TABLE login_attempts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    email VARCHAR(255) NOT NULL,
                    ip_address VARCHAR(45) NOT NULL,
                    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    success BOOLEAN DEFAULT FALSE
                )",
                
                "CREATE TABLE csrf_tokens (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    token VARCHAR(255) NOT NULL UNIQUE,
                    user_id CHAR(36),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    expires_at TIMESTAMP NOT NULL
                )",
                
                "INSERT INTO users (id, email, name, password, role) VALUES 
                ('550e8400-e29b-41d4-a716-446655440000', 'admin@fed.nl', 'Admin User', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN'),
                ('550e8400-e29b-41d4-a716-446655440001', 'teacher@fed.nl', 'Teacher User', '$2y$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TEACHER')"
            ];
            
            foreach ($commands as $i => $command) {
                try {
                    $pdo->exec($command);
                    echo '<div class="success">✅ Step ' . ($i + 1) . ' completed</div>';
                } catch (PDOException $e) {
                    echo '<div class="error">❌ Step ' . ($i + 1) . ' failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
            
            echo '<div class="success">';
            echo '<h2>🎉 Database Setup Complete!</h2>';
            echo '<p>Your database is now ready to use.</p>';
            echo '<p><strong>Default Login:</strong> admin@fed.nl / admin123</p>';
            echo '<p><a href="../" class="btn">Go to Workshop Planner</a></p>';
            echo '</div>';
            
        } catch (PDOException $e) {
            echo '<div class="error">';
            echo '<h2>❌ Database Connection Failed</h2>';
            echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
    } else {
        ?>
        <div class="info">
            <h2>🔧 Database Configuration</h2>
            <p><strong>Host:</strong> <?php echo $config['DB_HOST'] ?? 'Not set'; ?></p>
            <p><strong>Database:</strong> <?php echo $config['DB_NAME'] ?? 'Not set'; ?></p>
            <p><strong>User:</strong> <?php echo $config['DB_USER'] ?? 'Not set'; ?></p>
        </div>
        
        <div class="step">
            <h3>⚡ One-Click Setup</h3>
            <p>This will create all necessary tables and insert default users.</p>
            <p><strong>⚠️ Warning:</strong> This will delete any existing data!</p>
            <a href="?action=setup" class="btn btn-danger">Setup Database Now</a>
        </div>
        
        <div class="step">
            <h3>📋 Manual Setup Options</h3>
            <p>If the one-click setup doesn't work, try these alternatives:</p>
            
            <h4>Option 1: Step-by-Step SQL</h4>
            <p>Copy and paste each section from <code>database/setup-steps.sql</code> into phpMyAdmin one at a time.</p>
            
            <h4>Option 2: Minimal SQL</h4>
            <p>Try importing the smaller <code>database/minimal-setup.sql</code> file.</p>
            
            <h4>Option 3: Individual Commands</h4>
            <p>Run these commands one by one in phpMyAdmin SQL tab:</p>
            
            <pre>-- 1. Clean existing tables
DROP TABLE IF EXISTS workshops;
DROP TABLE IF EXISTS csrf_tokens; 
DROP TABLE IF EXISTS login_attempts;
DROP TABLE IF EXISTS users;

-- 2. Create users table
CREATE TABLE users (
    id CHAR(36) PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('TEACHER', 'ADMIN') DEFAULT 'TEACHER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 3. Create workshops table
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

-- 4. Create security tables
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

-- 5. Insert default users
INSERT INTO users (id, email, name, password, role) VALUES 
('550e8400-e29b-41d4-a716-446655440000', 'admin@fed.nl', 'Admin User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ADMIN'),
('550e8400-e29b-41d4-a716-446655440001', 'teacher@fed.nl', 'Teacher User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'TEACHER');</pre>
        </div>
        
        <div class="step">
            <h3>🔍 Need Help?</h3>
            <p>Check the <a href="diagnose.php">database diagnostic tool</a> to see what's wrong.</p>
        </div>
        
        <?php
    }
    ?>
    
    <div class="info">
        <h2>⚠️ Security Notice</h2>
        <p>Delete this file after setup for security: <code>database/quick-setup.php</code></p>
    </div>
</body>
</html>