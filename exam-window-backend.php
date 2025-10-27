<?php

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'liscensexpress'; 

header('Content-Type: application/json; charset=utf-8');


if (isset($_SERVER['HTTP_ORIGIN'])) {
    ;
}


$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'DB connect error: ' . $mysqli->connect_error]);
    exit;
}


function json_out($data) {
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}


$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : '');


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if ($action === 'debug' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    json_out([
        'success' => true,
        'received_data' => $input,
        'session_data' => $_SESSION,
        'user_id_from_session' => $_SESSION['user_id'] ?? 'NOT SET',
        'message' => 'Debug endpoint working'
    ]);
}


if ($action === 'testSubmit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    
    $userId = null;
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    } elseif (isset($input['userId'])) {
        $userId = $input['userId'];
    }
    
    if (!$userId) {
        json_out(['success' => false, 'error' => 'User ID required']);
    }
    
    try {
        
        $userCheck = $mysqli->prepare("SELECT id FROM users WHERE user_id = ?");
        $userCheck->bind_param('s', $userId);
        $userCheck->execute();
        $userResult = $userCheck->get_result();
        
        if ($userResult->num_rows === 0) {
            json_out(['success' => false, 'error' => 'User not found']);
        }
        
        $userRow = $userResult->fetch_assoc();
        $dbUserId = $userRow['id'];
        $userCheck->close();
        
        
        $verifiedDate = date('Y-m-d H:i:s');
        $updateApp = $mysqli->prepare("UPDATE applications SET status = 'theory_passed', verified_date = ?, updated_at = NOW() WHERE user_id = ?");
        $updateApp->bind_param('si', $verifiedDate, $dbUserId);
        $updateApp->execute();
        $updateApp->close();
        
        
        $testId = 'TEST_' . time();
        $testDate = date('Y-m-d H:i:s');
        $insertTest = $mysqli->prepare("INSERT INTO theory_tests (user_id, test_id, score, total_questions, passed, test_date, scheduled_date, completed_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $score = 45;
        $total = 50;
        $passed = 1;
        $scheduledDate = date('Y-m-d', strtotime('-1 day')); // Scheduled yesterday
        $insertTest->bind_param('isiiisss', $dbUserId, $testId, $score, $total, $passed, $testDate, $scheduledDate, $testDate);
        $insertTest->execute();
        $insertTest->close();
        
        json_out([
            'success' => true,
            'message' => 'Test submission successful',
            'score' => $score,
            'total' => $total,
            'passed' => true,
            'test_id' => $testId,
            'user_id' => $userId,
            'db_user_id' => $dbUserId
        ]);
        
    } catch (Exception $e) {
        json_out(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
}


if ($action === 'getQuestions') {
    
    error_log("getQuestions action called");
    
    $sql = "SELECT id, question_text, option_a, option_b, option_c, option_d FROM theory_test_questions WHERE is_active = 1 ORDER BY id ASC";
    error_log("SQL Query: " . $sql);
    
    if ($res = $mysqli->query($sql)) {
        $questions = [];
        $count = 0;
        while ($row = $res->fetch_assoc()) {
            $count++;
            $questions[] = [
                'id' => (int)$row['id'],
                'text' => $row['question_text'],
                'image' => null,

                'options' => [
                    $row['option_a'],
                    $row['option_b'],
                    $row['option_c'],
                    $row['option_d'],
                ],
            ];
        }
        error_log("Found " . $count . " questions");
        $res->free();
        json_out(['success' => true, 'data' => $questions]);
    } else {
        error_log("Query failed: " . $mysqli->error);
        http_response_code(500);
        json_out(['success' => false, 'error' => 'Query failed: ' . $mysqli->error]);
    }
}


if ($action === 'getQuestion' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $mysqli->prepare("SELECT id, question_text, option_a, option_b, option_c, option_d FROM theory_test_questions WHERE id = ? AND is_active = 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $question = [
            'id' => (int)$row['id'],
            'text' => $row['question_text'],
            'image' => null,
            'options' => [
                $row['option_a'],
                $row['option_b'],
                $row['option_c'],
                $row['option_d'],
            ],
        ];
        json_out(['success' => true, 'data' => $question]);
    } else {
        http_response_code(404);
        json_out(['success' => false, 'error' => 'Question not found']);
    }
    $stmt->close();
}


if ($action === 'submitAnswers' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!$input || !isset($input['answers']) || !is_array($input['answers'])) {
        http_response_code(400);
        json_out(['success' => false, 'error' => 'Invalid JSON body or missing answers array']);
    }

    
    error_log("Exam submission - Input data: " . print_r($input, true));
    error_log("Exam submission - userId field: " . (isset($input['userId']) ? $input['userId'] : 'NOT SET'));
    error_log("Exam submission - userIdString field: " . (isset($input['userIdString']) ? $input['userIdString'] : 'NOT SET'));
    error_log("Exam submission - userIdInt field: " . (isset($input['userIdInt']) ? $input['userIdInt'] : 'NOT SET'));
    
    
    $userId = null;
    
    
    if (isset($input['userIdString']) && !empty($input['userIdString'])) {
        $userIdString = $input['userIdString'];
        error_log("Exam submission - Trying userIdString: " . $userIdString);
        $userCheck = $mysqli->prepare("SELECT id FROM users WHERE user_id = ?");
        if ($userCheck) {
            $userCheck->bind_param('s', $userIdString);
            $userCheck->execute();
            $userResult = $userCheck->get_result();
            if ($userResult->num_rows > 0) {
                $userRow = $userResult->fetch_assoc();
                $userId = (int)$userRow['id'];
                error_log("Exam submission - userIdString " . $userIdString . " found, converted to ID: " . $userId);
            } else {
                error_log("Exam submission - userIdString " . $userIdString . " not found");
            }
            $userCheck->close();
        }
    }
    
    
    if (!$userId && isset($input['userIdInt']) && !empty($input['userIdInt'])) {
        $userId = (int)$input['userIdInt'];
        error_log("Exam submission - Trying userIdInt: " . $userId);
        $userCheck = $mysqli->prepare("SELECT id FROM users WHERE id = ?");
        if ($userCheck) {
            $userCheck->bind_param('i', $userId);
            $userCheck->execute();
            $userResult = $userCheck->get_result();
            if ($userResult->num_rows > 0) {
                error_log("Exam submission - userIdInt " . $userId . " verified in database");
            } else {
                error_log("Exam submission - userIdInt " . $userId . " not found");
                $userId = null;
            }
            $userCheck->close();
        }
    }
    
    
    if (!$userId && isset($input['userId']) && !empty($input['userId'])) {
        $requestedUserId = $input['userId'];
        error_log("Exam submission - Trying userId fallback: " . $requestedUserId);
        

        $testUserId = (int)$requestedUserId;
        if ($testUserId > 0) {
            $userCheck = $mysqli->prepare("SELECT id FROM users WHERE id = ?");
            if ($userCheck) {
                $userCheck->bind_param('i', $testUserId);
                $userCheck->execute();
                $userResult = $userCheck->get_result();
                if ($userResult->num_rows > 0) {
                    $userId = $testUserId;
                    error_log("Exam submission - userId " . $testUserId . " verified as integer");
                }
                $userCheck->close();
            }
        }
        
        
        if (!$userId) {
            $userCheck = $mysqli->prepare("SELECT id FROM users WHERE user_id = ?");
            if ($userCheck) {
                $userCheck->bind_param('s', $requestedUserId);
                $userCheck->execute();
                $userResult = $userCheck->get_result();
                if ($userResult->num_rows > 0) {
                    $userRow = $userResult->fetch_assoc();
                    $userId = (int)$userRow['id'];
                    error_log("Exam submission - userId " . $requestedUserId . " found as string, converted to ID: " . $userId);
                }
                $userCheck->close();
            }
        }
    }
    
    elseif (isset($_SESSION['user_id'])) {
        $userId = (int)$_SESSION['user_id'];
        error_log("Exam submission - User ID from session user_id: " . $userId);
    } elseif (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
        $userId = (int)$_SESSION['user']['id'];
        error_log("Exam submission - User ID from session user.id: " . $userId);
    } elseif (isset($_SESSION['user']) && isset($_SESSION['user']['user_id'])) {
        $userId = (int)$_SESSION['user']['user_id'];
        error_log("Exam submission - User ID from session user.user_id: " . $userId);
    }

    error_log("Exam submission - Final User ID: " . ($userId ?: 'NULL'));

    if (!$userId) {
        error_log("Exam submission - No user ID found, returning 401");
        http_response_code(401);
        json_out(['success' => false, 'error' => 'User ID required. Please ensure you are logged in.']);
    }
    
    
    error_log("Exam submission - Session ID: " . session_id());
    error_log("Exam submission - User ID: " . $userId);
    error_log("Exam submission - Session data: " . print_r($_SESSION, true));

    $submitted = $input['answers'];

    
    $sql = "SELECT id, correct_answer FROM theory_test_questions WHERE is_active = 1 ORDER BY id ASC";
    if (!($res = $mysqli->query($sql))) {
        http_response_code(500);
        json_out(['success' => false, 'error' => 'Failed to load questions: ' . $mysqli->error]);
    }
    $questionIds = [];
    $correctAnswers = [];
    while ($row = $res->fetch_assoc()) {
        $questionIds[] = (int)$row['id'];
        $correctAnswers[] = strtoupper($row['correct_answer'] ?? '');
    }
    $res->free();

    $total = count($correctAnswers);


    $normalized = array_fill(0, $total, null);
    for ($i = 0; $i < $total; $i++) {
        if (!isset($submitted[$i]) || $submitted[$i] === null) {
            $normalized[$i] = null;
            continue;
        }
        $val = $submitted[$i];
        if (is_int($val) || ctype_digit(strval($val))) {
            $idx = (int)$val;
            $normalized[$i] = ($idx >= 0 && $idx <= 3) ? chr(65 + $idx) : null;
        } else {
            $letter = strtoupper(substr(trim($val), 0, 1));
            $normalized[$i] = in_array($letter, ['A','B','C','D']) ? $letter : null;
        }
    }

    
    $score = 0;
    $perQuestionCorrect = [];
    for ($i = 0; $i < $total; $i++) {
        $isCorrect = ($normalized[$i] !== null && $normalized[$i] === $correctAnswers[$i]);
        $perQuestionCorrect[] = $isCorrect ? 1 : 0;
        if ($isCorrect) $score++;
    }

    
    $passMark = 40;
    $stmt = $mysqli->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'theory_pass_mark' LIMIT 1");
    if ($stmt) {
        $stmt->execute();
        $stmt->bind_result($setting_value);
        if ($stmt->fetch()) {
            $p = (int)$setting_value;
            if ($p > 0) $passMark = $p;
        }
        $stmt->close();
    }
    $passed = ($score >= $passMark) ? 1 : 0;


    $mysqli->begin_transaction();
    try {
        
        $applicationId = null;
        $appStmt = $mysqli->prepare("SELECT id FROM applications WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        if ($appStmt) {
            $appStmt->bind_param('i', $userId);
            $appStmt->execute();
            $appStmt->bind_result($foundAppId);
            if ($appStmt->fetch()) {
                $applicationId = (int)$foundAppId;
                error_log("Exam submission - Found application ID: " . $applicationId);
            }
            $appStmt->close();
        }
        
        
        if (!$applicationId) {
            $appStmt = $mysqli->prepare("SELECT id FROM applications WHERE user_id = ? ORDER BY id DESC LIMIT 1");
            if ($appStmt) {
                $appStmt->bind_param('i', $userId);
                $appStmt->execute();
                $appStmt->bind_result($foundAppId);
                if ($appStmt->fetch()) {
                    $applicationId = (int)$foundAppId;
                    error_log("Exam submission - Found application ID (alternative query): " . $applicationId);
                }
                $appStmt->close();
            }
        }


        if ($applicationId === null) {
            
            $createAppSql = "INSERT INTO applications (user_id, created_at) VALUES (?, NOW())";
            $createAppStmt = $mysqli->prepare($createAppSql);
            if (!$createAppStmt) {
                throw new Exception('Cannot prepare application insert. Please ensure applications table allows minimal creation or create an application for the user.');
            }
            $createAppStmt->bind_param('i', $userId);
            if (!$createAppStmt->execute()) {
                $createAppStmt->close();
                throw new Exception('Failed to create application record: ' . $mysqli->error);
            }
            $applicationId = (int)$mysqli->insert_id;
            $createAppStmt->close();
        }

        if (!$applicationId) {
            throw new Exception('Unable to determine application_id for user; cannot insert test with NOT NULL FK.');
        }

        
        $insertTestSql = "INSERT INTO theory_tests (application_id, scheduled_date, scheduled_time, score, total_questions, passed, started_at, completed_at) VALUES (?, CURDATE(), CURTIME(), ?, ?, ?, NOW(), NOW())";
        $insStmt = $mysqli->prepare($insertTestSql);
        if (!$insStmt) throw new Exception('Prepare failed (insert test): ' . $mysqli->error);
        $insStmt->bind_param('iiii', $applicationId, $score, $total, $passed);
        if (!$insStmt->execute()) {
            $err = $insStmt->error;
            $insStmt->close();
            throw new Exception('Failed to insert theory_tests: ' . $err);
        }
        $testId = (int)$mysqli->insert_id;
        $insStmt->close();

        if (!$testId) throw new Exception('Failed to create theory_tests record');

        
        for ($i = 0; $i < $total; $i++) {
            $qId = isset($questionIds[$i]) ? (int)$questionIds[$i] : 0;
            $userAns = !empty($normalized[$i]) ? $normalized[$i] : null; 
            $isCorr = ($perQuestionCorrect[$i] ? 1 : 0);
            
            
            if ($userAns === null) {

                $answerStmt = $mysqli->prepare("INSERT INTO theory_test_answers (test_id, question_id, user_answer, is_correct, answered_at) VALUES (?, ?, NULL, ?, NOW())");
                if (!$answerStmt) throw new Exception('Prepare failed (insert answer): ' . $mysqli->error);
                $answerStmt->bind_param('iii', $testId, $qId, $isCorr);
            } else {
                
                $answerStmt = $mysqli->prepare("INSERT INTO theory_test_answers (test_id, question_id, user_answer, is_correct, answered_at) VALUES (?, ?, ?, ?, NOW())");
                if (!$answerStmt) throw new Exception('Prepare failed (insert answer): ' . $mysqli->error);
                $answerStmt->bind_param('iisi', $testId, $qId, $userAns, $isCorr);
            }
            
            if (!$answerStmt->execute()) {
                $answerStmt->close();
                throw new Exception('Failed to insert answer: ' . $mysqli->error);
            }
            $answerStmt->close();
        }

        
        $statusUpdate = $passed ? 'theory_passed' : 'theory_failed';
        $statusStmt = $mysqli->prepare("UPDATE applications SET status = ?, updated_at = NOW() WHERE id = ?");
        if ($statusStmt) {
            $statusStmt->bind_param('si', $statusUpdate, $applicationId);
            $statusStmt->execute();
            $statusStmt->close();
        }

        $mysqli->commit();
    } catch (Exception $ex) {
        $mysqli->rollback();
        http_response_code(500);
        json_out(['success' => false, 'error' => 'Failed to persist exam results: ' . $ex->getMessage()]);
    }

   
    json_out([
        'success' => true,
        'score' => $score,
        'total' => $total,
        'passed' => (bool)$passed,
        'test_id' => $testId,
        'correctAnswers' => $correctAnswers,
        'perQuestionCorrect' => $perQuestionCorrect
    ]);
}


http_response_code(400);
json_out(['success' => false, 'error' => 'Unknown action']);
?>