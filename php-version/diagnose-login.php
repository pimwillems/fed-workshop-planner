<?php
/**
 * Login Page Diagnostic Tool
 * This will help identify why the login page is giving a 500 error
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Set up error handler
set_error_handler(function($severity, $message, $file, $line) {
    echo "<div style='background:#f8d7da;color:#721c24;padding:1rem;margin:1rem 0;border-radius:6px;'>";
    echo "<strong>PHP Error:</strong> $message<br>";
    echo "<strong>File:</strong> $file<br>";
    echo "<strong>Line:</strong> $line<br>";
    echo "</div>";
    return false;
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
<html>
<head>
    <title>Login Page Diagnostic</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .section { margin: 2rem 0; padding: 1rem; border: 1px solid #ddd; border-radius: 6px; }
    </style>
</head>
<body>
    <h1>🔍 Login Page Diagnostic</h1>
    
    <?php
    
    echo "<div class='section'>";
    echo "<h2>Step 1: Loading Core System</h2>";
    
    try {
        // Load environment variables
        if (file_exists('.env')) {
            $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && $line[0] !== '#') {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
            echo "<div class='success'>✅ Environment variables loaded</div>";
        }
        
        // Load core classes
        require_once 'config/paths.php';
        require_once 'config/database.php';
        require_once 'src/Core/Router.php';
        require_once 'src/Core/Request.php';
        require_once 'src/Core/Response.php';
        require_once 'src/Core/Security.php';
        require_once 'src/Core/JWT.php';
        
        echo "<div class='success'>✅ Core classes loaded</div>";
        
        // Initialize security
        Security::init();
        echo "<div class='success'>✅ Security initialized</div>";
        
        // Create request/response objects
        $request = new Request();
        $response = new Response();
        echo "<div class='success'>✅ Request/Response objects created</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Core system error: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>Step 2: Testing AuthController</h2>";
    
    try {
        // Load AuthController
        require_once 'src/Controllers/AuthController.php';
        echo "<div class='success'>✅ AuthController loaded</div>";
        
        // Try to instantiate AuthController
        $authController = new AuthController();
        echo "<div class='success'>✅ AuthController instantiated</div>";
        
        // Check if showLogin method exists
        if (method_exists($authController, 'showLogin')) {
            echo "<div class='success'>✅ showLogin method exists</div>";
        } else {
            echo "<div class='error'>❌ showLogin method not found</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ AuthController error: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>Step 3: Testing View System</h2>";
    
    try {
        // Check if login view exists
        $loginViewPath = 'views/auth/login.php';
        if (file_exists($loginViewPath)) {
            echo "<div class='success'>✅ Login view file exists: $loginViewPath</div>";
        } else {
            echo "<div class='error'>❌ Login view file not found: $loginViewPath</div>";
        }
        
        // Check if layout exists
        $layoutPath = 'views/layout.php';
        if (file_exists($layoutPath)) {
            echo "<div class='success'>✅ Layout file exists: $layoutPath</div>";
        } else {
            echo "<div class='error'>❌ Layout file not found: $layoutPath</div>";
        }
        
        // Test response->view method
        echo "<div class='info'>Testing Response::view method...</div>";
        
        // Create a dummy response object to test view rendering
        ob_start();
        try {
            $response->view('auth/login', [
                'csrf_token' => 'test-token',
                'error' => null
            ]);
        } catch (Exception $e) {
            ob_end_clean();
            echo "<div class='error'>❌ View rendering error: " . $e->getMessage() . "</div>";
            echo "<div class='error'>File: " . $e->getFile() . "</div>";
            echo "<div class='error'>Line: " . $e->getLine() . "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ View system error: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>Step 4: Simulating Login Request</h2>";
    
    try {
        echo "<div class='info'>Simulating GET request to /login...</div>";
        
        // Simulate the login route
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/fed-workshops/login';
        
        // Create new request object with simulated data
        $testRequest = new Request();
        echo "<div class='success'>✅ Test request created</div>";
        echo "<div class='info'>Request path: " . $testRequest->getPath() . "</div>";
        
        // Test calling the showLogin method
        $authController = new AuthController();
        
        // Capture any output
        ob_start();
        try {
            $authController->showLogin($testRequest, $response);
            $output = ob_get_clean();
            echo "<div class='success'>✅ showLogin method executed successfully</div>";
        } catch (Exception $e) {
            ob_end_clean();
            echo "<div class='error'>❌ showLogin execution error: " . $e->getMessage() . "</div>";
            echo "<div class='error'>File: " . $e->getFile() . "</div>";
            echo "<div class='error'>Line: " . $e->getLine() . "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Login simulation error: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    ?>
    
    <div class="section">
        <h2>🎯 Diagnosis Results</h2>
        <div class="info">
            <p>If all tests above passed, the login page should work. The 500 error might be caused by:</p>
            <ul>
                <li>Missing .env file or incorrect database credentials</li>
                <li>File permissions issues</li>
                <li>Server configuration problems</li>
                <li>Missing view files or incorrect paths</li>
            </ul>
            
            <p><strong>Try these links after reviewing the results:</strong></p>
            <p><a href="index.php">Test main page</a></p>
            <p><a href="login">Test login page directly</a></p>
        </div>
    </div>
    
    <div class="error">
        <h2>⚠️ Security Note</h2>
        <p>Delete this file after debugging: <code>diagnose-login.php</code></p>
    </div>
    
</body>
</html>