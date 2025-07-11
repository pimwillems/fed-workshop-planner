<?php
// Core security class for CSRF, XSS, and other security features
class Security {
    private static $config;
    
    public static function init() {
        self::$config = require_once __DIR__ . '/../../config/app.php';
        
        // Start secure session
        self::startSecureSession();
        
        // Set security headers
        self::setSecurityHeaders();
        
        // Initialize CSRF protection
        self::initCSRF();
    }
    
    private static function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            $config = self::$config['security'];
            
            // Configure session settings
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
            ini_set('session.cookie_samesite', 'Strict');
            ini_set('session.use_strict_mode', 1);
            ini_set('session.gc_maxlifetime', 3600); // 1 hour
            
            session_name($config['session_name']);
            session_start();
            
            // Regenerate session ID periodically
            if (!isset($_SESSION['last_regenerate'])) {
                $_SESSION['last_regenerate'] = time();
            } elseif (time() - $_SESSION['last_regenerate'] > 300) { // 5 minutes
                session_regenerate_id(true);
                $_SESSION['last_regenerate'] = time();
            }
        }
    }
    
    private static function setSecurityHeaders() {
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\'; img-src \'self\' data:; font-src \'self\';');
    }
    
    private static function initCSRF() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = self::generateCSRFToken();
        }
    }
    
    public static function generateCSRFToken() {
        return bin2hex(random_bytes(32));
    }
    
    public static function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function getCSRFToken() {
        return $_SESSION['csrf_token'] ?? null;
    }
    
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePassword($password) {
        $config = self::$config['security'];
        return strlen($password) >= $config['password_min_length'];
    }
    
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    public static function checkRateLimit($identifier, $maxAttempts = 5, $window = 900) {
        $db = Database::getInstance()->getConnection();
        
        // Clean old attempts
        $stmt = $db->prepare("DELETE FROM login_attempts WHERE attempted_at < ?");
        $stmt->execute([date('Y-m-d H:i:s', time() - $window)]);
        
        // Count recent attempts
        $stmt = $db->prepare("SELECT COUNT(*) FROM login_attempts WHERE email = ? AND attempted_at > ?");
        $stmt->execute([$identifier, date('Y-m-d H:i:s', time() - $window)]);
        $attempts = $stmt->fetchColumn();
        
        return $attempts < $maxAttempts;
    }
    
    public static function logLoginAttempt($email, $success = false) {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO login_attempts (email, ip_address, success) VALUES (?, ?, ?)");
        $stmt->execute([$email, $_SERVER['REMOTE_ADDR'], $success]);
    }
    
    public static function isValidUUID($uuid) {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $uuid) === 1;
    }
    
    public static function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
    
    public static function preventXSS($input) {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateSubject($subject) {
        $config = self::$config;
        return array_key_exists($subject, $config['subjects']);
    }
    
    public static function validateRole($role) {
        $config = self::$config;
        return in_array($role, $config['roles']);
    }
    
    public static function isValidDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}