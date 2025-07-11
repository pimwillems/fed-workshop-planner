<?php
/**
 * Advanced Debug Tool - Comprehensive 500 Error Investigation
 * This will capture ALL errors and show exactly what's failing
 */

// Enable ALL error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Buffer all output to capture errors
ob_start();

// Custom error handler to capture everything
$errors = [];
set_error_handler(function($severity, $message, $file, $line) use (&$errors) {
    $errors[] = [
        'type' => 'Error',
        'severity' => $severity,
        'message' => $message,
        'file' => $file,
        'line' => $line
    ];
    return false; // Let PHP handle it normally too
});

// Custom exception handler
set_exception_handler(function($exception) use (&$errors) {
    $errors[] = [
        'type' => 'Exception',
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ];
});

?>
<!DOCTYPE html>
<html>
<head>
    <title>Advanced Debug Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .warning { background: #fff3cd; color: #856404; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .section { margin: 2rem 0; padding: 1rem; border: 1px solid #ddd; border-radius: 6px; }
        pre { background: #f8f9fa; padding: 1rem; border-radius: 6px; overflow-x: auto; font-size: 0.9rem; white-space: pre-wrap; }
        .step { margin: 1rem 0; padding: 0.5rem; background: #f8f9fa; border-left: 4px solid #007bff; }
    </style>
</head>
<body>
    <h1>🔍 Advanced Debug Tool</h1>
    
    <?php
    
    echo "<div class='section'>";
    echo "<h2>Step 1: Basic Environment Check</h2>";
    
    echo "<div class='step'>PHP Version: " . phpversion() . "</div>";
    echo "<div class='step'>Current Directory: " . getcwd() . "</div>";
    echo "<div class='step'>Script Path: " . __FILE__ . "</div>";
    
    // Check if .env exists
    if (file_exists('.env')) {
        echo "<div class='success'>✅ .env file exists</div>";
    } else {
        echo "<div class='error'>❌ .env file missing</div>";
    }
    
    echo "</div>";
    
    // Step 2: Environment loading test
    echo "<div class='section'>";
    echo "<h2>Step 2: Environment Loading Test</h2>";
    
    try {
        if (file_exists('.env')) {
            $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && $line[0] !== '#') {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
            echo "<div class='success'>✅ Environment variables loaded</div>";
            
            // Show key variables (without passwords)
            $envVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'JWT_SECRET'];
            foreach ($envVars as $var) {
                if (isset($_ENV[$var])) {
                    $value = $var === 'JWT_SECRET' ? '***HIDDEN***' : $_ENV[$var];
                    echo "<div class='info'>$var: $value</div>";
                } else {
                    echo "<div class='warning'>⚠️ $var not set</div>";
                }
            }
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Environment loading failed: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Step 3: File existence check
    echo "<div class='section'>";
    echo "<h2>Step 3: Critical Files Check</h2>";
    
    $criticalFiles = [
        'config/paths.php',
        'config/database.php',
        'config/app.php',
        'src/Core/Router.php',
        'src/Core/Request.php',
        'src/Core/Response.php',
        'src/Core/Security.php',
        'src/Core/JWT.php',
        'src/Controllers/AuthController.php',
        'views/layout.php',
        'views/auth/login.php',
        '.htaccess'
    ];
    
    foreach ($criticalFiles as $file) {
        if (file_exists($file)) {
            echo "<div class='success'>✅ $file exists</div>";
        } else {
            echo "<div class='error'>❌ $file missing</div>";
        }
    }
    
    echo "</div>";
    
    // Step 4: Core class loading
    echo "<div class='section'>";
    echo "<h2>Step 4: Core Class Loading</h2>";
    
    try {
        echo "<div class='step'>Loading config/paths.php...</div>";
        require_once 'config/paths.php';
        echo "<div class='success'>✅ Paths class loaded</div>";
        
        echo "<div class='step'>Loading config/database.php...</div>";
        require_once 'config/database.php';
        echo "<div class='success'>✅ Database class loaded</div>";
        
        echo "<div class='step'>Loading src/Core/Router.php...</div>";
        require_once 'src/Core/Router.php';
        echo "<div class='success'>✅ Router class loaded</div>";
        
        echo "<div class='step'>Loading src/Core/Request.php...</div>";
        require_once 'src/Core/Request.php';
        echo "<div class='success'>✅ Request class loaded</div>";
        
        echo "<div class='step'>Loading src/Core/Response.php...</div>";
        require_once 'src/Core/Response.php';
        echo "<div class='success'>✅ Response class loaded</div>";
        
        echo "<div class='step'>Loading src/Core/Security.php...</div>";
        require_once 'src/Core/Security.php';
        echo "<div class='success'>✅ Security class loaded</div>";
        
        echo "<div class='step'>Loading src/Core/JWT.php...</div>";
        require_once 'src/Core/JWT.php';
        echo "<div class='success'>✅ JWT class loaded</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Class loading failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    echo "</div>";
    
    // Step 5: Database connection test
    echo "<div class='section'>";
    echo "<h2>Step 5: Database Connection Test</h2>";
    
    try {
        echo "<div class='step'>Testing database connection...</div>";
        $db = Database::getInstance();
        echo "<div class='success'>✅ Database connection successful</div>";
        
        $connection = $db->getConnection();
        $stmt = $connection->query("SELECT COUNT(*) FROM users");
        $userCount = $stmt->fetchColumn();
        echo "<div class='success'>✅ Database query successful - Found $userCount users</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Database connection failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
    }
    
    echo "</div>";
    
    // Step 6: Security initialization
    echo "<div class='section'>";
    echo "<h2>Step 6: Security Initialization</h2>";
    
    try {
        echo "<div class='step'>Initializing security...</div>";
        Security::init();
        echo "<div class='success'>✅ Security initialized</div>";
        
        $csrfToken = Security::getCSRFToken();
        if ($csrfToken) {
            echo "<div class='success'>✅ CSRF token generated</div>";
        } else {
            echo "<div class='error'>❌ CSRF token generation failed</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Security initialization failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
    }
    
    echo "</div>";
    
    // Step 7: Request/Response objects
    echo "<div class='section'>";
    echo "<h2>Step 7: Request/Response Objects</h2>";
    
    try {
        echo "<div class='step'>Creating Request object...</div>";
        $request = new Request();
        echo "<div class='success'>✅ Request object created</div>";
        echo "<div class='info'>Request path: " . $request->getPath() . "</div>";
        echo "<div class='info'>Request method: " . $request->getMethod() . "</div>";
        
        echo "<div class='step'>Creating Response object...</div>";
        $response = new Response();
        echo "<div class='success'>✅ Response object created</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Request/Response creation failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    echo "</div>";
    
    // Step 8: AuthController test
    echo "<div class='section'>";
    echo "<h2>Step 8: AuthController Test</h2>";
    
    try {
        echo "<div class='step'>Loading AuthController...</div>";
        require_once 'src/Controllers/AuthController.php';
        echo "<div class='success'>✅ AuthController file loaded</div>";
        
        echo "<div class='step'>Creating AuthController instance...</div>";
        $authController = new AuthController();
        echo "<div class='success'>✅ AuthController instantiated</div>";
        
        echo "<div class='step'>Testing showLogin method...</div>";
        if (method_exists($authController, 'showLogin')) {
            echo "<div class='success'>✅ showLogin method exists</div>";
        } else {
            echo "<div class='error'>❌ showLogin method not found</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ AuthController test failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    echo "</div>";
    
    // Step 9: View system test
    echo "<div class='section'>";
    echo "<h2>Step 9: View System Test</h2>";
    
    try {
        echo "<div class='step'>Testing view file paths...</div>";
        
        $viewFiles = [
            'views/layout.php',
            'views/auth/login.php'
        ];
        
        foreach ($viewFiles as $viewFile) {
            if (file_exists($viewFile)) {
                echo "<div class='success'>✅ $viewFile exists</div>";
                
                // Check if it's readable
                if (is_readable($viewFile)) {
                    echo "<div class='success'>✅ $viewFile is readable</div>";
                } else {
                    echo "<div class='error'>❌ $viewFile is not readable</div>";
                }
                
                // Check file size
                $size = filesize($viewFile);
                echo "<div class='info'>File size: $size bytes</div>";
                
            } else {
                echo "<div class='error'>❌ $viewFile not found</div>";
            }
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ View system test failed: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Step 10: Simulate login request
    echo "<div class='section'>";
    echo "<h2>Step 10: Simulate Login Request</h2>";
    
    try {
        echo "<div class='step'>Simulating login request...</div>";
        
        // Simulate the exact request that would come to /login
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/fed-workshops/login';
        $_SERVER['HTTP_HOST'] = 'i888908.apollo.fontysict.net';
        $_SERVER['SCRIPT_NAME'] = '/fed-workshops/index.php';
        
        $testRequest = new Request();
        $testResponse = new Response();
        
        echo "<div class='success'>✅ Test request/response created</div>";
        echo "<div class='info'>Simulated path: " . $testRequest->getPath() . "</div>";
        
        // Test the actual showLogin call
        $authController = new AuthController();
        
        echo "<div class='step'>Calling showLogin method...</div>";
        
        // This is where the error might happen
        ob_start();
        $authController->showLogin($testRequest, $testResponse);
        $output = ob_get_clean();
        
        echo "<div class='success'>✅ showLogin method executed successfully</div>";
        echo "<div class='info'>Output length: " . strlen($output) . " bytes</div>";
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<div class='error'>❌ Login simulation failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    echo "</div>";
    
    // Display all captured errors
    if (!empty($errors)) {
        echo "<div class='section'>";
        echo "<h2>❌ Captured Errors</h2>";
        
        foreach ($errors as $error) {
            echo "<div class='error'>";
            echo "<h3>{$error['type']}</h3>";
            echo "<p><strong>Message:</strong> {$error['message']}</p>";
            echo "<p><strong>File:</strong> {$error['file']}</p>";
            echo "<p><strong>Line:</strong> {$error['line']}</p>";
            if (isset($error['trace'])) {
                echo "<p><strong>Stack Trace:</strong><pre>{$error['trace']}</pre></p>";
            }
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    echo "<div class='section'>";
    echo "<h2>🎯 Summary</h2>";
    
    if (empty($errors)) {
        echo "<div class='success'>";
        echo "<h3>✅ No errors detected!</h3>";
        echo "<p>The application components are loading correctly. The 500 error might be:</p>";
        echo "<ul>";
        echo "<li>Server-level configuration issue</li>";
        echo "<li>Permission problems</li>";
        echo "<li>Missing server modules</li>";
        echo "<li>URL rewriting issues</li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div class='error'>";
        echo "<h3>❌ Errors found!</h3>";
        echo "<p>The errors above show the root cause of the 500 error.</p>";
        echo "</div>";
    }
    
    echo "<div class='info'>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ol>";
    echo "<li>Check the errors above for specific issues</li>";
    echo "<li>Verify server error logs</li>";
    echo "<li>Check file permissions (should be 644 for files, 755 for directories)</li>";
    echo "<li>Ensure PHP modules are installed</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "</div>";
    
    ?>
    
</body>
</html>