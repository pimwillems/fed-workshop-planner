<?php
/**
 * Isolate Login Error - Find the exact cause of the 500 error
 * Since database is working, let's find what else is failing
 */

// Enable maximum error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// Capture all PHP errors
$capturedErrors = [];
set_error_handler(function($severity, $message, $file, $line) use (&$capturedErrors) {
    $capturedErrors[] = [
        'type' => 'PHP Error',
        'severity' => $severity,
        'message' => $message,
        'file' => $file,
        'line' => $line
    ];
    return false; // Don't suppress the error
});

// Capture all exceptions
set_exception_handler(function($exception) use (&$capturedErrors) {
    $capturedErrors[] = [
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
    <title>Isolate Login Error</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .warning { background: #fff3cd; color: #856404; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .step { margin: 0.5rem 0; padding: 0.5rem 1rem; background: #f8f9fa; border-left: 4px solid #007bff; }
        pre { background: #f8f9fa; padding: 1rem; border-radius: 6px; overflow-x: auto; white-space: pre-wrap; }
        .section { margin: 2rem 0; padding: 1rem; border: 1px solid #ddd; border-radius: 8px; }
        .critical { background: #721c24; color: white; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
    </style>
</head>
<body>
    <h1>🔍 Isolate Login Error</h1>
    
    <?php
    
    echo "<div class='section'>";
    echo "<h2>Step 1: Minimal System Check</h2>";
    
    // Test basic environment
    echo "<div class='step'>PHP Version: " . phpversion() . "</div>";
    echo "<div class='step'>Current Directory: " . getcwd() . "</div>";
    echo "<div class='step'>Memory Limit: " . ini_get('memory_limit') . "</div>";
    
    // Load environment
    try {
        if (file_exists('.env')) {
            $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && $line[0] !== '#') {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
            echo "<div class='success'>✅ Environment loaded</div>";
        } else {
            echo "<div class='error'>❌ .env file missing</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Environment loading failed: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    // Test each component individually
    echo "<div class='section'>";
    echo "<h2>Step 2: Individual Component Testing</h2>";
    
    $components = [
        ['file' => 'config/paths.php', 'class' => 'Paths', 'test' => 'getBasePath'],
        ['file' => 'config/database.php', 'class' => 'Database', 'test' => 'getInstance'],
        ['file' => 'src/Core/Security.php', 'class' => 'Security', 'test' => 'init'],
        ['file' => 'src/Core/JWT.php', 'class' => 'JWT', 'test' => null],
        ['file' => 'src/Core/Request.php', 'class' => 'Request', 'test' => 'construct'],
        ['file' => 'src/Core/Response.php', 'class' => 'Response', 'test' => 'construct'],
        ['file' => 'src/Core/Router.php', 'class' => 'Router', 'test' => 'construct'],
    ];
    
    foreach ($components as $component) {
        echo "<div class='step'>Testing {$component['class']}...</div>";
        
        try {
            require_once $component['file'];
            echo "<div class='success'>✅ {$component['class']} loaded</div>";
            
            // Test specific functionality
            if ($component['test'] === 'getBasePath') {
                $basePath = Paths::getBasePath();
                echo "<div class='info'>Base path: $basePath</div>";
            } elseif ($component['test'] === 'getInstance') {
                $db = Database::getInstance();
                echo "<div class='success'>✅ Database instance created</div>";
            } elseif ($component['test'] === 'init') {
                Security::init();
                echo "<div class='success'>✅ Security initialized</div>";
            } elseif ($component['test'] === 'construct') {
                if ($component['class'] === 'Request') {
                    $obj = new Request();
                    echo "<div class='success'>✅ Request object created</div>";
                } elseif ($component['class'] === 'Response') {
                    $obj = new Response();
                    echo "<div class='success'>✅ Response object created</div>";
                } elseif ($component['class'] === 'Router') {
                    $obj = new Router();
                    echo "<div class='success'>✅ Router object created</div>";
                }
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>❌ {$component['class']} failed: " . $e->getMessage() . "</div>";
            echo "<div class='error'>File: {$e->getFile()} Line: {$e->getLine()}</div>";
        }
    }
    
    echo "</div>";
    
    // Test AuthController specifically
    echo "<div class='section'>";
    echo "<h2>Step 3: AuthController Specific Test</h2>";
    
    try {
        echo "<div class='step'>Loading AuthController...</div>";
        require_once 'src/Controllers/AuthController.php';
        echo "<div class='success'>✅ AuthController file loaded</div>";
        
        echo "<div class='step'>Creating AuthController instance...</div>";
        $authController = new AuthController();
        echo "<div class='success'>✅ AuthController instantiated</div>";
        
        echo "<div class='step'>Testing showLogin method exists...</div>";
        if (method_exists($authController, 'showLogin')) {
            echo "<div class='success'>✅ showLogin method exists</div>";
        } else {
            echo "<div class='error'>❌ showLogin method missing</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ AuthController test failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: {$e->getFile()} Line: {$e->getLine()}</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    echo "</div>";
    
    // Test the exact showLogin call
    echo "<div class='section'>";
    echo "<h2>Step 4: Exact showLogin Call Test</h2>";
    
    try {
        echo "<div class='step'>Setting up exact login simulation...</div>";
        
        // Simulate the exact server environment for /login
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/fed-workshops/login';
        $_SERVER['HTTP_HOST'] = 'i888908.apollo.fontysict.net';
        $_SERVER['SCRIPT_NAME'] = '/fed-workshops/index.php';
        
        $request = new Request();
        $response = new Response();
        
        echo "<div class='success'>✅ Request/Response objects created</div>";
        echo "<div class='info'>Request path: " . $request->getPath() . "</div>";
        
        echo "<div class='step'>Calling AuthController->showLogin()...</div>";
        
        // This is the exact call that's failing
        ob_start();
        $authController->showLogin($request, $response);
        $output = ob_get_clean();
        
        echo "<div class='success'>✅ showLogin executed successfully!</div>";
        echo "<div class='info'>Output length: " . strlen($output) . " bytes</div>";
        
        if (strlen($output) > 100) {
            echo "<div class='info'>Output preview: <pre>" . htmlspecialchars(substr($output, 0, 200)) . "...</pre></div>";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<div class='critical'>🚨 FOUND THE ERROR!</div>";
        echo "<div class='error'>❌ showLogin call failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: {$e->getFile()} Line: {$e->getLine()}</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    echo "</div>";
    
    // Test view system
    echo "<div class='section'>";
    echo "<h2>Step 5: View System Test</h2>";
    
    try {
        echo "<div class='step'>Testing view file access...</div>";
        
        if (file_exists('views/auth/login.php')) {
            echo "<div class='success'>✅ Login view file exists</div>";
            
            if (is_readable('views/auth/login.php')) {
                echo "<div class='success'>✅ Login view is readable</div>";
            } else {
                echo "<div class='error'>❌ Login view not readable (permissions)</div>";
            }
        } else {
            echo "<div class='error'>❌ Login view file missing</div>";
        }
        
        if (file_exists('views/layout.php')) {
            echo "<div class='success'>✅ Layout file exists</div>";
        } else {
            echo "<div class='error'>❌ Layout file missing</div>";
        }
        
        echo "<div class='step'>Testing view inclusion...</div>";
        
        $csrf_token = Security::getCSRFToken();
        $error = null;
        
        ob_start();
        include 'views/auth/login.php';
        $viewOutput = ob_get_clean();
        
        echo "<div class='success'>✅ View inclusion successful</div>";
        echo "<div class='info'>View output length: " . strlen($viewOutput) . " bytes</div>";
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<div class='error'>❌ View system failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: {$e->getFile()} Line: {$e->getLine()}</div>";
    }
    
    echo "</div>";
    
    // Test Response->view method
    echo "<div class='section'>";
    echo "<h2>Step 6: Response->view() Method Test</h2>";
    
    try {
        echo "<div class='step'>Testing Response->view() method...</div>";
        
        $response = new Response();
        $csrf_token = Security::getCSRFToken();
        
        // This is what AuthController->showLogin actually calls
        ob_start();
        $response->view('auth/login', [
            'csrf_token' => $csrf_token,
            'error' => null
        ]);
        $responseOutput = ob_get_clean();
        
        echo "<div class='success'>✅ Response->view() executed successfully</div>";
        echo "<div class='info'>Response output length: " . strlen($responseOutput) . " bytes</div>";
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<div class='critical'>🚨 FOUND THE ERROR IN RESPONSE->VIEW()!</div>";
        echo "<div class='error'>❌ Response->view() failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: {$e->getFile()} Line: {$e->getLine()}</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    echo "</div>";
    
    // Show all captured errors
    if (!empty($capturedErrors)) {
        echo "<div class='section'>";
        echo "<h2>❌ All Captured Errors</h2>";
        
        foreach ($capturedErrors as $error) {
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
    
    // Final recommendation
    echo "<div class='section'>";
    echo "<h2>🎯 Next Steps</h2>";
    
    if (empty($capturedErrors)) {
        echo "<div class='warning'>";
        echo "<h3>⚠️ All Tests Passed But Still Getting 500 Error</h3>";
        echo "<p>This means the issue is likely:</p>";
        echo "<ul>";
        echo "<li><strong>Output buffering issues</strong></li>";
        echo "<li><strong>Headers already sent</strong></li>";
        echo "<li><strong>Server configuration problems</strong></li>";
        echo "<li><strong>Apache/PHP module issues</strong></li>";
        echo "</ul>";
        echo "</div>";
        
        echo "<div class='info'>";
        echo "<h4>Try these direct tests:</h4>";
        echo "<p><a href='bypass-login.php' target='_blank'>Bypass Login Test</a></p>";
        echo "<p><a href='test-response-view.php' target='_blank'>Test Response View (if created)</a></p>";
        echo "<p>Check server error logs in DirectAdmin</p>";
        echo "</div>";
        
    } else {
        echo "<div class='critical'>";
        echo "<h3>🎯 Root Cause Found!</h3>";
        echo "<p>The errors above show exactly what's causing the 500 error.</p>";
        echo "<p>Fix these specific issues and the /login route should work.</p>";
        echo "</div>";
    }
    
    echo "</div>";
    
    ?>
    
</body>
</html>