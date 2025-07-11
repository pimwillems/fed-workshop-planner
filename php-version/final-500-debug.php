<?php
/**
 * Final 500 Error Debug Tool
 * This will identify exactly what's causing the 500 error on /login
 */

// Enable all error reporting and capture everything
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Capture all errors and exceptions
$allErrors = [];
$allOutput = [];

set_error_handler(function($severity, $message, $file, $line) use (&$allErrors) {
    $allErrors[] = [
        'type' => 'PHP Error',
        'severity' => $severity,
        'message' => $message,
        'file' => $file,
        'line' => $line
    ];
    return false;
});

set_exception_handler(function($exception) use (&$allErrors) {
    $allErrors[] = [
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
    <title>Final 500 Error Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; font-size: 14px; }
        .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin: 0.5rem 0; }
        .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin: 0.5rem 0; }
        .warning { background: #fff3cd; color: #856404; padding: 1rem; border-radius: 6px; margin: 0.5rem 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 6px; margin: 0.5rem 0; }
        .section { margin: 1rem 0; padding: 1rem; border: 1px solid #ddd; border-radius: 6px; }
        pre { background: #f8f9fa; padding: 1rem; border-radius: 6px; overflow-x: auto; font-size: 12px; white-space: pre-wrap; }
        .step { margin: 0.5rem 0; padding: 0.5rem; background: #f8f9fa; border-left: 4px solid #007bff; }
        h1 { color: #333; }
        h2 { color: #666; border-bottom: 2px solid #eee; padding-bottom: 0.5rem; }
        .critical { background: #721c24; color: white; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
    </style>
</head>
<body>
    <h1>🔍 Final 500 Error Debug Tool</h1>
    
    <?php
    
    // Step 1: Basic environment check
    echo "<div class='section'>";
    echo "<h2>Step 1: Basic Environment</h2>";
    echo "<div class='step'>PHP Version: " . phpversion() . "</div>";
    echo "<div class='step'>Current Directory: " . getcwd() . "</div>";
    echo "<div class='step'>Memory Limit: " . ini_get('memory_limit') . "</div>";
    echo "<div class='step'>Max Execution Time: " . ini_get('max_execution_time') . "s</div>";
    echo "</div>";
    
    // Step 2: File system check
    echo "<div class='section'>";
    echo "<h2>Step 2: Critical Files Verification</h2>";
    
    $criticalFiles = [
        '.env' => 'Environment variables',
        'config/paths.php' => 'Path configuration',
        'config/database.php' => 'Database configuration',
        'config/app.php' => 'Application configuration',
        'src/Core/Router.php' => 'Router class',
        'src/Core/Request.php' => 'Request class',
        'src/Core/Response.php' => 'Response class',
        'src/Core/Security.php' => 'Security class',
        'src/Core/JWT.php' => 'JWT class',
        'src/Controllers/AuthController.php' => 'Authentication controller',
        'views/auth/login.php' => 'Login view',
        'views/layout.php' => 'Layout template',
        '.htaccess' => 'URL rewriting rules'
    ];
    
    foreach ($criticalFiles as $file => $description) {
        if (file_exists($file)) {
            if (is_readable($file)) {
                echo "<div class='success'>✅ $file ($description) - Readable</div>";
            } else {
                echo "<div class='error'>❌ $file - Not readable (permissions issue)</div>";
            }
        } else {
            echo "<div class='error'>❌ $file - Missing</div>";
        }
    }
    
    echo "</div>";
    
    // Step 3: Environment loading
    echo "<div class='section'>";
    echo "<h2>Step 3: Environment Variable Loading</h2>";
    
    try {
        if (file_exists('.env')) {
            $envContent = file_get_contents('.env');
            echo "<div class='info'>Environment file size: " . strlen($envContent) . " bytes</div>";
            
            $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $envCount = 0;
            
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && $line[0] !== '#') {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                    $envCount++;
                }
            }
            
            echo "<div class='success'>✅ Loaded $envCount environment variables</div>";
            
            // Check critical env vars
            $requiredEnvVars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASSWORD'];
            foreach ($requiredEnvVars as $var) {
                if (isset($_ENV[$var])) {
                    $displayValue = $var === 'DB_PASSWORD' ? '***SET***' : $_ENV[$var];
                    echo "<div class='success'>✅ $var: $displayValue</div>";
                } else {
                    echo "<div class='error'>❌ $var not set</div>";
                }
            }
        } else {
            echo "<div class='error'>❌ .env file not found</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Environment loading failed: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Step 4: Core class loading
    echo "<div class='section'>";
    echo "<h2>Step 4: Core Class Loading</h2>";
    
    $coreClasses = [
        'config/paths.php' => 'Paths',
        'config/database.php' => 'Database',
        'src/Core/Router.php' => 'Router',
        'src/Core/Request.php' => 'Request',
        'src/Core/Response.php' => 'Response',
        'src/Core/Security.php' => 'Security',
        'src/Core/JWT.php' => 'JWT'
    ];
    
    foreach ($coreClasses as $file => $className) {
        try {
            echo "<div class='step'>Loading $file...</div>";
            require_once $file;
            echo "<div class='success'>✅ $className class loaded</div>";
        } catch (Exception $e) {
            echo "<div class='error'>❌ $className loading failed: " . $e->getMessage() . "</div>";
            echo "<div class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</div>";
        }
    }
    
    echo "</div>";
    
    // Step 5: Database connection test
    echo "<div class='section'>";
    echo "<h2>Step 5: Database Connection Test</h2>";
    
    try {
        echo "<div class='step'>Testing Database::getInstance()...</div>";
        $db = Database::getInstance();
        echo "<div class='success'>✅ Database singleton created</div>";
        
        echo "<div class='step'>Testing database connection...</div>";
        $connection = $db->getConnection();
        echo "<div class='success'>✅ Database connection retrieved</div>";
        
        echo "<div class='step'>Testing database query...</div>";
        $stmt = $connection->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        echo "<div class='success'>✅ Database query successful - Found {$result['count']} users</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Database test failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</div>";
    }
    
    echo "</div>";
    
    // Step 6: Security initialization
    echo "<div class='section'>";
    echo "<h2>Step 6: Security System Test</h2>";
    
    try {
        echo "<div class='step'>Initializing Security...</div>";
        Security::init();
        echo "<div class='success'>✅ Security initialized</div>";
        
        echo "<div class='step'>Testing CSRF token generation...</div>";
        $csrfToken = Security::getCSRFToken();
        if ($csrfToken) {
            echo "<div class='success'>✅ CSRF token generated: " . substr($csrfToken, 0, 10) . "...</div>";
        } else {
            echo "<div class='error'>❌ CSRF token generation failed</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Security test failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</div>";
    }
    
    echo "</div>";
    
    // Step 7: Request/Response creation
    echo "<div class='section'>";
    echo "<h2>Step 7: Request/Response Objects</h2>";
    
    try {
        echo "<div class='step'>Creating Request object...</div>";
        $request = new Request();
        echo "<div class='success'>✅ Request created</div>";
        echo "<div class='info'>Path: " . $request->getPath() . "</div>";
        echo "<div class='info'>Method: " . $request->getMethod() . "</div>";
        
        echo "<div class='step'>Creating Response object...</div>";
        $response = new Response();
        echo "<div class='success'>✅ Response created</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Request/Response creation failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</div>";
    }
    
    echo "</div>";
    
    // Step 8: AuthController test
    echo "<div class='section'>";
    echo "<h2>Step 8: AuthController Test</h2>";
    
    try {
        echo "<div class='step'>Loading AuthController...</div>";
        require_once 'src/Controllers/AuthController.php';
        echo "<div class='success'>✅ AuthController loaded</div>";
        
        echo "<div class='step'>Creating AuthController instance...</div>";
        $authController = new AuthController();
        echo "<div class='success'>✅ AuthController instantiated</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ AuthController test failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</div>";
    }
    
    echo "</div>";
    
    // Step 9: View system test
    echo "<div class='section'>";
    echo "<h2>Step 9: View System Test</h2>";
    
    try {
        echo "<div class='step'>Testing login view inclusion...</div>";
        
        $csrf_token = Security::getCSRFToken();
        $error = null;
        
        ob_start();
        include 'views/auth/login.php';
        $viewContent = ob_get_clean();
        
        echo "<div class='success'>✅ Login view rendered successfully</div>";
        echo "<div class='info'>View content length: " . strlen($viewContent) . " bytes</div>";
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<div class='error'>❌ View test failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</div>";
    }
    
    echo "</div>";
    
    // Step 10: Full routing simulation
    echo "<div class='section'>";
    echo "<h2>Step 10: Full Login Route Simulation</h2>";
    
    try {
        echo "<div class='step'>Setting up full routing test...</div>";
        
        // Simulate the exact request
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/fed-workshops/login';
        $_SERVER['HTTP_HOST'] = 'i888908.apollo.fontysict.net';
        $_SERVER['SCRIPT_NAME'] = '/fed-workshops/index.php';
        
        $testRequest = new Request();
        $testResponse = new Response();
        $router = new Router();
        
        echo "<div class='success'>✅ Test objects created</div>";
        echo "<div class='info'>Simulated path: " . $testRequest->getPath() . "</div>";
        
        // Add routes
        $router->get('/', 'HomeController@index');
        $router->get('/login', 'AuthController@showLogin');
        
        echo "<div class='step'>Executing full dispatch...</div>";
        
        // This is the critical test - exactly what happens when someone visits /login
        ob_start();
        $router->dispatch($testRequest, $testResponse);
        $routerOutput = ob_get_clean();
        
        echo "<div class='success'>✅ Full routing simulation successful!</div>";
        echo "<div class='info'>Router output length: " . strlen($routerOutput) . " bytes</div>";
        
        if (strlen($routerOutput) > 0) {
            echo "<div class='info'>Output preview (first 200 chars): <pre>" . htmlspecialchars(substr($routerOutput, 0, 200)) . "...</pre></div>";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<div class='error'>❌ CRITICAL: Full routing simulation failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    echo "</div>";
    
    // Show all captured errors
    if (!empty($allErrors)) {
        echo "<div class='section'>";
        echo "<h2>❌ All Captured Errors</h2>";
        
        foreach ($allErrors as $error) {
            echo "<div class='error'>";
            echo "<h4>{$error['type']}</h4>";
            echo "<p><strong>Message:</strong> {$error['message']}</p>";
            echo "<p><strong>File:</strong> {$error['file']}</p>";
            echo "<p><strong>Line:</strong> {$error['line']}</p>";
            if (isset($error['trace'])) {
                echo "<p><strong>Stack Trace:</strong></p>";
                echo "<pre>{$error['trace']}</pre>";
            }
            echo "</div>";
        }
        
        echo "</div>";
    }
    
    // Final diagnosis
    echo "<div class='section'>";
    echo "<h2>🎯 Final Diagnosis</h2>";
    
    if (empty($allErrors)) {
        echo "<div class='critical'>";
        echo "<h3>🚨 CRITICAL FINDING</h3>";
        echo "<p>All tests passed successfully, but you're still getting a 500 error on /login.</p>";
        echo "<p>This means the issue is likely:</p>";
        echo "<ul>";
        echo "<li><strong>Server-level configuration</strong> (Apache/PHP settings)</li>";
        echo "<li><strong>File permissions</strong> issues</li>";
        echo "<li><strong>URL rewriting problems</strong> in .htaccess</li>";
        echo "<li><strong>PHP module missing</strong> on the server</li>";
        echo "<li><strong>Output buffering</strong> or headers already sent</li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<div class='warning'>";
        echo "<h4>🔧 Immediate Actions:</h4>";
        echo "<ol>";
        echo "<li>Check server error logs in DirectAdmin</li>";
        echo "<li>Try accessing: <a href='direct-login-test.php'>Direct Login Test</a> (bypasses routing)</li>";
        echo "<li>Try accessing: <a href='index.php?test=login'>Index.php with parameter</a></li>";
        echo "<li>Check file permissions (should be 644 for files, 755 for directories)</li>";
        echo "</ol>";
        echo "</div>";
        
    } else {
        echo "<div class='error'>";
        echo "<h3>❌ Errors Found</h3>";
        echo "<p>The errors above show the root cause of the 500 error.</p>";
        echo "<p>Fix these errors and the /login route should work.</p>";
        echo "</div>";
    }
    
    echo "</div>";
    
    ?>
    
    <div class="section">
        <h2>🧪 Additional Tests</h2>
        <div class="info">
            <p>If all tests above passed, try these direct links:</p>
            <p><a href="direct-login-test.php" target="_blank">Direct Login Test (bypasses router)</a></p>
            <p><a href="index.php" target="_blank">Direct index.php access</a></p>
            <p><a href="." target="_blank">Current directory listing</a></p>
        </div>
    </div>
    
</body>
</html>