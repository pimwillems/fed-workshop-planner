<?php
/**
 * Test Response->view() Method
 * This tests the specific method that renders the login page
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Test Response View</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .success{background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin:1rem 0;} .error{background:#f8d7da;color:#721c24;padding:1rem;border-radius:6px;margin:1rem 0;} .info{background:#d1ecf1;color:#0c5460;padding:1rem;border-radius:6px;margin:1rem 0;} pre{background:#f8f9fa;padding:1rem;border-radius:6px;overflow-x:auto;}</style>";
echo "</head><body>";

echo "<h1>🧪 Test Response->view() Method</h1>";

try {
    // Load required components
    echo "<p>Loading components...</p>";
    
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
    require_once 'src/Core/Response.php';
    require_once 'src/Core/Security.php';
    
    echo "<div class='success'>✅ Components loaded</div>";
    
    // Initialize security
    Security::init();
    echo "<div class='success'>✅ Security initialized</div>";
    
    // Create response object
    $response = new Response();
    echo "<div class='success'>✅ Response object created</div>";
    
    // Test the view method with login template
    echo "<h2>Testing Response->view() with login template</h2>";
    
    $csrf_token = Security::getCSRFToken();
    echo "<div class='info'>CSRF token: " . substr($csrf_token, 0, 10) . "...</div>";
    
    // Test 1: Check if template exists
    $templatePath = 'views/auth/login.php';
    if (file_exists($templatePath)) {
        echo "<div class='success'>✅ Template exists: $templatePath</div>";
    } else {
        echo "<div class='error'>❌ Template missing: $templatePath</div>";
    }
    
    // Test 2: Try to call the view method
    echo "<h3>Calling Response->view('auth/login', [...]):</h3>";
    
    ob_start();
    try {
        $response->view('auth/login', [
            'csrf_token' => $csrf_token,
            'error' => null
        ]);
        
        // If we get here, the view method worked
        echo "<div class='success'>✅ Response->view() method executed successfully!</div>";
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<div class='error'>❌ Response->view() failed: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    // Test 3: Try manual template inclusion
    echo "<h3>Testing Manual Template Inclusion:</h3>";
    
    try {
        $error = null;
        
        ob_start();
        include 'views/auth/login.php';
        $manualOutput = ob_get_clean();
        
        echo "<div class='success'>✅ Manual template inclusion worked</div>";
        echo "<div class='info'>Output length: " . strlen($manualOutput) . " bytes</div>";
        
        if (strlen($manualOutput) > 100) {
            echo "<div class='info'>Output preview: <pre>" . htmlspecialchars(substr($manualOutput, 0, 300)) . "...</pre></div>";
        }
        
    } catch (Exception $e) {
        ob_end_clean();
        echo "<div class='error'>❌ Manual template inclusion failed: " . $e->getMessage() . "</div>";
    }
    
    // Test 4: Check Response class view method
    echo "<h3>Checking Response Class View Method:</h3>";
    
    // Let's look at what the view method is actually doing
    $reflection = new ReflectionClass('Response');
    if ($reflection->hasMethod('view')) {
        echo "<div class='success'>✅ Response->view() method exists</div>";
        
        $viewMethod = $reflection->getMethod('view');
        echo "<div class='info'>Method is " . ($viewMethod->isPublic() ? 'public' : 'private') . "</div>";
        
        // Test the method step by step
        echo "<h4>Testing view method step by step:</h4>";
        
        $templatePath = __DIR__ . "/views/auth/login.php";
        echo "<div class='info'>Template path: $templatePath</div>";
        
        if (file_exists($templatePath)) {
            echo "<div class='success'>✅ Template file exists at computed path</div>";
        } else {
            echo "<div class='error'>❌ Template file not found at computed path</div>";
        }
        
    } else {
        echo "<div class='error'>❌ Response->view() method doesn't exist</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>❌ General error: " . $e->getMessage() . "</div>";
    echo "<div class='error'>File: " . $e->getFile() . "</div>";
    echo "<div class='error'>Line: " . $e->getLine() . "</div>";
}

echo "<hr>";
echo "<h2>🎯 Conclusion</h2>";
echo "<p>This test will help identify if the issue is with:</p>";
echo "<ul>";
echo "<li>The Response->view() method itself</li>";
echo "<li>Template file access</li>";
echo "<li>Variable extraction in templates</li>";
echo "<li>Output buffering issues</li>";
echo "</ul>";

echo "</body></html>";
?>