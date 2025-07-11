<?php
/**
 * Final Router Fix - Comprehensive solution for routing issues
 */

echo "<!DOCTYPE html><html><head><title>Final Router Fix</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .success{background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin:1rem 0;} .error{background:#f8d7da;color:#721c24;padding:1rem;border-radius:6px;margin:1rem 0;} .info{background:#d1ecf1;color:#0c5460;padding:1rem;border-radius:6px;margin:1rem 0;}</style>";
echo "</head><body>";

echo "<h1>🔧 Final Router Fix</h1>";

$fixes = [];

// Fix 1: Check and fix .htaccess
echo "<h2>Fix 1: .htaccess Configuration</h2>";
if (file_exists('.htaccess')) {
    $htaccess = file_get_contents('.htaccess');
    
    // Check if RewriteBase is in the right place
    if (strpos($htaccess, 'RewriteBase /fed-workshops/') === false) {
        $fixes[] = "❌ RewriteBase missing";
    } else {
        $fixes[] = "✅ RewriteBase found";
    }
    
    // Check if the order is correct
    $rewriteEnginePos = strpos($htaccess, 'RewriteEngine On');
    $rewriteBasePos = strpos($htaccess, 'RewriteBase /fed-workshops/');
    
    if ($rewriteBasePos !== false && $rewriteEnginePos !== false) {
        if ($rewriteBasePos > $rewriteEnginePos && $rewriteBasePos < strpos($htaccess, 'RewriteRule')) {
            $fixes[] = "✅ RewriteBase in correct position";
        } else {
            $fixes[] = "⚠️ RewriteBase position might be wrong";
        }
    }
    
} else {
    $fixes[] = "❌ .htaccess file missing";
}

// Fix 2: Create a simpler .htaccess for testing
echo "<h2>Fix 2: Creating Simple .htaccess</h2>";
$simpleHtaccess = "RewriteEngine On
RewriteBase /fed-workshops/
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
";

if (file_put_contents('.htaccess.simple', $simpleHtaccess)) {
    $fixes[] = "✅ Created simple .htaccess.simple file";
} else {
    $fixes[] = "❌ Failed to create simple .htaccess.simple";
}

// Fix 3: Test the router directly
echo "<h2>Fix 3: Testing Router Components</h2>";
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
    
    $fixes[] = "✅ All components loaded successfully";
    
    // Test path detection
    $basePath = Paths::getBasePath();
    $fixes[] = "✅ Base path detected: '$basePath'";
    
    // Test request creation
    Security::init();
    $request = new Request();
    $fixes[] = "✅ Request created - Path: '" . $request->getPath() . "'";
    
} catch (Exception $e) {
    $fixes[] = "❌ Component test failed: " . $e->getMessage();
}

// Fix 4: Create a direct routing test
echo "<h2>Fix 4: Direct Routing Test</h2>";
$directTest = '<?php
// Direct routing test - bypasses .htaccess
if (isset($_GET["route"])) {
    $route = $_GET["route"];
    
    // Load the application
    if (file_exists(".env")) {
        $lines = file(".env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, "=") !== false && $line[0] !== "#") {
                list($key, $value) = explode("=", $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
    
    require_once "config/paths.php";
    require_once "config/database.php";
    require_once "src/Core/Router.php";
    require_once "src/Core/Request.php";
    require_once "src/Core/Response.php";
    require_once "src/Core/Security.php";
    require_once "src/Core/JWT.php";
    
    Security::init();
    
    // Simulate the request
    $_SERVER["REQUEST_METHOD"] = "GET";
    $_SERVER["REQUEST_URI"] = "/fed-workshops/" . $route;
    
    $request = new Request();
    $response = new Response();
    $router = new Router();
    
    // Add routes
    $router->get("/", "HomeController@index");
    $router->get("/login", "AuthController@showLogin");
    
    // Dispatch
    $router->dispatch($request, $response);
    
} else {
    echo "<h1>Direct Route Test</h1>";
    echo "<p><a href=\"?route=login\">Test Login Route</a></p>";
    echo "<p><a href=\"?route=\">Test Home Route</a></p>";
}
?>';

if (file_put_contents('direct-route-test.php', $directTest)) {
    $fixes[] = "✅ Created direct-route-test.php";
} else {
    $fixes[] = "❌ Failed to create direct-route-test.php";
}

// Display all fixes
foreach ($fixes as $fix) {
    echo "<div class='" . (strpos($fix, '✅') === 0 ? 'success' : (strpos($fix, '❌') === 0 ? 'error' : 'info')) . "'>$fix</div>";
}

echo "<div class='info'>";
echo "<h2>🎯 Next Steps</h2>";
echo "<ol>";
echo "<li>Upload the corrected .htaccess file</li>";
echo "<li>Test with: <a href='router-debug.php'>Router Debug Tool</a></li>";
echo "<li>Try: <a href='direct-route-test.php'>Direct Route Test</a></li>";
echo "<li>If .htaccess doesn't work, try renaming .htaccess.simple to .htaccess</li>";
echo "<li>Test the actual login: <a href='login'>Login Page</a></li>";
echo "</ol>";
echo "</div>";

echo "<div class='error'>";
echo "<h2>⚠️ Alternative Solution</h2>";
echo "<p>If routing still doesn't work, you can access pages directly:</p>";
echo "<p><a href='direct-login-test.php'>Direct Login (bypasses router)</a></p>";
echo "<p><a href='index.php'>Direct Index</a></p>";
echo "</div>";

echo "</body></html>";
?>