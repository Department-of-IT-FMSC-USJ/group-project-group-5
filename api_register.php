<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');


require_once 'database/database_connection.php';


try {
    $db = new Database();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }
        
        
        $requiredFields = ['fullName', 'nic', 'email', 'phone', 'password'];
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        
        $sql = "SELECT id FROM users WHERE nic = :nic LIMIT 1";
        $existingUser = $db->fetch($sql, ['nic' => $input['nic']]);
        
        if ($existingUser) {
            throw new Exception('An account with this NIC already exists');
        }
        
        
        $sql = "SELECT id FROM users WHERE email = :email LIMIT 1";
        $existingEmail = $db->fetch($sql, ['email' => $input['email']]);
        
        if ($existingEmail) {
            throw new Exception('An account with this email already exists');
        }
        
        
        $userData = [
            'user_id' => 'USER' . time(),
            'nic' => $input['nic'],
            'password_hash' => password_hash($input['password'], PASSWORD_DEFAULT),
            'email' => $input['email'],
            'phone' => $input['phone'],
            'full_name' => $input['fullName'],
            'date_of_birth' => '2000-01-01',
            'gender' => 'Other', 
            'district' => 'Colombo', 
            'transmission_type' => 'Manual', 
            'registration_date' => date('Y-m-d H:i:s'),
            'is_active' => 1,
            'email_verified' => 0,
            'phone_verified' => 0
        ];
        
        
        $userId = $db->insert('users', $userData);
        
        if ($userId) {
            echo json_encode([
                'success' => true,
                'message' => 'Account created successfully',
                'userId' => $userId,
                'user' => [
                    'userId' => $userData['user_id'],
                    'fullName' => $input['fullName'],
                    'nic' => $input['nic'],
                    'email' => $input['email'],
                    'phone' => $input['phone'],
                    'registeredDate' => $userData['registration_date']
                ]
            ]);
        } else {
            throw new Exception('Failed to create account');
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}
?>
