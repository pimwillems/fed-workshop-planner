<?php
/**
 * Database Credentials Helper
 * This will help you find and test the correct database credentials
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Database Credentials Helper</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .success{background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin:1rem 0;} .error{background:#f8d7da;color:#721c24;padding:1rem;border-radius:6px;margin:1rem 0;} .info{background:#d1ecf1;color:#0c5460;padding:1rem;border-radius:6px;margin:1rem 0;} .warning{background:#fff3cd;color:#856404;padding:1rem;border-radius:6px;margin:1rem 0;} pre{background:#f8f9fa;padding:1rem;border-radius:6px;overflow-x:auto;} .form{background:#f8f9fa;padding:1.5rem;border-radius:6px;margin:1rem 0;}</style>";
echo "</head><body>";

echo "<h1>🔧 Database Credentials Helper</h1>";

// Step 1: Show current .env contents
echo "<h2>Step 1: Current .env File Analysis</h2>";
if (file_exists('.env')) {
    echo "<div class='success'>✅ .env file exists</div>";
    
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $envVars = [];
    
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && $line[0] !== '#') {
            list($key, $value) = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value);
        }
    }
    
    echo "<div class='info'><strong>Current credentials in .env:</strong></div>";
    echo "<div class='info'>DB_HOST: " . ($envVars['DB_HOST'] ?? 'NOT SET') . "</div>";
    echo "<div class='info'>DB_NAME: " . ($envVars['DB_NAME'] ?? 'NOT SET') . "</div>";
    echo "<div class='info'>DB_USER: " . ($envVars['DB_USER'] ?? 'NOT SET') . "</div>";
    echo "<div class='info'>DB_PASSWORD: " . (isset($envVars['DB_PASSWORD']) ? '***SET*** (length: ' . strlen($envVars['DB_PASSWORD']) . ')' : 'NOT SET') . "</div>";
    
} else {
    echo "<div class='error'>❌ .env file not found</div>";
}

echo "<h2>Step 2: Database Connection Test</h2>";

// Handle form submission
if ($_POST && isset($_POST['test_connection'])) {
    $testHost = $_POST['db_host'] ?? 'localhost';
    $testPort = $_POST['db_port'] ?? 3306;
    $testDatabase = $_POST['db_name'] ?? '';
    $testUsername = $_POST['db_user'] ?? '';
    $testPassword = $_POST['db_password'] ?? '';
    
    echo "<div class='info'><strong>Testing connection with:</strong></div>";
    echo "<div class='info'>Host: $testHost</div>";
    echo "<div class='info'>Port: $testPort</div>";
    echo "<div class='info'>Database: $testDatabase</div>";
    echo "<div class='info'>Username: $testUsername</div>";
    echo "<div class='info'>Password: " . (empty($testPassword) ? 'EMPTY' : '***SET*** (length: ' . strlen($testPassword) . ')') . "</div>";
    
    try {
        $dsn = "mysql:host=$testHost;port=$testPort;dbname=$testDatabase;charset=utf8mb4";
        $pdo = new PDO($dsn, $testUsername, $testPassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        echo "<div class='success'>✅ CONNECTION SUCCESSFUL!</div>";
        
        // Test query
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<div class='success'>✅ Database query successful - Found {$result['count']} users</div>";
        
        // Show success instructions
        echo "<div class='success'>";
        echo "<h3>🎉 Success! Use these credentials in your .env file:</h3>";
        echo "<pre>DB_HOST=$testHost
DB_PORT=$testPort
DB_NAME=$testDatabase
DB_USER=$testUsername
DB_PASSWORD=$testPassword
JWT_SECRET=your-random-32-character-secret-key</pre>";
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Connection failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>Error Code: " . $e->getCode() . "</div>";
        
        // Provide specific guidance based on error
        if (strpos($e->getMessage(), 'Access denied') !== false) {
            echo "<div class='warning'>";
            echo "<h4>Access Denied - Possible Issues:</h4>";
            echo "<ul>";
            echo "<li>Username is incorrect</li>";
            echo "<li>Password is incorrect</li>";
            echo "<li>User doesn't have permission to access this database</li>";
            echo "<li>User might need to be created in DirectAdmin</li>";
            echo "</ul>";
            echo "</div>";
        } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
            echo "<div class='warning'>Database name '$testDatabase' doesn't exist. Check the exact database name in DirectAdmin.</div>";
        }
    }
}

// Test form
echo "<div class='form'>";
echo "<h3>🧪 Test Database Credentials</h3>";
echo "<form method='POST'>";
echo "<p><label>Host: <input type='text' name='db_host' value='localhost' style='width:200px;padding:0.5rem;'></label></p>";
echo "<p><label>Port: <input type='text' name='db_port' value='3306' style='width:200px;padding:0.5rem;'></label></p>";
echo "<p><label>Database: <input type='text' name='db_name' value='i888908_workshopplanner' style='width:200px;padding:0.5rem;'></label></p>";
echo "<p><label>Username: <input type='text' name='db_user' value='i888908_pimwi' style='width:200px;padding:0.5rem;'></label></p>";
echo "<p><label>Password: <input type='password' name='db_password' value='' style='width:200px;padding:0.5rem;'></label></p>";
echo "<p><button type='submit' name='test_connection' style='background:#007bff;color:white;padding:0.75rem 1.5rem;border:none;border-radius:4px;cursor:pointer;'>Test Connection</button></p>";
echo "</form>";
echo "</div>";

echo "<h2>📋 Common DirectAdmin Database Issues</h2>";
echo "<div class='info'>";
echo "<h4>1. Database User Format</h4>";
echo "<p>DirectAdmin usually creates users in the format: <code>i888908_username</code></p>";
echo "<p>Your database user might be:</p>";
echo "<ul>";
echo "<li><code>i888908_pimwi</code> (current - failing)</li>";
echo "<li><code>i888908_admin</code></li>";
echo "<li><code>i888908_workshopplanner</code></li>";
echo "<li>Or something else you created</li>";
echo "</ul>";
echo "</div>";

echo "<div class='info'>";
echo "<h4>2. Finding Your Database Credentials</h4>";
echo "<p>In DirectAdmin:</p>";
echo "<ol>";
echo "<li>Go to <strong>MySQL Management</strong></li>";
echo "<li>Look for your database: <code>i888908_workshopplanner</code></li>";
echo "<li>Check the <strong>Users</strong> section to see which users have access</li>";
echo "<li>Note the exact username format</li>";
echo "<li>If needed, create a new user or reset the password</li>";
echo "</ol>";
echo "</div>";

echo "<div class='warning'>";
echo "<h4>3. Creating New Database User (if needed)</h4>";
echo "<p>If your current user doesn't work:</p>";
echo "<ol>";
echo "<li>In DirectAdmin, go to <strong>MySQL Management</strong></li>";
echo "<li>Create a new user (e.g., <code>i888908_workshop</code>)</li>";
echo "<li>Set a strong password</li>";
echo "<li>Grant ALL privileges to database <code>i888908_workshopplanner</code></li>";
echo "<li>Test the new credentials using the form above</li>";
echo "</ol>";
echo "</div>";

echo "<div class='success'>";
echo "<h4>4. Quick Test Variations</h4>";
echo "<p>Try these common username variations:</p>";
echo "<ul>";
echo "<li><code>i888908_admin</code></li>";
echo "<li><code>i888908</code> (without suffix)</li>";
echo "<li><code>i888908_workshopplanner</code></li>";
echo "<li><code>i888908_workshop</code></li>";
echo "</ul>";
echo "</div>";

echo "</body></html>";
?>