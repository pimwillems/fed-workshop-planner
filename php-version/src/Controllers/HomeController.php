<?php
// Home controller for public pages
class HomeController {
    
    public function index(Request $request, Response $response) {
        $user = $request->getCurrentUser();
        
        $response->view('index', [
            'user' => $user,
            'title' => 'FED Workshop Schedule'
        ]);
    }
}