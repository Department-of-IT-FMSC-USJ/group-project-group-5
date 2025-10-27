<?php


session_start();

ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'database/database_connection.php';
require_once 'database/queries.php';



function checkAdminAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        sendJsonResponse(false, 'Unauthorized access', null, 401);
        exit();
    }
    return true;
}



function sendJsonResponse($success, $message = '', $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    exit();
}

function logAdminAction($action, $targetType = null, $targetId = null, $details = null) {
    global $queries;
    
    if (!isset($_SESSION['admin_id'])) {
        return;
    }
    
    try {
        $queries->logAdminActivity(
            $_SESSION['admin_id'],
            $action,
            $targetType,
            $targetId,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        );
    } catch (Exception $e) {
        error_log("Failed to log admin activity: " . $e->getMessage());
    }
}



try {
    checkAdminAuth();
    
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'getAllApplications':
            getAllApplications();
            break;
        case 'getApplicationsBySection':
            getApplicationsBySection();
            break;
        case 'getApplicationDetails':
            getApplicationDetails();
            break;
        case 'reviewDocument':
            reviewDocument();
            break;
        case 'reviewApplication':
            reviewApplication();
            break;
        case 'getDocumentFile':
            getDocumentFile();
            break;
        case 'submitPracticalResult':
            submitPracticalResult();
            break;
        default:
            sendJsonResponse(false, 'Invalid action specified', null, 400);
    }
    
} catch (Exception $e) {
    error_log("Admin Review API Error: " . $e->getMessage());
    sendJsonResponse(false, 'An error occurred: ' . $e->getMessage(), null, 500);
}



