<?php
// Main entry point for the PHP Workshop Planner

require_once 'config/paths.php';
require_once 'config/database.php';
require_once 'src/Core/Router.php';
require_once 'src/Core/Request.php';
require_once 'src/Core/Response.php';
require_once 'src/Core/Security.php';
require_once 'src/Core/JWT.php';

// Initialize security measures
Security::init();

// Get the current request
$request = new Request();
$response = new Response();

// Initialize router
$router = new Router();

// Define routes
$router->get('/', 'HomeController@index');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
$router->get('/dashboard', 'DashboardController@index');
$router->get('/change-password', 'AuthController@showChangePassword');
$router->post('/change-password', 'AuthController@changePassword');

// Workshop routes
$router->get('/api/workshops', 'WorkshopController@index');
$router->post('/api/workshops', 'WorkshopController@store');
$router->put('/api/workshops/{id}', 'WorkshopController@update');
$router->delete('/api/workshops/{id}', 'WorkshopController@destroy');

// User routes
$router->get('/api/auth/me', 'AuthController@me');

// Handle the request
try {
    $router->dispatch($request, $response);
} catch (Exception $e) {
    error_log("Application Error: " . $e->getMessage());
    $response->json(['error' => 'Internal server error'], 500);
}