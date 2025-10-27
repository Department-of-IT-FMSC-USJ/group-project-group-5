<?php
session_start();
require_once 'database/database_connection.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $db = new Database();
    
    
    $protectedActions = ['book_theory_test', 'book_practical_test', 'reschedule_practical_test'];
    if (in_array($_GET['action'] ?? '', $protectedActions)) {
        if (!isset($_SESSION['user_id'])) {
            sendJsonResponse(false, 'User not logged in', null, 401);
        }
    }
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get_time_slots':
            getTimeSlots($db, $_GET);
            break;
            
        case 'get_test_centers':
            getTestCenters($db);
            break;
            
        case 'book_theory_test':
            bookTheoryTest($db);
            break;
            
        case 'book_practical_test':
            bookPracticalTest($db);
            break;
            
        case 'reschedule_practical_test':
            reschedulePracticalTest($db);
            break;
            
        case 'check_availability':
            checkAvailability($db, $_GET);
            break;
            
        default:
            sendJsonResponse(false, 'Invalid action', null, 400);
    }
    
} catch (Exception $e) {
    error_log("Booking API Error: " . $e->getMessage());
    sendJsonResponse(false, 'Internal server error: ' . $e->getMessage(), null, 500);
}

function getTimeSlots($db, $params) {
    $slotType = $params['type'] ?? 'theory';
    $date = $params['date'] ?? date('Y-m-d');
    $testCenterId = $params['test_center_id'] ?? null;
    
    
    $allTimeSlots = [
        '09:00:00' => ['icon' => '🌅', 'max_capacity' => 5],
        '10:00:00' => ['icon' => '☀️', 'max_capacity' => 5],
        '11:00:00' => ['icon' => '⏰', 'max_capacity' => 5],
        '12:00:00' => ['icon' => '🕛', 'max_capacity' => 5],
        '13:00:00' => ['icon' => '🌤️', 'max_capacity' => 5],
        '14:00:00' => ['icon' => '☀️', 'max_capacity' => 5],
        '15:00:00' => ['icon' => '🌤️', 'max_capacity' => 5],
        '16:00:00' => ['icon' => '🌆', 'max_capacity' => 5]
    ];
    
   
    $sql = "SELECT scheduled_time, COUNT(*) as booked_count
            FROM practical_tests 
            WHERE scheduled_date = :date";
    
    $queryParams = ['date' => $date];
    
    if ($testCenterId && $slotType === 'practical') {
        $sql .= " AND test_center_id = :test_center_id";
        $queryParams['test_center_id'] = $testCenterId;
    }
    
    $sql .= " GROUP BY scheduled_time";
    
    $bookedSlots = $db->fetchAll($sql, $queryParams);
    
    
    $bookedMap = [];
    foreach ($bookedSlots as $slot) {
        $bookedMap[$slot['scheduled_time']] = $slot['booked_count'];
    }
    
    
    $availableSlots = [];
    foreach ($allTimeSlots as $time => $slotInfo) {
        $bookedCount = isset($bookedMap[$time]) ? $bookedMap[$time] : 0;
        $available = $bookedCount < $slotInfo['max_capacity'];
        
        $availableSlots[] = [
            'time' => date('g:i A', strtotime($time)),
            'time_24h' => $time,
            'available' => $available,
            'booked_count' => $bookedCount,
            'max_capacity' => $slotInfo['max_capacity'],
            'icon' => $slotInfo['icon']
        ];
    }
    
    sendJsonResponse(true, 'Time slots retrieved', $availableSlots);
}

