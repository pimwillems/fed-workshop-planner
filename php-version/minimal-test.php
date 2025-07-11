<?php
/**
 * Minimal Test - Just test the absolute basics
 */

// Turn on error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Minimal Test</title></head><body>";
echo "<h1>Minimal Application Test</h1>";

// Test 1: PHP works
echo "<h2>Test 1: PHP Basic</h2>";
echo "<p>✅ PHP is working: " . phpversion() . "</p>";

// Test 2: Files exist
echo "<h2>Test 2: File Existence</h2>";
$files = ['config/paths.php', 'config/database.php', 'src/Core/Router.php'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p>✅ $file exists</p>";
    } else {
        echo "<p>❌ $file missing</p>";
    }
}

// Test 3: Simple class loading
echo "<h2>Test 3: Class Loading</h2>";
try {
    require_once 'config/paths.php';
    echo "<p>✅ Paths class loaded</p>";
    
    $basePath = Paths::getBasePath();
    echo "<p>✅ Base path: $basePath</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

// Test 4: Environment
echo "<h2>Test 4: Environment</h2>";
if (file_exists('.env')) {
    echo "<p>✅ .env file exists</p>";
    
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && $line[0] !== '#') {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
    
    if (isset($_ENV['DB_HOST'])) {
        echo "<p>✅ DB_HOST configured</p>";
    } else {
        echo "<p>❌ DB_HOST not configured</p>";
    }
} else {
    echo "<p>❌ .env file missing</p>";
}

// Test 5: Database
echo "<h2>Test 5: Database</h2>";
try {
    require_once 'config/database.php';
    echo "<p>✅ Database class loaded</p>";
    
    $db = Database::getInstance();
    echo "<p>✅ Database connection successful</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

// Test 6: Manual login page
echo "<h2>Test 6: Manual Login Test</h2>";
echo "<p>Try this basic login form:</p>";
echo "<form method='GET' action='login'>";
echo "<button type='submit'>Test Login Page</button>";
echo "</form>";

echo "<hr>";
echo "<p><strong>If all tests pass, the issue might be with URL rewriting or server configuration.</strong></p>";
echo "<p><a href='index.php'>Try index.php directly</a></p>";
echo "<p><a href='login'>Try login page</a></p>";

echo "</body></html>";
?>