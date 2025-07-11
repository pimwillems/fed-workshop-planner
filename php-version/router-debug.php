<?php
/**
 * Router Debug Tool - Test the actual routing system
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Capture all errors
$errors = [];
set_error_handler(function($severity, $message, $file, $line) use (&$errors) {
    $errors[] = "Error: $message in $file on line $line";
    return false;
});

set_exception_handler(function($exception) use (&$errors) {
    $errors[] = "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
});

?>
<!DOCTYPE html>
<html>
<head>
    <title>Router Debug Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .section { margin: 2rem 0; padding: 1rem; border: 1px solid #ddd; border-radius: 6px; }
        pre { background: #f8f9fa; padding: 1rem; border-radius: 6px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>🔍 Router Debug Tool</h1>
    
    <?php
    
    echo "<div class='section'>";
    echo "<h2>Current Request Information</h2>";
    echo "<div class='info'>";
    echo "<p><strong>REQUEST_METHOD:</strong> " . ($_SERVER['REQUEST_METHOD'] ?? 'Not set') . "</p>";
    echo "<p><strong>REQUEST_URI:</strong> " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "</p>";
    echo "<p><strong>SCRIPT_NAME:</strong> " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "</p>";
    echo "<p><strong>HTTP_HOST:</strong> " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "</p>";
    echo "<p><strong>PHP_SELF:</strong> " . ($_SERVER['PHP_SELF'] ?? 'Not set') . "</p>";
    echo "<p><strong>QUERY_STRING:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'Not set') . "</p>";
    echo "</div>";
    echo "</div>";
    
    // Test 1: Load basic components
    echo "<div class='section'>";
    echo "<h2>Test 1: Loading Core Components</h2>";
    
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
        }
        
        require_once 'config/paths.php';
        require_once 'config/database.php';
        require_once 'src/Core/Router.php';
        require_once 'src/Core/Request.php';
        require_once 'src/Core/Response.php';
        require_once 'src/Core/Security.php';
        require_once 'src/Core/JWT.php';
        
        echo "<div class='success'>✅ All core components loaded</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Component loading failed: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Test 2: Path detection
    echo "<div class='section'>";
    echo "<h2>Test 2: Path Detection</h2>";
    
    try {
        $basePath = Paths::getBasePath();
        echo "<div class='info'>Base path detected: '$basePath'</div>";
        
        $absoluteUrl = Paths::getAbsoluteUrl('login');
        echo "<div class='info'>Absolute URL for login: '$absoluteUrl'</div>";
        
        $relativeUrl = Paths::getRelativeUrl('login');
        echo "<div class='info'>Relative URL for login: '$relativeUrl'</div>";
        
        echo "<div class='success'>✅ Path detection working</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Path detection failed: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Test 3: Request object creation
    echo "<div class='section'>";
    echo "<h2>Test 3: Request Object</h2>";
    
    try {
        Security::init();
        
        $request = new Request();
        echo "<div class='info'>Request method: " . $request->getMethod() . "</div>";
        echo "<div class='info'>Request path: '" . $request->getPath() . "'</div>";
        
        echo "<div class='success'>✅ Request object created successfully</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Request object creation failed: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Test 4: Router setup
    echo "<div class='section'>";
    echo "<h2>Test 4: Router Setup</h2>";
    
    try {
        $router = new Router();
        echo "<div class='success'>✅ Router created</div>";
        
        // Add the same routes as index.php
        $router->get('/', 'HomeController@index');
        $router->get('/login', 'AuthController@showLogin');
        $router->post('/login', 'AuthController@login');
        
        echo "<div class='success'>✅ Routes added</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Router setup failed: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Test 5: Route matching simulation
    echo "<div class='section'>";
    echo "<h2>Test 5: Route Matching Simulation</h2>";
    
    try {
        // Simulate different login requests
        $testPaths = [
            '/login',
            'login',
            '/fed-workshops/login',
            'fed-workshops/login'
        ];
        
        foreach ($testPaths as $testPath) {
            echo "<div class='info'>Testing path: '$testPath'</div>";
            
            // Simulate the request
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = $testPath;
            
            $testRequest = new Request();
            echo "<div class='info'>→ Request path resolved to: '" . $testRequest->getPath() . "'</div>";
        }
        
        echo "<div class='success'>✅ Route matching test completed</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Route matching failed: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Test 6: Full dispatch simulation
    echo "<div class='section'>";
    echo "<h2>Test 6: Full Dispatch Simulation</h2>";
    
    try {
        // Create a clean request for /login
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/fed-workshops/login';
        $_SERVER['SCRIPT_NAME'] = '/fed-workshops/index.php';
        
        $request = new Request();
        $response = new Response();
        $router = new Router();
        
        // Add routes
        $router->get('/', 'HomeController@index');
        $router->get('/login', 'AuthController@showLogin');
        
        echo "<div class='info'>Simulating dispatch for path: '" . $request->getPath() . "'</div>";
        
        // This is where the actual error likely occurs
        ob_start();
        $router->dispatch($request, $response);
        $output = ob_get_clean();
        
        echo "<div class='success'>✅ Dispatch completed successfully</div>";
        echo "<div class='info'>Output length: " . strlen($output) . " bytes</div>";
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<div class='error'>❌ Dispatch failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    echo "</div>";
    
    // Test 7: Check .htaccess
    echo "<div class='section'>";
    echo "<h2>Test 7: URL Rewriting Check</h2>";
    
    if (file_exists('.htaccess')) {
        echo "<div class='success'>✅ .htaccess file exists</div>";
        
        $htaccess = file_get_contents('.htaccess');
        echo "<div class='info'>Content preview:</div>";
        echo "<pre>" . htmlspecialchars(substr($htaccess, 0, 500)) . "</pre>";
        
        // Check for common issues
        if (strpos($htaccess, 'RewriteEngine On') === false) {
            echo "<div class='error'>❌ RewriteEngine not enabled</div>";
        } else {
            echo "<div class='success'>✅ RewriteEngine enabled</div>";
        }
        
        if (strpos($htaccess, 'fed-workshops') !== false) {
            echo "<div class='success'>✅ Path configured for fed-workshops</div>";
        } else {
            echo "<div class='error'>❌ Path not configured for fed-workshops</div>";
        }
        
    } else {
        echo "<div class='error'>❌ .htaccess file missing</div>";
    }
    
    echo "</div>";
    
    // Show any captured errors
    if (!empty($errors)) {
        echo "<div class='section'>";
        echo "<h2>❌ Captured Errors</h2>";
        foreach ($errors as $error) {
            echo "<div class='error'>$error</div>";
        }
        echo "</div>";
    }
    
    echo "<div class='section'>";
    echo "<h2>🎯 Manual Testing</h2>";
    echo "<div class='info'>";
    echo "<p>Try these manual tests:</p>";
    echo "<p><a href='index.php' target='_blank'>Direct index.php access</a></p>";
    echo "<p><a href='index.php?route=login' target='_blank'>Index.php with route parameter</a></p>";
    echo "<p><a href='direct-login-test.php' target='_blank'>Direct login test (bypass router)</a></p>";
    echo "</div>";
    echo "</div>";
    
    ?>
    
</body>
</html>