<?php
/**
 * Complete Fix Script - Fixes all known issues
 * Run this once to fix all the problems
 */

echo "<h1>🔧 Complete Fix Script</h1>";

$fixes = [];

// Fix 1: Request.php PathManager issue
$requestFile = 'src/Core/Request.php';
if (file_exists($requestFile)) {
    $content = file_get_contents($requestFile);
    if (strpos($content, 'PathManager::getBasePath()') !== false) {
        $content = str_replace('PathManager::getBasePath()', 'Paths::getBasePath()', $content);
        if (file_put_contents($requestFile, $content)) {
            $fixes[] = "✅ Fixed Request.php - replaced PathManager with Paths";
        } else {
            $fixes[] = "❌ Failed to write to Request.php";
        }
    } else {
        $fixes[] = "✅ Request.php already fixed";
    }
} else {
    $fixes[] = "❌ Request.php not found";
}

// Fix 2: Database.php path issue
$databaseFile = 'config/database.php';
if (file_exists($databaseFile)) {
    $content = file_get_contents($databaseFile);
    if (strpos($content, "require_once 'app.php'") !== false) {
        $content = str_replace("require_once 'app.php'", "require_once __DIR__ . '/app.php'", $content);
        if (file_put_contents($databaseFile, $content)) {
            $fixes[] = "✅ Fixed database.php - corrected app.php path";
        } else {
            $fixes[] = "❌ Failed to write to database.php";
        }
    } else {
        $fixes[] = "✅ Database.php already fixed";
    }
} else {
    $fixes[] = "❌ Database.php not found";
}

// Fix 3: Check .env file
if (file_exists('.env')) {
    $fixes[] = "✅ .env file exists";
    
    $envContent = file_get_contents('.env');
    if (strpos($envContent, 'DB_HOST') !== false) {
        $fixes[] = "✅ .env contains database configuration";
    } else {
        $fixes[] = "❌ .env missing database configuration";
    }
} else {
    $fixes[] = "❌ .env file not found - you need to create this!";
}

// Display results
foreach ($fixes as $fix) {
    echo "<p>$fix</p>";
}

echo "<hr>";
echo "<h2>🎯 Next Steps</h2>";

if (!file_exists('.env')) {
    echo "<div style='background:#f8d7da;color:#721c24;padding:1rem;border-radius:6px;margin:1rem 0;'>";
    echo "<h3>⚠️ IMPORTANT: Create .env file</h3>";
    echo "<p>You need to create a .env file with your database credentials:</p>";
    echo "<pre>DB_HOST=localhost
DB_PORT=3306
DB_NAME=i888908_workshopplanner
DB_USER=your_database_username
DB_PASSWORD=your_database_password
JWT_SECRET=your-random-32-character-secret-key</pre>";
    echo "</div>";
} else {
    echo "<div style='background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin:1rem 0;'>";
    echo "<h3>✅ Ready to test!</h3>";
    echo "<p>Try these links:</p>";
    echo "<p><a href='index.php'>Test index.php</a></p>";
    echo "<p><a href='./'>Test main application</a></p>";
    echo "<p><a href='database/diagnose.php'>Test database connection</a></p>";
    echo "</div>";
}

echo "<p><strong>⚠️ Delete this file after use for security!</strong></p>";
?>