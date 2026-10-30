<?php
/**
 * StudentHub Portal - Authentication Engine & Session Fixation Shield
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

// 1. Verify CSRF Token
$token = $_POST['csrf_token'] ?? '';
if (!verify_csrf_token($token)) {
    header("Location: login.php?error=csrf_failed");
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: login.php?error=empty_fields");
    exit();
}

try {
    // 2. Query user via parameterized PDO statement (SQLi immunity)
    $stmt = $pdo->prepare("SELECT id, student_id, full_name, email, password_hash, role, status FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Account Status Check
        if ($user['status'] !== 'Active') {
            header("Location: login.php?error=account_suspended");
            exit();
        }

        // 3. DEFEND AGAINST SESSION FIXATION: Regenerate Session Token & Delete Old Session
        session_regenerate_id(true);

        // 4. Populate Session Payload
        $_SESSION['user_id']       = $user['id'];
        $_SESSION['student_id']    = $user['student_id'];
        $_SESSION['full_name']     = $user['full_name'];
        $_SESSION['email']         = $user['email'];
        $_SESSION['role']          = $user['role'];
        $_SESSION['last_activity'] = time();

        // 5. Record Security Audit Log
        $log_stmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, ip_address, user_agent, details) VALUES (?, 'AUTH_SUCCESS', ?, ?, ?)");
        $details = sprintf("Authenticated as %s. Session ID regenerated to protect against fixation.", $user['role']);
        $log_stmt->execute([
            $user['id'],
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown Agent',
            $details
        ]);

        // 6. Role-Based Redirection
        if ($user['role'] === 'Admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit();

    } else {
        // Log Failed Attempt
        $fail_stmt = $pdo->prepare("INSERT INTO audit_logs (action, ip_address, details) VALUES ('AUTH_FAILED', ?, ?)");
        $fail_stmt->execute([
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
            "Failed login attempt for email: " . htmlspecialchars($email)
        ]);

        header("Location: login.php?error=invalid_credentials");
        exit();
    }
} catch (PDOException $e) {
    header("Location: login.php?error=server_error");
    exit();
}
?>
