<?php
// HTTP Response handler
class Response {
    private $statusCode = 200;
    private $headers = [];
    private $body = '';
    
    public function setStatusCode($code) {
        $this->statusCode = $code;
        return $this;
    }
    
    public function setHeader($name, $value) {
        $this->headers[$name] = $value;
        return $this;
    }
    
    public function json($data, $statusCode = 200) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/json');
        
        $response = [
            'success' => $statusCode >= 200 && $statusCode < 300,
            'data' => $data,
            'timestamp' => date('c')
        ];
        
        if (!$response['success']) {
            $response['error'] = $data;
            unset($response['data']);
        }
        
        $this->body = json_encode($response, JSON_PRETTY_PRINT);
        $this->send();
    }
    
    public function html($content, $statusCode = 200) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'text/html; charset=utf-8');
        $this->body = $content;
        $this->send();
    }
    
    public function redirect($url, $statusCode = 302) {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        $this->send();
    }
    
    public function error($message, $statusCode = 500) {
        $this->json(['error' => $message], $statusCode);
    }
    
    public function success($data = null, $message = null) {
        $response = ['success' => true];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        if ($message !== null) {
            $response['message'] = $message;
        }
        
        $this->json($response);
    }
    
    public function created($data = null, $message = 'Resource created successfully') {
        $response = ['success' => true, 'message' => $message];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        $this->json($response, 201);
    }
    
    public function notFound($message = 'Resource not found') {
        $this->json(['error' => $message], 404);
    }
    
    public function unauthorized($message = 'Unauthorized') {
        $this->json(['error' => $message], 401);
    }
    
    public function forbidden($message = 'Forbidden') {
        $this->json(['error' => $message], 403);
    }
    
    public function badRequest($message = 'Bad request') {
        $this->json(['error' => $message], 400);
    }
    
    public function validationError($errors) {
        $this->json([
            'error' => 'Validation failed',
            'errors' => $errors
        ], 422);
    }
    
    public function rateLimitExceeded($message = 'Rate limit exceeded') {
        $this->json(['error' => $message], 429);
    }
    
    private function send() {
        // Send status code
        http_response_code($this->statusCode);
        
        // Send headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        
        // Send body
        echo $this->body;
        
        // Terminate script
        exit();
    }
    
    public function view($template, $data = []) {
        $templatePath = __DIR__ . "/../../views/{$template}.php";
        
        if (!file_exists($templatePath)) {
            $this->error("Template not found: {$template}", 500);
            return;
        }
        
        // Extract variables for the template
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include template
        include $templatePath;
        
        // Get content and clean buffer
        $content = ob_get_clean();
        
        $this->html($content);
    }
    
    public function download($filePath, $fileName = null) {
        if (!file_exists($filePath)) {
            $this->notFound('File not found');
            return;
        }
        
        $fileName = $fileName ?? basename($filePath);
        $fileSize = filesize($filePath);
        $mimeType = mime_content_type($filePath);
        
        $this->setHeader('Content-Type', $mimeType);
        $this->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $this->setHeader('Content-Length', $fileSize);
        $this->setHeader('Cache-Control', 'private, must-revalidate, max-age=0');
        $this->setHeader('Pragma', 'public');
        $this->setHeader('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
        
        $this->body = file_get_contents($filePath);
        $this->send();
    }
}