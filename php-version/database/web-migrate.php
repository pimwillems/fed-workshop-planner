<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workshop Planner - Database Setup</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #f8f9fa;
            color: #212529;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .step {
            margin: 2rem 0;
            padding: 1.5rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .step.completed {
            background: #d4edda;
            border-color: #c3e6cb;
        }
        .step.error {
            background: #f8d7da;
            border-color: #f5c6cb;
        }
        .btn {
            background: #0d6efd;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            margin: 0.5rem 0.5rem 0.5rem 0;
        }
        .btn:hover {
            background: #0b5ed7;
        }
        .btn-success {
            background: #198754;
        }
        .btn-danger {
            background: #dc3545;
        }
        .form-group {
            margin: 1rem 0;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 1rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin: 1rem 0;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        pre {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 0.875rem;
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎓 Workshop Planner - Database Setup</h1>
        <p>This wizard will help you set up your Workshop Planner database without needing command line access.</p>

        <?php
        // Include configuration
        $envPath = __DIR__ . '/../.env';
        $config = [];
        
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '=') !== false && $line[0] !== '#') {
                    list($key, $value) = explode('=', $line, 2);
                    $config[trim($key)] = trim($value);
                }
            }
        }

        $step = $_GET['step'] ?? 'check';
        $action = $_POST['action'] ?? '';

        // Step 1: Check environment
        if ($step === 'check') {
            echo '<div class="step">';
            echo '<h2>Step 1: Environment Check</h2>';
            
            $checks = [
                'PHP Version' => version_compare(PHP_VERSION, '7.4.0') >= 0,
                'PDO Extension' => extension_loaded('pdo'),
                'PDO MySQL' => extension_loaded('pdo_mysql'),
                '.env File' => file_exists($envPath),
                'Config Readable' => !empty($config['DB_HOST']),
                'Write Permissions' => is_writable(__DIR__ . '/../')
            ];
            
            $allPassed = true;
            foreach ($checks as $name => $passed) {
                $status = $passed ? '✅' : '❌';
                $class = $passed ? 'text-success' : 'text-danger';
                echo "<p><strong>{$status} {$name}</strong></p>";
                if (!$passed) $allPassed = false;
            }
            
            if ($allPassed) {
                echo '<div class="alert alert-success">All checks passed! You can proceed with the database setup.</div>';
                echo '<a href="?step=database" class="btn">Continue to Database Setup →</a>';
            } else {
                echo '<div class="alert alert-error">Please fix the issues above before continuing.</div>';
                if (!file_exists($envPath)) {
                    echo '<div class="alert alert-warning">Create your .env file by copying .env.example and editing it with your database credentials.</div>';
                }
            }
            echo '</div>';
        }

        // Step 2: Database setup
        elseif ($step === 'database') {
            echo '<div class="step">';
            echo '<h2>Step 2: Database Configuration</h2>';
            
            if ($action === 'test_connection') {
                try {
                    $dsn = "mysql:host={$config['DB_HOST']};port=" . ($config['DB_PORT'] ?? 3306);
                    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
                    echo '<div class="alert alert-success">✅ Database connection successful!</div>';
                    
                    // Check if database exists
                    $stmt = $pdo->query("SHOW DATABASES LIKE '{$config['DB_NAME']}'");
                    if ($stmt->rowCount() > 0) {
                        echo '<div class="alert alert-success">✅ Database "' . $config['DB_NAME'] . '" exists!</div>';
                        echo '<a href="?step=schema" class="btn">Continue to Schema Setup →</a>';
                    } else {
                        echo '<div class="alert alert-warning">⚠️ Database "' . $config['DB_NAME'] . '" does not exist.</div>';
                        echo '<form method="post">';
                        echo '<input type="hidden" name="action" value="create_database">';
                        echo '<button type="submit" class="btn">Create Database</button>';
                        echo '</form>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-error">❌ Database connection failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    echo '<p>Please check your .env configuration.</p>';
                }
            } elseif ($action === 'create_database') {
                try {
                    $dsn = "mysql:host={$config['DB_HOST']};port=" . ($config['DB_PORT'] ?? 3306);
                    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
                    $pdo->exec("CREATE DATABASE `{$config['DB_NAME']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    echo '<div class="alert alert-success">✅ Database created successfully!</div>';
                    echo '<a href="?step=schema" class="btn">Continue to Schema Setup →</a>';
                } catch (PDOException $e) {
                    echo '<div class="alert alert-error">❌ Failed to create database: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } else {
                echo '<p>Current configuration from .env:</p>';
                echo '<pre>';
                echo 'DB_HOST=' . ($config['DB_HOST'] ?? 'not set') . "\n";
                echo 'DB_NAME=' . ($config['DB_NAME'] ?? 'not set') . "\n";
                echo 'DB_USER=' . ($config['DB_USER'] ?? 'not set') . "\n";
                echo 'DB_PASSWORD=' . (isset($config['DB_PASSWORD']) ? '***' : 'not set') . "\n";
                echo '</pre>';
                
                echo '<form method="post">';
                echo '<input type="hidden" name="action" value="test_connection">';
                echo '<button type="submit" class="btn">Test Database Connection</button>';
                echo '</form>';
            }
            echo '</div>';
        }

        // Step 3: Schema setup
        elseif ($step === 'schema') {
            echo '<div class="step">';
            echo '<h2>Step 3: Database Schema</h2>';
            
            if ($action === 'create_schema') {
                try {
                    $dsn = "mysql:host={$config['DB_HOST']};port=" . ($config['DB_PORT'] ?? 3306) . ";dbname={$config['DB_NAME']};charset=utf8mb4";
                    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
                    
                    // Read and execute schema
                    $schema = file_get_contents(__DIR__ . '/schema.sql');
                    $statements = array_filter(array_map('trim', explode(';', $schema)));
                    
                    $pdo->beginTransaction();
                    $created = 0;
                    
                    foreach ($statements as $statement) {
                        if (empty($statement) || strpos($statement, '--') === 0 || strpos($statement, 'USE ') === 0) continue;
                        
                        try {
                            $pdo->exec($statement);
                            $created++;
                        } catch (PDOException $e) {
                            // Table might already exist, that's okay
                            if (strpos($e->getMessage(), 'already exists') === false) {
                                throw $e;
                            }
                        }
                    }
                    
                    $pdo->commit();
                    echo '<div class="alert alert-success">✅ Database schema created successfully! (' . $created . ' statements executed)</div>';
                    echo '<a href="?step=complete" class="btn btn-success">Complete Setup →</a>';
                    
                } catch (PDOException $e) {
                    echo '<div class="alert alert-error">❌ Failed to create schema: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            } else {
                // Check if tables exist
                try {
                    $dsn = "mysql:host={$config['DB_HOST']};port=" . ($config['DB_PORT'] ?? 3306) . ";dbname={$config['DB_NAME']};charset=utf8mb4";
                    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASSWORD']);
                    
                    $stmt = $pdo->query("SHOW TABLES");
                    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
                    
                    if (count($tables) > 0) {
                        echo '<div class="alert alert-warning">⚠️ Database already contains tables: ' . implode(', ', $tables) . '</div>';
                        echo '<p>Do you want to recreate the schema? This will delete existing data.</p>';
                        echo '<form method="post">';
                        echo '<input type="hidden" name="action" value="create_schema">';
                        echo '<button type="submit" class="btn btn-danger">Recreate Schema (DELETE EXISTING DATA)</button>';
                        echo '</form>';
                        echo '<a href="?step=complete" class="btn">Skip to Completion</a>';
                    } else {
                        echo '<p>Database is empty. Ready to create schema.</p>';
                        echo '<form method="post">';
                        echo '<input type="hidden" name="action" value="create_schema">';
                        echo '<button type="submit" class="btn">Create Database Schema</button>';
                        echo '</form>';
                    }
                } catch (PDOException $e) {
                    echo '<div class="alert alert-error">❌ Could not connect to database: ' . htmlspecialchars($e->getMessage()) . '</div>';
                }
            }
            echo '</div>';
        }

        // Step 4: Complete
        elseif ($step === 'complete') {
            echo '<div class="step completed">';
            echo '<h2>🎉 Setup Complete!</h2>';
            echo '<div class="alert alert-success">Your Workshop Planner database is ready!</div>';
            
            echo '<h3>Default Accounts</h3>';
            echo '<p>You can login with these default accounts:</p>';
            echo '<ul>';
            echo '<li><strong>Admin:</strong> admin@fed.nl / admin123</li>';
            echo '<li><strong>Teacher:</strong> teacher@fed.nl / admin123</li>';
            echo '</ul>';
            echo '<div class="alert alert-warning"><strong>⚠️ Important:</strong> Change these passwords immediately after logging in!</div>';
            
            echo '<h3>Next Steps</h3>';
            echo '<ol>';
            echo '<li><a href="../" target="_blank">Visit your Workshop Planner homepage</a></li>';
            echo '<li><a href="../login" target="_blank">Login with default accounts</a></li>';
            echo '<li>Change default passwords</li>';
            echo '<li>Create your first workshop</li>';
            echo '<li>Delete this setup file for security</li>';
            echo '</ol>';
            
            echo '<h3>Security Recommendations</h3>';
            echo '<ul>';
            echo '<li>Delete this setup file: <code>database/web-migrate.php</code></li>';
            echo '<li>Set a strong JWT_SECRET in your .env file</li>';
            echo '<li>Change all default passwords</li>';
            echo '<li>Regular database backups</li>';
            echo '</ul>';
            
            // Detect installation type
            $host = $_SERVER['HTTP_HOST'];
            $scriptName = $_SERVER['SCRIPT_NAME'];
            $basePath = dirname($scriptName);
            $isSubdirectory = $basePath !== '/' && $basePath !== '';
            
            echo '<div style="margin-top: 2rem;">';
            if ($isSubdirectory) {
                echo '<p><strong>📁 Subdirectory Installation Detected:</strong> ' . htmlspecialchars($host . $basePath) . '</p>';
                echo '<p>Your Workshop Planner is now available at the subdirectory path.</p>';
            } else {
                echo '<p><strong>🌐 Root Installation Detected:</strong> ' . htmlspecialchars($host) . '</p>';
                echo '<p>Your Workshop Planner is now available at the domain root.</p>';
            }
            echo '<a href="../" class="btn btn-success">Go to Workshop Planner →</a>';
            echo '</div>';
            echo '</div>';
        }
        ?>

        <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #dee2e6; color: #6c757d; font-size: 0.875rem;">
            <p><strong>Workshop Planner PHP Version</strong> - Converted from Nuxt 3 with enhanced security</p>
            <p>Need help? Check the README.md file for troubleshooting and detailed instructions.</p>
        </div>
    </div>
</body>
</html>