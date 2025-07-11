<?php
// Simple test to verify basic functionality
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Simple Application Test</h1>";

// Test 1: Basic PHP
echo "<p>✅ PHP is working: " . phpversion() . "</p>";

// Test 2: Try to load config
echo "<h2>Loading Configuration...</h2>";
try {
    require_once 'config/paths.php';
    echo "<p>✅ PathManager loaded</p>";
    
    $basePath = PathManager::getBasePath();
    echo "<p>✅ Base path: $basePath</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Config error: " . $e->getMessage() . "</p>";
}

// Test 3: Try to load core classes
echo "<h2>Loading Core Classes...</h2>";
try {
    require_once 'src/Core/Database.php';
    echo "<p>✅ Database class loaded</p>";
    
    require_once 'src/Core/Router.php';
    echo "<p>✅ Router class loaded</p>";
    
    require_once 'src/Core/Security.php';
    echo "<p>✅ Security class loaded</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Core class error: " . $e->getMessage() . "</p>";
}

// Test 4: Try to instantiate router
echo "<h2>Testing Router...</h2>";
try {
    $router = new Router();
    echo "<p>✅ Router instantiated successfully</p>";
    
    // Test adding a route
    $router->get('/', function() {
        return "Test route works!";
    });
    echo "<p>✅ Route added successfully</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Router error: " . $e->getMessage() . "</p>";
}

// Test 5: Database connection
echo "<h2>Testing Database...</h2>";
try {
    $db = new Database();
    echo "<p>✅ Database connection successful</p>";
    
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='debug-500.php'>Run Full Debug Tool</a></p>";
echo "<p><a href='database/diagnose.php'>Database Diagnostic</a></p>";
?>