<?php
// HTTP Request handler
class Request {
    private $method;
    private $path;
    private $params = [];
    private $query = [];
    private $body = [];
    private $headers = [];
    
    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        
        // Get path and remove base path for subdirectory installations
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = Paths::getBasePath();
        
        // Remove base path from request path
        if ($basePath !== '/' && strpos($requestPath, $basePath) === 0) {
            $requestPath = substr($requestPath, strlen($basePath) - 1);
        }
        
        $this->path = $requestPath ?: '/';
        $this->query = $_GET;
        $this->headers = getallheaders();
        
        // Handle different request methods
        switch ($this->method) {
            case 'POST':
                $this->body = $_POST;
                break;
            case 'PUT':
            case 'DELETE':
                $input = file_get_contents('php://input');
                if ($input) {
                    $contentType = $this->getHeader('Content-Type');
                    if (strpos($contentType, 'application/json') !== false) {
                        $this->body = json_decode($input, true) ?? [];
                    } else {
                        parse_str($input, $this->body);
                    }
                }
                break;
        }
        
        // Sanitize input
        $this->body = Security::sanitizeInput($this->body);
        $this->query = Security::sanitizeInput($this->query);
    }
    
    public function getMethod() {
        return $this->method;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function setParams($params) {
        $this->params = $params;
    }
    
    public function getParam($name, $default = null) {
        return $this->params[$name] ?? $default;
    }
    
    public function getQuery($name = null, $default = null) {
        if ($name === null) {
            return $this->query;
        }
        return $this->query[$name] ?? $default;
    }
    
    public function getBody($name = null, $default = null) {
        if ($name === null) {
            return $this->body;
        }
        return $this->body[$name] ?? $default;
    }
    
    public function getHeader($name) {
        return $this->headers[$name] ?? null;
    }
    
    public function isJson() {
        $contentType = $this->getHeader('Content-Type');
        return strpos($contentType, 'application/json') !== false;
    }
    
    public function isAjax() {
        return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }
    
    public function getUserAgent() {
        return $this->getHeader('User-Agent');
    }
    
    public function getIpAddress() {
        $ipKeys = ['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
    
    public function validate($rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            $value = $this->getBody($field);
            
            if (strpos($rule, 'required') !== false && empty($value)) {
                $errors[$field] = 'This field is required';
                continue;
            }
            
            if (strpos($rule, 'email') !== false && !Security::validateEmail($value)) {
                $errors[$field] = 'Invalid email format';
            }
            
            if (strpos($rule, 'min:') !== false) {
                preg_match('/min:(\d+)/', $rule, $matches);
                $min = (int)$matches[1];
                if (strlen($value) < $min) {
                    $errors[$field] = "Must be at least {$min} characters";
                }
            }
            
            if (strpos($rule, 'max:') !== false) {
                preg_match('/max:(\d+)/', $rule, $matches);
                $max = (int)$matches[1];
                if (strlen($value) > $max) {
                    $errors[$field] = "Must be no more than {$max} characters";
                }
            }
            
            if (strpos($rule, 'date') !== false && !Security::isValidDate($value)) {
                $errors[$field] = 'Invalid date format';
            }
        }
        
        return $errors;
    }
    
    public function getCurrentUser() {
        return JWT::getCurrentUser();
    }
    
    public function isAuthenticated() {
        return $this->getCurrentUser() !== null;
    }
}