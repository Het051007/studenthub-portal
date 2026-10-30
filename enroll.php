<?php
/**
 * StudentHub Portal - Course Enrollment Action
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/includes/rbac_check.php';

require_role('Student');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: student_dashboard.php");
    exit();
}

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    die("Security verification failed. CSRF token mismatch.");
}

$course_id = (int)($_POST['course_id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($course_id > 0) {
    try {
        // Prevent duplicate enrollment
        $check = $pdo->prepare("SELECT enrollment_id FROM enrollments WHERE user_id = ? AND course_id = ?");
        $check->execute([$user_id, $course_id]);
        
        if (!$check->fetch()) {
            $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id, semester, grade, attendance_pct, status) VALUES (?, ?, 3, 'Pending', 100.00, 'Enrolled')");
            $stmt->execute([$user_id, $course_id]);

            // Audit
            $audit = $pdo->prepare("INSERT INTO audit_logs (user_id, action, ip_address, details) VALUES (?, 'COURSE_ENROLLED', ?, ?)");
            $audit->execute([$user_id, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', "Enrolled into course ID: " . $course_id]);
        }
    } catch (PDOException $e) {
        // Handle error
    }
}

header("Location: student_dashboard.php");
exit();
?>
