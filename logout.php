<?php
/**
 * StudentHub Portal - Secure Session Destruction & Cookie Invalidation
 * Subject: Web Development Framework | CHARUSAT | 25CS062
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/auth_check.php';

// Record audit log if user was authenticated
if (isset($_SESSION['user_id'])) {
    try {
        $audit = $pdo->prepare("INSERT INTO audit_logs (user_id, action, ip_address, details) VALUES (?, 'USER_LOGOUT', ?, 'User initiated safe sign-out')");
        $audit->execute([$_SESSION['user_id'], $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
    } catch (Exception $e) {
        // Silently continue logout
    }
}

// 1. Unset all session superglobal variables
$_SESSION = [];

// 2. Invalidate the session cookie in client browser by expiring in the past
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 86400,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Destroy session data on server
session_destroy();

// 4. Redirect to login with confirmation
header("Location: login.php?msg=logged_out");
exit();
?>
