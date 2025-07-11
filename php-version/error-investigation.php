<?php
/**
 * 500 Error Investigation Tool
 * This script will help identify the exact cause of the 500 error
 */

// Turn on all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Set up error handler to catch all errors
set_error_handler(function($severity, $message, $file, $line) {
    echo "<div style='background:#f8d7da;color:#721c24;padding:1rem;margin:1rem 0;border-radius:6px;'>";
    echo "<strong>PHP Error:</strong> $message<br>";
    echo "<strong>File:</strong> $file<br>";
    echo "<strong>Line:</strong> $line<br>";
    echo "<strong>Severity:</strong> $severity";
    echo "</div>";
    return false; // Let PHP handle the error normally too
});

// Set up exception handler
set_exception_handler(function($exception) {
    echo "<div style='background:#f8d7da;color:#721c24;padding:1rem;margin:1rem 0;border-radius:6px;'>";
    echo "<strong>Uncaught Exception:</strong> " . $exception->getMessage() . "<br>";
    echo "<strong>File:</strong> " . $exception->getFile() . "<br>";
    echo "<strong>Line:</strong> " . $exception->getLine() . "<br>";
    echo "<strong>Stack Trace:</strong><br><pre>" . $exception->getTraceAsString() . "</pre>";
    echo "</div>";
});

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 Error Investigation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; line-height: 1.6; }
        .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .warning { background: #fff3cd; color: #856404; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        pre { background: #f8f9fa; padding: 1rem; border-radius: 6px; overflow-x: auto; font-size: 0.9rem; }
        .test-section { margin: 2rem 0; padding: 1rem; border: 1px solid #ddd; border-radius: 6px; }
    </style>
</head>
<body>
    <h1>🔍 500 Error Investigation</h1>
    
    <?php
    
    echo "<div class='test-section'>";
    echo "<h2>Step 1: Environment Check</h2>";
    
    // Check PHP version
    echo "<div class='info'>";
    echo "<strong>PHP Version:</strong> " . phpversion() . "<br>";
    echo "<strong>Current Directory:</strong> " . getcwd() . "<br>";
    echo "<strong>Script Path:</strong> " . __FILE__ . "<br>";
    echo "</div>";
    
    // Check .env file
    echo "<h3>Environment File Check</h3>";
    if (file_exists('.env')) {
        echo "<div class='success'>✅ .env file exists</div>";
        
        // Try to parse .env
        $envContent = file_get_contents('.env');
        if (strpos($envContent, 'DB_HOST') !== false) {
            echo "<div class='success'>✅ .env contains database configuration</div>";
        } else {
            echo "<div class='error'>❌ .env missing database configuration</div>";
        }
    } else {
        echo "<div class='error'>❌ .env file not found</div>";
        echo "<div class='warning'>Expected location: " . realpath('.') . "/.env</div>";
    }
    
    echo "</div>";
    
    // Test file existence
    echo "<div class='test-section'>";
    echo "<h2>Step 2: Critical Files Check</h2>";
    
    $requiredFiles = [
        'index.php' => 'Main entry point',
        'config/paths.php' => 'Path configuration',
        'src/Core/Database.php' => 'Database class',
        'src/Core/Router.php' => 'Router class',
        'src/Core/Security.php' => 'Security class',
        'src/Controllers/HomeController.php' => 'Home controller',
        '.htaccess' => 'URL rewriting rules'
    ];
    
    foreach ($requiredFiles as $file => $description) {
        if (file_exists($file)) {
            echo "<div class='success'>✅ $file ($description)</div>";
        } else {
            echo "<div class='error'>❌ $file missing ($description)</div>";
        }
    }
    
    echo "</div>";
    
    // Test class loading
    echo "<div class='test-section'>";
    echo "<h2>Step 3: Class Loading Test</h2>";
    
    try {
        echo "<h3>Loading PathManager...</h3>";
        require_once 'config/paths.php';
        echo "<div class='success'>✅ PathManager loaded successfully</div>";
        
        $basePath = PathManager::getBasePath();
        echo "<div class='info'>Base path detected: $basePath</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ PathManager failed: " . $e->getMessage() . "</div>";
    }
    
    try {
        echo "<h3>Loading Database class...</h3>";
        require_once 'src/Core/Database.php';
        echo "<div class='success'>✅ Database class loaded</div>";
        
        // Try to instantiate (this might fail due to missing .env)
        $db = new Database();
        echo "<div class='success'>✅ Database instantiated successfully</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Database failed: " . $e->getMessage() . "</div>";
    }
    
    try {
        echo "<h3>Loading Router class...</h3>";
        require_once 'src/Core/Router.php';
        echo "<div class='success'>✅ Router class loaded</div>";
        
        $router = new Router();
        echo "<div class='success'>✅ Router instantiated successfully</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Router failed: " . $e->getMessage() . "</div>";
    }
    
    try {
        echo "<h3>Loading Security class...</h3>";
        require_once 'src/Core/Security.php';
        echo "<div class='success'>✅ Security class loaded</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Security failed: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Test index.php simulation
    echo "<div class='test-section'>";
    echo "<h2>Step 4: Index.php Simulation</h2>";
    
    echo "<div class='info'>Attempting to simulate the main index.php flow...</div>";
    
    try {
        echo "<h3>Simulating index.php startup...</h3>";
        
        // This is what index.php does
        ob_start(); // Capture any output
        
        // The actual code from index.php
        require_once 'config/paths.php';
        require_once 'src/Core/Database.php';
        require_once 'src/Core/Router.php';
        require_once 'src/Core/Security.php';
        
        // Initialize session
        session_start();
        
        // Initialize security
        Security::init();
        
        // Create router
        $router = new Router();
        
        // Add routes (simplified version)
        $router->get('/', 'HomeController@index');
        $router->get('/login', 'HomeController@login');
        
        $output = ob_get_clean();
        
        echo "<div class='success'>✅ Index.php simulation completed successfully</div>";
        if (!empty($output)) {
            echo "<div class='info'>Output captured: <pre>" . htmlspecialchars($output) . "</pre></div>";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<div class='error'>❌ Index.php simulation failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    echo "</div>";
    
    // Check server configuration
    echo "<div class='test-section'>";
    echo "<h2>Step 5: Server Configuration</h2>";
    
    echo "<div class='info'>";
    echo "<strong>Server Software:</strong> " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
    echo "<strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "<br>";
    echo "<strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "<br>";
    echo "<strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
    echo "<strong>HTTP Host:</strong> " . $_SERVER['HTTP_HOST'] . "<br>";
    echo "</div>";
    
    // Check .htaccess
    if (file_exists('.htaccess')) {
        echo "<div class='success'>✅ .htaccess file exists</div>";
        $htaccess = file_get_contents('.htaccess');
        echo "<div class='info'>Content preview: <pre>" . htmlspecialchars(substr($htaccess, 0, 500)) . "</pre></div>";
    } else {
        echo "<div class='error'>❌ .htaccess file missing</div>";
    }
    
    echo "</div>";
    
    ?>
    
    <div class="test-section">
        <h2>Step 6: Manual Testing</h2>
        <div class="info">
            <p>Try these manual tests:</p>
            <ul>
                <li><a href="index.php" target="_blank">Direct index.php access</a></li>
                <li><a href="simple-test.php" target="_blank">Simple test script</a></li>
                <li><a href="database/diagnose.php" target="_blank">Database diagnostic</a></li>
            </ul>
        </div>
    </div>
    
    <div class="test-section">
        <h2>🎯 Diagnosis Summary</h2>
        <div class="info">
            <p><strong>Based on the tests above:</strong></p>
            <ul>
                <li>If all tests pass but the main site still shows 500 error, the issue is likely in the routing or controller loading</li>
                <li>If class loading fails, check file permissions and paths</li>
                <li>If database connection fails, verify .env configuration</li>
                <li>If .htaccess is missing, URL rewriting won't work</li>
            </ul>
        </div>
    </div>
    
    <div class="error">
        <h2>⚠️ Security Note</h2>
        <p>Delete this file after debugging: <code>error-investigation.php</code></p>
    </div>
    
</body>
</html>