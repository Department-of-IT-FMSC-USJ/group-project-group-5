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


function generateApplicationId() {
    return 'APP' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}


function getUserIdByNIC($db, $nic) {
    $sql = "SELECT id FROM users WHERE nic = :nic";
    $result = $db->fetch($sql, ['nic' => $nic]);
    return $result ? $result['id'] : null;
}


function createUser($db, $userData) {
    $userData['user_id'] = 'USER' . time();
    $userData['password_hash'] = password_hash('default123', PASSWORD_DEFAULT);
    $userData['email'] = $userData['nic'] . '@licensexpress.com';
    $userData['phone'] = '0770000000';
    $userData['registration_date'] = date('Y-m-d H:i:s');
    
    return $db->insert('users', $userData);
}


function submitApplication($db, $applicationData) {
    try {
        $db->beginTransaction();
        
        
        $userId = getUserIdByNIC($db, $applicationData['nic']);
        if (!$userId) {
            $userData = [
                'nic' => $applicationData['nic'],
                'full_name' => $applicationData['fullName'],
                'date_of_birth' => $applicationData['dateOfBirth'],
                'gender' => $applicationData['gender'],
                'district' => $applicationData['district'],
                'transmission_type' => $applicationData['transmissionType']
            ];
            $userId = createUser($db, $userData);
        }
        
        
        $applicationId = generateApplicationId();
        
        
        $progress = 14; 
        
        
        $appData = [
            'application_id' => $applicationId,
            'user_id' => $userId,
            'status' => 'pending_verification',
            'progress' => $progress,
            'submitted_date' => date('Y-m-d H:i:s'),
            'verification_due_date' => date('Y-m-d H:i:s', strtotime('+2 days'))
        ];
        
        $appId = $db->insert('applications', $appData);
        error_log("Inserted application with ID: $appId at stage: pending_verification with progress: $progress%");
        
        
        $paymentData = [
            'payment_id' => 'PAY' . time(),
            'application_id' => $appId,
            'amount' => 3200.00,
            'currency' => 'LKR',
            'payment_method' => 'credit_card',
            'payment_status' => 'completed',
            'transaction_id' => 'TXN' . time(),
            'paid_date' => date('Y-m-d H:i:s')
        ];
        
        $db->insert('payments', $paymentData);
        
        
        error_log("Documents data: " . json_encode($applicationData['documents'] ?? 'No documents'));
        
        
        if (isset($applicationData['documents']) && is_array($applicationData['documents'])) {
            error_log("Processing " . count($applicationData['documents']) . " documents");
            
            $uploadDir = 'uploads/documents/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            foreach ($applicationData['documents'] as $docType => $docData) {
                error_log("Processing document type: $docType");
                
                
                $docTypeMapping = [
                    'birthCertificate' => 'birth_certificate',
                    'nicCopy' => 'nic_copy',
                    'medicalCertificate' => 'medical_certificate',
                    'photo' => 'photo'
                ];
                
                $dbDocType = $docTypeMapping[$docType] ?? $docType;
                
            
                if (!empty($docData['uploaded']) && !empty($docData['fileData'])) {
                    
                    $fileData = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $docData['fileData']));
                    $fileExtension = pathinfo($docData['fileName'], PATHINFO_EXTENSION);
                    $uniqueFileName = uniqid() . '_' . time() . '.' . $fileExtension;
                    $filePath = $uploadDir . $uniqueFileName;
                    
                    if (file_put_contents($filePath, $fileData)) {
                        $docRecord = [
                            'application_id' => $appId,
                            'document_type' => $dbDocType,
                            'file_name' => $uniqueFileName,
                            'file_path' => $filePath,
                            'file_size' => strlen($fileData),
                            'file_type' => $docData['fileType'] ?? 'application/octet-stream',
                            'status' => 'pending'
                        ];
                        $documentId = $db->insert('application_documents', $docRecord);
                        error_log("Inserted document with ID: $documentId for type: $dbDocType");
                    } else {
                        error_log("Failed to save file for document type: $docType");
                    }
                }
            }
        }
        
        $db->commit();
        
        return [
            'success' => true,
            'applicationId' => $applicationId,
            'applicationDbId' => $appId,
            'userId' => $userId,
            'message' => 'Application submitted successfully'
        ];
        
    } catch (Exception $e) {
        $db->rollback();
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            throw new Exception('Invalid JSON input');
        }
        
        
        $requiredFields = ['fullName', 'nic', 'dateOfBirth', 'gender', 'district', 'transmissionType'];
        foreach ($requiredFields as $field) {
            if (empty($input[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }
        
        
        $result = submitApplication($db, $input);
        
        echo json_encode($result);
        
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