function getApplicationsBySection() {
    global $db;
    
    try {
        $section = $_GET['section'] ?? '';
        $search = $_GET['search'] ?? '';
        
        if (empty($section)) {
            sendJsonResponse(false, 'Section parameter is required', null, 400);
        }
        
        $sql = "SELECT 
                    a.id,
                    a.application_id,
                    a.status,
                    a.progress,
                    a.submitted_date,
                    a.verification_due_date,
                    a.verified_date,
                    a.rejected_date,
                    a.rejection_reason,
                    a.created_at,
                    u.user_id,
                    u.full_name as fullName,
                    u.nic,
                    u.email,
                    u.phone,
                    u.date_of_birth,
                    u.gender,
                    u.district,
                    u.transmission_type,
                    (SELECT COUNT(*) FROM application_documents WHERE application_id = a.id) as document_count,
                    (SELECT COUNT(*) FROM application_documents WHERE application_id = a.id AND status = 'approved') as approved_docs,
                    (SELECT COUNT(*) FROM application_documents WHERE application_id = a.id AND status = 'rejected') as rejected_docs
                FROM applications a
                JOIN users u ON a.user_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        // Filter by section
        switch ($section) {
            case 'pending':
                $sql .= " AND a.status IN ('pending_verification', 'rejected')";
                break;
            case 'practical':
                $sql .= " AND a.status = 'practical_scheduled'";
                break;
            case 'approved':
                $sql .= " AND a.status IN ('verified', 'theory_scheduled', 'theory_passed', 'license_issued')";
                break;
            default:
                sendJsonResponse(false, 'Invalid section specified', null, 400);
        }
        
        if (!empty($search)) {
            $sql .= " AND (u.full_name LIKE :search OR u.nic LIKE :search OR u.email LIKE :search OR a.application_id LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        
        
        $sql .= " ORDER BY 
                    CASE a.status 
                        WHEN 'pending_verification' THEN 1
                        WHEN 'rejected' THEN 2
                        WHEN 'practical_scheduled' THEN 1
                        WHEN 'verified' THEN 1
                        WHEN 'theory_scheduled' THEN 2
                        WHEN 'theory_passed' THEN 3
                        WHEN 'license_issued' THEN 4
                        ELSE 9
                    END,
                    a.created_at DESC";
        
        $applications = $db->fetchAll($sql, $params);
        
        foreach ($applications as &$app) {
            $payment = $db->fetch(
                "SELECT * FROM payments WHERE application_id = :app_id ORDER BY created_at DESC LIMIT 1",
                ['app_id' => $app['id']]
            );
            $app['payment'] = $payment;
            
            // Get practical test details for practical_scheduled applications
            if ($app['status'] === 'practical_scheduled') {
                $practicalTest = $db->fetch(
                    "SELECT pt.*, tc.name as center_name, tc.address as center_address 
                     FROM practical_tests pt 
                     LEFT JOIN test_centers tc ON pt.test_center_id = tc.id 
                     WHERE pt.application_id = :app_id 
                     ORDER BY pt.created_at DESC LIMIT 1",
                    ['app_id' => $app['id']]
                );
                $app['practicalTest'] = $practicalTest;
            }
        }
        
        logAdminAction('api_access', 'applications_by_section', null, ['section' => $section, 'count' => count($applications)]);
        
        sendJsonResponse(true, 'Applications retrieved successfully', $applications);
        
    } catch (Exception $e) {
        throw new Exception('Failed to retrieve applications by section: ' . $e->getMessage());
    }
}



function getAllApplications() {
    global $db;
    
    try {
        $status = $_GET['status'] ?? '';
        $dateFilter = $_GET['dateFilter'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $sql = "SELECT 
                    a.id,
                    a.application_id,
                    a.status,
                    a.progress,
                    a.submitted_date,
                    a.verification_due_date,
                    a.verified_date,
                    a.rejected_date,
                    a.rejection_reason,
                    a.created_at,
                    u.user_id,
                    u.full_name as fullName,
                    u.nic,
                    u.email,
                    u.phone,
                    u.date_of_birth,
                    u.gender,
                    u.district,
                    u.transmission_type,
                    (SELECT COUNT(*) FROM application_documents WHERE application_id = a.id) as document_count,
                    (SELECT COUNT(*) FROM application_documents WHERE application_id = a.id AND status = 'approved') as approved_docs,
                    (SELECT COUNT(*) FROM application_documents WHERE application_id = a.id AND status = 'rejected') as rejected_docs
                FROM applications a
                JOIN users u ON a.user_id = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($status)) {
            $sql .= " AND a.status = :status";
            $params['status'] = $status;
        }
        
        if (!empty($dateFilter)) {
            switch ($dateFilter) {
                case 'today':
                    $sql .= " AND DATE(a.submitted_date) = CURDATE()";
                    break;
                case 'week':
                    $sql .= " AND a.submitted_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                    break;
                case 'month':
                    $sql .= " AND a.submitted_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                    break;
            }
        }
        
        if (!empty($search)) {
            $sql .= " AND (u.full_name LIKE :search OR u.nic LIKE :search OR u.email LIKE :search OR a.application_id LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }
        
        $sql .= " ORDER BY 
                    CASE a.status 
                        WHEN 'pending_verification' THEN 1
                        WHEN 'rejected' THEN 2
                        WHEN 'verified' THEN 3
                        WHEN 'theory_scheduled' THEN 4
                        WHEN 'theory_passed' THEN 5
                        WHEN 'theory_failed' THEN 6
                        WHEN 'practical_scheduled' THEN 7
                        WHEN 'license_issued' THEN 8
                        ELSE 9
                    END,
                    a.created_at DESC";
        
        $applications = $db->fetchAll($sql, $params);
        
        foreach ($applications as &$app) {
            $payment = $db->fetch(
                "SELECT * FROM payments WHERE application_id = :app_id ORDER BY created_at DESC LIMIT 1",
                ['app_id' => $app['id']]
            );
            $app['payment'] = $payment;
        }
        
        logAdminAction('api_access', 'applications_list', null, ['count' => count($applications)]);
        
        sendJsonResponse(true, 'Applications retrieved successfully', $applications);
        
    } catch (Exception $e) {
        throw new Exception('Failed to retrieve applications: ' . $e->getMessage());
    }
}



function getApplicationDetails() {
    global $db;
    
    try {
        $applicationId = $_GET['id'] ?? null;
        
        if (!$applicationId) {
            sendJsonResponse(false, 'Application ID is required', null, 400);
        }
        
        $sql = "SELECT 
                    a.*,
                    u.user_id,
                    u.full_name,
                    u.nic,
                    u.email,
                    u.phone,
                    u.date_of_birth,
                    u.gender,
                    u.district,
                    u.transmission_type,
                    u.registration_date
                FROM applications a
                JOIN users u ON a.user_id = u.id
                WHERE a.id = :id";
        
        $application = $db->fetch($sql, ['id' => $applicationId]);
        
        if (!$application) {
            sendJsonResponse(false, 'Application not found', null, 404);
        }
        
        $documents = $db->fetchAll(
            "SELECT 
                id,
                document_type,
                file_name,
                file_path,
                file_size,
                file_type,
                upload_date,
                status,
                rejection_reason,
                reviewed_by,
                reviewed_at
            FROM application_documents 
            WHERE application_id = :app_id
            ORDER BY upload_date ASC",
            ['app_id' => $applicationId]
        );
        
        $documentsByType = [];
        foreach ($documents as $doc) {
            $documentsByType[$doc['document_type']] = $doc;
        }
        
        $payment = $db->fetch(
            "SELECT * FROM payments WHERE application_id = :app_id ORDER BY created_at DESC LIMIT 1",
            ['app_id' => $applicationId]
        );
        
        $theoryTest = $db->fetch(
            "SELECT * FROM theory_tests WHERE application_id = :app_id ORDER BY created_at DESC LIMIT 1",
            ['app_id' => $applicationId]
        );
        
        $practicalTest = $db->fetch(
            "SELECT pt.*, tc.name as center_name, tc.address as center_address 
             FROM practical_tests pt 
             LEFT JOIN test_centers tc ON pt.test_center_id = tc.id 
             WHERE pt.application_id = :app_id 
             ORDER BY pt.created_at DESC LIMIT 1",
            ['app_id' => $applicationId]
        );
        
        $reviewHistory = $db->fetchAll(
            "SELECT aal.*, au.full_name as admin_name
             FROM admin_activity_log aal
             JOIN admin_users au ON aal.admin_id = au.id
             WHERE aal.target_type = 'application' AND aal.target_id = :app_id
             ORDER BY aal.created_at DESC",
            ['app_id' => $applicationId]
        );
        
        $applicationDetails = [
            'application' => $application,
            'documents' => $documentsByType,
            'payment' => $payment,
            'theoryTest' => $theoryTest,
            'practicalTest' => $practicalTest,
            'reviewHistory' => $reviewHistory
        ];
        
        logAdminAction('view_application', 'application', $applicationId, null);
        
        sendJsonResponse(true, 'Application details retrieved successfully', $applicationDetails);
        
    } catch (Exception $e) {
        throw new Exception('Failed to retrieve application details: ' . $e->getMessage());
    }
}



function reviewDocument() {
    global $db;
    
    try {
        
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendJsonResponse(false, 'Invalid JSON data', null, 400);
        }
        
        $documentId = $data['documentId'] ?? null;
        $status = $data['status'] ?? null;
        $rejectionReason = $data['rejectionReason'] ?? null;
        
        if (!$documentId || !$status) {
            sendJsonResponse(false, 'Document ID and status are required', null, 400);
        }
        
        if (!in_array($status, ['approved', 'rejected'])) {
            sendJsonResponse(false, 'Invalid status. Must be approved or rejected', null, 400);
        }
        
        if ($status === 'rejected' && empty($rejectionReason)) {
            sendJsonResponse(false, 'Rejection reason is required when rejecting a document', null, 400);
        }
        
        
        $document = $db->fetch(
            "SELECT * FROM application_documents WHERE id = :id",
            ['id' => $documentId]
        );
        
        if (!$document) {
            sendJsonResponse(false, 'Document not found', null, 404);
        }
        
        
        $updateSql = "UPDATE application_documents 
                      SET status = :status, 
                          rejection_reason = :rejection_reason,
                          reviewed_by = :reviewed_by,
                          reviewed_at = NOW()
                      WHERE id = :id";
        
        $updated = $db->query($updateSql, [
            'id' => $documentId,
            'status' => $status,
            'rejection_reason' => $rejectionReason,
            'reviewed_by' => $_SESSION['admin_id']
        ]);
        
        if (!$updated) {
            throw new Exception('Failed to update document status in database');
        }
        
        
        logAdminAction(
            'review_document_' . $status,
            'document',
            $documentId,
            [
                'document_type' => $document['document_type'],
                'application_id' => $document['application_id'],
                'rejection_reason' => $rejectionReason
            ]
        );
        
        
        $allDocs = $db->fetchAll(
            "SELECT status FROM application_documents WHERE application_id = :app_id",
            ['app_id' => $document['application_id']]
        );
        
        $allReviewed = true;
        $anyRejected = false;
        
        foreach ($allDocs as $doc) {
            if ($doc['status'] === 'pending') {
                $allReviewed = false;
            }
            if ($doc['status'] === 'rejected') {
                $anyRejected = true;
            }
        }
        
        sendJsonResponse(true, 'Document status updated to ' . $status, [
            'documentId' => $documentId,
            'status' => $status,
            'allDocumentsReviewed' => $allReviewed,
            'anyDocumentRejected' => $anyRejected
        ]);
        
    } catch (Exception $e) {
        error_log("Review Document Error: " . $e->getMessage());
        throw new Exception('Failed to review document: ' . $e->getMessage());
    }
}


function reviewApplication() {
    global $db;
    
    try {
        
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendJsonResponse(false, 'Invalid JSON data', null, 400);
        }
        
        $applicationId = $data['applicationId'] ?? null;
        $decision = $data['decision'] ?? null;
        $comments = $data['comments'] ?? '';
        $rejectionReasons = $data['rejectionReasons'] ?? [];
        
        if (!$applicationId || !$decision) {
            sendJsonResponse(false, 'Application ID and decision are required', null, 400);
        }
        
        if (!in_array($decision, ['approve', 'reject'])) {
            sendJsonResponse(false, 'Invalid decision. Must be approve or reject', null, 400);
        }
        
        
        $application = $db->fetch(
            "SELECT * FROM applications WHERE id = :id",
            ['id' => $applicationId]
        );
        
        if (!$application) {
            sendJsonResponse(false, 'Application not found', null, 404);
        }
        
        
        $pendingDocs = $db->fetch(
            "SELECT COUNT(*) as count FROM application_documents 
             WHERE application_id = :app_id AND status = 'pending'",
            ['app_id' => $applicationId]
        );
        
        if ($pendingDocs['count'] > 0 && $decision === 'approve') {
            sendJsonResponse(false, 'All documents must be reviewed before approving', null, 400);
        }
        
        
        $db->beginTransaction();
        
        try {
            if ($decision === 'approve') {
                
                $updateSql = "UPDATE applications 
                             SET status = 'verified',
                                 verified_date = NOW(),
                                 progress = 50,
                                 updated_at = NOW()
                             WHERE id = :id";
                
                $updated = $db->query($updateSql, ['id' => $applicationId]);
                
                if (!$updated) {
                    throw new Exception('Failed to update application status to verified');
                }
                
                
                $db->query(
                    "INSERT INTO notifications (user_id, type, title, message, status, created_at)
                     VALUES (:user_id, 'system', 'Application Approved ✓', 
                             'Congratulations! Your application has been verified and approved. You can now proceed with scheduling your theory test.', 
                             'pending', NOW())",
                    ['user_id' => $application['user_id']]
                );
                
                $message = 'Application approved and status updated to VERIFIED';
                $newStatus = 'verified';
                
            } else {
                
                $rejectionText = !empty($rejectionReasons) 
                    ? implode(', ', $rejectionReasons) 
                    : 'Application rejected by admin';
                
                if (!empty($comments)) {
                    $rejectionText .= '. Additional comments: ' . $comments;
                }
                
                $updateSql = "UPDATE applications 
                             SET status = 'rejected',
                                 rejected_date = NOW(),
                                 rejection_reason = :reason,
                                 updated_at = NOW()
                             WHERE id = :id";
                
                $updated = $db->query($updateSql, [
                    'id' => $applicationId, 
                    'reason' => $rejectionText
                ]);
                
                if (!$updated) {
                    throw new Exception('Failed to update application status to not_verified');
                }
                
                
                $db->query(
                    "INSERT INTO notifications (user_id, type, title, message, status, created_at)
                     VALUES (:user_id, 'system', 'Application Rejected ✗', 
                             :message, 
                             'pending', NOW())",
                    [
                        'user_id' => $application['user_id'],
                        'message' => 'Unfortunately, your application has been rejected. Reason: ' . $rejectionText . '. Please review the rejection reasons and resubmit your application with the required corrections.'
                    ]
                );
                
                $message = 'Application rejected and status updated to NOT_VERIFIED';
                $newStatus = 'not_verified';
            }
            
            
            logAdminAction(
                'review_application_' . $decision,
                'application',
                $applicationId,
                [
                    'decision' => $decision,
                    'comments' => $comments,
                    'rejection_reasons' => $rejectionReasons,
                    'new_status' => $newStatus
                ]
            );
            
            $db->commit();
            
            sendJsonResponse(true, $message, [
                'applicationId' => $applicationId,
                'decision' => $decision,
                'newStatus' => $newStatus
            ]);
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log("Review Application Error: " . $e->getMessage());
        throw new Exception('Failed to review application: ' . $e->getMessage());
    }
}



function getDocumentFile() {
    global $db;
    
    try {
        $documentId = $_GET['id'] ?? null;
        $download = isset($_GET['download']) && $_GET['download'] === 'true';
        
        if (!$documentId || !is_numeric($documentId)) {
            sendJsonResponse(false, 'Invalid document ID', null, 400);
        }
        
        
        $document = $db->fetch(
            "SELECT d.*, a.user_id 
             FROM application_documents d
             JOIN applications a ON d.application_id = a.id
             WHERE d.id = :id",
            ['id' => $documentId]
        );
        
        if (!$document) {
            sendJsonResponse(false, 'Document not found', null, 404);
        }
        
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . $document['file_path'];
        
        if (!file_exists($filePath)) {
            error_log("File not found: " . $filePath);
            sendJsonResponse(false, 'Document file not found on server', null, 404);
        }
        
        if (!is_readable($filePath)) {
            error_log("File not readable: " . $filePath);
            sendJsonResponse(false, 'Document file is not accessible', null, 403);
        }
        
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';
        $fileSize = filesize($filePath);
        $fileName = $document['file_name'] ?: basename($filePath);
        
        
        if ($download) {
            logAdminAction('download_document', 'document', $documentId, [
                'document_type' => $document['document_type'],
                'file_name' => $document['file_name']
            ]);
            
            while (ob_get_level()) ob_end_clean();
            
            header('Content-Type: ' . $mimeType);
            header('Content-Length: ' . $fileSize);
            header('Content-Disposition: inline; filename="' . $fileName . '"');
            header('Cache-Control: public, max-age=0');
            
            readfile($filePath);
            exit;
        }
        
        logAdminAction('view_document', 'document', $documentId, [
            'document_type' => $document['document_type'],
            'file_name' => $document['file_name']
        ]);
        
        
        sendJsonResponse(true, 'Document file retrieved', [
            'id' => $document['id'],
            'fileName' => $fileName,
            'originalName' => $document['file_name'],
            'fileType' => $mimeType,
            'fileSize' => $fileSize,
            'downloadUrl' => $_SERVER['PHP_SELF'] . '?action=getDocumentFile&id=' . $documentId . '&download=true',
            'documentType' => $document['document_type'],
            'uploadDate' => $document['upload_date'],
            'status' => $document['status']
        ]);
        
    } catch (Exception $e) {
        error_log("Get Document File Error: " . $e->getMessage());
        throw new Exception('Failed to retrieve document file: ' . $e->getMessage());
    }
}



function submitPracticalResult() {
    global $db;
    
    try {
        
        $rawInput = file_get_contents('php://input');
        $data = json_decode($rawInput, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendJsonResponse(false, 'Invalid JSON data', null, 400);
        }
        
        $applicationId = $data['applicationId'] ?? null;
        $result = $data['result'] ?? null;
        $comments = $data['comments'] ?? '';
        
        if (!$applicationId || !$result) {
            sendJsonResponse(false, 'Application ID and result are required', null, 400);
        }
        
        if (!in_array($result, ['passed', 'failed'])) {
            sendJsonResponse(false, 'Invalid result. Must be passed or failed', null, 400);
        }
        
        
        $application = $db->fetch(
            "SELECT * FROM applications WHERE id = :id",
            ['id' => $applicationId]
        );
        
        if (!$application) {
            sendJsonResponse(false, 'Application not found', null, 404);
        }
        
        if ($application['status'] !== 'practical_scheduled') {
            sendJsonResponse(false, 'Application is not in practical_scheduled status', null, 400);
        }
        
        
        $db->beginTransaction();
        
        try {
            if ($result === 'passed') {
                
                $updateSql = "UPDATE applications 
                             SET status = 'license_issued',
                                 progress = 100,
                                 updated_at = NOW()
                             WHERE id = :id";
                
                $updated = $db->query($updateSql, ['id' => $applicationId]);
                
                if (!$updated) {
                    throw new Exception('Failed to update application status to license_issued');
                }
                
                
                $db->query(
                    "UPDATE practical_tests 
                     SET passed = 1, 
                         score = 100,
                         feedback = :feedback,
                         completed_at = NOW()
                     WHERE application_id = :app_id 
                     ORDER BY created_at DESC LIMIT 1",
                    [
                        'app_id' => $applicationId,
                        'feedback' => $comments
                    ]
                );
                
                
                $db->query(
                    "INSERT INTO notifications (user_id, type, title, message, status, created_at)
                     VALUES (:user_id, 'system', 'License Issued! 🎉', 
                             'Congratulations! You have successfully passed your practical exam and your driving license has been issued. You can now collect your license from the licensing office.', 
                             'pending', NOW())",
                    ['user_id' => $application['user_id']]
                );
                
                $message = 'Practical exam passed! Application status updated to LICENSE_ISSUED';
                $newStatus = 'license_issued';
                
            } else {
                
                $db->query(
                    "UPDATE practical_tests 
                     SET passed = 0, 
                         score = 0,
                         feedback = :feedback,
                         completed_at = NOW()
                     WHERE application_id = :app_id 
                     ORDER BY created_at DESC LIMIT 1",
                    [
                        'app_id' => $applicationId,
                        'feedback' => $comments
                    ]
                );
                
                
                $db->query(
                    "INSERT INTO notifications (user_id, type, title, message, status, created_at)
                     VALUES (:user_id, 'system', 'Practical Exam Failed', 
                             'Unfortunately, you did not pass your practical exam. Please review the feedback and schedule a retest when you are ready. Feedback: ' . :feedback, 
                             'pending', NOW())",
                    [
                        'user_id' => $application['user_id'],
                        'feedback' => $comments
                    ]
                );
                
                $message = 'Practical exam failed. Application status remains as PRACTICAL_SCHEDULED';
                $newStatus = 'practical_scheduled';
            }
            
            
            logAdminAction(
                'practical_exam_' . $result,
                'application',
                $applicationId,
                [
                    'result' => $result,
                    'comments' => $comments,
                    'new_status' => $newStatus
                ]
            );
            
            $db->commit();
            
            sendJsonResponse(true, $message, [
                'applicationId' => $applicationId,
                'result' => $result,
                'newStatus' => $newStatus
            ]);
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
        
    } catch (Exception $e) {
        error_log("Submit Practical Result Error: " . $e->getMessage());
        throw new Exception('Failed to submit practical exam result: ' . $e->getMessage());
    }
}
?>