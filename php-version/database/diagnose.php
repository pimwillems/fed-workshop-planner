<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Diagnostic Tool</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 2rem auto; padding: 2rem; }
        .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .warning { background: #fff3cd; color: #856404; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        pre { background: #f8f9fa; padding: 1rem; border-radius: 6px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 0.5rem 1rem; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🔍 Database Diagnostic Tool</h1>
    
    <?php
    // Load configuration
    require_once '../config/app.php';
    
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
    
    echo '<div class="info">';
    echo '<h2>📋 Configuration Check</h2>';
    echo '<p><strong>Database Host:</strong> ' . ($config['DB_HOST'] ?? 'Not set') . '</p>';
    echo '<p><strong>Database Name:</strong> ' . ($config['DB_NAME'] ?? 'Not set') . '</p>';
    echo '<p><strong>Database User:</strong> ' . ($config['DB_USER'] ?? 'Not set') . '</p>';
    echo '<p><strong>Password Set:</strong> ' . (isset($config['DB_PASSWORD']) && !empty($config['DB_PASSWORD']) ? 'Yes' : 'No') . '</p>';
    echo '</div>';
    
    if (empty($config['DB_HOST']) || empty($config['DB_NAME']) || empty($config['DB_USER'])) {
        echo '<div class="error">';
        echo '<h2>❌ Configuration Error</h2>';
        echo '<p>Your .env file is missing required database configuration.</p>';
        echo '<p>Please check your .env file and ensure all database settings are configured.</p>';
        echo '</div>';
        exit;
    }
    
    try {
        // Test database connection
        $dsn = "mysql:host={$config['DB_HOST']};port=" . ($config['DB_PORT'] ?? 3306);
        $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
        
        echo '<div class="success">';
        echo '<h2>✅ Database Connection</h2>';
        echo '<p>Successfully connected to MySQL server!</p>';
        echo '</div>';
        
        // Check if database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE '{$config['DB_NAME']}'");
        if ($stmt->rowCount() > 0) {
            echo '<div class="success">';
            echo '<h2>✅ Database Exists</h2>';
            echo '<p>Database "' . $config['DB_NAME'] . '" exists.</p>';
            echo '</div>';
            
            // Connect to the specific database
            $dsn = "mysql:host={$config['DB_HOST']};port=" . ($config['DB_PORT'] ?? 3306) . ";dbname={$config['DB_NAME']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
            
            // Check tables
            $stmt = $pdo->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $expectedTables = ['users', 'workshops', 'login_attempts', 'csrf_tokens'];
            $missingTables = array_diff($expectedTables, $tables);
            
            if (empty($missingTables)) {
                echo '<div class="success">';
                echo '<h2>✅ All Tables Exist</h2>';
                echo '<p>Found tables: ' . implode(', ', $tables) . '</p>';
                echo '</div>';
                
                // Check if users exist
                $stmt = $pdo->query("SELECT COUNT(*) FROM users");
                $userCount = $stmt->fetchColumn();
                
                if ($userCount > 0) {
                    echo '<div class="success">';
                    echo '<h2>✅ Users Table Populated</h2>';
                    echo '<p>Found ' . $userCount . ' users in the database.</p>';
                    
                    // Show users
                    $stmt = $pdo->query("SELECT email, name, role FROM users");
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo '<h3>👥 Current Users:</h3>';
                    echo '<pre>';
                    foreach ($users as $user) {
                        echo sprintf("%-25s %-20s %s\n", $user['email'], $user['name'], $user['role']);
                    }
                    echo '</pre>';
                    echo '</div>';
                } else {
                    echo '<div class="warning">';
                    echo '<h2>⚠️ No Users Found</h2>';
                    echo '<p>Tables exist but no users were found. You may need to run the setup wizard again.</p>';
                    echo '</div>';
                }
                
                // Check workshops
                $stmt = $pdo->query("SELECT COUNT(*) FROM workshops");
                $workshopCount = $stmt->fetchColumn();
                
                echo '<div class="info">';
                echo '<h2>📚 Workshops</h2>';
                echo '<p>Found ' . $workshopCount . ' workshops in the database.</p>';
                echo '</div>';
                
            } else {
                echo '<div class="error">';
                echo '<h2>❌ Missing Tables</h2>';
                echo '<p>The following tables are missing: ' . implode(', ', $missingTables) . '</p>';
                echo '<p>Found tables: ' . (empty($tables) ? 'None' : implode(', ', $tables)) . '</p>';
                echo '</div>';
            }
            
        } else {
            echo '<div class="error">';
            echo '<h2>❌ Database Not Found</h2>';
            echo '<p>Database "' . $config['DB_NAME'] . '" does not exist.</p>';
            echo '<p>Please create the database first.</p>';
            echo '</div>';
        }
        
    } catch (PDOException $e) {
        echo '<div class="error">';
        echo '<h2>❌ Database Error</h2>';
        echo '<p>Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '</div>';
    }
    ?>
    
    <div class="info">
        <h2>🛠️ Next Steps</h2>
        <?php if (!empty($missingTables) || !isset($userCount) || $userCount == 0): ?>
        <ol>
            <li>If tables are missing, run the <a href="web-migrate.php">setup wizard</a></li>
            <li>Or manually execute the SQL from <code>database/manual-setup.sql</code> in phpMyAdmin</li>
            <li>Then test your application at <a href="../">the homepage</a></li>
        </ol>
        <?php else: ?>
        <ol>
            <li>✅ Database setup is complete!</li>
            <li>Go to <a href="../">your application homepage</a></li>
            <li>Login with: admin@fed.nl / admin123</li>
            <li>Change the default passwords immediately</li>
        </ol>
        <?php endif; ?>
    </div>
    
    <div class="warning">
        <h2>⚠️ Security Notice</h2>
        <p>Delete this diagnostic file after resolving your database issues.</p>
    </div>
    
    <div style="margin-top: 2rem;">
        <a href="web-migrate.php" class="btn">Run Setup Wizard</a>
        <a href="../" class="btn" style="margin-left: 1rem;">Go to Application</a>
    </div>
</body>
</html>