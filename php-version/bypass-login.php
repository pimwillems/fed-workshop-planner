<?php
/**
 * Bypass Login Test - Complete bypass of the routing system
 * This should work even if the main routing is broken
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Bypass Login Test</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .success{background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin:1rem 0;} .error{background:#f8d7da;color:#721c24;padding:1rem;border-radius:6px;margin:1rem 0;} .info{background:#d1ecf1;color:#0c5460;padding:1rem;border-radius:6px;margin:1rem 0;}</style>";
echo "</head><body>";

echo "<h1>🔧 Bypass Login Test</h1>";

try {
    // Load everything step by step
    echo "<p>Loading environment...</p>";
    if (file_exists('.env')) {
        $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && $line[0] !== '#') {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
    
    echo "<p>Loading core classes...</p>";
    require_once 'config/paths.php';
    require_once 'config/database.php';
    require_once 'src/Core/Security.php';
    require_once 'src/Core/JWT.php';
    
    echo "<p>Initializing security...</p>";
    Security::init();
    
    echo "<p>Getting CSRF token...</p>";
    $csrf_token = Security::getCSRFToken();
    $error = null;
    
    echo "<div class='success'>✅ All systems loaded successfully!</div>";
    echo "<div class='info'>CSRF Token: " . substr($csrf_token, 0, 10) . "...</div>";
    
    echo "<hr>";
    echo "<h2>Login Form (Direct)</h2>";
    echo "<p>This form bypasses all routing and goes directly to a login processor:</p>";
    
    // Simple direct login form
    echo '<form method="POST" action="bypass-login-process.php" style="max-width:400px;margin:2rem auto;padding:2rem;border:1px solid #ddd;border-radius:8px;">';
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($csrf_token) . '">';
    echo '<div style="margin-bottom:1rem;">';
    echo '<label style="display:block;margin-bottom:0.5rem;">Email:</label>';
    echo '<input type="email" name="email" value="admin@fed.nl" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">';
    echo '</div>';
    echo '<div style="margin-bottom:1rem;">';
    echo '<label style="display:block;margin-bottom:0.5rem;">Password:</label>';
    echo '<input type="password" name="password" placeholder="admin123" required style="width:100%;padding:0.5rem;border:1px solid #ddd;border-radius:4px;">';
    echo '</div>';
    echo '<button type="submit" style="width:100%;padding:0.75rem;background:#007bff;color:white;border:none;border-radius:4px;cursor:pointer;">Login (Bypass Test)</button>';
    echo '</form>';
    
    echo "<div class='info'>";
    echo "<p><strong>Test Credentials:</strong></p>";
    echo "<p>Email: admin@fed.nl</p>";
    echo "<p>Password: admin123</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
    echo "<div class='error'>File: " . $e->getFile() . "</div>";
    echo "<div class='error'>Line: " . $e->getLine() . "</div>";
}

echo "</body></html>";
?>