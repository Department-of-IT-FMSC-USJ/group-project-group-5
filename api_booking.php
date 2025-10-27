<?php
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
    
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'get_time_slots':
            getTimeSlots($db, $_GET);
            break;
            
        case 'book_theory_test':
            bookTheoryTest($db);
            break;
            
        case 'book_practical_test':
            bookPracticalTest($db);
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
    
    
    $sql = "SELECT ts.slot_time, ts.max_capacity, 
                   COALESCE(bs.booked_count, 0) as booked_count
            FROM time_slots ts
            LEFT JOIN (
                SELECT slot_time, COUNT(*) as booked_count
                FROM booked_slots 
                WHERE slot_date = :date AND slot_type = :slot_type";
    
    $queryParams = ['date' => $date, 'slot_type' => $slotType];
    
    if ($testCenterId && $slotType === 'practical') {
        $sql .= " AND test_center_id = :test_center_id";
        $queryParams['test_center_id'] = $testCenterId;
    }
    
    $sql .= " GROUP BY slot_time
            ) bs ON ts.slot_time = bs.slot_time
            WHERE ts.slot_type = :slot_type2 AND ts.is_active = 1
            ORDER BY ts.slot_time";
    
    $queryParams['slot_type2'] = $slotType;
    
    $timeSlots = $db->fetchAll($sql, $queryParams);
    
    
    $availableSlots = [];
    foreach ($timeSlots as $slot) {
        $available = $slot['booked_count'] < $slot['max_capacity'];
        
        $availableSlots[] = [
            'time' => date('g:i A', strtotime($slot['slot_time'])),
            'time_24h' => $slot['slot_time'],
            'available' => $available,
            'booked_count' => $slot['booked_count'],
            'max_capacity' => $slot['max_capacity'],
            'icon' => getTimeSlotIcon($slot['slot_time'])
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
        
        
        $sql = "SELECT COUNT(*) as count FROM booked_slots 
                WHERE slot_date = :date AND slot_time = :time AND slot_type = 'theory'";
        $booked = $db->fetch($sql, [
            'date' => $input['scheduled_date'],
            'time' => $input['scheduled_time']
        ]);
        
        if ($booked['count'] > 0) {
            throw new Exception('Time slot is no longer available');
        }
        
        
        $sql = "INSERT INTO booked_slots (slot_date, slot_time, slot_type, application_id) 
                VALUES (:date, :time, 'theory', :application_id)";
        $db->query($sql, [
            'date' => $input['scheduled_date'],
            'time' => $input['scheduled_time'],
            'application_id' => $app['id'] // Use numeric ID
        ]);
        
       
        $testData = [
            'application_id' => $app['id'], // Use numeric ID
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
        
        
        $sql = "SELECT COUNT(*) as count FROM booked_slots 
                WHERE slot_date = :date AND slot_time = :time AND slot_type = 'practical' 
                AND test_center_id = :test_center_id";
        $booked = $db->fetch($sql, [
            'date' => $input['scheduled_date'],
            'time' => $input['scheduled_time'],
            'test_center_id' => $input['test_center_id']
        ]);
        
        if ($booked['count'] > 0) {
            throw new Exception('Time slot is no longer available for this test center');
        }
        
       
        $sql = "INSERT INTO booked_slots (slot_date, slot_time, slot_type, application_id, test_center_id) 
                VALUES (:date, :time, 'practical', :application_id, :test_center_id)";
        $db->query($sql, [
            'date' => $input['scheduled_date'],
            'time' => $input['scheduled_time'],
            'application_id' => $app['id'], // Use numeric ID
            'test_center_id' => $input['test_center_id']
        ]);
        
       
        $testData = [
            'application_id' => $app['id'], // Use numeric ID
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

function checkAvailability($db, $params) {
    $slotType = $params['type'] ?? 'theory';
    $date = $params['date'] ?? date('Y-m-d');
    $time = $params['time'] ?? '';
    $testCenterId = $params['test_center_id'] ?? null;
    
    $sql = "SELECT COUNT(*) as count FROM booked_slots 
            WHERE slot_date = :date AND slot_time = :time AND slot_type = :slot_type";
    $params_array = ['date' => $date, 'time' => $time, 'slot_type' => $slotType];
    
    if ($testCenterId && $slotType === 'practical') {
        $sql .= " AND test_center_id = :test_center_id";
        $params_array['test_center_id'] = $testCenterId;
    }
    
    $result = $db->fetch($sql, $params_array);
    $available = $result['count'] === 0;
    
    sendJsonResponse(true, 'Availability checked', [
        'available' => $available,
        'booked_count' => $result['count']
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
?>
