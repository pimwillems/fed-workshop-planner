<?php
// Test file to verify path configuration for subdomain/subdirectory installations
require_once 'config/paths.php';

echo "<h1>Workshop Planner - Path Configuration Test</h1>";

// Detect installation type
$host = $_SERVER['HTTP_HOST'];
$basePath = Paths::getBasePath();
$isSubdirectory = $basePath !== '/';
$installationType = $isSubdirectory ? 'Subdirectory' : 'Root/Subdomain';

echo "<div style='background: #e7f3ff; padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
echo "<h2>🔍 Installation Detection</h2>";
echo "<p><strong>Host:</strong> " . htmlspecialchars($host) . "</p>";
echo "<p><strong>Installation Type:</strong> " . $installationType . "</p>";
echo "<p><strong>Base Path:</strong> " . htmlspecialchars($basePath) . "</p>";
echo "</div>";

echo "<div style='background: #f0f8e7; padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
echo "<h2>🔗 Generated URLs</h2>";
echo "<p><strong>Home URL:</strong> " . htmlspecialchars(Paths::getRelativeUrl()) . "</p>";
echo "<p><strong>Login URL:</strong> " . htmlspecialchars(Paths::getRelativeUrl('login')) . "</p>";
echo "<p><strong>Dashboard URL:</strong> " . htmlspecialchars(Paths::getRelativeUrl('dashboard')) . "</p>";
echo "<p><strong>API Base URL:</strong> " . htmlspecialchars(Paths::getRelativeUrl('api/')) . "</p>";
echo "<p><strong>CSS Asset URL:</strong> " . htmlspecialchars(Paths::asset('css/style.css')) . "</p>";
echo "<p><strong>Absolute Home URL:</strong> " . htmlspecialchars(Paths::getAbsoluteUrl()) . "</p>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
echo "<h2>🧪 Navigation Test</h2>";
echo "<p>Test these links to verify routing works correctly:</p>";
echo "<p>";
echo "<a href='" . htmlspecialchars(Paths::getRelativeUrl()) . "' style='margin-right: 1rem; color: #0066cc;'>🏠 Home</a>";
echo "<a href='" . htmlspecialchars(Paths::getRelativeUrl('login')) . "' style='margin-right: 1rem; color: #0066cc;'>🔐 Login</a>";
echo "<a href='" . htmlspecialchars(Paths::getRelativeUrl('register')) . "' style='margin-right: 1rem; color: #0066cc;'>✏️ Register</a>";
echo "</p>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
echo "<h2>🔧 Server Debug Info</h2>";
echo "<pre style='font-size: 0.9rem; overflow-x: auto;'>";
echo "SCRIPT_NAME: " . htmlspecialchars($_SERVER['SCRIPT_NAME']) . "\n";
echo "REQUEST_URI: " . htmlspecialchars($_SERVER['REQUEST_URI']) . "\n";
echo "HTTP_HOST: " . htmlspecialchars($_SERVER['HTTP_HOST']) . "\n";
echo "DOCUMENT_ROOT: " . htmlspecialchars($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "PHP_SELF: " . htmlspecialchars($_SERVER['PHP_SELF']) . "\n";

if (isset($_SERVER['HTTPS'])) {
    echo "HTTPS: " . htmlspecialchars($_SERVER['HTTPS']) . "\n";
}
echo "</pre>";
echo "</div>";

echo "<div style='background: #f8d7da; padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
echo "<h2>⚠️ Security Notice</h2>";
echo "<p><strong>Important:</strong> Delete this test file (<code>test-paths.php</code>) after confirming your installation works correctly.</p>";
echo "<p>This file exposes server configuration details and should not remain in production.</p>";
echo "</div>";

echo "<div style='background: #d4edda; padding: 1rem; border-radius: 8px; margin: 1rem 0;'>";
echo "<h2>✅ Expected Configuration</h2>";
if ($isSubdirectory) {
    echo "<p><strong>Subdirectory Setup (i888908.apollo.fontysict.net/fed-workshops):</strong></p>";
    echo "<ul>";
    echo "<li>Base Path should be: <code>/fed-workshops/</code></li>";
    echo "<li>Home URL should be: <code>/fed-workshops/</code></li>";
    echo "<li>Files uploaded to subdirectory on server</li>";
    echo "<li>All URLs should include the directory prefix</li>";
    echo "</ul>";
} else {
    echo "<p><strong>Root/Subdomain Setup:</strong></p>";
    echo "<ul>";
    echo "<li>Base Path should be: <code>/</code></li>";
    echo "<li>Home URL should be: <code>/</code></li>";
    echo "<li>Files uploaded to web root</li>";
    echo "</ul>";
}
echo "</div>";
?>