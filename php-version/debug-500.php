<?php
// Debug 500 Error Tool
// This will help identify what's causing the 500 error

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<!DOCTYPE html><html><head><title>500 Error Debug</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .error{background:#f8d7da;color:#721c24;padding:1rem;border-radius:6px;margin:1rem 0;} .success{background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin:1rem 0;} .info{background:#d1ecf1;color:#0c5460;padding:1rem;border-radius:6px;margin:1rem 0;} pre{background:#f8f9fa;padding:1rem;border-radius:6px;overflow-x:auto;}</style>";
echo "</head><body>";

echo "<h1>🔍 500 Error Debug Tool</h1>";

// Test 1: Basic PHP functionality
echo "<div class='info'><h2>Test 1: Basic PHP</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current directory: " . getcwd() . "<br>";
echo "Script file: " . __FILE__ . "<br>";
echo "</div>";

// Test 2: Check if .env file exists
echo "<div class='info'><h2>Test 2: Environment File</h2>";
$envPath = '.env';
if (file_exists($envPath)) {
    echo "✅ .env file exists<br>";
    $envContent = file_get_contents($envPath);
    if (strpos($envContent, 'DB_HOST') !== false) {
        echo "✅ .env contains database config<br>";
    } else {
        echo "❌ .env missing database config<br>";
    }
} else {
    echo "❌ .env file not found<br>";
    echo "Expected location: " . realpath('.') . "/.env<br>";
}
echo "</div>";

// Test 3: Check critical files
echo "<div class='info'><h2>Test 3: Critical Files</h2>";
$criticalFiles = [
    'index.php',
    'src/Core/Router.php',
    'src/Core/Database.php',
    'src/Core/Security.php',
    'src/Controllers/HomeController.php',
    'config/paths.php',
    '.htaccess'
];

foreach ($criticalFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}
echo "</div>";

// Test 4: Try loading core classes
echo "<div class='info'><h2>Test 4: Class Loading</h2>";
try {
    // Test autoloading
    if (file_exists('vendor/autoload.php')) {
        echo "✅ Composer autoloader exists<br>";
        require_once 'vendor/autoload.php';
    } else {
        echo "⚠️ No Composer autoloader, using manual loading<br>";
    }
    
    // Test manual loading of core classes
    $coreClasses = [
        'src/Core/Database.php' => 'Database',
        'src/Core/Router.php' => 'Router',
        'src/Core/Security.php' => 'Security',
        'config/paths.php' => 'PathManager'
    ];
    
    foreach ($coreClasses as $file => $class) {
        if (file_exists($file)) {
            try {
                require_once $file;
                echo "✅ $class loaded successfully<br>";
            } catch (Exception $e) {
                echo "❌ $class failed to load: " . $e->getMessage() . "<br>";
            }
        } else {
            echo "❌ $file not found<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Class loading error: " . $e->getMessage() . "<br>";
}
echo "</div>";

// Test 5: Database connection
echo "<div class='info'><h2>Test 5: Database Connection</h2>";
try {
    // Load .env manually
    $config = [];
    if (file_exists('.env')) {
        $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && $line[0] !== '#') {
                list($key, $value) = explode('=', $line, 2);
                $config[trim($key)] = trim($value);
            }
        }
    }
    
    if (isset($config['DB_HOST']) && isset($config['DB_NAME'])) {
        $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset=utf8mb4";
        $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✅ Database connection successful<br>";
        
        // Test table existence
        $tables = ['users', 'workshops', 'login_attempts', 'csrf_tokens'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->rowCount() > 0) {
                echo "✅ Table '$table' exists<br>";
            } else {
                echo "❌ Table '$table' missing<br>";
            }
        }
    } else {
        echo "❌ Database configuration missing from .env<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}
echo "</div>";

// Test 6: Try to simulate the main index.php flow
echo "<div class='info'><h2>Test 6: Main Application Flow</h2>";
try {
    // Test if we can access the Router
    if (class_exists('Router')) {
        echo "✅ Router class available<br>";
        $router = new Router();
        echo "✅ Router instantiated<br>";
    } else {
        echo "❌ Router class not available<br>";
    }
    
    // Test path detection
    if (class_exists('PathManager')) {
        echo "✅ PathManager class available<br>";
        $basePath = PathManager::getBasePath();
        echo "✅ Base path detected: $basePath<br>";
    } else {
        echo "❌ PathManager class not available<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Application flow error: " . $e->getMessage() . "<br>";
}
echo "</div>";

// Test 7: Check .htaccess
echo "<div class='info'><h2>Test 7: URL Rewriting</h2>";
if (file_exists('.htaccess')) {
    echo "✅ .htaccess file exists<br>";
    $htaccess = file_get_contents('.htaccess');
    if (strpos($htaccess, 'RewriteEngine On') !== false) {
        echo "✅ URL rewriting enabled<br>";
    } else {
        echo "❌ URL rewriting not enabled<br>";
    }
    
    if (strpos($htaccess, 'fed-workshops') !== false) {
        echo "✅ Path configured for fed-workshops<br>";
    } else {
        echo "⚠️ Path may not be configured correctly<br>";
    }
} else {
    echo "❌ .htaccess file missing<br>";
}
echo "</div>";

// Test 8: Test direct access to index.php
echo "<div class='info'><h2>Test 8: Direct Index Access</h2>";
echo "Try accessing: <a href='index.php' target='_blank'>index.php directly</a><br>";
echo "Current URL: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Script name: " . $_SERVER['SCRIPT_NAME'] . "<br>";
echo "Server name: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "</div>";

// Test 9: PHP Error Log
echo "<div class='info'><h2>Test 9: PHP Error Information</h2>";
echo "Error reporting level: " . error_reporting() . "<br>";
echo "Display errors: " . (ini_get('display_errors') ? 'ON' : 'OFF') . "<br>";
echo "Log errors: " . (ini_get('log_errors') ? 'ON' : 'OFF') . "<br>";
echo "Error log location: " . ini_get('error_log') . "<br>";
echo "</div>";

echo "<div class='success'><h2>🎯 Next Steps</h2>";
echo "<p>1. Check the results above for any ❌ errors</p>";
echo "<p>2. Try accessing <a href='index.php'>index.php directly</a></p>";
echo "<p>3. Check your server's error logs for more details</p>";
echo "<p>4. If all looks good, the issue might be server configuration</p>";
echo "</div>";

echo "</body></html>";
?>