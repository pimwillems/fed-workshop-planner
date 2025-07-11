<?php
// Dashboard controller for authenticated users
class DashboardController {
    
    public function index(Request $request, Response $response) {
        $user = $request->getCurrentUser();
        
        if (!$user) {
            $response->redirect(Paths::getRelativeUrl('login'));
            return;
        }
        
        $csrfToken = Security::getCSRFToken();
        
        $response->view('dashboard', [
            'user' => $user,
            'csrf_token' => $csrfToken,
            'title' => 'Dashboard - FED Workshop Planner'
        ]);
    }
}