function bookTheoryTest($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(false, 'Invalid JSON input', null, 400);
        return;
    }
    
    $requiredFields = ['application_id', 'scheduled_date', 'scheduled_time'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field])) {
            sendJsonResponse(false, "Missing required field: $field", null, 400);
            return;
        }
    }
    
    $db->beginTransaction();
    
    try {
        
        $sql = "SELECT id FROM applications WHERE application_id = :application_id";
        $app = $db->fetch($sql, ['application_id' => $input['application_id']]);
        
        if (!$app) {
            throw new Exception('Application not found');
        }
        
        
        $testData = [
            'application_id' => $app['id'],
            'scheduled_date' => $input['scheduled_date'],
            'scheduled_time' => $input['scheduled_time'],
            'test_link' => generateTestLink($input['application_id'])
        ];
        
        $sql = "INSERT INTO theory_tests (application_id, scheduled_date, scheduled_time, test_link) 
                VALUES (:application_id, :scheduled_date, :scheduled_time, :test_link)";
        $testId = $db->query($sql, $testData);
        
        
        $sql = "UPDATE applications SET status = 'theory_scheduled', progress = 60, updated_at = NOW() 
                WHERE application_id = :application_id";
        $db->query($sql, ['application_id' => $input['application_id']]);
        
        $db->commit();
        
        sendJsonResponse(true, 'Theory test booked successfully', [
            'test_id' => $testId,
            'scheduled_date' => $input['scheduled_date'],
            'scheduled_time' => $input['scheduled_time'],
            'test_link' => $testData['test_link']
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        sendJsonResponse(false, $e->getMessage(), null, 400);
    }
}

function bookPracticalTest($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        sendJsonResponse(false, 'Invalid JSON input', null, 400);
        return;
    }
    
    $requiredFields = ['application_id', 'test_center_id', 'scheduled_date', 'scheduled_time', 'vehicle_type'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field])) {
            sendJsonResponse(false, "Missing required field: $field", null, 400);
            return;
        }
    }
    
    $db->beginTransaction();
    
    try {
        
        $sql = "SELECT id FROM applications WHERE application_id = :application_id";
        $app = $db->fetch($sql, ['application_id' => $input['application_id']]);
        
        if (!$app) {
            throw new Exception('Application not found');
        }
        
        
        $sql = "SELECT id FROM practical_tests WHERE application_id = :application_id";
        $existingTest = $db->fetch($sql, ['application_id' => $app['id']]);
        
        if ($existingTest) {
            throw new Exception('Practical test already scheduled. Please use reschedule option.');
        }
        
        
        $testData = [
            'application_id' => $app['id'],
            'test_center_id' => $input['test_center_id'],
            'scheduled_date' => $input['scheduled_date'],
            'scheduled_time' => $input['scheduled_time'],
            'examiner_id' => $input['examiner_id'] ?? null,
            'vehicle_type' => $input['vehicle_type'],
            'vehicle_details' => json_encode($input['vehicle_details'] ?? [])
        ];
        
        $sql = "INSERT INTO practical_tests (application_id, test_center_id, scheduled_date, scheduled_time, examiner_id, vehicle_type, vehicle_details) 
                VALUES (:application_id, :test_center_id, :scheduled_date, :scheduled_time, :examiner_id, :vehicle_type, :vehicle_details)";
        $testId = $db->query($sql, $testData);
        
        
        $sql = "UPDATE applications SET status = 'practical_scheduled', progress = 85, updated_at = NOW() 
                WHERE application_id = :application_id";
        $db->query($sql, ['application_id' => $input['application_id']]);
        
        
        $notificationData = [
            'user_id' => $app['id'],
            'admin_id' => null,
            'type' => 'system',
            'title' => 'Practical Test Scheduled',
            'message' => 'Your practical driving test has been scheduled for ' . date('F j, Y', strtotime($input['scheduled_date'])) . ' at ' . date('g:i A', strtotime($input['scheduled_time'])),
            'status' => 'pending'
        ];
        
        $sql = "INSERT INTO notifications (user_id, admin_id, type, title, message, status) 
                VALUES (:user_id, :admin_id, :type, :title, :message, :status)";
        $db->query($sql, $notificationData);
        
        $db->commit();
        
        sendJsonResponse(true, 'Practical test booked successfully', [
            'test_id' => $testId,
            'scheduled_date' => $input['scheduled_date'],
            'scheduled_time' => $input['scheduled_time'],
            'test_center_id' => $input['test_center_id']
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        sendJsonResponse(false, $e->getMessage(), null, 400);
    }
}

function reschedulePracticalTest($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    
    error_log("Reschedule API Debug - Input received: " . json_encode($input));
    
    if (!$input) {
        sendJsonResponse(false, 'Invalid JSON input', null, 400);
        return;
    }
    
    $requiredFields = ['application_id', 'test_center_id', 'scheduled_date', 'scheduled_time'];
    foreach ($requiredFields as $field) {
        if (!isset($input[$field])) {
            sendJsonResponse(false, "Missing required field: $field", null, 400);
            return;
        }
    }
    
    $db->beginTransaction();
    
    try {
        
        $sql = "SELECT id FROM applications WHERE application_id = :application_id";
        error_log("Reschedule API Debug - Looking for application_id: " . $input['application_id']);
        $app = $db->fetch($sql, ['application_id' => $input['application_id']]);
        
        error_log("Reschedule API Debug - Application found: " . json_encode($app));
        
        if (!$app) {
            throw new Exception('Application not found');
        }
        
        
        $sql = "SELECT id, reschedule_count FROM practical_tests WHERE application_id = :application_id";
        $existingTest = $db->fetch($sql, ['application_id' => $app['id']]);
        
        if ($existingTest) {
            
            if ($existingTest['reschedule_count'] >= 1) {
                throw new Exception('You have already rescheduled your practical test once. No further reschedules are allowed.');
            }
            
            $sql = "UPDATE practical_tests 
                    SET test_center_id = :test_center_id, 
                        scheduled_date = :scheduled_date, 
                        scheduled_time = :scheduled_time,
                        reschedule_count = reschedule_count + 1,
                        updated_at = NOW()
                    WHERE application_id = :application_id";
            
            $db->query($sql, [
                'application_id' => $app['id'],
                'test_center_id' => $input['test_center_id'],
                'scheduled_date' => $input['scheduled_date'],
                'scheduled_time' => $input['scheduled_time']
            ]);
            
            $testId = $existingTest['id'];
        } else {
            
            $testData = [
                'application_id' => $app['id'],
                'test_center_id' => $input['test_center_id'],
                'scheduled_date' => $input['scheduled_date'],
                'scheduled_time' => $input['scheduled_time'],
                'examiner_id' => null,
                'vehicle_type' => 'own',
                'vehicle_details' => json_encode([])
            ];
            
            $sql = "INSERT INTO practical_tests (application_id, test_center_id, scheduled_date, scheduled_time, examiner_id, vehicle_type, vehicle_details) 
                    VALUES (:application_id, :test_center_id, :scheduled_date, :scheduled_time, :examiner_id, :vehicle_type, :vehicle_details)";
            $testId = $db->query($sql, $testData);
        }
        
        
        $sql = "UPDATE applications 
                SET status = 'practical_scheduled', 
                    progress = 85, 
                    updated_at = NOW() 
                WHERE application_id = :application_id AND status != 'practical_scheduled'";
        $db->query($sql, ['application_id' => $input['application_id']]);
        
        
        $notificationData = [
            'user_id' => $app['id'],
            'admin_id' => null,
            'type' => 'system',
            'title' => 'Practical Test Rescheduled',
            'message' => 'Your practical driving test has been rescheduled to ' . date('F j, Y', strtotime($input['scheduled_date'])) . ' at ' . date('g:i A', strtotime($input['scheduled_time'])),
            'status' => 'pending'
        ];
        
        $sql = "INSERT INTO notifications (user_id, admin_id, type, title, message, status) 
                VALUES (:user_id, :admin_id, :type, :title, :message, :status)";
        $db->query($sql, $notificationData);
        
        $db->commit();
        
        sendJsonResponse(true, 'Practical test rescheduled successfully', [
            'test_id' => $testId,
            'scheduled_date' => $input['scheduled_date'],
            'scheduled_time' => $input['scheduled_time'],
            'test_center_id' => $input['test_center_id']
        ]);
        
    } catch (Exception $e) {
        $db->rollback();
        sendJsonResponse(false, $e->getMessage(), null, 400);
    }
}

function checkAvailability($db, $params) {
    $slotType = $params['type'] ?? 'theory';
    $date = $params['date'] ?? date('Y-m-d');
    $time = $params['time'] ?? '';
    $testCenterId = $params['test_center_id'] ?? null;
    
    if ($slotType === 'practical') {
        $sql = "SELECT COUNT(*) as count FROM practical_tests 
                WHERE scheduled_date = :date AND scheduled_time = :time";
        $params_array = ['date' => $date, 'time' => $time];
        
        if ($testCenterId) {
            $sql .= " AND test_center_id = :test_center_id";
            $params_array['test_center_id'] = $testCenterId;
        }
    } else {
        $sql = "SELECT COUNT(*) as count FROM theory_tests 
                WHERE scheduled_date = :date AND scheduled_time = :time";
        $params_array = ['date' => $date, 'time' => $time];
    }
    
    $result = $db->fetch($sql, $params_array);
    $maxCapacity = 5; 
    $available = $result['count'] < $maxCapacity;
    
    sendJsonResponse(true, 'Availability checked', [
        'available' => $available,
        'booked_count' => $result['count'],
        'max_capacity' => $maxCapacity
    ]);
}

function generateTestLink($applicationId) {
    $baseUrl = 'https://licensexpress.lk/exam-window.php';
    $token = base64_encode($applicationId . '_' . time());
    return $baseUrl . '?token=' . $token;
}

function getTimeSlotIcon($time) {
    $hour = (int)date('H', strtotime($time));
    
    if ($hour >= 6 && $hour < 9) return '🌅';
    if ($hour >= 9 && $hour < 12) return '☀️';
    if ($hour >= 12 && $hour < 15) return '🕛';
    if ($hour >= 15 && $hour < 18) return '🌤️';
    if ($hour >= 18 && $hour < 21) return '🌆';
    return '🌇';
}

function getTestCenters($db) {
    try {
        $sql = "SELECT * FROM test_centers WHERE is_active = 1 ORDER BY name";
        $testCenters = $db->fetchAll($sql);
        
        
        foreach ($testCenters as &$center) {
            if ($center['facilities']) {
                $center['facilities'] = json_decode($center['facilities'], true);
            } else {
                $center['facilities'] = [];
            }
        }
        
        sendJsonResponse(true, 'Test centers retrieved successfully', $testCenters);
    } catch (Exception $e) {
        error_log("Error fetching test centers: " . $e->getMessage());
        sendJsonResponse(false, 'Failed to fetch test centers', null, 500);
    }
}

function sendJsonResponse($success, $message, $data = null, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit;
}

//test
?>