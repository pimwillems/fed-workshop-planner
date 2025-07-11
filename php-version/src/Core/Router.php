<?php
// Simple router for handling HTTP requests
class Router {
    private $routes = [];
    private $middleware = [];
    
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }
    
    public function put($path, $handler) {
        $this->addRoute('PUT', $path, $handler);
    }
    
    public function delete($path, $handler) {
        $this->addRoute('DELETE', $path, $handler);
    }
    
    public function middleware($middleware) {
        $this->middleware[] = $middleware;
    }
    
    private function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function dispatch(Request $request, Response $response) {
        $requestMethod = $request->getMethod();
        $requestPath = $request->getPath();
        
        // Find matching route
        $matchedRoute = null;
        $params = [];
        
        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }
            
            $pattern = $this->convertToRegex($route['path']);
            if (preg_match($pattern, $requestPath, $matches)) {
                $matchedRoute = $route;
                $params = $this->extractParams($route['path'], $matches);
                break;
            }
        }
        
        if (!$matchedRoute) {
            $response->json(['error' => 'Route not found'], 404);
            return;
        }
        
        // Run middleware
        foreach ($this->middleware as $middleware) {
            $result = $middleware($request, $response);
            if ($result === false) {
                return; // Middleware blocked the request
            }
        }
        
        // Set route parameters
        $request->setParams($params);
        
        // Execute handler
        $this->executeHandler($matchedRoute['handler'], $request, $response);
    }
    
    private function convertToRegex($path) {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    private function extractParams($path, $matches) {
        $params = [];
        preg_match_all('/\{([^}]+)\}/', $path, $paramNames);
        
        for ($i = 1; $i < count($matches); $i++) {
            $paramName = $paramNames[1][$i - 1];
            $params[$paramName] = $matches[$i];
        }
        
        return $params;
    }
    
    private function executeHandler($handler, Request $request, Response $response) {
        if (is_string($handler)) {
            [$controllerName, $methodName] = explode('@', $handler);
            
            $controllerFile = __DIR__ . "/../Controllers/{$controllerName}.php";
            if (!file_exists($controllerFile)) {
                $response->json(['error' => 'Controller not found'], 500);
                return;
            }
            
            require_once $controllerFile;
            
            if (!class_exists($controllerName)) {
                $response->json(['error' => 'Controller class not found'], 500);
                return;
            }
            
            $controller = new $controllerName();
            
            if (!method_exists($controller, $methodName)) {
                $response->json(['error' => 'Controller method not found'], 500);
                return;
            }
            
            $controller->$methodName($request, $response);
        } elseif (is_callable($handler)) {
            $handler($request, $response);
        } else {
            $response->json(['error' => 'Invalid handler'], 500);
        }
    }
}