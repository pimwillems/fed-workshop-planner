<?php
// Workshop controller for CRUD operations
class WorkshopController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function index(Request $request, Response $response) {
        try {
            // Build query with filters
            $where = [];
            $params = [];
            
            if ($request->getQuery('subject')) {
                $where[] = "subject = ?";
                $params[] = strtoupper($request->getQuery('subject'));
            }
            
            if ($request->getQuery('date')) {
                $where[] = "date = ?";
                $params[] = $request->getQuery('date');
            }
            
            if ($request->getQuery('teacher_id')) {
                $where[] = "teacher_id = ?";
                $params[] = $request->getQuery('teacher_id');
            }
            
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
            
            // Fetch workshops with teacher information
            $sql = "
                SELECT 
                    w.*,
                    u.name as teacher_name,
                    u.email as teacher_email,
                    u.role as teacher_role
                FROM workshops w
                JOIN users u ON w.teacher_id = u.id
                {$whereClause}
                ORDER BY w.date ASC, w.created_at ASC
            ";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $workshops = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Transform data for frontend
            $transformedWorkshops = array_map(function($workshop) {
                return [
                    'id' => $workshop['id'],
                    'title' => $workshop['title'],
                    'description' => $workshop['description'],
                    'subject' => $workshop['subject'],
                    'date' => $workshop['date'],
                    'teacher' => [
                        'id' => $workshop['teacher_id'],
                        'name' => $workshop['teacher_name'],
                        'email' => $workshop['teacher_email'],
                        'role' => strtolower($workshop['teacher_role'])
                    ],
                    'createdAt' => $workshop['created_at'],
                    'updatedAt' => $workshop['updated_at']
                ];
            }, $workshops);
            
            $response->success([
                'workshops' => $transformedWorkshops
            ]);
        } catch (PDOException $e) {
            error_log("Error fetching workshops: " . $e->getMessage());
            $response->error('Failed to fetch workshops');
        }
    }
    
    public function store(Request $request, Response $response) {
        $user = $request->getCurrentUser();
        if (!$user) {
            $response->unauthorized('Authentication required');
            return;
        }
        
        // Validate CSRF token
        if (!Security::validateCSRFToken($request->getBody('csrf_token'))) {
            $response->forbidden('Invalid CSRF token');
            return;
        }
        
        $title = $request->getBody('title');
        $description = $request->getBody('description');
        $subject = $request->getBody('subject');
        $date = $request->getBody('date');
        
        // Validate input
        $errors = $request->validate([
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:10|max:1000',
            'subject' => 'required',
            'date' => 'required|date'
        ]);
        
        if (!Security::validateSubject($subject)) {
            $errors['subject'] = 'Invalid subject';
        }
        
        if (!empty($errors)) {
            $response->validationError($errors);
            return;
        }
        
        // Check if date is in the future
        if (strtotime($date) < strtotime(date('Y-m-d'))) {
            $response->badRequest('Workshop date must be in the future');
            return;
        }
        
        try {
            $workshopId = Security::generateUUID();
            $stmt = $this->db->prepare("
                INSERT INTO workshops (id, title, description, subject, date, teacher_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $workshopId,
                $title,
                $description,
                strtoupper($subject),
                $date,
                $user['id']
            ]);
            
            // Fetch the created workshop with teacher info
            $stmt = $this->db->prepare("
                SELECT 
                    w.*,
                    u.name as teacher_name,
                    u.email as teacher_email,
                    u.role as teacher_role
                FROM workshops w
                JOIN users u ON w.teacher_id = u.id
                WHERE w.id = ?
            ");
            
            $stmt->execute([$workshopId]);
            $workshop = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $transformedWorkshop = [
                'id' => $workshop['id'],
                'title' => $workshop['title'],
                'description' => $workshop['description'],
                'subject' => $workshop['subject'],
                'date' => $workshop['date'],
                'teacher' => [
                    'id' => $workshop['teacher_id'],
                    'name' => $workshop['teacher_name'],
                    'email' => $workshop['teacher_email'],
                    'role' => strtolower($workshop['teacher_role'])
                ],
                'createdAt' => $workshop['created_at'],
                'updatedAt' => $workshop['updated_at']
            ];
            
            $response->created([
                'workshop' => $transformedWorkshop
            ], 'Workshop created successfully');
            
        } catch (PDOException $e) {
            error_log("Error creating workshop: " . $e->getMessage());
            $response->error('Failed to create workshop');
        }
    }
    
    public function update(Request $request, Response $response) {
        $user = $request->getCurrentUser();
        if (!$user) {
            $response->unauthorized('Authentication required');
            return;
        }
        
        $workshopId = $request->getParam('id');
        if (!Security::isValidUUID($workshopId)) {
            $response->badRequest('Invalid workshop ID');
            return;
        }
        
        // Check if workshop exists and user has permission
        $stmt = $this->db->prepare("SELECT * FROM workshops WHERE id = ?");
        $stmt->execute([$workshopId]);
        $workshop = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$workshop) {
            $response->notFound('Workshop not found');
            return;
        }
        
        // Only allow teacher to edit their own workshops, or admin to edit any
        if ($workshop['teacher_id'] !== $user['id'] && $user['role'] !== 'ADMIN') {
            $response->forbidden('You can only edit your own workshops');
            return;
        }
        
        // Validate CSRF token
        if (!Security::validateCSRFToken($request->getBody('csrf_token'))) {
            $response->forbidden('Invalid CSRF token');
            return;
        }
        
        $title = $request->getBody('title') ?? $workshop['title'];
        $description = $request->getBody('description') ?? $workshop['description'];
        $subject = $request->getBody('subject') ?? $workshop['subject'];
        $date = $request->getBody('date') ?? $workshop['date'];
        
        // Validate input
        $errors = [];
        
        if (strlen($title) < 3 || strlen($title) > 255) {
            $errors['title'] = 'Title must be between 3 and 255 characters';
        }
        
        if (strlen($description) < 10 || strlen($description) > 1000) {
            $errors['description'] = 'Description must be between 10 and 1000 characters';
        }
        
        if (!Security::validateSubject($subject)) {
            $errors['subject'] = 'Invalid subject';
        }
        
        if (!Security::isValidDate($date)) {
            $errors['date'] = 'Invalid date format';
        }
        
        if (!empty($errors)) {
            $response->validationError($errors);
            return;
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE workshops 
                SET title = ?, description = ?, subject = ?, date = ?, updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            $stmt->execute([
                $title,
                $description,
                strtoupper($subject),
                $date,
                $workshopId
            ]);
            
            // Fetch updated workshop with teacher info
            $stmt = $this->db->prepare("
                SELECT 
                    w.*,
                    u.name as teacher_name,
                    u.email as teacher_email,
                    u.role as teacher_role
                FROM workshops w
                JOIN users u ON w.teacher_id = u.id
                WHERE w.id = ?
            ");
            
            $stmt->execute([$workshopId]);
            $updatedWorkshop = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $transformedWorkshop = [
                'id' => $updatedWorkshop['id'],
                'title' => $updatedWorkshop['title'],
                'description' => $updatedWorkshop['description'],
                'subject' => $updatedWorkshop['subject'],
                'date' => $updatedWorkshop['date'],
                'teacher' => [
                    'id' => $updatedWorkshop['teacher_id'],
                    'name' => $updatedWorkshop['teacher_name'],
                    'email' => $updatedWorkshop['teacher_email'],
                    'role' => strtolower($updatedWorkshop['teacher_role'])
                ],
                'createdAt' => $updatedWorkshop['created_at'],
                'updatedAt' => $updatedWorkshop['updated_at']
            ];
            
            $response->success([
                'workshop' => $transformedWorkshop
            ], 'Workshop updated successfully');
            
        } catch (PDOException $e) {
            error_log("Error updating workshop: " . $e->getMessage());
            $response->error('Failed to update workshop');
        }
    }
    
    public function destroy(Request $request, Response $response) {
        $user = $request->getCurrentUser();
        if (!$user) {
            $response->unauthorized('Authentication required');
            return;
        }
        
        $workshopId = $request->getParam('id');
        if (!Security::isValidUUID($workshopId)) {
            $response->badRequest('Invalid workshop ID');
            return;
        }
        
        // Check if workshop exists and user has permission
        $stmt = $this->db->prepare("SELECT * FROM workshops WHERE id = ?");
        $stmt->execute([$workshopId]);
        $workshop = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$workshop) {
            $response->notFound('Workshop not found');
            return;
        }
        
        // Only allow teacher to delete their own workshops, or admin to delete any
        if ($workshop['teacher_id'] !== $user['id'] && $user['role'] !== 'ADMIN') {
            $response->forbidden('You can only delete your own workshops');
            return;
        }
        
        try {
            $stmt = $this->db->prepare("DELETE FROM workshops WHERE id = ?");
            $stmt->execute([$workshopId]);
            
            $response->success(null, 'Workshop deleted successfully');
            
        } catch (PDOException $e) {
            error_log("Error deleting workshop: " . $e->getMessage());
            $response->error('Failed to delete workshop');
        }
    }
}