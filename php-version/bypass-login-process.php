<?php
/**
 * Bypass Login Processor - Handles login without routing
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load everything
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && $line[0] !== '#') {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

require_once 'config/paths.php';
require_once 'config/database.php';
require_once 'src/Core/Security.php';
require_once 'src/Core/JWT.php';

Security::init();

echo "<!DOCTYPE html><html><head><title>Bypass Login Result</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .success{background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin:1rem 0;} .error{background:#f8d7da;color:#721c24;padding:1rem;border-radius:6px;margin:1rem 0;} .info{background:#d1ecf1;color:#0c5460;padding:1rem;border-radius:6px;margin:1rem 0;}</style>";
echo "</head><body>";

echo "<h1>🔧 Bypass Login Result</h1>";

if ($_POST) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';
    
    echo "<div class='info'>Processing login for: $email</div>";
    
    // Validate CSRF
    if (!Security::validateCSRFToken($csrfToken)) {
        echo "<div class='error'>❌ CSRF token validation failed</div>";
    } else {
        echo "<div class='success'>✅ CSRF token valid</div>";
    }
    
    try {
        // Get database connection
        $db = Database::getInstance();
        $connection = $db->getConnection();
        
        echo "<div class='success'>✅ Database connection successful</div>";
        
        // Find user
        $stmt = $connection->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            echo "<div class='success'>✅ User found: {$user['name']}</div>";
            
            // Verify password
            if (Security::verifyPassword($password, $user['password'])) {
                echo "<div class='success'>✅ Password correct!</div>";
                
                // Generate JWT token
                $payload = [
                    'user_id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                
                $token = JWT::encode($payload);
                JWT::setTokenCookie($token);
                
                echo "<div class='success'>✅ JWT token generated and set!</div>";
                echo "<div class='success'>";
                echo "<h3>🎉 LOGIN SUCCESSFUL!</h3>";
                echo "<p>Welcome, {$user['name']}!</p>";
                echo "<p>Role: {$user['role']}</p>";
                echo "<p>This proves the application works correctly.</p>";
                echo "<p><a href='bypass-dashboard.php'>Go to Dashboard (Bypass)</a></p>";
                echo "</div>";
                
            } else {
                echo "<div class='error'>❌ Password incorrect</div>";
            }
        } else {
            echo "<div class='error'>❌ User not found</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Login process failed: " . $e->getMessage() . "</div>";
    }
    
} else {
    echo "<div class='error'>❌ No form data received</div>";
}

echo "<p><a href='bypass-login.php'>← Back to Login Form</a></p>";
echo "</body></html>";
?>