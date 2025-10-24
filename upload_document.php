<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');


require_once 'database/database_connection.php';


$uploadDir = 'uploads/documents/';
$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
$maxFileSize = 5 * 1024 * 1024; 


if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}


function generateUniqueFilename($originalName, $uploadDir) {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    return $uploadDir . $filename;
}

function validateFile($file) {
    global $allowedTypes, $maxFileSize;
    
    $errors = [];
    
   
    if ($file['size'] > $maxFileSize) {
        $errors[] = 'File size exceeds 5MB limit';
    }
    
  
    if (!in_array($file['type'], $allowedTypes)) {
        $errors[] = 'Invalid file type. Only JPG, PNG, and PDF files are allowed';
    }
    
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'File upload error: ' . $file['error'];
    }
    
    return $errors;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
        if (!isset($_FILES['document']) || $_FILES['document']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new Exception('No file uploaded');
        }
        
        $file = $_FILES['document'];
        $documentType = $_POST['documentType'] ?? '';
        $applicationId = $_POST['applicationId'] ?? '';
        
       
        if (empty($documentType)) {
            throw new Exception('Document type is required');
        }
        
        if (empty($applicationId)) {
            throw new Exception('Application ID is required');
        }
        
        
        $docTypeMapping = [
            'birthCertificate' => 'birth_certificate',
            'nicCopy' => 'nic_copy',
            'medicalCertificate' => 'medical_certificate',
            'photo' => 'photo'
        ];
        
        $dbDocType = $docTypeMapping[$documentType] ?? $documentType;
        
        
        $errors = validateFile($file);
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }
        
        
        $filename = generateUniqueFilename($file['name'], $uploadDir);
        
        
        if (!move_uploaded_file($file['tmp_name'], $filename)) {
            throw new Exception('Failed to save file');
        }
        
       
        $fileInfo = [
            'fileName' => basename($filename),
            'filePath' => $filename,
            'fileSize' => $file['size'],
            'fileType' => $file['type'],
            'originalName' => $file['name']
        ];
        
        
        $db->beginTransaction();
        
        try {
            
            
            $isTempId = strpos($applicationId, 'TEMP_') === 0;
            
            $documentData = [
                'document_type' => $dbDocType,
                'file_name' => $fileInfo['fileName'],
                'file_path' => $fileInfo['filePath'],
                'file_size' => $fileInfo['fileSize'],
                'file_type' => $fileInfo['fileType'],
                'status' => 'pending'
            ];
            
            if ($isTempId) {
               
                $documentData['temp_application_id'] = $applicationId;
                $documentData['application_id'] = null;
            } else {
                
                $documentData['application_id'] = intval($applicationId);
                $documentData['temp_application_id'] = null;
            }
            
            $documentId = $db->insert('application_documents', $documentData);
            
            $db->commit();
            
            echo json_encode([
                'success' => true,
                'documentId' => $documentId,
                'fileInfo' => $fileInfo,
                'message' => 'Document uploaded successfully'
            ]);
            
        } catch (Exception $e) {
            $db->rollback();
            
            
            if (file_exists($filename)) {
                unlink($filename);
            }
            
            throw $e;
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
