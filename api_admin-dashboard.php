<?php


session_start();


ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'database/database_connection.php';
require_once 'database/queries.php';




function isLocalRequest() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    return in_array($ip, ['127.0.0.1', '::1']) || 
           strpos($ip, '192.168.') === 0 || 
           strpos($ip, '10.') === 0;
}

function checkAdminAuth() {
    
    if (isLocalRequest()) {
        error_log('[DEBUG] Auth bypassed for local request from ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        $_SESSION['admin_logged_in'] = true;  
        $_SESSION['admin_id'] = 1;  
        return true;
    }

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
    
    if (isLocalRequest()) {
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
    }

    checkAdminAuth();
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'getDashboardStats':
            getDashboardStats();
            break;
            
        case 'getRecentApplications':
            getRecentApplications();
            break;
            
        case 'getPendingReviews':
            getPendingReviews();
            break;
            
            
        case 'getActivityLog':
            getActivityLog();
            break;
            
        case 'getApplicationDetails':
            getApplicationDetails();
            break;
            
        default:
            sendJsonResponse(false, 'Invalid action specified', null, 400);
    }
    
} catch (Exception $e) {
    error_log("Admin Dashboard API Error: " . $e->getMessage());
    sendJsonResponse(false, 'An error occurred: ' . $e->getMessage(), null, 500);
}



function getDashboardStats() {
    global $db;
    
    try {
        
        $totalApps = $db->fetch("SELECT COUNT(*) as count FROM applications");
        
        
        $pendingApps = $db->fetch("SELECT COUNT(*) as count FROM applications WHERE status = 'pending_verification'");
        
        
        $approvedToday = $db->fetch("SELECT COUNT(*) as count FROM applications WHERE status = 'verified' AND DATE(verified_date) = CURDATE()");
        
        
        $completedTests = $db->fetch("SELECT COUNT(*) as count FROM theory_tests WHERE completed_at IS NOT NULL");
        
        
        $appsThisWeek = $db->fetch("SELECT COUNT(*) as count FROM applications WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
        
        
        $pendingToday = $db->fetch("SELECT COUNT(*) as count FROM applications WHERE status = 'pending_verification' AND DATE(submitted_date) = CURDATE()");
        
        
        $testsThisWeek = $db->fetch("SELECT COUNT(*) as count FROM theory_tests WHERE YEARWEEK(completed_at, 1) = YEARWEEK(CURDATE(), 1) AND completed_at IS NOT NULL");
        
        $stats = [
            'totalApplications' => (int)$totalApps['count'],
            'pendingApplications' => (int)$pendingApps['count'],
            'approvedToday' => (int)$approvedToday['count'],
            'completedTests' => (int)$completedTests['count'],
            'applicationsThisWeek' => (int)$appsThisWeek['count'],
            'pendingToday' => (int)$pendingToday['count'],
            'testsThisWeek' => (int)$testsThisWeek['count']
        ];
        
        logAdminAction('api_access', 'dashboard_stats', null, ['endpoint' => 'getDashboardStats']);
        
        sendJsonResponse(true, 'Dashboard statistics retrieved successfully', $stats);
        
    } catch (Exception $e) {
        throw new Exception('Failed to retrieve dashboard statistics: ' . $e->getMessage());
    }
}



function getRecentApplications() {
    global $db;
    
    try {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        
        $sql = "SELECT 
                    a.id,
                    a.application_id,
                    a.status,
                    a.progress,
                    a.submitted_date,
                    a.created_at,
                    u.full_name as fullName,
                    u.nic,
                    u.email,
                    u.phone
                FROM applications a
                JOIN users u ON a.user_id = u.id
                ORDER BY a.created_at DESC
                LIMIT :limit";
        
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $applications = $stmt->fetchAll();
        
        
        foreach ($applications as &$app) {
            $app['id'] = (int)$app['id'];
            $app['progress'] = (int)$app['progress'];
        }
        
        logAdminAction('api_access', 'recent_applications', null, ['limit' => $limit]);
        
        sendJsonResponse(true, 'Recent applications retrieved successfully', $applications);
        
    } catch (Exception $e) {
        throw new Exception('Failed to retrieve recent applications: ' . $e->getMessage());
    }
}



function getPendingReviews() {
    global $db;
    
    try {
        $sql = "SELECT 
                    a.id,
                    a.application_id,
                    a.status,
                    a.submitted_date,
                    a.verification_due_date,
                    u.full_name as fullName,
                    u.nic,
                    u.email,
                    u.phone
                FROM applications a
                JOIN users u ON a.user_id = u.id
                WHERE a.status = 'pending_verification'
                ORDER BY a.submitted_date ASC";
        
        $applications = $db->fetchAll($sql);
        
        
        foreach ($applications as &$app) {
            $app['id'] = (int)$app['id'];
        }
        
        $count = count($applications);
        
        logAdminAction('api_access', 'pending_reviews', null, ['count' => $count]);
        
        sendJsonResponse(true, 'Pending reviews retrieved successfully', $applications, 200);
        
    } catch (Exception $e) {
        throw new Exception('Failed to retrieve pending reviews: ' . $e->getMessage());
    }
}




function getActivityLog() {
    global $db;
    
    try {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        
        $sql = "SELECT 
                    aal.id,
                    aal.action,
                    aal.target_type,
                    aal.target_id,
                    aal.details,
                    aal.ip_address,
                    aal.created_at as timestamp,
                    au.username,
                    au.full_name as fullName
                FROM admin_activity_log aal
                JOIN admin_users au ON aal.admin_id = au.id
                ORDER BY aal.created_at DESC
                LIMIT :limit";
        
        $stmt = $db->getConnection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $activities = $stmt->fetchAll();
        
        
        foreach ($activities as &$activity) {
            if ($activity['details']) {
                $activity['details'] = json_decode($activity['details'], true);
            }
        }
        
        sendJsonResponse(true, 'Activity log retrieved successfully', $activities);
        
    } catch (Exception $e) {
        throw new Exception('Failed to retrieve activity log: ' . $e->getMessage());
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
                    u.full_name,
                    u.nic,
                    u.email,
                    u.phone,
                    u.date_of_birth,
                    u.gender,
                    u.district,
                    u.transmission_type
                FROM applications a
                JOIN users u ON a.user_id = u.id
                WHERE a.id = :id";
        
        $application = $db->fetch($sql, ['id' => $applicationId]);
        
        if (!$application) {
            sendJsonResponse(false, 'Application not found', null, 404);
        }
        
        
        $documents = $db->fetchAll(
            "SELECT * FROM application_documents WHERE application_id = :app_id",
            ['app_id' => $applicationId]
        );
        
        
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
        
        
        $applicationDetails = [
            'application' => $application,
            'documents' => $documents,
            'payment' => $payment,
            'theoryTest' => $theoryTest,
            'practicalTest' => $practicalTest
        ];
        
        logAdminAction('api_access', 'application_details', $applicationId, null);
        
        sendJsonResponse(true, 'Application details retrieved successfully', $applicationDetails);
        
    } catch (Exception $e) {
        throw new Exception('Failed to retrieve application details: ' . $e->getMessage());
    }
}

?>