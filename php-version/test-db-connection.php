<?php
/**
 * Simple Database Connection Test
 */

echo "<h1>🔗 Database Connection Test</h1>";

// Load environment variables
if (file_exists('.env')) {
    echo "<p>✅ .env file found</p>";
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && $line[0] !== '#') {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
} else {
    echo "<p>❌ .env file not found</p>";
    echo "<p>Create .env file with your database credentials:</p>";
    echo "<pre>DB_HOST=localhost
DB_PORT=3306
DB_NAME=i888908_workshopplanner
DB_USER=your_database_username
DB_PASSWORD=your_database_password
JWT_SECRET=your-random-32-character-secret-key</pre>";
    exit();
}

// Test database connection directly
echo "<h2>Testing Database Connection...</h2>";

$host = $_ENV['DB_HOST'] ?? 'localhost';
$port = $_ENV['DB_PORT'] ?? 3306;
$database = $_ENV['DB_NAME'] ?? '';
$username = $_ENV['DB_USER'] ?? '';
$password = $_ENV['DB_PASSWORD'] ?? '';

echo "<p><strong>Configuration:</strong></p>";
echo "<p>Host: $host</p>";
echo "<p>Port: $port</p>";
echo "<p>Database: $database</p>";
echo "<p>Username: $username</p>";
echo "<p>Password: " . (empty($password) ? 'NOT SET' : 'SET') . "</p>";

if (empty($database) || empty($username)) {
    echo "<p>❌ Database credentials not properly set in .env</p>";
    exit();
}

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<p>✅ Database connection successful!</p>";
    
    // Test tables
    $tables = ['users', 'workshops', 'login_attempts', 'csrf_tokens'];
    echo "<h3>Checking Tables:</h3>";
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p>✅ Table '$table' exists</p>";
        } else {
            echo "<p>❌ Table '$table' missing</p>";
        }
    }
    
    // Test user count
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $userCount = $stmt->fetchColumn();
    echo "<p>✅ Found $userCount users in database</p>";
    
    echo "<hr>";
    echo "<h2>✅ Database is working correctly!</h2>";
    echo "<p>Now try your application: <a href='index.php'>Test Application</a></p>";
    
} catch (PDOException $e) {
    echo "<p>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p>Check your .env file credentials and make sure the database exists.</p>";
}

echo "<p><strong>⚠️ Delete this file after use for security!</strong></p>";
?>