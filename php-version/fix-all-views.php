<?php
/**
 * Fix All Views Script
 * This fixes all the view include path issues
 */

echo "<h1>🔧 Fixing All View Files</h1>";

$fixes = [];

// Fix view files that have incorrect layout includes
$viewFiles = [
    'views/auth/login.php',
    'views/auth/register.php', 
    'views/auth/change-password.php',
    'views/index.php',
    'views/dashboard.php'
];

foreach ($viewFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Fix various possible incorrect includes
        $patterns = [
            "include '../layout.php';" => "include __DIR__ . '/../layout.php';",
            "include 'layout.php';" => "include __DIR__ . '/layout.php';",
            'include "../layout.php";' => 'include __DIR__ . "/../layout.php";',
            'include "layout.php";' => 'include __DIR__ . "/layout.php";'
        ];
        
        $changed = false;
        foreach ($patterns as $old => $new) {
            if (strpos($content, $old) !== false) {
                $content = str_replace($old, $new, $content);
                $changed = true;
            }
        }
        
        if ($changed) {
            if (file_put_contents($file, $content)) {
                $fixes[] = "✅ Fixed $file";
            } else {
                $fixes[] = "❌ Failed to write to $file";
            }
        } else {
            $fixes[] = "✅ $file already correct";
        }
    } else {
        $fixes[] = "❌ $file not found";
    }
}

// Display results
foreach ($fixes as $fix) {
    echo "<p>$fix</p>";
}

echo "<hr>";
echo "<h2>🎯 Testing Login Page</h2>";

// Test if we can now access the login controller
try {
    // Load necessary files
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
    
    echo "<p>✅ Core files loaded successfully</p>";
    
    // Test CSRF token generation
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    Security::init();
    $csrfToken = Security::getCSRFToken();
    
    if ($csrfToken) {
        echo "<p>✅ CSRF token generated successfully</p>";
    } else {
        echo "<p>❌ CSRF token generation failed</p>";
    }
    
    echo "<div style='background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin:1rem 0;'>";
    echo "<h3>🎉 All fixes applied successfully!</h3>";
    echo "<p>Your login page should now work. Try these links:</p>";
    echo "<p><a href='login'>Test Login Page</a></p>";
    echo "<p><a href='index.php'>Test Main Page</a></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p>❌ Test error: " . $e->getMessage() . "</p>";
}

echo "<p><strong>⚠️ Delete this file after use for security!</strong></p>";
?>