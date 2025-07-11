<?php
// JWT implementation for authentication
class JWT {
    private static $config;
    
    public static function init() {
        self::$config = require_once __DIR__ . '/../../config/app.php';
    }
    
    public static function encode($payload) {
        if (!self::$config) {
            self::init();
        }
        
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $payload['iat'] = time();
        $payload['exp'] = time() + self::$config['jwt']['expiration'];
        $payload['iss'] = self::$config['jwt']['issuer'];
        $payload['aud'] = self::$config['jwt']['audience'];
        
        $payload = json_encode($payload);
        
        $headerEncoded = self::base64UrlEncode($header);
        $payloadEncoded = self::base64UrlEncode($payload);
        
        $signature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, self::$config['jwt']['secret'], true);
        $signatureEncoded = self::base64UrlEncode($signature);
        
        return $headerEncoded . "." . $payloadEncoded . "." . $signatureEncoded;
    }
    
    public static function decode($token) {
        if (!self::$config) {
            self::init();
        }
        
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        
        [$headerEncoded, $payloadEncoded, $signatureEncoded] = $parts;
        
        $header = json_decode(self::base64UrlDecode($headerEncoded), true);
        $payload = json_decode(self::base64UrlDecode($payloadEncoded), true);
        $signature = self::base64UrlDecode($signatureEncoded);
        
        // Verify signature
        $expectedSignature = hash_hmac('sha256', $headerEncoded . "." . $payloadEncoded, self::$config['jwt']['secret'], true);
        if (!hash_equals($signature, $expectedSignature)) {
            return false;
        }
        
        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            return false;
        }
        
        // Check issuer and audience
        if ($payload['iss'] !== self::$config['jwt']['issuer'] || $payload['aud'] !== self::$config['jwt']['audience']) {
            return false;
        }
        
        return $payload;
    }
    
    public static function getTokenFromRequest() {
        // Check Authorization header
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }
        
        // Check cookies
        if (isset($_COOKIE['auth_token'])) {
            return $_COOKIE['auth_token'];
        }
        
        // Check POST data
        if (isset($_POST['token'])) {
            return $_POST['token'];
        }
        
        return null;
    }
    
    public static function getCurrentUser() {
        $token = self::getTokenFromRequest();
        if (!$token) {
            return null;
        }
        
        $payload = self::decode($token);
        if (!$payload) {
            return null;
        }
        
        // Get user from database
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$payload['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return null;
        }
        
        // Remove password from response
        unset($user['password']);
        
        return $user;
    }
    
    public static function setTokenCookie($token) {
        $expiration = time() + self::$config['jwt']['expiration'];
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        
        setcookie('auth_token', $token, [
            'expires' => $expiration,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    public static function clearTokenCookie() {
        setcookie('auth_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => '',
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    private static function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    private static function base64UrlDecode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}