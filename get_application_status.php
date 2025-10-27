<?php

error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
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


$nic = $_GET['nic'] ?? $_POST['nic'] ?? null;

if (!$nic) {
    echo json_encode([
        'success' => false,
        'error' => 'NIC is required'
    ]);
    exit;
}

try {
    
    $sql = "SELECT * FROM users WHERE nic = :nic LIMIT 1";
    $user = $db->fetch($sql, ['nic' => $nic]);
    
    if (!$user) {
        echo json_encode([
            'success' => false,
            'error' => 'User not found'
        ]);
        exit;
    }
    
    
    $sql = "SELECT * FROM applications WHERE user_id = :user_id ORDER BY progress DESC, created_at DESC LIMIT 1";
    $application = $db->fetch($sql, ['user_id' => $user['id']]);
    
    
    $payment = null;
    if ($application) {
        $sql = "SELECT * FROM payments WHERE application_id = :application_id LIMIT 1";
        $payment = $db->fetch($sql, ['application_id' => $application['id']]);
    }
    
    
    $documents = [];
    if ($application) {
        $sql = "SELECT * FROM application_documents WHERE application_id = :application_id";
        $documents = $db->fetchAll($sql, ['application_id' => $application['id']]);
    }
    
    
    $theoryTest = null;
    if ($application) {
        $sql = "SELECT * FROM theory_tests WHERE application_id = :application_id ORDER BY created_at DESC LIMIT 1";
        $theoryTest = $db->fetch($sql, ['application_id' => $application['id']]);
    }
    
    
    $practicalTest = null;
    if ($application) {
        $sql = "SELECT * FROM practical_tests WHERE application_id = :application_id ORDER BY created_at DESC LIMIT 1";
        $practicalTest = $db->fetch($sql, ['application_id' => $application['id']]);
    }
    
    
    $license = null;
    if ($application) {
        $sql = "SELECT * FROM licenses WHERE application_id = :application_id LIMIT 1";
        $license = $db->fetch($sql, ['application_id' => $application['id']]);
    }
    
    
    $response = [
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'user_id' => $user['user_id'],
            'nic' => $user['nic'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'date_of_birth' => $user['date_of_birth'],
            'gender' => $user['gender'],
            'district' => $user['district'],
            'transmission_type' => $user['transmission_type'],
            'registration_date' => $user['registration_date'],
            'created_at' => $user['created_at']
        ],
        'application' => $application ? [
            'id' => $application['id'],
            'application_id' => $application['application_id'],
            'status' => $application['status'],
            'progress' => $application['progress'],
            'submitted_date' => $application['submitted_date'],
            'verification_due_date' => $application['verification_due_date'],
            'verified_date' => $application['verified_date'],
            'rejected_date' => $application['rejected_date'],
            'rejection_reason' => $application['rejection_reason'],
            'created_at' => $application['created_at'],
            'updated_at' => $application['updated_at']
        ] : null,
        'payment' => $payment ? [
            'id' => $payment['id'],
            'payment_id' => $payment['payment_id'],
            'amount' => $payment['amount'],
            'currency' => $payment['currency'],
            'payment_method' => $payment['payment_method'],
            'payment_status' => $payment['payment_status'],
            'transaction_id' => $payment['transaction_id'],
            'paid_date' => $payment['paid_date']
        ] : null,
        'documents' => array_map(function($doc) {
            return [
                'id' => $doc['id'],
                'document_type' => $doc['document_type'],
                'file_name' => $doc['file_name'],
                'file_path' => $doc['file_path'],
                'file_size' => $doc['file_size'],
                'file_type' => $doc['file_type'],
                'status' => $doc['status'],
                'upload_date' => $doc['upload_date']
            ];
        }, $documents),
        'theory_test' => $theoryTest ? [
            'id' => $theoryTest['id'],
            'scheduled_date' => $theoryTest['scheduled_date'],
            'scheduled_time' => $theoryTest['scheduled_time'],
            'score' => $theoryTest['score'],
            'total_questions' => $theoryTest['total_questions'],
            'passed' => $theoryTest['passed'],
            'completed_at' => $theoryTest['completed_at']
        ] : null,
        'practical_test' => $practicalTest ? [
            'id' => $practicalTest['id'],
            'scheduled_date' => $practicalTest['scheduled_date'],
            'scheduled_time' => $practicalTest['scheduled_time'],
            'passed' => $practicalTest['passed'],
            'score' => $practicalTest['score']
        ] : null,
        'license' => $license ? [
            'id' => $license['id'],
            'license_number' => $license['license_number'],
            'issue_date' => $license['issue_date'],
            'expiry_date' => $license['expiry_date'],
            'status' => $license['status']
        ] : null
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
