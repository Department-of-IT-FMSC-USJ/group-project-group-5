<?php

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'licensexpress'; 

header('Content-Type: application/json; charset=utf-8');


if (isset($_SERVER['HTTP_ORIGIN'])) {}


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


if ($action === 'getQuestions') {
   
    $sql = "SELECT id, question_text, option_a, option_b, option_c, option_d FROM theory_test_questions WHERE is_active = 1 ORDER BY id ASC";
    if ($res = $mysqli->query($sql)) {
        $questions = [];
        while ($row = $res->fetch_assoc()) {
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
        $res->free();
        json_out(['success' => true, 'data' => $questions]);
    } else {
        http_response_code(500);
        json_out(['success' => false, 'error' => 'Query failed: ' . $mysqli->error]);
    }
}

//return single q. by id
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

    // authenticated user 
    $userId = null;
    if (isset($_SESSION['user_id'])) {
        $userId = (int)$_SESSION['user_id'];
    } elseif (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
        $userId = (int)$_SESSION['user']['id'];
    } elseif (isset($_SESSION['user']) && isset($_SESSION['user']['user_id'])) {
        $userId = (int)$_SESSION['user']['user_id'];
    }

    if (!$userId) {
        http_response_code(401);
        json_out(['success' => false, 'error' => 'Authentication required. Please login before submitting the exam.']);
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
        
        // If no application found, try alternative column name
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

        // If no application found
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