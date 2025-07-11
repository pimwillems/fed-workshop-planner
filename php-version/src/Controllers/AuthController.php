<?php
// Authentication controller
require_once __DIR__ . '/../Core/JWT.php';

class AuthController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function showLogin(Request $request, Response $response) {
        // Check if already authenticated
        if ($request->isAuthenticated()) {
            $response->redirect(Paths::getRelativeUrl('dashboard'));
            return;
        }
        
        $csrfToken = Security::getCSRFToken();
        $response->view('auth/login', [
            'csrf_token' => $csrfToken,
            'error' => $_SESSION['error'] ?? null
        ]);
        
        unset($_SESSION['error']);
    }
    
    public function login(Request $request, Response $response) {
        // Validate CSRF token
        if (!Security::validateCSRFToken($request->getBody('csrf_token'))) {
            $response->forbidden('Invalid CSRF token');
            return;
        }
        
        $email = $request->getBody('email');
        $password = $request->getBody('password');
        
        // Validate input
        $errors = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);
        
        if (!empty($errors)) {
            if ($request->isAjax()) {
                $response->validationError($errors);
                return;
            }
            
            $_SESSION['error'] = 'Invalid email or password';
            $response->redirect(Paths::getRelativeUrl('login'));
            return;
        }
        
        // Check rate limiting
        if (!Security::checkRateLimit($email)) {
            Security::logLoginAttempt($email, false);
            $response->rateLimitExceeded('Too many login attempts. Please try again later.');
            return;
        }
        
        // Find user
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || !Security::verifyPassword($password, $user['password'])) {
            Security::logLoginAttempt($email, false);
            
            if ($request->isAjax()) {
                $response->unauthorized('Invalid credentials');
                return;
            }
            
            $_SESSION['error'] = 'Invalid email or password';
            $response->redirect(Paths::getRelativeUrl('login'));
            return;
        }
        
        // Log successful login
        Security::logLoginAttempt($email, true);
        
        // Generate JWT token
        $payload = [
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ];
        
        $token = JWT::encode($payload);
        
        // Set token cookie
        JWT::setTokenCookie($token);
        
        // Remove password from response
        unset($user['password']);
        
        if ($request->isAjax()) {
            $response->success([
                'user' => $user,
                'token' => $token,
                'redirect' => Paths::getRelativeUrl('dashboard')
            ], 'Login successful');
        } else {
            $response->redirect(Paths::getRelativeUrl('dashboard'));
        }
    }
    
    public function showRegister(Request $request, Response $response) {
        // Check if already authenticated
        if ($request->isAuthenticated()) {
            $response->redirect(Paths::getRelativeUrl('dashboard'));
            return;
        }
        
        $csrfToken = Security::getCSRFToken();
        $response->view('auth/register', [
            'csrf_token' => $csrfToken,
            'error' => $_SESSION['error'] ?? null
        ]);
        
        unset($_SESSION['error']);
    }
    
    public function register(Request $request, Response $response) {
        // Validate CSRF token
        if (!Security::validateCSRFToken($request->getBody('csrf_token'))) {
            $response->forbidden('Invalid CSRF token');
            return;
        }
        
        $name = $request->getBody('name');
        $email = $request->getBody('email');
        $password = $request->getBody('password');
        $confirmPassword = $request->getBody('confirm_password');
        
        // Validate input
        $errors = $request->validate([
            'name' => 'required|min:2|max:100',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'confirm_password' => 'required'
        ]);
        
        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            if ($request->isAjax()) {
                $response->validationError($errors);
                return;
            }
            
            $_SESSION['error'] = 'Please fix the errors below';
            $response->redirect(Paths::getRelativeUrl('register'));
            return;
        }
        
        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            if ($request->isAjax()) {
                $response->badRequest('Email already exists');
                return;
            }
            
            $_SESSION['error'] = 'Email already exists';
            $response->redirect(Paths::getRelativeUrl('register'));
            return;
        }
        
        // Hash password
        $hashedPassword = Security::hashPassword($password);
        
        // Create user
        $userId = Security::generateUUID();
        $stmt = $this->db->prepare("INSERT INTO users (id, name, email, password, role) VALUES (?, ?, ?, ?, 'TEACHER')");
        
        try {
            $stmt->execute([$userId, $name, $email, $hashedPassword]);
            
            // Auto-login after registration
            $payload = [
                'user_id' => $userId,
                'email' => $email,
                'role' => 'TEACHER'
            ];
            
            $token = JWT::encode($payload);
            JWT::setTokenCookie($token);
            
            if ($request->isAjax()) {
                $response->created([
                    'user' => [
                        'id' => $userId,
                        'name' => $name,
                        'email' => $email,
                        'role' => 'TEACHER'
                    ],
                    'token' => $token,
                    'redirect' => Paths::getRelativeUrl('dashboard')
                ], 'Registration successful');
            } else {
                $response->redirect(Paths::getRelativeUrl('dashboard'));
            }
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $response->error('Registration failed');
        }
    }
    
    public function logout(Request $request, Response $response) {
        JWT::clearTokenCookie();
        session_destroy();
        $response->redirect(Paths::getRelativeUrl());
    }
    
    public function me(Request $request, Response $response) {
        $user = $request->getCurrentUser();
        if (!$user) {
            $response->unauthorized('Not authenticated');
            return;
        }
        
        $response->success(['user' => $user]);
    }
    
    public function showChangePassword(Request $request, Response $response) {
        if (!$request->isAuthenticated()) {
            $response->redirect(Paths::getRelativeUrl('login'));
            return;
        }
        
        $csrfToken = Security::getCSRFToken();
        $user = $request->getCurrentUser();
        
        $response->view('auth/change-password', [
            'csrf_token' => $csrfToken,
            'user' => $user,
            'error' => $_SESSION['error'] ?? null,
            'success' => $_SESSION['success'] ?? null
        ]);
        
        unset($_SESSION['error'], $_SESSION['success']);
    }
    
    public function changePassword(Request $request, Response $response) {
        if (!$request->isAuthenticated()) {
            $response->unauthorized('Not authenticated');
            return;
        }
        
        // Validate CSRF token
        if (!Security::validateCSRFToken($request->getBody('csrf_token'))) {
            $response->forbidden('Invalid CSRF token');
            return;
        }
        
        $currentPassword = $request->getBody('current_password');
        $newPassword = $request->getBody('new_password');
        $confirmPassword = $request->getBody('confirm_password');
        
        // Validate input
        $errors = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
            'confirm_password' => 'required'
        ]);
        
        if ($newPassword !== $confirmPassword) {
            $errors['confirm_password'] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            if ($request->isAjax()) {
                $response->validationError($errors);
                return;
            }
            
            $_SESSION['error'] = 'Please fix the errors below';
            $response->redirect(Paths::getRelativeUrl('change-password'));
            return;
        }
        
        $user = $request->getCurrentUser();
        
        // Get current password hash
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $userRecord = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$userRecord || !Security::verifyPassword($currentPassword, $userRecord['password'])) {
            if ($request->isAjax()) {
                $response->badRequest('Current password is incorrect');
                return;
            }
            
            $_SESSION['error'] = 'Current password is incorrect';
            $response->redirect(Paths::getRelativeUrl('change-password'));
            return;
        }
        
        // Hash new password
        $hashedPassword = Security::hashPassword($newPassword);
        
        // Update password
        $stmt = $this->db->prepare("UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        
        try {
            $stmt->execute([$hashedPassword, $user['id']]);
            
            if ($request->isAjax()) {
                $response->success(null, 'Password changed successfully');
            } else {
                $_SESSION['success'] = 'Password changed successfully';
                $response->redirect(Paths::getRelativeUrl('dashboard'));
            }
        } catch (PDOException $e) {
            error_log("Password change error: " . $e->getMessage());
            $response->error('Failed to change password');
        }
    }
}