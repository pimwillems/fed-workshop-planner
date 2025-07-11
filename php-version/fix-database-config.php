<?php
/**
 * Fix Database Configuration
 * This will create a working database.php that doesn't depend on app.php
 */

echo "<!DOCTYPE html><html><head><title>Fix Database Config</title>";
echo "<style>body{font-family:Arial,sans-serif;margin:2rem;} .success{background:#d4edda;color:#155724;padding:1rem;border-radius:6px;margin:1rem 0;} .error{background:#f8d7da;color:#721c24;padding:1rem;border-radius:6px;margin:1rem 0;} .info{background:#d1ecf1;color:#0c5460;padding:1rem;border-radius:6px;margin:1rem 0;} pre{background:#f8f9fa;padding:1rem;border-radius:6px;overflow-x:auto;}</style>";
echo "</head><body>";

echo "<h1>🔧 Fix Database Configuration</h1>";

// Step 1: Check current app.php
echo "<h2>Step 1: Checking app.php</h2>";
$appPath = 'config/app.php';
if (file_exists($appPath)) {
    echo "<div class='success'>✅ app.php exists</div>";
    
    try {
        $config = require $appPath;
        if ($config === null) {
            echo "<div class='error'>❌ app.php returns null</div>";
        } elseif (is_array($config)) {
            echo "<div class='success'>✅ app.php returns array</div>";
            if (isset($config['database'])) {
                echo "<div class='success'>✅ Database section exists</div>";
            } else {
                echo "<div class='error'>❌ Database section missing</div>";
            }
        } else {
            echo "<div class='error'>❌ app.php returns wrong type: " . gettype($config) . "</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Error loading app.php: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='error'>❌ app.php not found</div>";
}

// Step 2: Create new database.php that loads env directly
echo "<h2>Step 2: Creating Fixed Database Class</h2>";

$newDatabasePhp = '<?php
// Database connection configuration - Fixed version
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        // Load environment variables directly
        if (file_exists(__DIR__ . "/../.env")) {
            $lines = file(__DIR__ . "/../.env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, "=") !== false && $line[0] !== "#") {
                    list($key, $value) = explode("=", $line, 2);
                    $_ENV[trim($key)] = trim($value);
                }
            }
        }
        
        // Get database configuration from environment
        $host = $_ENV["DB_HOST"] ?? "localhost";
        $port = $_ENV["DB_PORT"] ?? 3306;
        $database = $_ENV["DB_NAME"] ?? "workshop_planner";
        $username = $_ENV["DB_USER"] ?? "root";
        $password = $_ENV["DB_PASSWORD"] ?? "";
        
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }
    
    public function commit() {
        return $this->connection->commit();
    }
    
    public function rollback() {
        return $this->connection->rollback();
    }
}';

// Write the new database.php
if (file_put_contents('config/database.php.new', $newDatabasePhp)) {
    echo "<div class='success'>✅ Created new database.php.new</div>";
} else {
    echo "<div class='error'>❌ Failed to create new database.php</div>";
}

// Step 3: Test the new database class
echo "<h2>Step 3: Testing New Database Class</h2>";
try {
    require_once 'config/database.php.new';
    echo "<div class='success'>✅ New Database class loaded</div>";
    
    $db = Database::getInstance();
    echo "<div class='success'>✅ Database connection successful!</div>";
    
    $connection = $db->getConnection();
    $stmt = $connection->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<div class='success'>✅ Query successful - Found {$result['count']} users</div>";
    
    echo "<div class='info'><strong>✅ The new database configuration works!</strong></div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ New database class failed: " . $e->getMessage() . "</div>";
}

// Step 4: Instructions
echo "<h2>🎯 Instructions</h2>";
echo "<div class='info'>";
echo "<p>If the test above was successful:</p>";
echo "<ol>";
echo "<li>Backup your current database.php: <code>mv config/database.php config/database.php.backup</code></li>";
echo "<li>Replace it with the new one: <code>mv config/database.php.new config/database.php</code></li>";
echo "<li>Test your login page: <a href='login'>Login Page</a></li>";
echo "</ol>";
echo "</div>";

echo "<div class='info'>";
echo "<p><strong>Manual fix:</strong> You can also manually replace the content of config/database.php with the working version shown above.</p>";
echo "</div>";

echo "<div class='success'>";
echo "<h3>🎉 Expected Result</h3>";
echo "<p>After applying this fix, your /login route should work properly!</p>";
echo "</div>";

echo "</body></html>";
?>