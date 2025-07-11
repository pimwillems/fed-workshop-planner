<?php
// Database connection configuration
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        // Direct database credentials
        $host = '192.168.15.56';
        $port = '3306';
        $database = 'i888908_workshopplanner';
        $username = 'i888908_workshopplanner';
        $password = 'Cookies2022!';
        $charset = 'utf8mb4';
        
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
            $this->connection = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
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
}