<?php
/**
 * Database Connection Debug Tool
 * This will identify the exact database connection issue
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Database Debug</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .success{background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin:1rem 0;} .error{background:#f8d7da;color:#721c24;padding:1rem;border-radius:6px;margin:1rem 0;} .info{background:#d1ecf1;color:#0c5460;padding:1rem;border-radius:6px;margin:1rem 0;} .warning{background:#fff3cd;color:#856404;padding:1rem;border-radius:6px;margin:1rem 0;} pre{background:#f8f9fa;padding:1rem;border-radius:6px;overflow-x:auto;}</style>";
echo "</head><body>";

echo "<h1>🔍 Database Connection Debug</h1>";

// Step 1: Check .env file
echo "<h2>Step 1: Environment File Check</h2>";
if (file_exists('.env')) {
    echo "<div class='success'>✅ .env file exists</div>";
    
    $envContent = file_get_contents('.env');
    echo "<div class='info'>File size: " . strlen($envContent) . " bytes</div>";
    
    // Parse .env file
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $envVars = [];
    
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && $line[0] !== '#') {
            list($key, $value) = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value);
        }
    }
    
    // Check required variables
    $requiredVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'];
    foreach ($requiredVars as $var) {
        if (isset($envVars[$var])) {
            $displayValue = $var === 'DB_PASSWORD' ? '***HIDDEN***' : $envVars[$var];
            echo "<div class='success'>✅ $var: $displayValue</div>";
        } else {
            echo "<div class='error'>❌ $var not set</div>";
        }
    }
    
    // Load into $_ENV
    foreach ($envVars as $key => $value) {
        $_ENV[$key] = $value;
    }
    
} else {
    echo "<div class='error'>❌ .env file not found</div>";
    echo "<div class='warning'>You need to create a .env file with database credentials</div>";
}

// Step 2: Check app.php configuration
echo "<h2>Step 2: App Configuration Check</h2>";
try {
    $configPath = __DIR__ . '/config/app.php';
    if (file_exists($configPath)) {
        echo "<div class='success'>✅ config/app.php exists</div>";
        
        $config = require $configPath;
        echo "<div class='info'>Config loaded successfully</div>";
        
        if (isset($config['database'])) {
            echo "<div class='success'>✅ Database configuration section exists</div>";
            
            $dbConfig = $config['database'];
            echo "<div class='info'>Database config:</div>";
            echo "<div class='info'>Host: " . ($dbConfig['host'] ?? 'NOT SET') . "</div>";
            echo "<div class='info'>Database: " . ($dbConfig['database'] ?? 'NOT SET') . "</div>";
            echo "<div class='info'>Username: " . ($dbConfig['username'] ?? 'NOT SET') . "</div>";
            echo "<div class='info'>Password: " . (isset($dbConfig['password']) ? '***SET***' : 'NOT SET') . "</div>";
            
        } else {
            echo "<div class='error'>❌ Database configuration missing from app.php</div>";
        }
        
    } else {
        echo "<div class='error'>❌ config/app.php not found</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error loading app.php: " . $e->getMessage() . "</div>";
}

// Step 3: Direct database connection test
echo "<h2>Step 3: Direct Database Connection Test</h2>";
if (isset($_ENV['DB_HOST']) && isset($_ENV['DB_NAME']) && isset($_ENV['DB_USER']) && isset($_ENV['DB_PASSWORD'])) {
    
    $host = $_ENV['DB_HOST'];
    $database = $_ENV['DB_NAME'];
    $username = $_ENV['DB_USER'];
    $password = $_ENV['DB_PASSWORD'];
    $port = $_ENV['DB_PORT'] ?? 3306;
    
    echo "<div class='info'>Testing connection with:</div>";
    echo "<div class='info'>Host: $host</div>";
    echo "<div class='info'>Port: $port</div>";
    echo "<div class='info'>Database: $database</div>";
    echo "<div class='info'>Username: $username</div>";
    
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
        echo "<div class='info'>DSN: $dsn</div>";
        
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        echo "<div class='success'>✅ Direct PDO connection successful!</div>";
        
        // Test a simple query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<div class='success'>✅ Query successful - Found {$result['count']} users</div>";
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Direct PDO connection failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>Error code: " . $e->getCode() . "</div>";
    }
    
} else {
    echo "<div class='error'>❌ Database credentials not available for direct test</div>";
}

// Step 4: Test Database class
echo "<h2>Step 4: Database Class Test</h2>";
try {
    require_once 'config/database.php';
    echo "<div class='success'>✅ Database class loaded</div>";
    
    echo "<div class='info'>Testing Database::getInstance()...</div>";
    $db = Database::getInstance();
    echo "<div class='success'>✅ Database singleton created</div>";
    
    $connection = $db->getConnection();
    echo "<div class='success'>✅ Connection retrieved from Database class</div>";
    
    // Test query through Database class
    $stmt = $connection->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<div class='success'>✅ Database class query successful - Found {$result['count']} users</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Database class test failed: " . $e->getMessage() . "</div>";
    echo "<div class='error'>File: " . $e->getFile() . "</div>";
    echo "<div class='error'>Line: " . $e->getLine() . "</div>";
    echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
}

// Step 5: Test AuthController instantiation
echo "<h2>Step 5: AuthController Instantiation Test</h2>";
try {
    require_once 'src/Controllers/AuthController.php';
    echo "<div class='success'>✅ AuthController class loaded</div>";
    
    echo "<div class='info'>Testing AuthController constructor...</div>";
    $authController = new AuthController();
    echo "<div class='success'>✅ AuthController instantiated successfully</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ AuthController instantiation failed: " . $e->getMessage() . "</div>";
    echo "<div class='error'>File: " . $e->getFile() . "</div>";
    echo "<div class='error'>Line: " . $e->getLine() . "</div>";
}

echo "<h2>🎯 Diagnosis Summary</h2>";
echo "<div class='info'>";
echo "<p>Based on the tests above, the issue is likely:</p>";
echo "<ol>";
echo "<li>Missing or incorrect .env file</li>";
echo "<li>Database credentials not properly configured</li>";
echo "<li>Database server not accessible</li>";
echo "<li>Database doesn't exist or user doesn't have permissions</li>";
echo "</ol>";
echo "</div>";

echo "<div class='warning'>";
echo "<h3>⚠️ Quick Fix</h3>";
echo "<p>If the .env file is missing or incorrect, create it with these contents:</p>";
echo "<pre>DB_HOST=localhost
DB_PORT=3306
DB_NAME=i888908_workshopplanner
DB_USER=your_database_username
DB_PASSWORD=your_database_password
JWT_SECRET=your-random-32-character-secret-key</pre>";
echo "</div>";

echo "</body></html>";
?>