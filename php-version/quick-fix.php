<?php
/**
 * Quick Fix Script - Fixes the PathManager class name issue
 * Run this once to fix the Request.php file
 */

echo "🔧 Quick Fix Script<br><br>";

$requestFile = 'src/Core/Request.php';

if (file_exists($requestFile)) {
    $content = file_get_contents($requestFile);
    
    // Fix the class name
    $content = str_replace('PathManager::getBasePath()', 'Paths::getBasePath()', $content);
    
    if (file_put_contents($requestFile, $content)) {
        echo "✅ Fixed Request.php - replaced PathManager with Paths<br>";
    } else {
        echo "❌ Failed to write to Request.php<br>";
    }
} else {
    echo "❌ Request.php not found<br>";
}

echo "<br>✅ Fix complete! Now try accessing your application:<br>";
echo "<a href='index.php'>Test index.php</a><br>";
echo "<a href='./'>Test main application</a><br>";
echo "<br>⚠️ Delete this file after use for security!";
?>