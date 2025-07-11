<?php
/**
 * Direct Login Test - Bypass router and test login directly
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Direct Login Test</title></head><body>";
echo "<h1>Direct Login Test</h1>";

try {
    // Load environment
    if (file_exists('.env')) {
        $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && $line[0] !== '#') {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
        echo "<p>✅ Environment loaded</p>";
    }
    
    // Load required files
    require_once 'config/paths.php';
    require_once 'config/database.php';
    require_once 'src/Core/Security.php';
    require_once 'src/Core/JWT.php';
    
    echo "<p>✅ Core files loaded</p>";
    
    // Initialize security
    Security::init();
    echo "<p>✅ Security initialized</p>";
    
    // Get CSRF token
    $csrfToken = Security::getCSRFToken();
    echo "<p>✅ CSRF token: " . substr($csrfToken, 0, 10) . "...</p>";
    
    // Test if we can include the login view directly
    echo "<h2>Testing Login View</h2>";
    
    // Set up variables that the view expects
    $error = null;
    $csrf_token = $csrfToken;
    
    // Test including the login view
    ob_start();
    include 'views/auth/login.php';
    $loginContent = ob_get_clean();
    
    echo "<p>✅ Login view rendered successfully</p>";
    echo "<p>Content length: " . strlen($loginContent) . " bytes</p>";
    
    // Show the actual login form
    echo "<hr>";
    echo "<h2>Login Form</h2>";
    echo $loginContent;
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
?>