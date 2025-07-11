<?php
// Database migration script to convert PostgreSQL data to MySQL
require_once '../config/database.php';

class DatabaseMigration {
    private $mysqlDb;
    private $postgresDb;
    
    public function __construct() {
        $this->mysqlDb = Database::getInstance()->getConnection();
        
        // PostgreSQL connection (if needed to migrate existing data)
        $postgresConfig = [
            'host' => $_ENV['POSTGRES_HOST'] ?? 'localhost',
            'port' => $_ENV['POSTGRES_PORT'] ?? 5432,
            'database' => $_ENV['POSTGRES_DB'] ?? 'workshop_planner',
            'username' => $_ENV['POSTGRES_USER'] ?? 'postgres',
            'password' => $_ENV['POSTGRES_PASSWORD'] ?? ''
        ];
        
        try {
            $dsn = "pgsql:host={$postgresConfig['host']};port={$postgresConfig['port']};dbname={$postgresConfig['database']}";
            $this->postgresDb = new PDO($dsn, $postgresConfig['username'], $postgresConfig['password']);
            $this->postgresDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "PostgreSQL connection failed (this is OK if you don't have existing data): " . $e->getMessage() . "\n";
            $this->postgresDb = null;
        }
    }
    
    public function createSchema() {
        echo "Creating MySQL schema...\n";
        
        $schema = file_get_contents(__DIR__ . '/schema.sql');
        $statements = explode(';', $schema);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (empty($statement) || strpos($statement, '--') === 0) continue;
            
            try {
                $this->mysqlDb->exec($statement);
                echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                echo "⚠ Warning: " . $e->getMessage() . "\n";
            }
        }
        
        echo "Schema created successfully!\n";
    }
    
    public function migrateData() {
        if (!$this->postgresDb) {
            echo "No PostgreSQL connection, skipping data migration.\n";
            return;
        }
        
        echo "Migrating data from PostgreSQL to MySQL...\n";
        
        try {
            // Migrate users
            $this->migrateUsers();
            
            // Migrate workshops
            $this->migrateWorkshops();
            
            echo "Data migration completed successfully!\n";
        } catch (Exception $e) {
            echo "Migration failed: " . $e->getMessage() . "\n";
            throw $e;
        }
    }
    
    private function migrateUsers() {
        echo "Migrating users...\n";
        
        $stmt = $this->postgresDb->query("SELECT * FROM users ORDER BY created_at");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $insertStmt = $this->mysqlDb->prepare("
            INSERT INTO users (id, email, name, password, role, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($users as $user) {
            $insertStmt->execute([
                $user['id'],
                $user['email'],
                $user['name'],
                $user['password'],
                $user['role'],
                $user['created_at'],
                $user['updated_at']
            ]);
        }
        
        echo "✓ Migrated " . count($users) . " users\n";
    }
    
    private function migrateWorkshops() {
        echo "Migrating workshops...\n";
        
        $stmt = $this->postgresDb->query("SELECT * FROM workshops ORDER BY created_at");
        $workshops = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $insertStmt = $this->mysqlDb->prepare("
            INSERT INTO workshops (id, title, description, subject, date, teacher_id, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($workshops as $workshop) {
            $insertStmt->execute([
                $workshop['id'],
                $workshop['title'],
                $workshop['description'],
                $workshop['subject'],
                $workshop['date'],
                $workshop['teacher_id'],
                $workshop['created_at'],
                $workshop['updated_at']
            ]);
        }
        
        echo "✓ Migrated " . count($workshops) . " workshops\n";
    }
    
    public function generateSampleData() {
        echo "Generating sample data...\n";
        
        // Create sample users
        $users = [
            ['John Doe', 'john@fed.nl', 'TEACHER'],
            ['Jane Smith', 'jane@fed.nl', 'TEACHER'],
            ['Mike Johnson', 'mike@fed.nl', 'ADMIN']
        ];
        
        $userStmt = $this->mysqlDb->prepare("
            INSERT INTO users (id, name, email, password, role) 
            VALUES (UUID(), ?, ?, ?, ?)
        ");
        
        foreach ($users as $user) {
            $userStmt->execute([
                $user[0],
                $user[1],
                password_hash('password123', PASSWORD_BCRYPT),
                $user[2]
            ]);
        }
        
        // Get user IDs for workshops
        $userIds = $this->mysqlDb->query("SELECT id FROM users WHERE role = 'TEACHER' LIMIT 2")->fetchAll(PDO::FETCH_COLUMN);
        
        // Create sample workshops
        $workshops = [
            ['Advanced PHP Development', 'Deep dive into modern PHP practices', 'DEV', '2024-01-15'],
            ['User Experience Design', 'Creating intuitive user interfaces', 'UX', '2024-01-16'],
            ['Project Management Basics', 'Essential PM skills for developers', 'PO', '2024-01-17'],
            ['Research Methods', 'Conducting effective user research', 'RESEARCH', '2024-01-18'],
            ['Portfolio Development', 'Building an impressive portfolio', 'PORTFOLIO', '2024-01-19'],
            ['Communication Skills', 'Effective team communication', 'MISC', '2024-01-20']
        ];
        
        $workshopStmt = $this->mysqlDb->prepare("
            INSERT INTO workshops (id, title, description, subject, date, teacher_id) 
            VALUES (UUID(), ?, ?, ?, ?, ?)
        ");
        
        foreach ($workshops as $index => $workshop) {
            $teacherId = $userIds[$index % count($userIds)];
            $workshopStmt->execute([
                $workshop[0],
                $workshop[1],
                $workshop[2],
                $workshop[3],
                $teacherId
            ]);
        }
        
        echo "✓ Generated sample data\n";
    }
}

// Run migration
if (php_sapi_name() === 'cli') {
    $migration = new DatabaseMigration();
    
    echo "=== Workshop Planner Database Migration ===\n";
    
    try {
        $migration->createSchema();
        $migration->migrateData();
        
        // Ask if user wants sample data
        echo "\nDo you want to generate sample data? (y/n): ";
        $handle = fopen("php://stdin", "r");
        $input = trim(fgets($handle));
        fclose($handle);
        
        if (strtolower($input) === 'y') {
            $migration->generateSampleData();
        }
        
        echo "\n=== Migration Complete ===\n";
        echo "Your workshop planner database is ready!\n";
        
    } catch (Exception $e) {
        echo "Migration failed: " . $e->getMessage() . "\n";
        exit(1);
    }
} else {
    echo "This script must be run from the command line.\n";
}