<?php


require_once 'database_connection.php';

class LicenseXpressQueries {
    private $db;
    
    public function __construct() {
        global $db;
        $this->db = $db;
    }
    
    // user management 
    
    public function createUser($userData) {
        $sql = "INSERT INTO users (user_id, nic, password_hash, email, phone, full_name, date_of_birth, gender, district, transmission_type) 
                VALUES (:user_id, :nic, :password_hash, :email, :phone, :full_name, :date_of_birth, :gender, :district, :transmission_type)";
        return $this->db->query($sql, $userData);
    }
    
    public function getUserByNIC($nic) {
        $sql = "SELECT * FROM users WHERE nic = :nic AND is_active = 1";
        return $this->db->fetch($sql, ['nic' => $nic]);
    }
    
    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email AND is_active = 1";
        return $this->db->fetch($sql, ['email' => $email]);
    }
    
    public function updateUserLastLogin($userId) {
        $sql = "UPDATE users SET last_login = NOW() WHERE id = :user_id";
        return $this->db->query($sql, ['user_id' => $userId]);
    }
    
    public function createUserSession($userId, $sessionToken, $expiresAt, $ipAddress, $userAgent) {
        $sql = "INSERT INTO user_sessions (user_id, session_token, expires_at, ip_address, user_agent) 
                VALUES (:user_id, :session_token, :expires_at, :ip_address, :user_agent)";
        return $this->db->query($sql, [
            'user_id' => $userId,
            'session_token' => $sessionToken,
            'expires_at' => $expiresAt,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);
    }
    
    public function validateSession($sessionToken) {
        $sql = "SELECT u.*, us.expires_at FROM users u 
                JOIN user_sessions us ON u.id = us.user_id 
                WHERE us.session_token = :session_token AND us.expires_at > NOW() AND u.is_active = 1";
        return $this->db->fetch($sql, ['session_token' => $sessionToken]);
    }
    
    
    // Application management
   
    public function createApplication($applicationData) {
        $sql = "INSERT INTO applications (application_id, user_id, status, progress) 
                VALUES (:application_id, :user_id, :status, :progress)";
        return $this->db->query($sql, $applicationData);
    }
    
    public function getApplicationByUserId($userId) {
        $sql = "SELECT * FROM applications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 1";
        return $this->db->fetch($sql, ['user_id' => $userId]);
    }
    
    public function updateApplicationStatus($applicationId, $status, $progress = null) {
        $sql = "UPDATE applications SET status = :status, progress = :progress, updated_at = NOW() WHERE application_id = :application_id";
        $params = ['application_id' => $applicationId, 'status' => $status];
        if ($progress !== null) {
            $params['progress'] = $progress;
        }
        return $this->db->query($sql, $params);
    }
    
    public function getAllApplications($filters = []) {
        $sql = "SELECT a.*, u.full_name, u.nic, u.email, u.phone 
                FROM applications a 
                JOIN users u ON a.user_id = u.id 
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND a.submitted_date >= :date_from";
            $params['date_from'] = $filters['date_from'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (u.full_name LIKE :search OR u.nic LIKE :search OR u.email LIKE :search)";
            $params['search'] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY a.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT :limit";
            $params['limit'] = $filters['limit'];
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    
    // doc management
   
    
    public function uploadDocument($documentData) {
        $sql = "INSERT INTO application_documents (application_id, document_type, file_name, file_path, file_size, file_type) 
                VALUES (:application_id, :document_type, :file_name, :file_path, :file_size, :file_type)";
        return $this->db->query($sql, $documentData);
    }
    
    public function getApplicationDocuments($applicationId) {
        $sql = "SELECT * FROM application_documents WHERE application_id = :application_id";
        return $this->db->fetchAll($sql, ['application_id' => $applicationId]);
    }
    
    public function updateDocumentStatus($documentId, $status, $reviewedBy = null, $rejectionReason = null) {
        $sql = "UPDATE application_documents SET status = :status, reviewed_by = :reviewed_by, reviewed_at = NOW(), rejection_reason = :rejection_reason 
                WHERE id = :document_id";
        return $this->db->query($sql, [
            'document_id' => $documentId,
            'status' => $status,
            'reviewed_by' => $reviewedBy,
            'rejection_reason' => $rejectionReason
        ]);
    }
    
    
    // payment management
    
    
    public function createPayment($paymentData) {
        $sql = "INSERT INTO payments (payment_id, application_id, amount, currency, payment_method, payment_status, transaction_id) 
                VALUES (:payment_id, :application_id, :amount, :currency, :payment_method, :payment_status, :transaction_id)";
        return $this->db->query($sql, $paymentData);
    }
    
    public function updatePaymentStatus($paymentId, $status, $transactionId = null) {
        $sql = "UPDATE payments SET payment_status = :status, transaction_id = :transaction_id, paid_date = NOW() 
                WHERE payment_id = :payment_id";
        return $this->db->query($sql, [
            'payment_id' => $paymentId,
            'status' => $status,
            'transaction_id' => $transactionId
        ]);
    }
    
    public function getPaymentByApplicationId($applicationId) {
        $sql = "SELECT * FROM payments WHERE application_id = :application_id ORDER BY created_at DESC LIMIT 1";
        return $this->db->fetch($sql, ['application_id' => $applicationId]);
    }
    
 
    // Theory test queries
    
    public function scheduleTheoryTest($testData) {
        $sql = "INSERT INTO theory_tests (application_id, scheduled_date, scheduled_time, test_link) 
                VALUES (:application_id, :scheduled_date, :scheduled_time, :test_link)";
        return $this->db->query($sql, $testData);
    }
    
    public function getTheoryTestByApplicationId($applicationId) {
        $sql = "SELECT * FROM theory_tests WHERE application_id = :application_id ORDER BY created_at DESC LIMIT 1";
        return $this->db->fetch($sql, ['application_id' => $applicationId]);
    }
    
    public function updateTheoryTestResults($testId, $score, $passed, $duration, $violations = null) {
        $sql = "UPDATE theory_tests SET score = :score, passed = :passed, completed_at = NOW(), duration_minutes = :duration, security_violations = :violations 
                WHERE id = :test_id";
        return $this->db->query($sql, [
            'test_id' => $testId,
            'score' => $score,
            'passed' => $passed,
            'duration' => $duration,
            'violations' => $violations
        ]);
    }
    
    public function getRandomTheoryQuestions($limit = 50) {
        $sql = "SELECT * FROM theory_test_questions WHERE is_active = 1 ORDER BY RAND() LIMIT :limit";
        return $this->db->fetchAll($sql, ['limit' => $limit]);
    }
    
    public function saveTheoryTestAnswers($testId, $answers) {
        $this->db->beginTransaction();
        try {
            foreach ($answers as $answer) {
                $sql = "INSERT INTO theory_test_answers (test_id, question_id, user_answer, is_correct) 
                        VALUES (:test_id, :question_id, :user_answer, :is_correct)";
                $this->db->query($sql, [
                    'test_id' => $testId,
                    'question_id' => $answer['question_id'],
                    'user_answer' => $answer['user_answer'],
                    'is_correct' => $answer['is_correct']
                ]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }    }
    
    
    public function schedulePracticalTest($testData) {
        $sql = "INSERT INTO practical_tests (application_id, test_center_id, scheduled_date, scheduled_time, examiner_id, vehicle_type, vehicle_details) 
                VALUES (:application_id, :test_center_id, :scheduled_date, :scheduled_time, :examiner_id, :vehicle_type, :vehicle_details)";
        return $this->db->query($sql, $testData);
    }
    
   
    
    public function getAvailableTimeSlots($slotType, $date, $testCenterId = null) {
        $sql = "SELECT ts.slot_time, ts.max_capacity, 
                       COALESCE(bs.booked_count, 0) as booked_count
                FROM time_slots ts
                LEFT JOIN (
                    SELECT slot_time, COUNT(*) as booked_count
                    FROM booked_slots 
                    WHERE slot_date = :date AND slot_type = :slot_type";
        
        $params = ['date' => $date, 'slot_type' => $slotType];
        
        if ($testCenterId && $slotType === 'practical') {
            $sql .= " AND test_center_id = :test_center_id";
            $params['test_center_id'] = $testCenterId;
        }
        
        $sql .= " GROUP BY slot_time
                ) bs ON ts.slot_time = bs.slot_time
                WHERE ts.slot_type = :slot_type AND ts.is_active = 1
                ORDER BY ts.slot_time";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    public function bookTimeSlot($slotData) {
        $sql = "INSERT INTO booked_slots (slot_date, slot_time, slot_type, application_id, test_center_id) 
                VALUES (:slot_date, :slot_time, :slot_type, :application_id, :test_center_id)";
        return $this->db->query($sql, $slotData);
    }
    
    public function checkSlotAvailability($slotDate, $slotTime, $slotType, $testCenterId = null) {
        $sql = "SELECT COUNT(*) as count FROM booked_slots 
                WHERE slot_date = :slot_date AND slot_time = :slot_time AND slot_type = :slot_type";
        
        $params = [
            'slot_date' => $slotDate,
            'slot_time' => $slotTime,
            'slot_type' => $slotType
        ];
        
        if ($testCenterId && $slotType === 'practical') {
            $sql .= " AND test_center_id = :test_center_id";
            $params['test_center_id'] = $testCenterId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] === 0;
    }
    
    public function getPracticalTestByApplicationId($applicationId) {
        $sql = "SELECT pt.*, tc.name as center_name, tc.address as center_address, e.full_name as examiner_name 
                FROM practical_tests pt 
                JOIN test_centers tc ON pt.test_center_id = tc.id 
                LEFT JOIN examiners e ON pt.examiner_id = e.id 
                WHERE pt.application_id = :application_id ORDER BY pt.created_at DESC LIMIT 1";
        return $this->db->fetch($sql, ['application_id' => $applicationId]);
    }
    
    public function updatePracticalTestResults($testId, $passed, $score, $feedback) {
        $sql = "UPDATE practical_tests SET passed = :passed, score = :score, feedback = :feedback, completed_at = NOW() 
                WHERE id = :test_id";
        return $this->db->query($sql, [
            'test_id' => $testId,
            'passed' => $passed,
            'score' => $score,
            'feedback' => $feedback
        ]);
    }
    

    
    public function getTestCenters() {
        $sql = "SELECT * FROM test_centers WHERE is_active = 1 ORDER BY name";
        return $this->db->fetchAll($sql);
    }
    
    public function getTestCenterById($centerId) {
        $sql = "SELECT * FROM test_centers WHERE id = :id AND is_active = 1";
        return $this->db->fetch($sql, ['id' => $centerId]);
    }
    
    public function getExaminersByCenter($centerId) {
        $sql = "SELECT * FROM examiners WHERE test_center_id = :center_id AND is_active = 1";
        return $this->db->fetchAll($sql, ['center_id' => $centerId]);
    }
    
    
    public function createLicense($licenseData) {
        $sql = "INSERT INTO licenses (license_number, application_id, user_id, category, transmission_type, issue_date, expiry_date, digital_url, qr_code) 
                VALUES (:license_number, :application_id, :user_id, :category, :transmission_type, :issue_date, :expiry_date, :digital_url, :qr_code)";
        return $this->db->query($sql, $licenseData);
    }
    
    public function getLicenseByUserId($userId) {
        $sql = "SELECT l.*, u.full_name, u.nic FROM licenses l 
                JOIN users u ON l.user_id = u.id 
                WHERE l.user_id = :user_id ORDER BY l.created_at DESC LIMIT 1";
        return $this->db->fetch($sql, ['user_id' => $userId]);
    }
    
    
    
    public function getAdminByUsername($username) {
        $sql = "SELECT * FROM admin_users WHERE username = :username AND is_active = 1";
        return $this->db->fetch($sql, ['username' => $username]);
    }
    
    public function logAdminActivity($adminId, $action, $targetType = null, $targetId = null, $details = null, $ipAddress = null, $userAgent = null) {
        $sql = "INSERT INTO admin_activity_log (admin_id, action, target_type, target_id, details, ip_address, user_agent) 
                VALUES (:admin_id, :action, :target_type, :target_id, :details, :ip_address, :user_agent)";
        return $this->db->query($sql, [
            'admin_id' => $adminId,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'details' => $details ? json_encode($details) : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent
        ]);
    }
    
    // NOTIFICATION QUERIES
   
    
    public function createNotification($notificationData) {
        $sql = "INSERT INTO notifications (user_id, admin_id, type, title, message, status) 
                VALUES (:user_id, :admin_id, :type, :title, :message, :status)";
        return $this->db->query($sql, $notificationData);
    }
    
    public function getNotificationsByUserId($userId, $limit = 10) {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit";
        return $this->db->fetchAll($sql, ['user_id' => $userId, 'limit' => $limit]);
    }
    
    public function updateNotificationStatus($notificationId, $status) {
        $sql = "UPDATE notifications SET status = :status, sent_at = NOW() WHERE id = :notification_id";
        return $this->db->query($sql, ['notification_id' => $notificationId, 'status' => $status]);
    }
    
    
    // system settings queries
   
    
    public function getSystemSetting($key) {
        $sql = "SELECT setting_value FROM system_settings WHERE setting_key = :key AND is_active = 1";
        $result = $this->db->fetch($sql, ['key' => $key]);
        return $result ? $result['setting_value'] : null;
    }
    
    public function getDistricts() {
        $sql = "SELECT * FROM districts WHERE is_active = 1 ORDER BY name";
        return $this->db->fetchAll($sql);
    }
    
    
    // statistics queries
    
    public function getApplicationStats() {
        $sql = "SELECT 
                    COUNT(*) as total_applications,
                    SUM(CASE WHEN status = 'pending_verification' THEN 1 ELSE 0 END) as pending_verification,
                    SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified,
                    SUM(CASE WHEN status = 'theory_scheduled' THEN 1 ELSE 0 END) as theory_scheduled,
                    SUM(CASE WHEN status = 'theory_passed' THEN 1 ELSE 0 END) as theory_passed,
                    SUM(CASE WHEN status = 'practical_scheduled' THEN 1 ELSE 0 END) as practical_scheduled,
                    SUM(CASE WHEN status = 'license_issued' THEN 1 ELSE 0 END) as license_issued
                FROM applications";
        return $this->db->fetch($sql);
    }
    
    public function getPaymentStats() {
        $sql = "SELECT 
                    COUNT(*) as total_payments,
                    SUM(amount) as total_revenue,
                    SUM(CASE WHEN payment_status = 'completed' THEN amount ELSE 0 END) as completed_revenue,
                    SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_payments
                FROM payments";
        return $this->db->fetch($sql);
    }
    
    public function getTestStats() {
        $sql = "SELECT 
                    COUNT(*) as total_theory_tests,
                    SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END) as passed_tests,
                    SUM(CASE WHEN passed = 0 THEN 1 ELSE 0 END) as failed_tests,
                    AVG(score) as average_score
                FROM theory_tests WHERE completed_at IS NOT NULL";
        return $this->db->fetch($sql);
    }
}

// Global queries instance
$queries = new LicenseXpressQueries();
?>
