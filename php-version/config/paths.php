<?php
// Path configuration for subdomain and subdirectory installations
class Paths {
    private static $basePath = null;
    
    public static function getBasePath() {
        if (self::$basePath === null) {
            // Get the directory path from the script name
            $scriptName = $_SERVER['SCRIPT_NAME'];
            $scriptDir = dirname($scriptName);
            
            // Normalize path separators
            $scriptDir = str_replace('\\', '/', $scriptDir);
            
            // Handle different installation scenarios
            if ($scriptDir === '.' || $scriptDir === '' || $scriptDir === '/') {
                // Root installation
                $scriptDir = '/';
            } else {
                // Subdirectory installation - ensure it ends with /
                if (substr($scriptDir, -1) !== '/') {
                    $scriptDir .= '/';
                }
            }
            
            self::$basePath = $scriptDir;
        }
        
        return self::$basePath;
    }
    
    public static function getAbsoluteUrl($path = '') {
        $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $basePath = self::getBasePath();
        
        // Remove leading slash from path if present
        $path = ltrim($path, '/');
        
        return $protocol . '://' . $host . $basePath . $path;
    }
    
    public static function getRelativeUrl($path = '') {
        $basePath = self::getBasePath();
        
        // Remove leading slash from path if present
        $path = ltrim($path, '/');
        
        return $basePath . $path;
    }
    
    public static function asset($path) {
        return self::getRelativeUrl('assets/' . ltrim($path, '/'));
    }
}