<?php
/**
 * Database Connection Fixer
 * This will help you get the database working once and for all
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Database Connection Fixer</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .success{background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin:1rem 0;} .error{background:#f8d7da;color:#721c24;padding:1rem;border-radius:6px;margin:1rem 0;} .info{background:#d1ecf1;color:#0c5460;padding:1rem;border-radius:6px;margin:1rem 0;} .warning{background:#fff3cd;color:#856404;padding:1rem;border-radius:6px;margin:1rem 0;} pre{background:#f8f9fa;padding:1rem;border-radius:6px;overflow-x:auto;} .form{background:#f8f9fa;padding:1.5rem;border-radius:6px;margin:1rem 0;}</style>";
echo "</head><body>";

echo "<h1>🔧 Database Connection Fixer</h1>";

// Step 1: Check current .env file
echo "<h2>Step 1: Current .env File Status</h2>";
if (file_exists('.env')) {
    echo "<div class='success'>✅ .env file exists</div>";
    
    $envContent = file_get_contents('.env');
    echo "<div class='info'>File size: " . strlen($envContent) . " bytes</div>";
    
    // Show current contents (hiding password)
    echo "<div class='info'><strong>Current .env contents:</strong></div>";
    echo "<pre>";
    $lines = explode("\n", $envContent);
    foreach ($lines as $line) {
        if (strpos($line, 'DB_PASSWORD') !== false) {
            echo "DB_PASSWORD=***HIDDEN***\n";
        } else {
            echo htmlspecialchars($line) . "\n";
        }
    }
    echo "</pre>";
    
    // Parse env vars
    $envVars = [];
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && $line[0] !== '#') {
            list($key, $value) = explode('=', $line, 2);
            $envVars[trim($key)] = trim($value);
        }
    }
    
} else {
    echo "<div class='error'>❌ .env file does not exist!</div>";
    $envVars = [];
}

// Step 2: Test current credentials if they exist
if (!empty($envVars) && isset($envVars['DB_HOST'])) {
    echo "<h2>Step 2: Testing Current Database Credentials</h2>";
    
    $host = $envVars['DB_HOST'] ?? 'localhost';
    $port = $envVars['DB_PORT'] ?? 3306;
    $database = $envVars['DB_NAME'] ?? '';
    $username = $envVars['DB_USER'] ?? '';
    $password = $envVars['DB_PASSWORD'] ?? '';
    
    echo "<div class='info'>";
    echo "Host: $host<br>";
    echo "Port: $port<br>";
    echo "Database: $database<br>";
    echo "Username: $username<br>";
    echo "Password: " . (empty($password) ? 'EMPTY' : '***SET*** (length: ' . strlen($password) . ')');
    echo "</div>";
    
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='success'>✅ Current credentials work!</div>";
        
        // Test tables
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<div class='success'>✅ Found {$result['count']} users in database</div>";
        
        echo "<div class='success'><h3>🎉 Database is working! The issue might be elsewhere.</h3></div>";
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Current credentials failed: " . $e->getMessage() . "</div>";
    }
}

// Step 3: Interactive credential tester
echo "<h2>Step 3: Test New Database Credentials</h2>";

if ($_POST && isset($_POST['test_db'])) {
    $testHost = $_POST['host'] ?? 'localhost';
    $testPort = $_POST['port'] ?? 3306;
    $testDatabase = $_POST['database'] ?? '';
    $testUsername = $_POST['username'] ?? '';
    $testPassword = $_POST['password'] ?? '';
    
    echo "<div class='warning'><strong>Testing credentials:</strong><br>";
    echo "Host: $testHost<br>";
    echo "Port: $testPort<br>";
    echo "Database: $testDatabase<br>";
    echo "Username: $testUsername<br>";
    echo "Password: " . (empty($testPassword) ? 'EMPTY' : '***PROVIDED***');
    echo "</div>";
    
    try {
        $dsn = "mysql:host=$testHost;port=$testPort;dbname=$testDatabase;charset=utf8mb4";
        $testPdo = new PDO($dsn, $testUsername, $testPassword);
        $testPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        echo "<div class='success'>✅ TEST CONNECTION SUCCESSFUL!</div>";
        
        // Test if our tables exist
        try {
            $stmt = $testPdo->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch();
            echo "<div class='success'>✅ Users table exists with {$result['count']} users</div>";
            
            // Test if our default users exist
            $stmt = $testPdo->prepare("SELECT email, name, role FROM users WHERE email IN (?, ?)");
            $stmt->execute(['admin@fed.nl', 'teacher@fed.nl']);
            $users = $stmt->fetchAll();
            
            echo "<div class='success'>✅ Found " . count($users) . " default users:</div>";
            foreach ($users as $user) {
                echo "<div class='info'>- {$user['email']} ({$user['name']}) - {$user['role']}</div>";
            }
            
        } catch (PDOException $e) {
            echo "<div class='warning'>⚠️ Connected but tables missing: " . $e->getMessage() . "</div>";
        }
        
        // Generate .env content
        echo "<div class='success'>";
        echo "<h3>✅ Working Credentials Found!</h3>";
        echo "<p>Create/update your .env file with these credentials:</p>";
        echo "<pre>DB_HOST=$testHost
DB_PORT=$testPort
DB_NAME=$testDatabase
DB_USER=$testUsername
DB_PASSWORD=$testPassword
JWT_SECRET=your-random-32-character-secret-key-here-change-this</pre>";
        echo "</div>";
        
        // Auto-create .env file option
        echo "<div class='form'>";
        echo "<h4>Auto-create .env file</h4>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='create_env' value='1'>";
        echo "<input type='hidden' name='env_host' value='$testHost'>";
        echo "<input type='hidden' name='env_port' value='$testPort'>";
        echo "<input type='hidden' name='env_database' value='$testDatabase'>";
        echo "<input type='hidden' name='env_username' value='$testUsername'>";
        echo "<input type='hidden' name='env_password' value='$testPassword'>";
        echo "<button type='submit' style='background:#28a745;color:white;padding:0.75rem 1.5rem;border:none;border-radius:4px;cursor:pointer;'>Create .env File Automatically</button>";
        echo "</form>";
        echo "</div>";
        
    } catch (PDOException $e) {
        echo "<div class='error'>❌ Test connection failed: " . $e->getMessage() . "</div>";
        
        // Provide specific help based on error
        $errorCode = $e->getCode();
        $errorMessage = $e->getMessage();
        
        if (strpos($errorMessage, 'Access denied') !== false) {
            echo "<div class='warning'>";
            echo "<h4>Access Denied - Try These Solutions:</h4>";
            echo "<ul>";
            echo "<li>Double-check the username (try: i888908_admin, i888908_workshop, etc.)</li>";
            echo "<li>Verify the password is correct</li>";
            echo "<li>In DirectAdmin, go to MySQL Management and check user permissions</li>";
            echo "<li>Create a new database user if needed</li>";
            echo "</ul>";
            echo "</div>";
        } elseif (strpos($errorMessage, 'Unknown database') !== false) {
            echo "<div class='warning'>";
            echo "<h4>Database Not Found - Try These:</h4>";
            echo "<ul>";
            echo "<li>Check exact database name in DirectAdmin</li>";
            echo "<li>Common format: i888908_workshopplanner</li>";
            echo "<li>Make sure the database was created</li>";
            echo "</ul>";
            echo "</div>";
        }
    }
}

// Handle auto-create .env
if ($_POST && isset($_POST['create_env'])) {
    $envContent = "# Environment Configuration
DEBUG=false
APP_ENV=production

# Database Configuration
DB_HOST={$_POST['env_host']}
DB_PORT={$_POST['env_port']}
DB_NAME={$_POST['env_database']}
DB_USER={$_POST['env_username']}
DB_PASSWORD={$_POST['env_password']}

# JWT Security - CHANGE THIS TO A RANDOM 32-CHARACTER STRING
JWT_SECRET=your-random-32-character-secret-key-here-change-this

# Security Settings
CSRF_EXPIRATION=3600
SESSION_NAME=workshop_session
PASSWORD_MIN_LENGTH=6
MAX_LOGIN_ATTEMPTS=5
LOCKOUT_DURATION=900";

    if (file_put_contents('.env', $envContent)) {
        echo "<div class='success'>✅ .env file created successfully!</div>";
        echo "<div class='success'>Now test your login page: <a href='login'>Test Login</a></div>";
    } else {
        echo "<div class='error'>❌ Failed to create .env file</div>";
    }
}

// Credential test form
echo "<div class='form'>";
echo "<form method='post'>";
echo "<h4>Test Database Credentials</h4>";
echo "<p><label>Host: <input type='text' name='host' value='localhost' style='width:200px;padding:0.5rem;margin-left:0.5rem;'></label></p>";
echo "<p><label>Port: <input type='text' name='port' value='3306' style='width:200px;padding:0.5rem;margin-left:0.5rem;'></label></p>";
echo "<p><label>Database: <input type='text' name='database' value='i888908_workshopplanner' style='width:200px;padding:0.5rem;margin-left:0.5rem;'></label></p>";
echo "<p><label>Username: <input type='text' name='username' value='' placeholder='i888908_username' style='width:200px;padding:0.5rem;margin-left:0.5rem;'></label></p>";
echo "<p><label>Password: <input type='password' name='password' value='' style='width:200px;padding:0.5rem;margin-left:0.5rem;'></label></p>";
echo "<p><button type='submit' name='test_db' style='background:#007bff;color:white;padding:0.75rem 1.5rem;border:none;border-radius:4px;cursor:pointer;'>Test Connection</button></p>";
echo "</form>";
echo "</div>";

echo "<h2>📋 DirectAdmin Database Help</h2>";
echo "<div class='info'>";
echo "<h4>Finding Your Database Credentials in DirectAdmin:</h4>";
echo "<ol>";
echo "<li>Login to DirectAdmin</li>";
echo "<li>Go to <strong>MySQL Management</strong></li>";
echo "<li>Look for database: <code>i888908_workshopplanner</code></li>";
echo "<li>Check which users have access to this database</li>";
echo "<li>Common username formats: <code>i888908_admin</code>, <code>i888908_workshop</code>, etc.</li>";
echo "<li>If needed, create a new user or reset password</li>";
echo "</ol>";
echo "</div>";

echo "<div class='warning'>";
echo "<h4>⚠️ Important Notes:</h4>";
echo "<ul>";
echo "<li>The database <code>i888908_workshopplanner</code> should already exist from your earlier setup</li>";
echo "<li>If you can't find working credentials, create a new database user in DirectAdmin</li>";
echo "<li>Make sure the user has ALL privileges on the database</li>";
echo "<li>Once you get working credentials, your login page should work immediately</li>";
echo "</ul>";
echo "</div>";

echo "</body></html>";
?>