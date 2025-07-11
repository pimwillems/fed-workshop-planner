<?php
/**
 * Final Debug Script - Fixed Version
 * This tests the corrected application flow
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Final Debug - Fixed Version</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 2rem; }
        .success { background: #d4edda; color: #155724; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        .info { background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 6px; margin: 1rem 0; }
        pre { background: #f8f9fa; padding: 1rem; border-radius: 6px; overflow-x: auto; }
        .section { margin: 2rem 0; padding: 1rem; border: 1px solid #ddd; border-radius: 6px; }
    </style>
</head>
<body>
    <h1>🔧 Final Debug - Fixed Version</h1>
    
    <?php
    
    echo "<div class='section'>";
    echo "<h2>Test 1: Fixed Application Flow</h2>";
    
    try {
        echo "<h3>Loading Environment Variables...</h3>";
        // Load environment variables (same as index.php)
        if (file_exists('.env')) {
            $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && $line[0] !== '#') {
                    list($key, $value) = explode('=', $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
            echo "<div class='success'>✅ Environment variables loaded</div>";
        } else {
            echo "<div class='error'>❌ .env file not found</div>";
        }
        
        echo "<h3>Loading Core Classes...</h3>";
        require_once 'config/paths.php';
        echo "<div class='success'>✅ Paths class loaded</div>";
        
        require_once 'config/database.php';
        echo "<div class='success'>✅ Database class loaded</div>";
        
        require_once 'src/Core/Router.php';
        echo "<div class='success'>✅ Router loaded</div>";
        
        require_once 'src/Core/Request.php';
        echo "<div class='success'>✅ Request loaded</div>";
        
        require_once 'src/Core/Response.php';
        echo "<div class='success'>✅ Response loaded</div>";
        
        require_once 'src/Core/Security.php';
        echo "<div class='success'>✅ Security loaded</div>";
        
        require_once 'src/Core/JWT.php';
        echo "<div class='success'>✅ JWT loaded</div>";
        
        echo "<h3>Initializing Security...</h3>";
        Security::init();
        echo "<div class='success'>✅ Security initialized</div>";
        
        echo "<h3>Creating Request and Response Objects...</h3>";
        $request = new Request();
        echo "<div class='success'>✅ Request object created</div>";
        echo "<div class='info'>Request path: " . $request->getPath() . "</div>";
        echo "<div class='info'>Request method: " . $request->getMethod() . "</div>";
        
        $response = new Response();
        echo "<div class='success'>✅ Response object created</div>";
        
        echo "<h3>Initializing Router...</h3>";
        $router = new Router();
        echo "<div class='success'>✅ Router created</div>";
        
        // Test adding routes
        $router->get('/', 'HomeController@index');
        $router->get('/login', 'AuthController@showLogin');
        echo "<div class='success'>✅ Routes added successfully</div>";
        
        echo "<h3>Database Connection Test...</h3>";
        $db = Database::getInstance();
        echo "<div class='success'>✅ Database connection successful</div>";
        
        $connection = $db->getConnection();
        $stmt = $connection->query("SELECT COUNT(*) FROM users");
        $userCount = $stmt->fetchColumn();
        echo "<div class='success'>✅ Database query successful - Found $userCount users</div>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
        echo "<div class='error'>File: " . $e->getFile() . "</div>";
        echo "<div class='error'>Line: " . $e->getLine() . "</div>";
        echo "<div class='error'>Stack trace: <pre>" . $e->getTraceAsString() . "</pre></div>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>Test 2: Controller Loading Test</h2>";
    
    try {
        echo "<h3>Testing HomeController...</h3>";
        $controllerFile = 'src/Controllers/HomeController.php';
        if (file_exists($controllerFile)) {
            require_once $controllerFile;
            echo "<div class='success'>✅ HomeController file loaded</div>";
            
            if (class_exists('HomeController')) {
                $controller = new HomeController();
                echo "<div class='success'>✅ HomeController instantiated</div>";
                
                if (method_exists($controller, 'index')) {
                    echo "<div class='success'>✅ HomeController::index method exists</div>";
                } else {
                    echo "<div class='error'>❌ HomeController::index method missing</div>";
                }
            } else {
                echo "<div class='error'>❌ HomeController class not found</div>";
            }
        } else {
            echo "<div class='error'>❌ HomeController file not found</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Controller test error: " . $e->getMessage() . "</div>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>Test 3: Views Directory</h2>";
    
    if (is_dir('views')) {
        echo "<div class='success'>✅ Views directory exists</div>";
        
        $viewFiles = glob('views/*.php');
        if (count($viewFiles) > 0) {
            echo "<div class='success'>✅ Found " . count($viewFiles) . " view files</div>";
            foreach ($viewFiles as $viewFile) {
                echo "<div class='info'>- " . basename($viewFile) . "</div>";
            }
        } else {
            echo "<div class='error'>❌ No view files found</div>";
        }
        
    } else {
        echo "<div class='error'>❌ Views directory not found</div>";
    }
    
    echo "</div>";
    
    echo "<div class='section'>";
    echo "<h2>✅ Summary</h2>";
    echo "<div class='info'>";
    echo "<p><strong>If all tests above pass:</strong></p>";
    echo "<ul>";
    echo "<li>The application should now work properly</li>";
    echo "<li>Try accessing: <a href='index.php'>index.php</a></li>";
    echo "<li>Try accessing: <a href='./'>main homepage</a></li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<p><strong>If there are still issues:</strong></p>";
    echo "<ul>";
    echo "<li>Check that .env file exists and contains correct database credentials</li>";
    echo "<li>Verify that all required files are uploaded to the server</li>";
    echo "<li>Check server error logs for more detailed error messages</li>";
    echo "<li>Ensure .htaccess is properly configured for your server</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "</div>";
    
    ?>
    
    <div class="section">
        <h2>🎯 Next Steps</h2>
        <div class="info">
            <ol>
                <li>Make sure you have renamed <code>.env.fontys</code> to <code>.env</code> with your database credentials</li>
                <li>Test the application by visiting: <a href="index.php">index.php</a></li>
                <li>If it works, try the main URL: <a href="./">https://i888908.apollo.fontysict.net/fed-workshops/</a></li>
                <li>Login with: <strong>admin@fed.nl</strong> / <strong>admin123</strong></li>
                <li>Once confirmed working, delete all debug files for security</li>
            </ol>
        </div>
    </div>
    
</body>
</html>